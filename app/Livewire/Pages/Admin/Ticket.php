<?php

namespace App\Livewire\Pages\Admin;

use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use App\Models\Ticket as TicketModel;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;

#[Layout('layouts.admin')]
#[Title('Admin - Ticket')]
class Ticket extends Component
{
    use WithPagination;

    public ?string $search   = null;
    public ?string $priority = null;  // low|medium|high
    public ?string $status   = null;  // open|in_progress|resolved|closed

    protected string $paginationTheme = 'tailwind';

    private const ADMIN_ROLE_NAMES = ['Superadmin', 'Admin'];

    private const UI_TO_DB_STATUS_MAP = [
        'open'        => 'OPEN',
        'in_progress' => 'IN_PROGRESS',
        'resolved'    => 'RESOLVED',
        'closed'      => 'CLOSED',
    ];

    public function tick() {}

    public function updatingSearch()   { $this->resetPage(); }
    public function updatingPriority() { $this->resetPage(); }
    public function updatingStatus()   { $this->resetPage(); }

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

    public function resetFilters(): void
    {
        $this->search = null;
        $this->priority = null;
        $this->status = null;
        $this->resetPage();
    }

    /**
     * Soft delete Ticket (set deleted_at).
     */
    public function deleteTicket(int $ticketId): void
    {
        if (!$this->ensureAdmin()) {
            session()->flash('error', 'Unauthorized.');
            return;
        }

        $ticket = TicketModel::where('ticket_id', $ticketId)->first();
        if (!$ticket) {
            session()->flash('error', 'Ticket not found.');
            return;
        }


        $ticket->delete(); // <-- soft delete

        $this->resetPage();
        session()->flash('message', "Ticket #{$ticketId} moved to Trash.");
    }


    public function restoreTicket(int $ticketId): void
    {
        if (!$this->ensureAdmin()) {
            session()->flash('error', 'Unauthorized.');
            return;
        }

        $ticket = TicketModel::onlyTrashed()->where('ticket_id', $ticketId)->first();
        if (!$ticket) {
            session()->flash('error', 'Trashed ticket not found.');
            return;
        }

        $ticket->restore();
        session()->flash('message', "Ticket #{$ticketId} restored.");
    }

    public function forceDeleteTicket(int $ticketId): void
    {
        if (!$this->isSuperadmin()) {
            session()->flash('error', 'Only Superadmin can permanently delete.');
            return;
        }

        $ticket = TicketModel::onlyTrashed()->where('ticket_id', $ticketId)->first();
        if (!$ticket) {
            session()->flash('error', 'Trashed ticket not found.');
            return;
        }

        $ticket->forceDelete();

        session()->flash('message', "Ticket #{$ticketId} permanently deleted.");
    }

    public function render()
    {
        $query = TicketModel::query()
            ->with([
                'user:user_id,full_name,department_id',
                'department:department_id,department_name',
                'assignment.agent:user_id,full_name',
                'attachments:attachment_id,ticket_id,file_url,file_type,original_filename,bytes',
            ])
            ->withCount('attachments')
            ->orderByDesc('ticket_id');

        $admin = $this->currentAdmin();

        if ($admin && !$this->isSuperadmin()) {
            if (Schema::hasColumn('tickets', 'company_id') && isset($admin->company_id)) {
                $query->where('company_id', $admin->company_id);
            }
            if (Schema::hasColumn('tickets', 'department_id') && !empty($admin->department_id)) {
                $query->where('department_id', $admin->department_id);
            }
        }

        if ($this->search) {
            $s = '%' . $this->search . '%';
            $query->where(function ($q) use ($s) {
                $q->where('subject', 'like', $s)
                  ->orWhere('description', 'like', $s);
            });
        }

        if ($this->priority) {
            $query->where('priority', $this->priority);
        }

        if ($this->status) {
            $dbStatus = self::UI_TO_DB_STATUS_MAP[$this->status] ?? null;
            if ($dbStatus) {
                $query->where('status', $dbStatus);
            }
        }

        $tickets = $query->paginate(30);

        return view('livewire.pages.admin.ticket', [
            'tickets' => $tickets,
        ]);
    }
}
