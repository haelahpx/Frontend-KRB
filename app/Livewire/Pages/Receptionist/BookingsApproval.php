<?php

namespace App\Livewire\Pages\Receptionist;

use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\BookingRoom;
use App\Services\GoogleMeetService;
use App\Services\ZoomService;
use Carbon\Carbon;

#[Layout('layouts.receptionist')]
#[Title('Bookings Approval')]
class BookingsApproval extends Component
{
    use WithPagination;

    protected string $paginationTheme = 'tailwind';

    // UI filters
    public string $q = '';
    public ?string $selectedDate = null;   // manual date (YYYY-MM-DD)
    public string $dateMode = 'semua';     // semua | terbaru | terlama

    // Pagination per box
    public int $perPending = 5;
    public int $perOngoing = 5;

    // Reject modal state
    public bool $showRejectModal = false;
    public ?int $rejectId = null;
    public string $rejectReason = '';

    private string $tz = 'Asia/Jakarta';

    // ─────────────────────────────────────────────────────────────────────
    // NEW: Auto-progress approved → completed when due time has passed
    // ─────────────────────────────────────────────────────────────────────
    private function autoProgressToDone(): int
    {
        $now = Carbon::now($this->tz)->format('Y-m-d H:i:s');

        return DB::transaction(function () use ($now) {
            // Move all approved rows whose end datetime is <= now to 'completed'
            return BookingRoom::query()
                ->where('status', 'approved')
                ->whereNotNull('date')
                ->whereNotNull('end_time')
                // MySQL: TIMESTAMP(date, time) treats 'HH:MM' as 'HH:MM:00' – perfect for us
                ->whereRaw("TIMESTAMP(`date`, `end_time`) <= ?", [$now])
                ->update(['status' => 'completed']);
        });
    }

    // Reset pages when filters change
    public function updatingQ(): void
    {
        $this->resetPage('pendingPage');
        $this->resetPage('ongoingPage');
    }
    public function updatingSelectedDate(): void
    {
        $this->resetPage('pendingPage');
        $this->resetPage('ongoingPage');
    }
    public function updatingDateMode(): void
    {
        $this->resetPage('pendingPage');
        $this->resetPage('ongoingPage');
    }

    // Computed: Google connected
    public function getGoogleConnectedProperty(): bool
    {
        return app(GoogleMeetService::class)->isConnected(Auth::id());
    }

    // Helpers
    private function selectedDateValue(): ?string
    {
        if (is_string($this->selectedDate) && preg_match('/^\d{4}-\d{2}-\d{2}$/', $this->selectedDate)) {
            return $this->selectedDate;
        }
        return null;
    }

    private function sortingDirection(): string
    {
        return $this->dateMode === 'terlama' ? 'ASC' : 'DESC';
    }

    private function applyDateTimeOrdering($query)
    {
        $dir = $this->sortingDirection();
        $invalidFirstExpr = "CASE WHEN (`date` IS NULL OR `start_time` IS NULL) THEN 1 ELSE 0 END";
        $dtExpr = "CONCAT(`date`, ' ', `start_time`)";

        return $query->orderByRaw("$invalidFirstExpr ASC")
            ->orderByRaw("$dtExpr $dir")
            ->orderByDesc('created_at');
    }

    // Actions
    public function openReject(int $id): void
    {
        $this->rejectId = $id;
        $this->rejectReason = '';
        $this->showRejectModal = true;
    }

    public function confirmReject(): void
    {
        $this->validate([
            'rejectId' => 'required|integer|exists:booking_rooms,bookingroom_id',
            'rejectReason' => 'required|string|min:3|max:500',
        ]);

        try {
            DB::transaction(function () {
                /** @var BookingRoom $b */
                $b = BookingRoom::lockForUpdate()->findOrFail($this->rejectId);

                $b->status = 'rejected';
                $b->is_approve = 0;
                $b->approved_by = Auth::id();
                $b->book_reject = $this->rejectReason;
                $b->save();
            });

            $this->showRejectModal = false;
            $this->dispatch('toast', type: 'info', title: 'Rejected', message: 'Booking ditolak dan alasan disimpan.');
            $this->resetPage('pendingPage');
            $this->resetPage('ongoingPage');
        } catch (\Throwable $e) {
            report($e);
            $this->dispatch('toast', type: 'error', title: 'Error', message: 'Gagal menolak: ' . $e->getMessage());
        }
    }

    public function approve(int $id): void
    {
        try {
            DB::transaction(function () use ($id) {
                /** @var BookingRoom $b */
                $b = BookingRoom::lockForUpdate()->findOrFail($id);

                // OFFLINE checks
                if ($b->booking_type !== 'online_meeting') {
                    if (!$b->room_id || !$b->date || !$b->start_time || !$b->end_time) {
                        throw new \RuntimeException('Data ruangan/tanggal/waktu tidak lengkap.');
                    }

                    $start = Carbon::parse($b->date . ' ' . $b->start_time, $this->tz);
                    $end = Carbon::parse($b->date . ' ' . $b->end_time, $this->tz);
                    if ($end->lte($start)) {
                        throw new \RuntimeException('Waktu tidak valid (end <= start).');
                    }

                    $overlapExists = BookingRoom::query()
                        ->where('bookingroom_id', '!=', $b->bookingroom_id)
                        ->where('status', 'approved')
                        ->where('booking_type', '!=', 'online_meeting')
                        ->where('room_id', $b->room_id)
                        ->whereDate('date', $b->date)
                        ->where(function ($q) use ($b) {
                            $q->whereRaw('TIME(start_time) < TIME(?)', [$b->end_time])
                                ->whereRaw('TIME(end_time) > TIME(?)', [$b->start_time]);
                        })
                        ->exists();

                    if ($overlapExists) {
                        throw new \RuntimeException('Jadwal bentrok dengan booking lain pada ruangan & tanggal yang sama.');
                    }
                }

                // ONLINE: create link on approval
                if ($b->booking_type === 'online_meeting' && empty($b->online_meeting_url)) {
                    $start = Carbon::parse($b->date . ' ' . $b->start_time, $this->tz);
                    $end = Carbon::parse($b->date . ' ' . $b->end_time, $this->tz);

                    $provider = strtolower((string) $b->online_provider);
                    $provider = str_replace([' ', '-'], '_', $provider);
                    $isGoogle = str_starts_with($provider, 'google');

                    if ($isGoogle) {
                        if (!app(GoogleMeetService::class)->isConnected(Auth::id())) {
                            throw new \RuntimeException('Google belum terhubung untuk pengguna ini.');
                        }
                        $meet = app(GoogleMeetService::class)->createMeet(
                            $b->meeting_title,
                            $start,
                            $end,
                            'Auto-created from KRBS approval'
                        );
                        $b->online_provider = 'google_meet';
                    } else {
                        $meet = app(ZoomService::class)->createMeeting(
                            $b->meeting_title,
                            $start,
                            $end,
                            'Auto-created from KRBS approval'
                        );
                        $b->online_provider = 'zoom';
                    }

                    $b->online_meeting_url = $meet['url'] ?? null;
                    $b->online_meeting_code = $meet['code'] ?? null;
                    $b->online_meeting_password = $meet['password'] ?? null;
                }

                // Approve
                $b->status = 'approved';
                $b->is_approve = 1;
                $b->approved_by = Auth::id();
                $b->book_reject = null;
                $b->save();
            });

            $this->dispatch('toast', type: 'success', title: 'Approved', message: 'Booking disetujui.');
            $this->resetPage('pendingPage');
            $this->resetPage('ongoingPage');
        } catch (\RuntimeException $e) {
            $this->dispatch('toast', type: 'warning', title: 'Tidak Bisa Disetujui', message: $e->getMessage());
        } catch (\Throwable $e) {
            report($e);
            $this->dispatch('toast', type: 'error', title: 'Error', message: 'Gagal menyetujui: ' . $e->getMessage());
        }
    }

    public function reject(int $id): void
    {
        $this->openReject($id);
    }

    public function render()
    {
        // Ensure any past-due approvals are moved before listing
        $this->autoProgressToDone();

        $baseCols = [
            'bookingroom_id',
            'meeting_title',
            'booking_type',
            'online_provider',
            'online_meeting_url',
            'online_meeting_code',
            'online_meeting_password',
            'status',
            'date',
            'start_time',
            'end_time',
            'room_id',
            'user_id',
            'approved_by',
            'book_reject',
        ];

        // Common filter/sort (applied to both lists)
        $common = function ($q) {
            if ($this->q !== '') {
                $q->where('meeting_title', 'like', '%' . $this->q . '%');
            }

            $selected = $this->selectedDateValue();
            if ($this->dateMode !== 'semua' && $selected) {
                $q->whereDate('date', $selected);
            }

            $this->applyDateTimeOrdering($q);
        };

        $pending = BookingRoom::query()
            ->where('status', 'pending')
            ->tap($common)
            ->paginate($this->perPending, $baseCols, 'pendingPage');

        $ongoing = BookingRoom::query()
            ->where('status', 'approved')
            ->tap($common)
            ->paginate($this->perOngoing, $baseCols, 'ongoingPage');

        return view('livewire.pages.receptionist.bookings-approval', compact('pending', 'ongoing'));
    }
}
