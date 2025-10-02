<?php

namespace App\Livewire\Pages\Admin;

use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Auth;
use App\Models\Ticket as TicketModel;
use App\Models\User as UserModel;
use App\Models\TicketAssignment as TicketAssignmentModel;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;

#[Layout('layouts.admin')]
#[Title('Admin-Ticket')]
class Ticket extends Component
{
    use WithPagination;

    public ?string $search = null;
    public ?string $priority = null;
    public ?string $status = null;

    protected $paginationTheme = 'tailwind';

    private const ADMIN_ROLE_NAMES = ['Superadmin', 'Admin'];
    private const AGENT_ROLE_NAMES = ['User'];
    private const DB_ALLOWED_STATUSES = ['OPEN', 'IN_PROGRESS', 'RESOLVED', 'CLOSED', 'DELETED'];
    private const UI_TO_DB_STATUS_MAP = [
        'open' => 'OPEN',
        'in_progress' => 'IN_PROGRESS',
        'resolved' => 'RESOLVED',
        'closed' => 'CLOSED',
        'deleted' => 'DELETED',
    ];

    public function tick()
    {
    }
    public function updatingSearch()
    {
        $this->resetPage();
    }
    public function updatingPriority()
    {
        $this->resetPage();
    }
    public function updatingStatus()
    {
        $this->resetPage();
    }

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

    public function getAgentsProperty()
    {
        return $this->allowedAgentsQuery()
            ->orderBy('full_name')
            ->get(['user_id', 'full_name', 'department_id']);
    }

    public bool $modalEdit = false;
    public ?int $editId = null;
    public ?int $edit_agent_id = null;
    public string $edit_status = 'open';

    public function openEdit(int $ticketId): void
    {
        if (!$this->ensureAdmin()) {
            session()->flash('error', 'Unauthorized.');
            return;
        }

        $ticket = TicketModel::with('assignment')->findOrFail($ticketId);

        if ($ticket->status === 'CLOSED') {
            session()->flash('error', "Ticket #{$ticketId} is closed and cannot be edited.");
            return;
        }

        $this->editId = $ticketId;
        $this->edit_agent_id = optional($ticket->assignment)->user_id;
        $this->edit_status = strtolower($ticket->status);
        $this->modalEdit = true;
    }

    public function closeEdit(): void
    {
        $this->reset(['modalEdit', 'editId', 'edit_agent_id', 'edit_status']);
        $this->edit_status = 'open';
    }

    public function saveEdit(): void
    {
        if (!$this->ensureAdmin()) {
            session()->flash('error', 'Unauthorized.');
            return;
        }
        if (!$this->editId)
            return;

        $ticket = TicketModel::with('assignment')->findOrFail($this->editId);

        if ($ticket->status === 'CLOSED') {
            $this->addError('edit_status', 'This ticket is already closed.');
            return;
        }

        if ($this->edit_agent_id) {
            $isAllowed = $this->allowedAgentsQuery()
                ->where('user_id', $this->edit_agent_id)
                ->exists();
            if (!$isAllowed) {
                $this->addError('edit_agent_id', 'Agent is not in your department.');
                return;
            }
        }

        if ($this->edit_agent_id) {
            TicketAssignmentModel::updateOrCreate(
                ['ticket_id' => $ticket->ticket_id],
                ['user_id' => $this->edit_agent_id]
            );
        } else {
            if ($ticket->assignment) {
                $ticket->assignment()->delete();
            }
        }

        $targetDb = self::UI_TO_DB_STATUS_MAP[$this->edit_status] ?? 'OPEN';
        if (!\in_array($targetDb, self::DB_ALLOWED_STATUSES, true)) {
            $this->addError('edit_status', 'Invalid status.');
            return;
        }

        if ($ticket->status !== $targetDb) {
            $ticket->status = $targetDb;
            $ticket->save();
        }

        $id = $ticket->ticket_id;
        $this->closeEdit();
        session()->flash('message', "Ticket #{$id} saved.");
    }

    public function deleteTicket(int $ticketId): void
    {
        if (!$this->ensureAdmin()) {
            session()->flash('error', 'Unauthorized.');
            return;
        }

        $ticket = TicketModel::find($ticketId);
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

        session()->flash('message', "Ticket #{$ticketId} moved to Trash.");
    }

    public function render()
    {
        $query = TicketModel::query()
            ->with([
                'user:user_id,full_name',
                'assignment.agent:user_id,full_name',
            ])
            ->orderByDesc('ticket_id');

        if ($this->search) {
            $s = '%' . $this->search . '%';
            $query->where(
                fn($q) =>
                $q->where('subject', 'like', $s)
                    ->orWhere('description', 'like', $s)
            );
        }

        if ($this->priority) {
            $query->where('priority', $this->priority);
        }

        if ($this->status) {
            $dbStatus = self::UI_TO_DB_STATUS_MAP[$this->status] ?? null;
            if ($dbStatus) {
                $query->where('status', $dbStatus);
            }
        } else {
            $query->where('status', '!=', 'DELETED');
        }

        $tickets = $query->paginate(30);
        $collection = $tickets->getCollection();
        $open = $collection->filter(fn($t) => $t->status === 'OPEN');
        $inProgress = $collection->filter(fn($t) => $t->status === 'IN_PROGRESS');
        $resolved = $collection->filter(fn($t) => $t->status === 'RESOLVED');
        $closed = $collection->filter(fn($t) => $t->status === 'CLOSED');
        $deleted = null;
        if ($this->status === 'deleted') {
            $deleted = $collection->filter(fn($t) => $t->status === 'DELETED');
        }

        return view('livewire.pages.admin.ticket', [
            'tickets' => $tickets,
            'open' => $open,
            'inProgress' => $inProgress,
            'resolved' => $resolved,
            'closed' => $closed,
            'deleted' => $deleted,
            'agents' => $this->agents,
        ]);
    }
}
