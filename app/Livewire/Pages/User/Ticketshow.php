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

#[Layout('layouts.app')]
#[Title('Ticket Detail')]
class Ticketshow extends Component
{
    public Ticket $ticket;
    public string $newComment = '';

    public bool $canEditStatus = false;
    public string $statusEdit = '';
    protected array $allowedStatuses = ['OPEN', 'IN_PROGRESS', 'RESOLVED', 'CLOSED'];

    public function mount(Ticket $ticket): void
    {
        $this->ensureAccess($ticket);

        $this->ticket = $ticket->load([
            'department:department_id,department_name',
            'user:user_id,full_name',
            'attachments',
            'comments' => fn ($q) => $q->orderBy('created_at', 'asc'),
            'comments.user:user_id,full_name',
            // load assignments with agent user (for showing agent names)
            'assignments' => fn ($q) => $q->whereNull('deleted_at')->with([
                'user:user_id,full_name'
            ]),
        ]);

        $this->canEditStatus = $this->isAssignedAgent($this->ticket->ticket_id, Auth::user()->user_id);
        $this->statusEdit    = $this->ticket->status;
    }

    protected function ensureAccess(Ticket $ticket): void
    {
        $me = Auth::user();
        $isRequester = $ticket->user_id === $me->user_id;
        $isAssigned  = $this->isAssignedAgent($ticket->ticket_id, $me->user_id);
        if (! $isRequester && ! $isAssigned) {
            abort(403);
        }
    }

    protected function isAssignedAgent(int $ticketId, int $userId): bool
    {
        return TicketAssignment::where('ticket_id', $ticketId)
            ->where('user_id', $userId)
            ->whereNull('deleted_at')
            ->exists();
    }

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
            'comments' => fn ($q) => $q->orderBy('created_at', 'asc'),
            'comments.user:user_id,full_name',
            'assignments' => fn ($q) => $q->whereNull('deleted_at')->with([
                'user:user_id,full_name'
            ]),
        ]);

        $this->dispatch('toast', type: 'success', title: 'Updated', message: 'Status updated.', duration: 2500);
    }

    public function addComment(): void
    {
        try {
            $this->validate([
                'newComment' => ['required', 'string', 'min:3'],
            ]);

            $this->ensureAccess($this->ticket);

            TicketComment::create([
                'ticket_id'    => $this->ticket->ticket_id,
                'user_id'      => Auth::id(),
                'comment_text' => $this->newComment,
            ]);

            $this->reset('newComment');

            $this->ticket->load([
                'comments' => fn ($q) => $q->orderBy('created_at', 'asc'),
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
