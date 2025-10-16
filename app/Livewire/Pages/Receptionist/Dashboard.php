<?php

namespace App\Livewire\Pages\Receptionist;

use App\Models\BookingRoom;
use App\Models\Delivery; // ⬅️ ganti ke Delivery
use App\Models\Guestbook;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.receptionist')]
#[Title('Dashboard')]
class Dashboard extends Component
{
    /**
     * Determines the current status of a meeting.
     */
    private function computeMeetingStatus(Carbon $startAt, Carbon $endAt): string
    {
        $now = now();

        if ($now->lt($startAt)) {
            if ($startAt->isToday() && $now->diffInMinutes($startAt) < 60) {
                return 'Berikutnya';
            }
            return 'Terjadwal';
        }

        if ($now->between($startAt, $endAt)) {
            return 'Berlangsung';
        }

        return 'Selesai';
    }

    /**
     * Maps a room ID to its name.
     */
    private function mapRoomName(?int $id): string
    {
        return match ($id) {
            1 => 'Ruangan 1',
            2 => 'Ruangan 2',
            3 => 'Ruangan 3',
            default => 'Lokasi Tidak Diketahui',
        };
    }

    /**
     * Render the dashboard view with dynamic data.
     */
    public function render()
    {
        $companyId = optional(Auth::user())->company_id;
        $today     = Carbon::today();

        // 1) Kartu statistik utama
        $todayGuestsCount = Guestbook::where('company_id', $companyId)
            ->whereDate('date', $today)
            ->count();

        $todayMeetings = BookingRoom::where('company_id', $companyId)
            ->whereDate('start_time', $today)
            ->get();

        $ongoingMeetingsCount = $todayMeetings
            ->filter(fn ($m) => $this->computeMeetingStatus($m->start_time, $m->end_time) === 'Berlangsung')
            ->count();

        // ⬇️ Ganti Documents -> Deliveries
        $newDocumentsCount = Delivery::where('company_id', $companyId)
            ->where('created_at', '>=', now()->subWeek())
            ->count();

        $pendingDocumentsCount = Delivery::where('company_id', $companyId)
            ->where('status', 'pending')
            ->count();

        $stats = [
            ['label' => 'Tamu Hari Ini',   'value' => $todayGuestsCount,        'badge' => 'hari ini',   'badgeClass' => 'bg-green-100 text-green-700'],
            ['label' => 'Jadwal Meeting',  'value' => $todayMeetings->count(),  'badge' => $ongoingMeetingsCount . ' berlangsung', 'badgeClass' => 'bg-blue-100 text-blue-700'],
            // Label tetap "Dokumen" agar UI kamu nggak berubah—datanya dari deliveries
            ['label' => 'Dokumen Baru',    'value' => $newDocumentsCount,       'badge' => 'minggu ini', 'badgeClass' => 'bg-purple-100 text-purple-700'],
            ['label' => 'Dokumen Antri',   'value' => $pendingDocumentsCount,   'badge' => 'menunggu',   'badgeClass' => 'bg-amber-100 text-amber-700'],
        ];

        // 2) List "Jadwal Meeting Hari Ini"
        $meetings = $todayMeetings->map(function ($meeting) {
            return [
                'time'   => $meeting->start_time->format('H:i') . ' - ' . $meeting->end_time->format('H:i'),
                'title'  => $meeting->meeting_title,
                'room'   => $this->mapRoomName($meeting->room_id),
                'status' => $this->computeMeetingStatus($meeting->start_time, $meeting->end_time),
            ];
        })->where('status', '!=', 'Selesai')->sortBy('time')->take(5);

        // 3) List "Buku Tamu (Hari Ini)"
        $guests = Guestbook::where('company_id', $companyId)
            ->whereDate('date', $today)
            ->latest('jam_in')
            ->take(5)
            ->get()
            ->map(function ($guest) {
                return [
                    'name'    => $guest->name,
                    'purpose' => $guest->keperluan,
                    'time'    => Carbon::parse($guest->jam_in)->format('H:i'),
                ];
            });

        // 4) Tabel "Dokumen Terbaru" -> ambil dari deliveries
        $documents = Delivery::where('company_id', $companyId)
            ->latest()
            ->take(5)
            ->get()
            ->map(function ($d) {
                return [
                    'name' => $d->item_name,                 // item_name pada deliveries
                    'cat'  => ucfirst($d->type),             // 'document' | 'package'
                    'date' => $d->created_at->format('Y-m-d'),
                ];
            });

        return view('livewire.pages.receptionist.dashboard', [
            'stats'     => $stats,
            'meetings'  => $meetings,
            'guests'    => $guests,
            'documents' => $documents,
        ]);
    }
}
