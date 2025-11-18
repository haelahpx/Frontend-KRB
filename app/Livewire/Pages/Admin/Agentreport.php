<?php

namespace App\Livewire\Pages\Admin;

use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Ticket;
use App\Models\Department;
use App\Models\Company;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

#[Layout('layouts.admin')]
#[Title('Admin - Agent Report')]
class Agentreport extends Component
{
    use WithPagination;

    protected $paginationTheme = 'tailwind';

    public $openAgent = null;
    public $search = '';
    public $companyId;
    public $departmentId;

    public function mount()
    {
        /** @var User|null $user */
        $user = Auth::user();
        if ($user) {
            $user->load(['company', 'department']);
            $this->companyId = optional($user->company)->company_id;
            $this->departmentId = optional($user->department)->department_id;
        }
    }

    public function updatingPage()
    {
        $this->openAgent = null;
    }

    public function toggleAgent($userId)
    {
        $this->openAgent = $this->openAgent === $userId ? null : $userId;
    }

    public function render()
    {
        $query = User::where('role_id', 3)
            ->whereIn('user_id', Ticket::select('user_id')->distinct())
            ->when($this->companyId, fn($q) => $q->where('company_id', $this->companyId))
            ->when($this->departmentId, fn($q) => $q->where('department_id', $this->departmentId))
            ->when($this->search, function ($q) {
                $q->where(function ($qq) {
                    $qq->where('full_name', 'like', '%' . $this->search . '%')
                        ->orWhere('user_id', 'like', '%' . $this->search . '%');
                });
            });

        $agents = $query->orderBy('full_name')->paginate(5);
        $agentIds = $agents->pluck('user_id')->toArray();

        $tickets = Ticket::whereIn('user_id', $agentIds)
            ->orderByDesc('ticket_id')
            ->get();

        $ticketStats = $tickets->groupBy('user_id')->map(fn($t) => $t->count());

        $agents->getCollection()->transform(function ($agent) use ($tickets) {
            $agent->company_name = Company::where('company_id', $agent->company_id)->value('company_name');
            $agent->department_name = Department::where('department_id', $agent->department_id)->value('department_name');

            $agent->tickets = $tickets->where('user_id', $agent->user_id)->map(function ($ticket) {
                $ticket->company_name = Company::where('company_id', $ticket->company_id)->value('company_name');
                $ticket->department_name = Department::where('department_id', $ticket->department_id)->value('department_name');
                $ticket->requestdept_name = Department::where('department_id', $ticket->requestdept_id)->value('department_name');
                return $ticket;
            });

            return $agent;
        });

        return view('livewire.pages.admin.agentreport', [
            'agents' => $agents,
            'ticketStats' => $ticketStats,
        ]);
    }
}
