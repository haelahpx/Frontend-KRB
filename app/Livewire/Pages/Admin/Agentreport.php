<?php

namespace App\Livewire\Pages\Admin;

use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;

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
        /** @var User|null $authUser */
        $authUser = Auth::user();

        if ($authUser) {
            $user = User::with(['company', 'department'])->find($authUser->user_id);
            if (!$user) {
                $user = $authUser;
            }

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
        // MAIN AGENT QUERY
        $query = User::where('role_id', 3)
            ->with(['company', 'department'])
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

        // LOAD TICKETS FOR CURRENT PAGE
        $tickets = Ticket::whereIn('user_id', $agentIds)
            ->with(['company', 'department', 'requesterDepartment'])
            ->orderByDesc('ticket_id')
            ->get();

        // Calculate SLA status for each ticket
        $tickets = $tickets->map(function ($ticket) {
            $ticket->sla_state = $this->calculateSLAStatus($ticket);
            return $ticket;
        });

        // PAGE-LEVEL STATS
        $ticketStatsDetailed = $tickets->groupBy('user_id')->map(function ($t) {
            return [
                'Open' => $t->where('status', 'OPEN')->count(),
                'Closed' => $t->where('status', 'CLOSED')->count(),
                'Resolved' => $t->where('status', 'RESOLVED')->count(),
                'IN_PROGRESS' => $t->where('status', 'IN_PROGRESS')->count(),
                'total' => $t->count(),
            ];
        });

        // FULL DATASET QUERY (NO SEARCH FILTER)
        $allAgentsQuery = User::where('role_id', 3)
            ->with(['company', 'department'])
            ->whereIn('user_id', Ticket::select('user_id')->distinct())
            ->when($this->companyId, fn($q) => $q->where('company_id', $this->companyId))
            ->when($this->departmentId, fn($q) => $q->where('department_id', $this->departmentId));

        $allAgents = $allAgentsQuery->get();
        $allAgentIds = $allAgents->pluck('user_id')->toArray();

        // ALL TICKET DATA
        $allTickets = Ticket::whereIn('user_id', $allAgentIds)
            ->with(['company', 'department', 'requesterDepartment'])
            ->get()
            ->map(function ($ticket) {
                $ticket->sla_state = $this->calculateSLAStatus($ticket);
                return $ticket;
            });

        // FULL STATS
        $allTicketStatsDetailed = $allTickets->groupBy('user_id')->map(function ($t) {
            return [
                'Open' => $t->where('status', 'OPEN')->count(),
                'Closed' => $t->where('status', 'CLOSED')->count(),
                'Resolved' => $t->where('status', 'RESOLVED')->count(),
                'IN_PROGRESS' => $t->where('status', 'IN_PROGRESS')->count(),
                'total' => $t->count(),
            ];
        });

        // TOP 3 AGENTS BY TOTAL TICKETS
        $topAgents = $allAgents
            ->sortByDesc(fn($a) => $allTicketStatsDetailed[$a->user_id]['total'] ?? 0)
            ->take(3)
            ->values();

        // ATTACH TICKETS + COMPANY/DEPT TEXT
        $agents->getCollection()->transform(function ($agent) use ($tickets) {
            $agent->tickets = $tickets->where('user_id', $agent->user_id)->values();
            $agent->company_name = $agent->company?->company_name ?? '-';
            $agent->department_name = $agent->department?->department_name ?? '-';
            return $agent;
        });

        return view('livewire.pages.admin.agentreport', [
            'agents' => $agents,
            'ticketStatsDetailed' => $ticketStatsDetailed,
            'topAgents' => $topAgents,
            'allTicketStatsDetailed' => $allTicketStatsDetailed,
            'allTickets' => $allTickets,
        ]);
    }

    /**
     * Calculate SLA status for a ticket
     */
    private function calculateSLAStatus($ticket)
    {
        // SLA LIMITS
        $priority = strtolower(trim((string) $ticket->priority));
        $slaLimit = match ($priority) {
            'high' => 24,
            'medium' => 48,
            'low' => 72,
            default => null,
        };

        if (!$slaLimit || !$ticket->created_at) {
            return [
                'state' => null,
                'label' => null,
                'classes' => '',
                'hours_elapsed' => 0,
            ];
        }

        // SLA Start = created_at
        $startTime = $ticket->created_at;

        // SLA End logic:
        // If ticket is CLOSED or RESOLVED → SLA stops at updated_at
        // If still OPEN or IN_PROGRESS → SLA continues until now
        if (in_array($ticket->status, ['CLOSED', 'RESOLVED'])) {
            $endTime = $ticket->updated_at ?? now();
        } else {
            // SLA still running
            $endTime = now();
        }

        $hoursElapsed = max(0, $startTime->diffInRealSeconds($endTime) / 3600);

        // SLA check
        if ($hoursElapsed > $slaLimit) {
            return [
                'state' => 'expired',
                'label' => 'EXPIRED',
                'classes' => 'bg-gradient-to-r from-red-500 to-red-600 text-white',
                'hours_elapsed' => $hoursElapsed,
            ];
        }

        return [
            'state' => 'ok',
            'label' => 'OK',
            'classes' => 'bg-gradient-to-r from-green-500 to-green-600 text-white',
            'hours_elapsed' => $hoursElapsed,
        ];
    }

    public function downloadReport()
    {
        // Same filters as the page
        $agents = User::where('role_id', 3)
            ->with(['company', 'department'])
            ->whereIn('user_id', Ticket::select('user_id')->distinct())
            ->when($this->companyId, fn($q) => $q->where('company_id', $this->companyId))
            ->when($this->departmentId, fn($q) => $q->where('department_id', $this->departmentId))
            ->when($this->search, fn($q) => $q->where(function ($qq) {
                $qq->where('full_name', 'like', '%' . $this->search . '%')
                    ->orWhere('user_id', 'like', '%' . $this->search . '%');
            }))
            ->orderBy('full_name')
            ->get();

        $allTickets = Ticket::whereIn('user_id', $agents->pluck('user_id'))
            ->get()
            ->map(function ($ticket) {
                $ticket->sla_state = $this->calculateSLAStatus($ticket);
                return $ticket;
            });

        $stats = $allTickets->groupBy('user_id')->map(function ($t) {
            return [
                'Open' => $t->where('status', 'OPEN')->count(),
                'InProgress' => $t->where('status', 'IN_PROGRESS')->count(),
                'Resolved' => $t->where('status', 'RESOLVED')->count(),
                'Closed' => $t->where('status', 'CLOSED')->count(),
                'Total' => $t->count(),
            ];
        });

        $pdf = Pdf::loadView('pdf.agentreport-pdf', [
            'agents' => $agents,
            'allTickets' => $allTickets,
            'stats' => $stats,
            'generatedAt' => now(),
        ])->setPaper('a4', 'portrait');

        return response()->streamDownload(function () use ($pdf) {
            echo $pdf->output();
        }, 'Agent Report - ' . now()->locale('id')->translatedFormat('d F Y') . '.pdf');
    }

    public function openToast($userId)
    {
        $this->openAgent = $userId;
    }

    public function closeToast()
    {
        $this->openAgent = null;
    }

}
