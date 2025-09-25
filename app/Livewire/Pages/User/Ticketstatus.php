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
    // Nilai harus match DB!
    public string $statusFilter    = '';          // '', OPEN, PROCESS, COMPLETE
    public string $priorityFilter  = '';          // '', LOW, MEDIUM, HIGH, CRITICAL
    public string $departmentFilter = '';         // '' atau department_id
    public string $sortFilter      = 'recent';    // recent | oldest | due

    public $departments;

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
            // DB simpan UPPERCASE -> kita pakai persis dari select
            $q->where('status', $this->statusFilter);
        }

        if ($this->priorityFilter !== '') {
            $q->where('priority', $this->priorityFilter);
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

    public function markComplete(int $ticketId): void
    {
        $ticket = Ticket::whereKey($ticketId)
            ->where('user_id', Auth::user()->getKey())
            ->first();

        if ($ticket && $ticket->status !== 'COMPLETE') {
            $ticket->update(['status' => 'COMPLETE']);
        }
    }
}
