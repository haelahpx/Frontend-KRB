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
use App\Models\TicketAssignment;

#[Layout('layouts.admin')]
#[Title('Admin - Dashboard')]
class Dashboard extends Component
{
    // Mengatur Timezone secara eksplisit
    protected string $tz = 'Asia/Jakarta';

    /**
     * Helper untuk mengubah nilai ke Carbon instance (tidak digunakan di render, tapi dipertahankan)
     */
    private function asCarbon(null|Carbon|\DateTimeInterface|string $v): ?Carbon
    {
        if ($v === null) return null;
        if ($v instanceof Carbon) return $v->timezone($this->tz);
        if ($v instanceof \DateTimeInterface) return Carbon::instance($v)->timezone($this->tz);
        try {
            return Carbon::parse($v)->timezone($this->tz);
        } catch (\Throwable) {
            return null;
        }
    }

    /**
     * Mengambil data aktivitas harian untuk 7 hari terakhir
     */
    private function getWeeklyActivityData($companyId, $departmentId): array
    {
        // Pastikan rentang waktu menggunakan timezone yang sama
        $now = Carbon::now($this->tz);
        $startOfRange = $now->copy()->subDays(6)->startOfDay();
        $endOfRange = $now->copy()->endOfDay();

        $days = [];
        $dayLabels = [];

        // 1. Inisialisasi array untuk 7 hari terakhir
        for ($i = 6; $i >= 0; $i--) {
            $date = $now->copy()->subDays($i);
            // Label harus sesuai urutan (D)
            $dayLabels[] = $date->format('D');
            // Kunci array adalah string Y-m-d
            $days[$date->toDateString()] = [
                'date' => $date->toDateString(),
                'ticket' => 0,
                'room' => 0,
                'information' => 0,
            ];
        }

        // --- 2. Tickets Data ---
        $ticketsData = Ticket::query()
            // Mengambil tanggal (Y-m-d) dari created_at, di-cast ke date
            ->select(DB::raw('DATE(created_at) as date_key'), DB::raw('count(*) as count'))
            ->when($companyId, fn($q) => $q->where('company_id', $companyId))
            ->when($departmentId, fn($q) => $q->where('department_id', $departmentId))
            ->whereBetween('created_at', [$startOfRange, $endOfRange])
            ->groupBy(DB::raw('DATE(created_at)'))
            ->get();

        foreach ($ticketsData as $data) {
            // Gunakan 'date_key' yang sudah di-select
            $dateKey = $data->date_key;

            if (isset($days[$dateKey])) {
                $days[$dateKey]['ticket'] = (int)$data->count;
            }
        }

        // --- 3. Room Bookings Data ---
        $roomData = BookingRoom::query()
            ->select(DB::raw('DATE(created_at) as date_key'), DB::raw('count(*) as count'))
            ->when($companyId, fn($q) => $q->where('company_id', $companyId))
            ->when($departmentId, fn($q) => $q->where('department_id', $departmentId))
            ->whereBetween('created_at', [$startOfRange, $endOfRange])
            ->groupBy(DB::raw('DATE(created_at)'))
            ->get();

        foreach ($roomData as $data) {
            $dateKey = $data->date_key;

            if (isset($days[$dateKey])) {
                $days[$dateKey]['room'] = (int)$data->count;
            }
        }

        // --- 4. Information Data ---
        // --- 4. Information Data (FIXED) ---
        $infoData = Information::query()
            ->select(DB::raw('DATE(created_at) as date_key'), DB::raw('count(*) as count'))
            // REMOVED: ->when($companyId, fn($q) => $q->where('company_id', $companyId))
            // REMOVED: ->when($departmentId, fn($q) => $q->where('department_id', $departmentId))
            ->whereBetween('created_at', [$startOfRange, $endOfRange])
            ->groupBy(DB::raw('DATE(created_at)'))
            ->get();

        foreach ($infoData as $data) {
            $dateKey = $data->date_key;

            if (isset($days[$dateKey])) {
                $days[$dateKey]['information'] = (int)$data->count;
            }
        }

        // 5. Format data untuk Chart.js (Mengambil values berdasarkan urutan inisialisasi)
        return [
            'labels' => $dayLabels,
            // array_values digunakan untuk memastikan urutan sesuai dengan inisialisasi $days
            'ticket' => array_column(array_values($days), 'ticket'),
            'room' => array_column(array_values($days), 'room'),
            'information' => array_column(array_values($days), 'information'),
        ];
    }


    public function render()
    {
        $user = Auth::user();
        $companyId = optional($user)->company_id;
        $departmentId = optional($user)->department_id;

        // Range 7 hari terakhir
        $startOfRange = Carbon::now($this->tz)->subDays(6)->startOfDay();
        $endOfRange = Carbon::now($this->tz)->endOfDay();

        // Range Bulan Ini
        $startOfMonth = Carbon::now($this->tz)->startOfMonth();
        $endOfMonth = Carbon::now($this->tz)->endOfMonth();

        // --- Weekly Statistics (7 days) ---
        $weeklyTicketsCount = Ticket::query()
            ->when($companyId, fn($q) => $q->where('company_id', $companyId))
            ->when($departmentId, fn($q) => $q->where('department_id', $departmentId))
            ->whereBetween('created_at', [$startOfRange, $endOfRange])
            ->count();

        $weeklyRoomBookingsCount = BookingRoom::query()
            ->when($companyId, fn($q) => $q->where('company_id', $companyId))
            ->when($departmentId, fn($q) => $q->where('department_id', $departmentId))
            ->whereBetween('created_at', [$startOfRange, $endOfRange])
            ->count();

        $weeklyInformationCount = Information::query()
            ->when($companyId, fn($q) => $q->where('company_id', $companyId))
            ->when($departmentId, fn($q) => $q->where('department_id', $departmentId))
            ->whereBetween('created_at', [$startOfRange, $endOfRange])
            ->count();

        // --- Ticket Priority Distribution (This Month) ---
        $totalTicketsThisMonth = Ticket::query()
            ->when($companyId, fn($q) => $q->where('company_id', $companyId))
            ->when($departmentId, fn($q) => $q->where('department_id', $departmentId))
            ->whereBetween('created_at', [$startOfMonth, $endOfMonth])
            ->count();

        $ticketPriorities = Ticket::query()
            ->select('priority', DB::raw('count(*) as count'))
            ->when($companyId, fn($q) => $q->where('company_id', $companyId))
            ->when($departmentId, fn($q) => $q->where('department_id', $departmentId))
            ->whereBetween('created_at', [$startOfMonth, $endOfMonth])
            ->groupBy('priority')
            ->get()
            ->keyBy('priority');

        $highCount = $ticketPriorities['high']->count ?? 0;
        $mediumCount = $ticketPriorities['medium']->count ?? 0;
        $lowCount = $ticketPriorities['low']->count ?? 0;

        $totalPriorityTickets = $highCount + $mediumCount + $lowCount;

        // Calculate percentages safely
        $highPercent = $totalTicketsThisMonth > 0 ? round(($highCount / $totalTicketsThisMonth) * 100) : 0;
        $mediumPercent = $totalTicketsThisMonth > 0 ? round(($mediumCount / $totalTicketsThisMonth) * 100) : 0;
        $lowPercent = 100 - $highPercent - $mediumPercent;
        if ($lowPercent < 0) $lowPercent = 0;

        // --- Top Agent (Solved Tickets) ---
        $topAgent = TicketAssignment::query()
            ->join('tickets', 'ticket_assignments.ticket_id', '=', 'tickets.ticket_id')
            ->join('users', 'ticket_assignments.user_id', '=', 'users.user_id')
            ->select('users.full_name', DB::raw('count(tickets.ticket_id) as solved_count'))
            ->where('tickets.status', 'closed') // Asumsi status 'closed' berarti solved
            ->when($companyId, fn($q) => $q->where('tickets.company_id', $companyId))
            ->when($departmentId, fn($q) => $q->where('tickets.department_id', $departmentId))
            ->groupBy('users.full_name')
            ->orderByDesc('solved_count')
            ->first();

        // --- Weekly Activity Data for Chart ---
        $weeklyActivityData = $this->getWeeklyActivityData($companyId, $departmentId);


        return view('livewire.pages.admin.dashboard', [
            'weeklyTicketsCount' => $weeklyTicketsCount,
            'weeklyRoomBookingsCount' => $weeklyRoomBookingsCount,
            'weeklyInformationCount' => $weeklyInformationCount,
            'topAgent' => $topAgent,
            'totalTicketsThisMonth' => $totalTicketsThisMonth,
            'highPercent' => $highPercent,
            'mediumPercent' => $mediumPercent,
            'lowPercent' => $lowPercent,
            'weeklyActivityData' => $weeklyActivityData, // Pass the data
        ]);
    }
}
