<?php

namespace App\Livewire\Pages\Receptionist;

use App\Models\BookingRoom;
use App\Models\Delivery; // menggunakan Deliveries sebagai sumber "Dokumen"
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
     * Normalize any nullable Carbon|string|\DateTimeInterface into Carbon.
     */
    private function asCarbon(null|Carbon|\DateTimeInterface|string $value): ?Carbon
    {
        if ($value === null) {
            return null;
        }
        if ($value instanceof Carbon) {
            return $value;
        }
        if ($value instanceof \DateTimeInterface) {
            return Carbon::instance($value);
        }
        try {
            return Carbon::parse($value);
        } catch (\Throwable $e) {
            return null;
        }
    }

    /**
     * Safe time formatter (returns '—' if not parseable).
     */
    private function fmtTime(null|Carbon|\DateTimeInterface|string $value): string
    {
        $c = $this->asCarbon($value);
        return $c ? $c->format('H:i') : '—';
    }

    /**
     * Determines the current status of a meeting.
     * Accepts Carbon|string to avoid type errors.
     */
    private function computeMeetingStatus(null|Carbon|\DateTimeInterface|string $startAt, null|Carbon|\DateTimeInterface|string $endAt): string
    {
        $start = $this->asCarbon($startAt);
        $end = $this->asCarbon($endAt);
        $now = now();

        if (!$start || !$end) {
            return 'Tidak diketahui';
        }

        if ($now->lt($start)) {
            if ($start->isToday() && $now->diffInMinutes($start) < 60) {
                return 'Berikutnya';
            }
            return 'Terjadwal';
        }

        if ($now->between($start, $end)) {
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
        $today = Carbon::today();

        // 1) Kartu statistik utama
        $todayGuestsCount = Guestbook::where('company_id', $companyId)
            ->whereDate('date', $today)
            ->count();

        $todayMeetings = BookingRoom::where('company_id', $companyId)
            ->whereDate('start_time', $today)
            ->get();

        $ongoingMeetingsCount = $todayMeetings
            ->filter(fn($m) => $this->computeMeetingStatus($m->start_time, $m->end_time) === 'Berlangsung')
            ->count();

        // Ambil data "dokumen" dari tabel deliveries
        $newDocumentsCount = Delivery::where('company_id', $companyId)
            ->where('created_at', '>=', now()->subWeek())
            ->count();

        $pendingDocumentsCount = Delivery::where('company_id', $companyId)
            ->where('status', 'pending')
            ->count();

        $stats = [
            ['label' => 'Tamu Hari Ini', 'value' => $todayGuestsCount, 'badge' => 'hari ini', 'badgeClass' => 'bg-green-100 text-green-700'],
            ['label' => 'Jadwal Meeting', 'value' => $todayMeetings->count(), 'badge' => $ongoingMeetingsCount . ' berlangsung', 'badgeClass' => 'bg-blue-100 text-blue-700'],
            ['label' => 'Dokumen Baru', 'value' => $newDocumentsCount, 'badge' => 'minggu ini', 'badgeClass' => 'bg-purple-100 text-purple-700'],
            ['label' => 'Dokumen Antri', 'value' => $pendingDocumentsCount, 'badge' => 'menunggu', 'badgeClass' => 'bg-amber-100 text-amber-700'],
        ];

        // 2) List "Jadwal Meeting Hari Ini"
        $meetings = $todayMeetings
            ->map(function ($meeting) {
                $start = $this->asCarbon($meeting->start_time);
                $end = $this->asCarbon($meeting->end_time);

                return [
                    'time' => $this->fmtTime($start) . ' - ' . $this->fmtTime($end),
                    'title' => $meeting->meeting_title,
                    'room' => $this->mapRoomName($meeting->room_id),
                    'status' => $this->computeMeetingStatus($start, $end),
                ];
            })
            ->where('status', '!=', 'Selesai')
            ->sortBy('time')
            ->take(5);

        // 3) List "Buku Tamu (Hari Ini)"
        $guests = Guestbook::where('company_id', $companyId)
            ->whereDate('date', $today)
            ->latest('jam_in')
            ->take(5)
            ->get()
            ->map(function ($guest) {
                return [
                    'name' => $guest->name,
                    'purpose' => $guest->keperluan,
                    'time' => $this->fmtTime($guest->jam_in),
                ];
            });

        // 4) Tabel "Dokumen Terbaru" -> ambil dari deliveries
        $documents = Delivery::where('company_id', $companyId)
            ->latest()
            ->take(5)
            ->get()
            ->map(function ($d) {
                return [
                    'name' => $d->item_name,
                    'cat' => ucfirst($d->type), // 'document' | 'package'
                    'date' => $this->asCarbon($d->created_at)?->format('Y-m-d') ?? '—',
                ];
            });

        return view('livewire.pages.receptionist.dashboard', [
            'stats' => $stats,
            'meetings' => $meetings,
            'guests' => $guests,
            'documents' => $documents,
        ]);
    }
}
