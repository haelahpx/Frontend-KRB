<?php

namespace App\Livewire\Pages\Admin;

use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use App\Models\Ticket as TicketModel;
use App\Models\Department;
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

    // ===== Department switcher =====
    public string $company_name = '-';
    public string $department_name = '-';
    public array  $deptOptions = [];              // [['id'=>..,'name'=>..], ...]
    public ?int   $selected_department_id = null; // live switch
    public ?int   $primary_department_id  = null; // users.department_id
    public bool $showSwitcher = false;    

    public function mount(): void
    {
        $user = Auth::user()->loadMissing(['company', 'department', 'role']);
        $this->company_name = optional($user->company)->company_name ?? '-';
        $this->primary_department_id = $user->department_id ?: null;

        $this->loadUserDepartments();

        // default selection: primary or first option
        if (!$this->selected_department_id) {
            $this->selected_department_id = $this->primary_department_id
                ?: ($this->deptOptions[0]['id'] ?? null);
        }
        $this->department_name = $this->resolveDeptName($this->selected_department_id);
    }

    protected function loadUserDepartments(): void
    {
        $user = Auth::user();

        $rows = DB::table('user_departments as ud')
            ->join('departments as d', 'd.department_id', '=', 'ud.department_id')
            ->where('ud.user_id', $user->user_id)
            ->orderBy('d.department_name')
            ->get(['d.department_id as id', 'd.department_name as name']);

        $this->deptOptions = $rows->map(fn($r) => ['id' => (int)$r->id, 'name' => (string)$r->name])->values()->all();


        $this->showSwitcher = true; 

        // fallback: if no pivot but has primary
        if (empty($this->deptOptions) && $this->primary_department_id) {
            $name = Department::where('department_id', $this->primary_department_id)->value('department_name') ?? 'Unknown';
            $this->deptOptions = [['id' => (int)$this->primary_department_id, 'name' => (string)$name]];
            $this->showSwitcher = false;
        }
    }

    protected function resolveDeptName(?int $deptId): string
    {
        if (!$deptId) return '-';
        foreach ($this->deptOptions as $opt) {
            if ($opt['id'] === (int)$deptId) return $opt['name'];
        }
        return Department::where('department_id', $deptId)->value('department_name') ?? '-';
    }

    public function resetToPrimaryDepartment(): void
    {
        if ($this->primary_department_id) {
            $this->selected_department_id = $this->primary_department_id;
            $this->department_name = $this->resolveDeptName($this->selected_department_id);
            $this->resetPage();
        }
    }

    // Livewire may call either depending on version
    public function updatedSelectedDepartment_id(): void
    {
        $this->updatedSelectedDepartmentId();
    }
    public function updatedSelectedDepartmentId(): void
    {
        $allowed = collect($this->deptOptions)->pluck('id')->all();
        $id = (int) $this->selected_department_id;

        if (!in_array($id, $allowed, true)) {
            $this->selected_department_id = $this->primary_department_id ?: ($this->deptOptions[0]['id'] ?? null);
            $id = (int) $this->selected_department_id;
        }
        $this->department_name = $this->resolveDeptName($id);
        $this->resetPage();
    }

    public function tick() {}

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
        if ($user && !$user->relationLoaded('role')) $user->load('role');
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
        $this->search = $this->priority = $this->status = null;
        $this->resetPage();
    }

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

        $ticket->delete();
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

        // Company scope for non-superadmin (and superadmin if company_id set)
        if (Schema::hasColumn('tickets', 'company_id') && isset($admin->company_id)) {
            $query->where('company_id', $admin->company_id);
        }

        // Department scope: use switcher if set; else fall back to user's dept (non-superadmin)
        $deptId = $this->selected_department_id ?: null;
        if ($deptId) {
            if (Schema::hasColumn('tickets', 'department_id')) {
                $query->where('department_id', $deptId);
            }
        } elseif ($admin && !$this->isSuperadmin()) {
            if (Schema::hasColumn('tickets', 'department_id') && !empty($admin->department_id)) {
                $query->where('department_id', $admin->department_id);
            }
        }

        if ($this->search) {
            $s = '%' . $this->search . '%';
            $query->where(fn($q) => $q->where('subject', 'like', $s)->orWhere('description', 'like', $s));
        }

        if ($this->priority) $query->where('priority', $this->priority);

        if ($this->status) {
            $dbStatus = self::UI_TO_DB_STATUS_MAP[$this->status] ?? null;
            if ($dbStatus) $query->where('status', $dbStatus);
        }

        $tickets = $query->paginate(12);

        return view('livewire.pages.admin.ticket', compact('tickets'));
    }
}
