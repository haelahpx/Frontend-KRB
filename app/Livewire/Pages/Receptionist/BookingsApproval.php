<?php

namespace App\Livewire\Pages\Receptionist;

use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
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
    public ?string $selectedDate = null;   // YYYY-MM-DD
    public string $dateMode = 'semua';     // semua | terbaru | terlama

    // Pagination per box
    public int $perPending = 5;
    public int $perOngoing = 5;

    // Reject modal state
    public bool $showRejectModal = false;
    public ?int $rejectId = null;
    public string $rejectReason = '';

    private string $tz = 'Asia/Jakarta';

    /** Build a Carbon datetime safely whether columns are DATE+TIME or already DATETIME. */
    private function buildDt(null|string $dateVal, null|string $timeVal): Carbon
    {
        if (!$dateVal && !$timeVal) {
            throw new \RuntimeException('Tanggal/waktu tidak lengkap.');
        }

        if (is_string($timeVal) && preg_match('/^\d{4}-\d{2}-\d{2}/', $timeVal)) {
            return Carbon::parse($timeVal, $this->tz);
        }

        if (is_string($dateVal) && preg_match('/^\d{4}-\d{2}-\d{2} \d{2}:/', $dateVal)) {
            $dt = Carbon::parse($dateVal, $this->tz);
            if (is_string($timeVal) && preg_match('/^\d{2}:\d{2}/', $timeVal)) {
                return $dt->setTimeFromTimeString($timeVal);
            }
            return $dt;
        }

        $dateStr = (string) $dateVal;
        $timeStr = (string) ($timeVal === '' ? '00:00:00' : $timeVal);

        return Carbon::parse(trim($dateStr . ' ' . $timeStr), $this->tz);
    }

    // Auto-progress approved → completed when due time has passed
    private function autoProgressToDone(): int
    {
        $now = Carbon::now($this->tz)->toDateTimeString();

        $endExpr = "COALESCE(
            CASE WHEN `end_time` REGEXP '^[0-9]{4}-' THEN `end_time` ELSE NULL END,
            CONCAT(`date`, ' ', `end_time`)
        )";

        return DB::transaction(function () use ($now, $endExpr) {
            return BookingRoom::query()
                ->where('status', 'approved')
                ->whereNotNull('end_time')
                ->whereRaw("$endExpr <= ?", [$now])
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
        return (is_string($this->selectedDate) && preg_match('/^\d{4}-\d{2}-\d{2}$/', $this->selectedDate))
            ? $this->selectedDate
            : null;
    }

    private function sortingDirection(): string
    {
        return $this->dateMode === 'terlama' ? 'ASC' : 'DESC';
    }

    private function applyDateTimeOrdering($query)
    {
        $dir = $this->sortingDirection();

        $invalidFirstExpr = "CASE
            WHEN (`date` IS NULL OR `start_time` IS NULL) THEN 1
            ELSE 0
        END";

        $dtExpr = "COALESCE(
            CASE WHEN `start_time` REGEXP '^[0-9]{4}-' THEN `start_time` ELSE NULL END,
            CONCAT(`date`, ' ', `start_time`)
        )";

        return $query->orderByRaw("$invalidFirstExpr ASC")
            ->orderByRaw("$dtExpr $dir")
            ->orderByDesc('created_at');
    }

    // ── Actions ──────────────────────────────────────────────────────────

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
                if (!in_array($b->booking_type, ['online_meeting', 'onlinemeeting'])) {
                    if (!$b->room_id || !$b->date || !$b->start_time || !$b->end_time) {
                        throw new \RuntimeException('Data ruangan/tanggal/waktu tidak lengkap.');
                    }

                    $start = $this->buildDt($b->date, $b->start_time);
                    $end   = $this->buildDt($b->date, $b->end_time);
                    if ($end->lte($start)) {
                        throw new \RuntimeException('Waktu tidak valid (end <= start).');
                    }

                    $startExpr = "COALESCE(
                        CASE WHEN `start_time` REGEXP '^[0-9]{4}-' THEN `start_time` ELSE NULL END,
                        CONCAT(`date`, ' ', `start_time`)
                    )";
                    $endExpr = "COALESCE(
                        CASE WHEN `end_time`   REGEXP '^[0-9]{4}-' THEN `end_time`   ELSE NULL END,
                        CONCAT(`date`, ' ', `end_time`)
                    )";

                    $overlapExists = BookingRoom::query()
                        ->where('bookingroom_id', '!=', $b->bookingroom_id)
                        ->where('status', 'approved')
                        ->whereNotIn('booking_type', ['online_meeting', 'onlinemeeting'])
                        ->where('room_id', $b->room_id)
                        ->whereDate('date', $b->date)
                        ->whereRaw("$startExpr < ?", [$end->toDateTimeString()])
                        ->whereRaw("$endExpr > ?", [$start->toDateTimeString()])
                        ->exists();

                    if ($overlapExists) {
                        throw new \RuntimeException('Jadwal bentrok dengan booking lain pada ruangan & tanggal yang sama.');
                    }
                }

                // ONLINE: create link on approval
                if (in_array($b->booking_type, ['online_meeting','onlinemeeting']) && empty($b->online_meeting_url)) {
                    $start = $this->buildDt($b->date, $b->start_time);
                    $end   = $this->buildDt($b->date, $b->end_time);

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

    // ── Data building ────────────────────────────────────────────────────

    private function applyCommonFilters($q)
    {
        if ($this->q !== '') {
            $q->where('meeting_title', 'like', '%' . $this->q . '%');
        }

        $selected = $this->selectedDateValue();
        if ($this->dateMode !== 'semua' && $selected) {
            $q->whereDate('date', $selected);
        }

        $this->applyDateTimeOrdering($q);
    }

    public function render()
    {
        // Move past-due approved → completed
        $this->autoProgressToDone();

        $cols = [
            'bookingroom_id', 'meeting_title', 'booking_type', 'online_provider',
            'online_meeting_url', 'online_meeting_code', 'online_meeting_password',
            'status', 'date', 'start_time', 'end_time', 'room_id',
            'user_id', 'approved_by', 'book_reject', 'created_at', 'updated_at'
        ];

        $pending = BookingRoom::query()
            ->with('room')
            ->where('status', 'pending')
            ->tap(fn($q) => $this->applyCommonFilters($q))
            ->paginate($this->perPending, $cols, 'pendingPage');

        $ongoing = BookingRoom::query()
            ->with('room')
            ->where('status', 'approved')
            ->tap(fn($q) => $this->applyCommonFilters($q))
            ->paginate($this->perOngoing, $cols, 'ongoingPage');

        return view('livewire.pages.receptionist.bookings-approval', compact('pending', 'ongoing'));
    }
}
