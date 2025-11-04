<?php

namespace App\Livewire\Pages\Admin;

use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Carbon;
use App\Models\BookingRoom;

#[Layout('layouts.admin')]
#[Title('History Room Booking')]
class RoomMonitoring extends Component
{
    // berapa item awal per kolom
    public int $limitOffline = 10;
    public int $limitOnline  = 10;

    // optional filter ringan (bisa dikembangkan)
    public ?string $search = null; // cari di meeting_title / special_notes

    public function mount(): void
    {
        // noop – properti default sudah diset
    }

    public function loadMore(string $side = 'offline'): void
    {
        if ($side === 'online') {
            $this->limitOnline += 10;
        } else {
            $this->limitOffline += 10;
        }
    }

    protected function baseHistoryQuery()
    {
        $companyId = Auth::user()?->company_id;

        return BookingRoom::query()
            ->with(['room']) // pastikan relasi room ada di model
            ->where('company_id', $companyId)
            ->where('end_time', '<', now()) // HANYA history (sudah selesai)
            ->when($this->search, function ($q, $s) {
                $q->where(function ($qq) use ($s) {
                    $qq->where('meeting_title', 'like', "%{$s}%")
                       ->orWhere('special_notes', 'like', "%{$s}%");
                });
            })
            ->orderByDesc('end_time');
    }

    public function render()
    {
        $base = $this->baseHistoryQuery();

        // OFFLINE = booking_type 'meeting'
        $offline = (clone $base)
            ->where('booking_type', 'meeting')
            ->limit($this->limitOffline)
            ->get();

        // ONLINE = booking_type 'online_meeting'
        $online = (clone $base)
            ->where('booking_type', 'online_meeting')
            ->limit($this->limitOnline)
            ->get();

        return view('livewire.pages.admin.roommonitoring', [
            'offline' => $offline,
            'online'  => $online,
        ]);
    }
}
