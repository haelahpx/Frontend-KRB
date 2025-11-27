<?php

namespace App\Livewire\Pages\User;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Illuminate\Support\Facades\Auth;
use App\Models\Ticket;
use App\Models\TicketComment;
use App\Models\TicketAssignment;
use Illuminate\Validation\ValidationException;
use Throwable;
use App\Models\User; // Ensure User model is imported

#[Layout('layouts.app')]
#[Title('Ticket Detail')]
class Ticketshow extends Component
{
    public Ticket $ticket;
    public string $newComment = '';

    public bool $canEditStatus = false;
    public string $statusEdit = '';
    protected array $allowedStatuses = ['OPEN', 'IN_PROGRESS', 'RESOLVED', 'CLOSED'];
    
    // Flag to control comment box visibility and submission permission
    public bool $canComment = false; 

    public function mount(Ticket $ticket): void
    {
        $this->ensureAccess($ticket);

        $this->ticket = $ticket->load([
            'department:department_id,department_name',
            'requesterDepartment:department_id,department_name',
            'user:user_id,full_name',
            'attachments',
            'comments' => fn($q) => $q->orderBy('created_at', 'asc'),
            'comments.user:user_id,full_name',
            'assignments' => fn($q) => $q->whereNull('deleted_at')->with([
                'user:user_id,full_name'
            ]),
        ]);

        $this->canEditStatus = $this->isAssignedAgent($this->ticket->ticket_id, Auth::user()->user_id);
        $this->statusEdit    = $this->ticket->status;
        
        // Calculate comment permission on mount using custom logic
        $this->canComment = $this->checkCommentPermission();
    }

    /**
     * Ensure the authenticated user has general access to view the ticket.
     */
    protected function ensureAccess(Ticket $ticket): void
    {
        // This will automatically check app/Policies/TicketPolicy.php
        $this->authorize('view', $ticket);
    }

    /**
     * Check if the authenticated user is currently assigned to this ticket.
     */
    protected function isAssignedAgent(int $ticketId, int $userId): bool
    {
        return TicketAssignment::where('ticket_id', $ticketId)
            ->where('user_id', $userId)
            ->whereNull('deleted_at')
            ->exists();
    }
    
    /**
     * Determine if the current authenticated user can add a comment.
     * Permissions: Admin/Superadmin OR Ticket Creator OR Assigned Agent.
     */
    protected function checkCommentPermission(): bool
    {
        // Check 1: Cannot comment if the ticket is CLOSED (resolved/closed/complete)
        $status = strtolower($this->ticket->status ?? 'open');
        $isClosed = in_array($status, ['resolved', 'closed', 'complete'], true);
        if ($isClosed) {
            return false;
        }

        /** @var User $user */
        $user = Auth::user();
        $userId = $user->user_id;
        
        // Check 2: Superadmins (role_id 1) and Admins (role_id 2) can always comment
        if (in_array($user->role_id, [1, 2], true)) {
            return true;
        }

        // Check 3: The ticket creator (Requester) can comment
        if ($userId === $this->ticket->user_id) {
            return true;
        }

        // Check 4: Any other user (Agent/User/Receptionist) can ONLY comment if they are an Assigned Agent.
        $isAssigned = $this->isAssignedAgent($this->ticket->ticket_id, $userId);

        return $isAssigned;
    }

    /**
     * Handle the submission for updating the ticket status.
     */
    public function updateStatus(): void
    {
        if (! $this->canEditStatus) {
            abort(403);
        }

        $this->validate([
            'statusEdit' => ['required', 'string', 'in:OPEN,IN_PROGRESS,RESOLVED,CLOSED'],
        ]);

        $this->ticket->update([
            'status'     => $this->statusEdit,
            'updated_at' => now(),
        ]);

        $this->ticket->refresh()->load([
            'comments' => fn($q) => $q->orderBy('created_at', 'asc'),
            'comments.user:user_id,full_name',
            'assignments' => fn($q) => $q->whereNull('deleted_at')->with([
                'user:user_id,full_name'
            ]),
        ]);
        
        // Recalculate comment permission in case the status change affects it (e.g., closing the ticket)
        $this->canComment = $this->checkCommentPermission();

        $this->dispatch('toast', type: 'success', title: 'Updated', message: 'Status updated.', duration: 2500);
    }

    /**
     * Handle the submission for adding a new comment.
     */
    public function addComment(): void
    {
        // Security check: must have permission to comment
        if (! $this->canComment) {
            abort(403, 'Unauthorized to post a comment on this ticket.');
        }

        try {
            $this->validate([
                'newComment' => ['required', 'string', 'min:3'],
            ]);

            TicketComment::create([
                'ticket_id'    => $this->ticket->ticket_id,
                'user_id'      => Auth::id(),
                'comment_text' => $this->newComment,
            ]);

            $this->reset('newComment');

            $this->ticket->load([
                'comments' => fn($q) => $q->orderBy('created_at', 'asc'),
                'comments.user:user_id,full_name',
            ]);

            $this->dispatch('toast', type: 'success', title: 'Berhasil', message: 'Komentar ditambahkan.', duration: 3000);
        } catch (ValidationException $e) {
            $first = collect($e->validator->errors()->all())->first() ?? 'Periksa kembali input Anda.';
            $this->dispatch('toast', type: 'error', title: 'Validasi Gagal', message: $first, duration: 3000);
            throw $e;
        } catch (Throwable $e) {
            $this->dispatch('toast', type: 'error', title: 'Gagal', message: 'Terjadi kesalahan saat menambah komentar.', duration: 3000);
        }
    }

    public function render()
    {
        return view('livewire.pages.user.ticketshow', [
            'allowedStatuses' => $this->allowedStatuses,
        ]);
    }
}