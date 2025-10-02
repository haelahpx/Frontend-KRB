<?php

namespace App\Livewire\Pages\User;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Illuminate\Support\Facades\Auth;
use App\Models\Ticket;
use App\Models\Department;

#[Layout('layouts.app')]
#[Title('Ticket Status')]
class Ticketstatus extends Component
{
    // accepts lowercase (UI) but maps to UPPERCASE (DB)
    public string $statusFilter      = '';       // '', open|assigned|in_progress|resolved|closed
    public string $priorityFilter    = '';       // '', low|medium|high
    public string $departmentFilter  = '';       // '' or department_id
    public string $sortFilter        = 'recent'; // recent|oldest|due

    public $departments;

    private const DB_ALLOWED_STATUSES = ['OPEN','ASSIGNED','IN_PROGRESS','RESOLVED','CLOSED'];

    private const UI_TO_DB_STATUS_MAP = [
        'open'        => 'OPEN',
        'assigned'    => 'ASSIGNED',
        'in_progress' => 'IN_PROGRESS',
        'resolved'    => 'RESOLVED',
        'closed'      => 'CLOSED',
        // accept legacy uppercase from querystring too
        'OPEN'        => 'OPEN',
        'ASSIGNED'    => 'ASSIGNED',
        'IN_PROGRESS' => 'IN_PROGRESS',
        'RESOLVED'    => 'RESOLVED',
        'CLOSED'      => 'CLOSED',
    ];

    private const UI_TO_DB_PRIORITY_MAP = [
        'low'    => 'low',
        'medium' => 'medium',
        'high'   => 'high',
        'LOW'    => 'low',
        'MEDIUM' => 'medium',
        'HIGH'   => 'high',
    ];

    protected $queryString = [
        'statusFilter'     => ['except' => ''],
        'priorityFilter'   => ['except' => ''],
        'departmentFilter' => ['except' => ''],
        'sortFilter'       => ['except' => 'recent'],
    ];

    public function mount(): void
    {
        $user = Auth::user();

        $this->departments = Department::query()
            ->when($user->company_id, fn ($q) => $q->where('company_id', $user->company_id))
            ->orderBy('department_name')
            ->get(['department_id', 'department_name']);
    }

    private function baseQuery()
    {
        $user = Auth::user();

        return Ticket::query()
            ->with([
                'department:department_id,department_name',
                'user:user_id,full_name',
            ])
            ->where('user_id', $user->getKey());
    }

    public function render()
    {
        $q = $this->baseQuery();

        if ($this->statusFilter !== '') {
            $mapped = self::UI_TO_DB_STATUS_MAP[$this->statusFilter] ?? '';
            if ($mapped && \in_array($mapped, self::DB_ALLOWED_STATUSES, true)) {
                $q->where('status', $mapped);
            } else {
                $q->whereRaw('1=0'); // invalid status -> no results
            }
        }

        if ($this->priorityFilter !== '') {
            $prio = self::UI_TO_DB_PRIORITY_MAP[$this->priorityFilter] ?? '';
            if ($prio) {
                $q->where('priority', $prio);
            } else {
                $q->whereRaw('1=0');
            }
        }

        if ($this->departmentFilter !== '') {
            $q->where('department_id', (int) $this->departmentFilter);
        }

        match ($this->sortFilter) {
            'oldest' => $q->orderBy('created_at', 'asc'),
            'due'    => $q->orderBy('due_at', 'asc')->orderBy('updated_at', 'asc'),
            default  => $q->orderBy('created_at', 'desc'),
        };

        $tickets = $q->get();

        return view('livewire.pages.user.ticketstatus', [
            'tickets'     => $tickets,
            'departments' => $this->departments,
        ]);
    }

    /** Requester marks their ticket complete → RESOLVED (or CLOSED if you prefer) */
    public function markComplete(int $ticketId): void
    {
        $ticket = Ticket::whereKey($ticketId)
            ->where('user_id', Auth::id())
            ->first();

        if (!$ticket) return;

        if ($ticket->status !== 'RESOLVED') {
            $ticket->update(['status' => 'RESOLVED']);
            session()->flash('message', 'Ticket marked as resolved.');
        }
    }
}
