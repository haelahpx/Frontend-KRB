<?php

namespace App\Livewire\Pages\Admin;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Carbon;

#[Layout('layouts.admin')]
#[Title('Admin - Dashboard')]
class Dashboard extends Component
{
    // Properties for data displayed on the dashboard
    public $admin_name;
    public $stats; // KPI strip data
    public $ticketStatusDistribution;
    public $weeklyTicketActivity; // <-- This holds the chart data

    /**
     * Mount method to set initial data on component load.
     */
    public function mount()
    {
        $this->admin_name = Auth::check() ? (Auth::user()->name ?? 'Administrator') : 'Guest Admin';
        $this->loadDashboardData();
    }

    /**
     * Custom method to fetch all dashboard data.
     * Called on mount and by the wire:poll update.
     */
    public function loadDashboardData()
    {
        // 1. Fetch KPI Strip Data (using dummy/placeholder logic)
        $this->stats = $this->getKpiStats();

        // 2. Fetch Ticket Status Distribution (using dummy/placeholder logic)
        $this->ticketStatusDistribution = $this->getTicketStatusDistribution();

        // 3. Fetch Weekly Ticket Activity (using dummy/placeholder logic)
        $this->weeklyTicketActivity = $this->getWeeklyTicketActivity();
    }

    /**
     * A Livewire poll action to refresh data periodically.
     */
    public function tick()
    {
        $this->loadDashboardData();

        // Dispatching the event with the updated chart data
        $this->dispatch('admin-chart-updated', ['weeklyData' => $this->weeklyTicketActivity]);
    }

    // ... (getKpiStats, getTicketStatusDistribution, getWeeklyTicketActivity methods remain the same) ...
    // Note: I will use a different event name ('admin-chart-updated') to avoid conflict.

    protected function getKpiStats()
    { /* ... unchanged ... */
        $totalTickets = 1500;
        $openTickets = 350;
        $unassignedTickets = 80;
        $ticketsClosedToday = 25;
        return [
            ['label' => 'Total Tickets', 'value' => number_format($totalTickets)],
            ['label' => 'Open Tickets', 'value' => number_format($openTickets)],
            ['label' => 'Unassigned Tickets', 'value' => number_format($unassignedTickets)],
            ['label' => 'Closed Today', 'value' => number_format($ticketsClosedToday)],
        ];
    }
    protected function getTicketStatusDistribution()
    { /* ... unchanged ... */
        $openCount = 350;
        $inProgressCount = 180;
        $closedCount = 970;
        $totalCount = $openCount + $inProgressCount + $closedCount;
        if ($totalCount === 0) return ['Open' => ['count' => 0, 'percent' => 0], 'In Progress' => ['count' => 0, 'percent' => 0], 'Closed' => ['count' => 0, 'percent' => 0], 'total_count' => 0,];
        $calcPercent = fn($count) => round(($count / $totalCount) * 100);
        return [
            'Open' => ['count' => $openCount, 'percent' => $calcPercent($openCount)],
            'In Progress' => ['count' => $inProgressCount, 'percent' => $calcPercent($inProgressCount)],
            'Closed' => ['count' => $closedCount, 'percent' => $calcPercent($closedCount)],
            'total_count' => $totalCount,
        ];
    }
    protected function getWeeklyTicketActivity()
    { /* ... unchanged ... */
        $days = [];
        $newTickets = [];
        $closedTickets = [];
        $inProgressTickets = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::today()->subDays($i);
            $days[] = $date->format('D');
            $newTickets[] = rand(10, 30);
            $closedTickets[] = rand(8, 25);
            $inProgressTickets[] = rand(3, 15);
        }
        return [
            'labels' => $days,
            'new' => $newTickets,
            'closed' => $closedTickets,
            'in_progress' => $inProgressTickets,
        ];
    }

    public function render()
    {
        return view('livewire.pages.admin.dashboard');
    }
}
