<?php

namespace App\Livewire\Pages\Admin;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

// Import Models based on your schema images
use App\Models\Ticket;
use App\Models\BookingRoom;
use App\Models\Information;
use App\Models\TicketAssignment; // Based on the ticket_assignments table

#[Layout('layouts.admin')]
#[Title('Admin - Dashboard')]
class Dashboard extends Component
{
    protected string $tz = 'Asia/Jakarta';

    private function asCarbon(null|Carbon|\DateTimeInterface|string $v): ?Carbon
    {
        if ($v === null)
            return null;
        if ($v instanceof Carbon)
            return $v->timezone($this->tz);
        if ($v instanceof \DateTimeInterface)
            return Carbon::instance($v)->timezone($this->tz);

        try {
            return Carbon::parse($v)->timezone($this->tz);
        } catch (\Throwable) {
            return null;
        }
    }

    public function render()
    {
        $user = Auth::user();
        $companyId = optional($user)->company_id;
        $departmentId = optional($user)->department_id;

        // Range 7 hari terakhir (hari ini + 6 hari ke belakang)
        $startOfRange = Carbon::now($this->tz)->subDays(6)->startOfDay();
        $endOfRange = Carbon::now($this->tz)->endOfDay();

        // --- Weekly Statistics (7 days) ---

        // 1. Tickets
        $weeklyTicketsCount = Ticket::query()
            ->when($companyId, fn($q) => $q->where('company_id', $companyId))
            ->when($departmentId, fn($q) => $q->where('department_id', $departmentId))
            ->whereBetween('created_at', [$startOfRange, $endOfRange])
            ->count();

        // 2. Booking Rooms
        $weeklyRoomBookingsCount = BookingRoom::query()
            ->when($companyId, fn($q) => $q->where('company_id', $companyId))
            ->when($departmentId, fn($q) => $q->where('department_id', $departmentId))
            ->whereBetween('created_at', [$startOfRange, $endOfRange])
            ->count();

        // 3. Information
        $weeklyInformationCount = Information::query()
            ->when($companyId, fn($q) => $q->where('company_id', $companyId))
            ->when($departmentId, fn($q) => $q->where('department_id', $departmentId))
            ->whereBetween('created_at', [$startOfRange, $endOfRange])
            ->count();

        // --- Ticket Status Distribution ---
        $totalTicketsThisMonth = Ticket::query()
            ->when($companyId, fn($q) => $q->where('company_id', $companyId))
            ->when($departmentId, fn($q) => $q->where('department_id', $departmentId))
            ->whereBetween('created_at', [Carbon::now($this->tz)->startOfMonth(), Carbon::now($this->tz)->endOfMonth()])
            ->count();

        $ticketStatuses = Ticket::query()
            ->select('priority', DB::raw('count(*) as count')) // Using 'priority' as a proxy for status distribution as seen in your view structure
            ->when($companyId, fn($q) => $q->where('company_id', $companyId))
            ->when($departmentId, fn($q) => $q->where('department_id', $departmentId))
            ->groupBy('priority')
            ->get()
            ->keyBy('priority');

        $approvedCount = $ticketStatuses['high']->count ?? 0;
        $pendingCount = $ticketStatuses['medium']->count ?? 0;
        $rejectedCount = $ticketStatuses['low']->count ?? 0; // Using low as proxy for 'rejected' or lowest priority

        $totalStatusTickets = $approvedCount + $pendingCount + $rejectedCount;

        $approvedPercent = $totalStatusTickets > 0 ? round(($approvedCount / $totalStatusTickets) * 100) : 0;
        $pendingPercent = $totalStatusTickets > 0 ? round(($pendingCount / $totalStatusTickets) * 100) : 0;
        $rejectedPercent = $totalStatusTickets > 0 ? round(($rejectedCount / $totalStatusTickets) * 100) : 0;

        // --- Top Agent (Solved Tickets) ---
        $topAgent = TicketAssignment::query()
            ->join('tickets', 'ticket_assignments.ticket_id', '=', 'tickets.ticket_id')
            ->join('users', 'ticket_assignments.user_id', '=', 'users.user_id')
            ->select('users.full_name', DB::raw('count(tickets.ticket_id) as solved_count'))
            // Filter by solved status - assuming a status column exists in 'tickets' table, adjust as needed.
            // Based on your tables, let's assume 'status' column exists in `tickets` table and 'closed' means solved.
            ->where('tickets.status', 'closed')
            ->when($companyId, fn($q) => $q->where('tickets.company_id', $companyId))
            ->when($departmentId, fn($q) => $q->where('tickets.department_id', $departmentId))
            ->groupBy('users.full_name')
            ->orderByDesc('solved_count')
            ->first();


        return view('livewire.pages.admin.dashboard', [
            'weeklyTicketsCount' => $weeklyTicketsCount,
            'weeklyRoomBookingsCount' => $weeklyRoomBookingsCount,
            'weeklyInformationCount' => $weeklyInformationCount,
            'topAgent' => $topAgent,
            'totalTicketsThisMonth' => $totalTicketsThisMonth,
            'approvedPercent' => $approvedPercent,
            'pendingPercent' => $pendingPercent,
            'rejectedPercent' => $rejectedPercent,
        ]);
    }
}