<?php

namespace App\Livewire\Pages\Admin;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;

use App\Models\Ticket as TicketModel;
use App\Models\User as UserModel;
use App\Models\TicketAssignment as TicketAssignmentModel;
use App\Models\TicketComment; // <— pastikan model ini ada

use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Illuminate\Validation\ValidationException;
use Throwable;

#[Layout('layouts.admin')]
#[Title('Admin - Ticket Detail')]
class Ticketshow extends Component
{
    public TicketModel $ticket;

    public string $status = 'open';
    public ?int $agent_id = null;

    public ?string $previewUrl = null; // modal image preview

    // NEW: composer komentar
    public string $newComment = '';

    private const ADMIN_ROLE_NAMES = ['Superadmin', 'Admin'];
    private const AGENT_ROLE_NAMES = ['User'];

    private const DB_TO_UI_STATUS_MAP = [
        'OPEN'        => 'open',
        'IN_PROGRESS' => 'in_progress',
        'RESOLVED'    => 'resolved',
        'CLOSED'      => 'closed',
        'DELETED'     => 'deleted',
    ];

    private const UI_TO_DB_STATUS_MAP = [
        'open'        => 'OPEN',
        'in_progress' => 'IN_PROGRESS',
        'resolved'    => 'RESOLVED',
        'closed'      => 'CLOSED',
        'deleted'     => 'DELETED',
    ];

    protected function currentAdmin()
    {
        $user = Auth::user();
        if ($user && !$user->relationLoaded('role')) {
            $user->load('role');
        }
        return $user;
    }

    protected function ensureAdmin(): bool
    {
        $u = $this->currentAdmin();
        return $u && $u->role && \in_array($u->role->name, self::ADMIN_ROLE_NAMES, true);
    }

    protected function isSuperadmin(): bool
    {
        $u = $this->currentAdmin();
        return $u && $u->role && $u->role->name === 'Superadmin';
    }

    protected function allowedAgentsQuery()
    {
        $admin = $this->currentAdmin();

        $q = UserModel::query()
            ->whereHas('role', fn($qr) => $qr->whereIn('name', self::AGENT_ROLE_NAMES));

        if (!$this->isSuperadmin()) {
            if (isset($admin->company_id)) {
                $q->where('company_id', $admin->company_id);
            }
            if (!empty($admin->department_id)) {
                $q->where('department_id', $admin->department_id);
            }
        }
        return $q;
    }

    public function mount(TicketModel $ticket)
    {
        if (!$this->ensureAdmin()) abort(403);

        $admin = $this->currentAdmin();
        if (!$this->isSuperadmin()) {
            if (isset($ticket->company_id) && isset($admin->company_id) && $ticket->company_id !== $admin->company_id) abort(403);
            if (isset($ticket->department_id) && !empty($admin->department_id) && $ticket->department_id !== $admin->department_id) abort(403);
        }

        $this->ticket = $ticket->load([
            'user:user_id,full_name',
            'department:department_id,department_name',
            'assignment.agent:user_id,full_name',
            'attachments:attachment_id,ticket_id,file_url,file_type,original_filename,bytes',
            // NEW: eager load comments + user
            'comments' => fn ($q) => $q->orderBy('created_at', 'asc'),
            'comments.user:user_id,full_name',
        ]);

        $this->status   = self::DB_TO_UI_STATUS_MAP[$ticket->status] ?? 'open';
        $this->agent_id = optional($ticket->assignment)->user_id;
    }

    public function save()
    {
        if (!$this->ensureAdmin()) {
            session()->flash('error', 'Unauthorized.');
            return;
        }

        $this->validate([
            'status'   => 'required|in:open,in_progress,resolved,closed,deleted',
            'agent_id' => 'nullable|integer',
        ]);

        $ticket = $this->ticket->fresh(['assignment']);

        if ($ticket->status === 'CLOSED') {
            session()->flash('error', 'Ticket is closed and cannot be edited.');
            return;
        }

        if ($this->agent_id) {
            $isAllowed = $this->allowedAgentsQuery()
                ->where('user_id', $this->agent_id)
                ->exists();
            if (!$isAllowed) {
                $this->addError('agent_id', 'Agent is not in your scope.');
                return;
            }
            TicketAssignmentModel::updateOrCreate(
                ['ticket_id' => $ticket->ticket_id],
                ['user_id'   => $this->agent_id]
            );
        } else {
            if ($ticket->assignment) $ticket->assignment()->delete();
        }

        $ticket->status = self::UI_TO_DB_STATUS_MAP[$this->status] ?? 'OPEN';
        $ticket->save();

        $this->ticket = $ticket->fresh([
            'user',
            'assignment.agent',
            'attachments',
            'comments' => fn ($q) => $q->orderBy('created_at', 'asc'),
            'comments.user:user_id,full_name',
        ]);

        session()->flash('message', "Ticket #{$ticket->ticket_id} updated.");
    }

    // ===== Image preview modal =====
    public function openPreview(string $url) { $this->previewUrl = $url; }
    public function closePreview() { $this->previewUrl = null; }

    // ===== NEW: Add comment =====
    public function addComment()
    {
        if (!$this->ensureAdmin()) {
            $this->dispatch('toast', type: 'error', title: 'Unauthorized', message: 'Anda tidak boleh berkomentar.', duration: 3000);
            return;
        }

        // Optional: cegah komentar di tiket CLOSED (boleh dihapus kalau mau tetap izinkan)
        if ($this->ticket->status === 'CLOSED') {
            $this->dispatch('toast', type: 'error', title: 'Ditolak', message: 'Ticket sudah CLOSED.', duration: 3000);
            return;
        }

        try {
            $this->validate([
                'newComment' => ['required','string','min:2'],
            ], [
                'newComment.required' => 'Komentar tidak boleh kosong.',
                'newComment.min'      => 'Komentar terlalu pendek.',
            ]);

            $this->ticket->comments()->create([
                'user_id'      => Auth::id(),
                'comment_text' => $this->newComment,
            ]);

            $this->reset('newComment');

            // refresh urutan komentar
            $this->ticket->load([
                'comments' => fn ($q) => $q->orderBy('created_at', 'asc'),
                'comments.user:user_id,full_name',
            ]);

            $this->dispatch('toast', type: 'success', title: 'Berhasil', message: 'Komentar ditambahkan.', duration: 2500);

        } catch (ValidationException $e) {
            $first = collect($e->validator->errors()->all())->first() ?? 'Periksa input Anda.';
            $this->dispatch('toast', type: 'error', title: 'Validasi', message: $first, duration: 3000);
            throw $e;
        } catch (Throwable $e) {
            $this->dispatch('toast', type: 'error', title: 'Gagal', message: 'Terjadi kesalahan saat menambah komentar.', duration: 3000);
        }
    }

    public function render()
    {
        $agents = $this->allowedAgentsQuery()->orderBy('full_name')->get(['user_id','full_name']);

        return view('livewire.pages.admin.ticketshow', [
            'agents' => $agents,
            't'      => $this->ticket,
        ]);
    }
}
