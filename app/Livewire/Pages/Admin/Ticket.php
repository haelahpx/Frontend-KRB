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
    public ?string $status   = null;  // open|in_progress|resolved|closed (no deleted in UI)

    protected string $paginationTheme = 'tailwind';

    private const ADMIN_ROLE_NAMES = ['Superadmin', 'Admin'];

    private const UI_TO_DB_STATUS_MAP = [
        'open'        => 'OPEN',
        'in_progress' => 'IN_PROGRESS',
        'resolved'    => 'RESOLVED',
        'closed'      => 'CLOSED',
    ];

    public function tick() {} // for wire:poll

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

    public function deleteTicket(int $ticketId): void
    {
        if (!$this->ensureAdmin()) {
            session()->flash('error', 'Unauthorized.');
            return;
        }

        // If your Ticket PK is ticket_id, ensure the model sets $primaryKey='ticket_id'
        $ticket = TicketModel::where('ticket_id', $ticketId)->first();
        if (!$ticket) {
            session()->flash('error', 'Ticket not found.');
            return;
        }

        if ($ticket->status === 'CLOSED') {
            session()->flash('error', "Ticket #{$ticketId} is closed and cannot be deleted.");
            return;
        }

        if ($ticket->status === 'DELETED') {
            session()->flash('message', "Ticket #{$ticketId} already deleted.");
            return;
        }

        $ticket->status = 'DELETED';
        $ticket->save();

        $this->resetPage();
        session()->flash('message', "Ticket #{$ticketId} moved to Trash.");
    }

    public function render()
    {
        $query = TicketModel::query()
            ->with([
                // Ticket owner (user) + its department_id for potential chaining
                'user:user_id,full_name,department_id',
                // Department that is directly referenced by the ticket (department_id on tickets table)
                'department:department_id,department_name',
                // If you have assignment relation
                'assignment.agent:user_id,full_name',
                // Attachments for count & quick preview purposes
                'attachments:attachment_id,ticket_id,file_url,file_type,original_filename,bytes',
            ])
            ->withCount('attachments')
            ->orderByDesc('ticket_id');

        $admin = $this->currentAdmin();

        // Hide DELETED tickets
        $query->where('status', '!=', 'DELETED');

        // Multi-tenant scoping (only if columns exist)
        if ($admin && !$this->isSuperadmin()) {
            if (Schema::hasColumn('tickets', 'company_id') && isset($admin->company_id)) {
                $query->where('company_id', $admin->company_id);
            }
            if (Schema::hasColumn('tickets', 'department_id') && !empty($admin->department_id)) {
                $query->where('department_id', $admin->department_id);
            }
        }

        // Search
        if ($this->search) {
            $s = '%' . $this->search . '%';
            $query->where(function ($q) use ($s) {
                $q->where('subject', 'like', $s)
                  ->orWhere('description', 'like', $s);
            });
        }

        // Priority
        if ($this->priority) {
            $query->where('priority', $this->priority);
        }

        // Status (UI → DB map)
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
