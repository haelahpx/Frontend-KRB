<?php

namespace App\Livewire\Pages\Receptionist;

use App\Models\BookingRoom;
use App\Models\Documents as DocumentModel;
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
            // Check if it's the next upcoming meeting
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
        $today = Carbon::today();

        // 1. Fetch data for the main statistics cards
        $todayGuestsCount = Guestbook::where('company_id', $companyId)
            ->whereDate('date', $today)
            ->count();

        $todayMeetings = BookingRoom::where('company_id', $companyId)
            ->whereDate('start_time', $today)
            ->get();
        
        $ongoingMeetingsCount = $todayMeetings->filter(fn($m) => $this->computeMeetingStatus($m->start_time, $m->end_time) === 'Berlangsung')->count();

        $newDocumentsCount = DocumentModel::where('company_id', $companyId)
            ->where('created_at', '>=', now()->subWeek())
            ->count();

        $pendingDocumentsCount = DocumentModel::where('company_id', $companyId)
            ->where('status', 'pending')
            ->count();
        
        $stats = [
            ['label' => 'Tamu Hari Ini', 'value' => $todayGuestsCount, 'badge' => 'hari ini', 'badgeClass' => 'bg-green-100 text-green-700'],
            ['label' => 'Jadwal Meeting', 'value' => $todayMeetings->count(), 'badge' => $ongoingMeetingsCount . ' berlangsung', 'badgeClass' => 'bg-blue-100 text-blue-700'],
            ['label' => 'Dokumen Baru', 'value' => $newDocumentsCount, 'badge' => 'minggu ini', 'badgeClass' => 'bg-purple-100 text-purple-700'],
            ['label' => 'Dokumen Antri', 'value' => $pendingDocumentsCount, 'badge' => 'menunggu', 'badgeClass' => 'bg-amber-100 text-amber-700'],
        ];

        // 2. Fetch data for the "Jadwal Meeting Hari Ini" list
        $meetings = $todayMeetings->map(function ($meeting) {
            return [
                'time' => $meeting->start_time->format('H:i') . ' - ' . $meeting->end_time->format('H:i'),
                'title' => $meeting->meeting_title,
                'room' => $this->mapRoomName($meeting->room_id),
                'status' => $this->computeMeetingStatus($meeting->start_time, $meeting->end_time),
            ];
        })->where('status', '!=', 'Selesai')->sortBy('time')->take(5);

        // 3. Fetch data for the "Buku Tamu (Hari Ini)" list
        $guests = Guestbook::where('company_id', $companyId)
            ->whereDate('date', $today)
            ->latest('jam_in')
            ->take(5)
            ->get()->map(function($guest) {
                return [
                    'name' => $guest->name,
                    'purpose' => $guest->keperluan,
                    'time' => Carbon::parse($guest->jam_in)->format('H:i'),
                ];
            });

        // 4. Fetch data for the "Dokumen Terbaru" table
        $documents = DocumentModel::where('company_id', $companyId)
            ->latest()
            ->take(5)
            ->get()->map(function($doc) {
                return [
                    'name' => $doc->document_name,
                    'cat' => ucfirst($doc->type),
                    'date' => $doc->created_at->format('Y-m-d'),
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