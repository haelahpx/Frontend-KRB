<?php

namespace App\Livewire\Pages\User;

use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Models\BookingRoom;
use App\Models\Room;

#[Layout('layouts.app')]
#[Title('Booking History')]
class BookingStatus extends Component
{
    use WithPagination;

    // Tabs & filters
    public string $tab = 'upcoming'; // upcoming|ongoing|past|all
    public string $q = '';           // search by title/room
    public ?string $dateFrom = null; // Y-m-d
    public ?string $dateTo   = null; // Y-m-d
    public ?int $roomFilter  = null;

    // UI
    public int $perPage = 10;

    protected string $tz = 'Asia/Jakarta';

    // Actions
    public function setTab(string $tab): void
    {
        $this->tab = in_array($tab, ['upcoming','ongoing','past','all'], true) ? $tab : 'upcoming';
        $this->resetPage();
    }

    public function updatingQ(): void         { $this->resetPage(); }
    public function updatingDateFrom(): void  { $this->resetPage(); }
    public function updatingDateTo(): void    { $this->resetPage(); }
    public function updatingRoomFilter(): void{ $this->resetPage(); }

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

        $this->dispatch('open-quick-book',
            roomId: (int)$b->room_id,
            ymd:    (string)$b->date,
            time:   Carbon::parse($b->start_time)->format('H:i')
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

        $now   = Carbon::now($this->tz);
        $start = Carbon::parse("{$b->date} {$b->start_time}", $this->tz);

        if ($start->lte($now)) {
            $this->dispatch('toast', type: 'error', message: 'Booking yang sudah berjalan / lewat tidak bisa dibatalkan.');
            return;
        }

        $b->delete();
        $this->dispatch('toast', type: 'success', message: 'Booking dibatalkan.');
        $this->resetPage();
    }

    protected function roundUpToSlot(Carbon $time, int $slotMinutes = 30): Carbon
    {
        $extra = $slotMinutes - ($time->minute % $slotMinutes);
        if ($extra === $slotMinutes) $extra = 0;
        return $time->copy()->addMinutes($extra)->setSecond(0);
    }

    public function render()
    {
        $userId = Auth::id();
        $now    = Carbon::now($this->tz);
        $today  = $now->toDateString();
        $nowH   = $now->format('H:i');

        $query = BookingRoom::query()->where('user_id', $userId);

        // Tab filter
        if ($this->tab === 'upcoming') {
            $query->where(function ($q) use ($today, $nowH) {
                $q->where('date', '>', $today)
                  ->orWhere(function ($qq) use ($today, $nowH) {
                      $qq->where('date', $today)->where('start_time', '>=', $nowH);
                  });
            })->orderBy('date')->orderBy('start_time');
        } elseif ($this->tab === 'ongoing') {
            $query->where('date', $today)
                  ->where('start_time', '<=', $nowH)
                  ->where('end_time',   '>',  $nowH)
                  ->orderBy('start_time');
        } elseif ($this->tab === 'past') {
            $query->where(function ($q) use ($today, $nowH) {
                $q->where('date', '<', $today)
                  ->orWhere(function ($qq) use ($today, $nowH) {
                      $qq->where('date', $today)->where('end_time', '<', $nowH);
                  });
            })->orderByDesc('date')->orderByDesc('end_time');
        } else { // all
            $query->orderByDesc('date')->orderByDesc('start_time');
        }

        // Search q (title/room)
        if (strlen(trim($this->q)) > 0) {
            $q = trim($this->q);
            $roomIds = Room::where('room_number', 'like', "%{$q}%")->pluck('room_id')->all();
            $query->where(function ($qq) use ($q, $roomIds) {
                $qq->where('meeting_title', 'like', "%{$q}%")
                   ->orWhereIn('room_id', $roomIds);
            });
        }

        // Date range
        if ($this->dateFrom) $query->where('date', '>=', $this->dateFrom);
        if ($this->dateTo)   $query->where('date', '<=', $this->dateTo);

        // Room filter
        if ($this->roomFilter) $query->where('room_id', $this->roomFilter);

        $bookings = $query->paginate($this->perPage);

        $roomMap = Room::pluck('room_number', 'room_id')->toArray();

        return view('livewire.pages.user.bookingstatus', [
            'bookings' => $bookings,
            'roomMap'  => $roomMap,
            'rooms'    => Room::orderBy('room_number')->get(['room_id','room_number']),
        ]);
    }
}
