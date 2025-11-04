<?php

namespace App\Livewire\Pages\User;

use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Models\BookingRoom;
use App\Models\Room;
use App\Models\Announcement;
use App\Models\Information;
use App\Models\Ticket;

#[Layout('layouts.app')]
#[Title('HomePage')]
class Home extends Component
{
    // Booking History summary (untuk card di Home)
    public string $activeTab = 'upcoming'; // upcoming|ongoing|past|all
    public array $nextBooking = [];
    public array $historyUpcoming = [];
    public array $historyOngoing = [];
    public array $historyPast = [];
    public array $historyAll = [];
    public int $limit = 10;

    // Tickets mini summary
    public int $openTicketsCount = 0;
    public int $inProgressTicketsCount = 0;
    public int $resolvedLast7d = 0;

    public int $upcomingBookings = 0; // angka “Minggu ini”
    protected string $tz = 'Asia/Jakarta';

    public function mount(): void
    {
        $this->loadBookingHistory();
        $this->loadTicketSummary();
    }

    public function setTab(string $tab): void
    {
        $this->activeTab = in_array($tab, ['upcoming', 'ongoing', 'past', 'all'], true) ? $tab : 'upcoming';
    }

    public function openQuickTicket(): void
    {
        $this->dispatch('open-quick-ticket');
    }

    public function openQuickBook(): void
    {
        $now = Carbon::now($this->tz)->addMinutes(15);
        $rounded = $this->roundUpToSlot($now, 30)->format('H:i');
        $this->dispatch('open-quick-book', roomId: 0, ymd: $now->toDateString(), time: $rounded);
    }

    public function rebook(int $bookingId): void
    {
        $b = BookingRoom::where('bookingroom_id', $bookingId)
            ->where('user_id', Auth::id())
            ->first();

        if (!$b) {
            $this->dispatch('toast', type: 'error', message: 'Booking tidak ditemukan.');
            return;
        }

        $this->dispatch(
            'open-quick-book',
            roomId: (int) $b->room_id,
            ymd: (string) $b->date,
            time: Carbon::parse($b->start_time)->format('H:i')
        );
    }

    public function cancelBooking(int $bookingId): void
    {
        $b = BookingRoom::where('bookingroom_id', $bookingId)
            ->where('user_id', Auth::id())
            ->first();

        if (!$b) {
            $this->dispatch('toast', type: 'error', message: 'Booking tidak ditemukan.');
            return;
        }

        $now = Carbon::now($this->tz);
        $start = Carbon::parse("{$b->date} {$b->start_time}", $this->tz);

        if ($start->lte($now)) {
            $this->dispatch('toast', type: 'error', message: 'Booking yang sudah berjalan / lewat tidak bisa dibatalkan.');
            return;
        }

        $b->delete();
        $this->dispatch('toast', type: 'success', message: 'Booking dibatalkan.');
        $this->loadBookingHistory();
    }

    protected function loadBookingHistory(): void
    {
        $userId = Auth::id();
        $now = Carbon::now($this->tz);
        $today = $now->toDateString();
        $nowH = $now->format('H:i');

        $roomMap = Room::pluck('room_name', 'room_id')->toArray();

        $upcoming = BookingRoom::query()
            ->where('user_id', $userId)
            ->where(function ($q) use ($today, $nowH) {
                $q->where('date', '>', $today)
                  ->orWhere(function ($qq) use ($today, $nowH) {
                      $qq->where('date', $today)->where('start_time', '>=', $nowH);
                  });
            })
            ->orderBy('date')->orderBy('start_time')
            ->limit($this->limit)
            ->get();

        $ongoing = BookingRoom::query()
            ->where('user_id', $userId)
            ->where('date', $today)
            ->where('start_time', '<=', $nowH)
            ->where('end_time', '>', $nowH)
            ->orderBy('start_time')
            ->limit($this->limit)
            ->get();

        $past = BookingRoom::query()
            ->where('user_id', $userId)
            ->where(function ($q) use ($today, $nowH) {
                $q->where('date', '<', $today)
                  ->orWhere(function ($qq) use ($today, $nowH) {
                      $qq->where('date', $today)->where('end_time', '<', $nowH);
                  });
            })
            ->orderByDesc('date')->orderByDesc('end_time')
            ->limit($this->limit)
            ->get();

        $all = BookingRoom::query()
            ->where('user_id', $userId)
            ->orderByDesc('date')->orderByDesc('start_time')
            ->limit($this->limit)
            ->get();

        $mapFn = function ($b) use ($roomMap) {
            return [
                'id'            => (int) $b->bookingroom_id,
                'room_id'       => (int) $b->room_id,
                'room_name'     => (string) ($roomMap[$b->room_id] ?? 'Unknown'),
                'date'          => (string) $b->date,
                'start_time'    => (string) $b->start_time,
                'end_time'      => (string) $b->end_time,
                'meeting_title' => (string) $b->meeting_title,
            ];
        };

        $this->historyUpcoming = $upcoming->map($mapFn)->values()->all();
        $this->historyOngoing  = $ongoing->map($mapFn)->values()->all();
        $this->historyPast     = $past->map($mapFn)->values()->all();
        $this->historyAll      = $all->map($mapFn)->values()->all();

        $next = $upcoming->first();
        $this->nextBooking = $next ? $mapFn($next) : [];

        // angka “Minggu ini”
        $startWeek = $now->copy()->startOfWeek()->toDateString();
        $endWeek   = $now->copy()->endOfWeek()->toDateString();

        $this->upcomingBookings = BookingRoom::query()
            ->where('user_id', $userId)
            ->whereBetween('date', [$startWeek, $endWeek])
            ->count();
    }

    protected function loadTicketSummary(): void
    {
        $userId = Auth::id();

        // Sesuaikan dengan enum/status real di tabel tickets
        $openStatuses       = ['OPEN', 'NEW', 'PENDING'];
        $inProgressStatuses = ['IN_PROGRESS', 'ASSIGNED', 'ON_GOING'];
        $resolvedStatuses   = ['RESOLVED', 'CLOSED', 'DONE'];

        $base = Ticket::query()->where('user_id', $userId);

        $this->openTicketsCount       = (clone $base)->whereIn('status', $openStatuses)->count();
        $this->inProgressTicketsCount = (clone $base)->whereIn('status', $inProgressStatuses)->count();

        $sevenDaysAgo = Carbon::now($this->tz)->subDays(7);
        $this->resolvedLast7d = (clone $base)
            ->whereIn('status', $resolvedStatuses)
            ->where('updated_at', '>=', $sevenDaysAgo)
            ->count();
    }

    protected function roundUpToSlot(Carbon $time, int $slotMinutes = 30): Carbon
    {
        $extra = $slotMinutes - ($time->minute % $slotMinutes);
        if ($extra === $slotMinutes) $extra = 0;
        return $time->copy()->addMinutes($extra)->setSecond(0);
    }

    public function render()
    {
        $user      = Auth::user();
        $companyId = (int) ($user->company_id ?? 0);
        $deptId    = $user->department_id ? (int) $user->department_id : null;

        // Announcements (pakai scope forCompany kalau ada)
        $annBase = Announcement::query();
        if (method_exists(Announcement::class, 'scopeForCompany')) {
            $annBase = $annBase->forCompany($companyId);
        } else {
            $annBase = $annBase->where('company_id', $companyId);
        }
        $announcements = $annBase
            ->orderBy('event_at', 'desc')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        // Informations: coba (global + user dept) dulu
        $informations = Information::forCompany($companyId)
            ->forDepartment($deptId)
            ->orderBy('event_at', 'desc')
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        // Fallback: kalau tidak ada untuk dept user, tampilkan semua informasi perusahaan
        if ($informations->isEmpty()) {
            $informations = Information::forCompany($companyId)
                ->orderBy('event_at', 'desc')
                ->orderBy('created_at', 'desc')
                ->limit(10)
                ->get();
        }

        // optional: refresh ringkasan tiket
        $this->loadTicketSummary();

        return view('livewire.pages.user.home', [
            'announcements'           => $announcements,
            'informations'            => $informations,
            'openTicketsCount'        => $this->openTicketsCount,
            'inProgressTicketsCount'  => $this->inProgressTicketsCount,
            'resolvedLast7d'          => $this->resolvedLast7d,
            'upcomingBookings'        => $this->upcomingBookings,
            'nextBooking'             => $this->nextBooking,
            'historyUpcoming'         => $this->historyUpcoming,
            'historyOngoing'          => $this->historyOngoing,
            'historyPast'             => $this->historyPast,
            'historyAll'              => $this->historyAll,
            'activeTab'               => $this->activeTab,
        ]);
    }
}
