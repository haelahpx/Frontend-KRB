<?php

namespace App\Livewire\Pages\Receptionist;

use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;
use Carbon\Carbon;

#[Layout('layouts.receptionist')]
#[Title('Booking Approval')]
class BookingsApproval extends Component
{
    use WithPagination;

    protected string $paginationTheme = 'tailwind';
    public int $perPage = 5;

    private const STATUS_PENDING = 'pending';
    private const STATUS_APPROVED = 'approved';
    private const STATUS_COMPLETED = 'completed';
    public string $q = '';
    public ?string $selected_date = null;   
    public string $list_mode = 'all';        

    private string $tz = 'Asia/Jakarta';
    private function toCarbon($value): ?Carbon
    {
        if ($value instanceof \DateTimeInterface) {
            return Carbon::instance($value)->setTimezone($this->tz);
        }
        if (is_string($value) && trim($value) !== '') {
            try {
                return Carbon::parse($value, $this->tz);
            } catch (\Throwable) {
                return null;
            }
        }
        return null;
    }

    private function normalizeDateTime($date, $time): ?Carbon
    {
        $timeStr = is_string($time) ? trim($time) : $time;

        if (is_string($timeStr) && preg_match('/^\d{4}-\d{2}-\d{2}[ T]/', $timeStr)) {
            return $this->toCarbon($timeStr);
        }

        if (is_string($timeStr) && preg_match('/^\d{2}:\d{2}(:\d{2})?$/', $timeStr) && !empty($date)) {
            $dateStr = $this->toCarbon($date)?->toDateString();
            if ($dateStr) {
                return $this->toCarbon($dateStr . ' ' . $timeStr);
            }
        }

        $c = $this->toCarbon($time);
        if ($c)
            return $c;
        return $this->toCarbon($date);
    }

    private function autoProgressToDone(): void
    {
        $now = Carbon::now($this->tz)->format('Y-m-d H:i:s');

        DB::table('booking_rooms')
            ->where('status', self::STATUS_APPROVED)
            ->where('end_time', '<', $now)
            ->update([
                'status' => self::STATUS_COMPLETED,
                'updated_at' => now(),
            ]);
    }

    private function hasTimeOverlap(object $row): array
    {
        $dateStr = $this->toCarbon($row->date)?->toDateString();
        $start = $this->normalizeDateTime($row->date, $row->start_time);
        $end = $this->normalizeDateTime($row->date, $row->end_time);

        if (!$dateStr || !$start || !$end) {
            return [false, null];
        }

        $startStr = $start->format('Y-m-d H:i:s');
        $endStr = $end->format('Y-m-d H:i:s');

        $conflict = DB::table('booking_rooms')
            ->select('bookingroom_id', 'meeting_title', 'date', 'start_time', 'end_time', 'room_id', 'status')
            ->where('bookingroom_id', '!=', $row->bookingroom_id)
            ->where('room_id', $row->room_id)
            ->where('status', self::STATUS_APPROVED)
            ->whereDate('date', $dateStr)
            ->where(function ($q) use ($startStr, $endStr) {
                $q->where('start_time', '<', $endStr)
                    ->where('end_time', '>', $startStr);
            })
            ->orderBy('start_time', 'asc')
            ->first();

        return [$conflict !== null, $conflict];
    }

    private function refreshPage(): void
    {
        $this->resetPage('pendingPage');
        $this->resetPage('ongoingPage');

        $this->js(<<<'JS'
            setTimeout(() => { window.location.reload(); }, 50);
        JS);
    }

    public function approve(int $id): void
    {
        $row = DB::table('booking_rooms')
            ->select('bookingroom_id', 'meeting_title', 'booking_type', 'status', 'is_approve', 'date', 'start_time', 'end_time', 'room_id')
            ->where('bookingroom_id', $id)
            ->first();

        if (!$row) {
            $this->dispatch('toast', type: 'warning', message: 'Data tidak ditemukan.');
            return;
        }

        if (($row->status ?? null) !== self::STATUS_PENDING) {
            $this->dispatch('toast', type: 'info', message: 'Booking sudah diproses.');
            return;
        }

        [$hasConflict, $other] = $this->hasTimeOverlap($row);
        if ($hasConflict) {
            $cStart = $this->normalizeDateTime($other->date, $other->start_time)?->format('H:i') ?? '??:??';
            $cEnd = $this->normalizeDateTime($other->date, $other->end_time)?->format('H:i') ?? '??:??';
            $cDate = $this->toCarbon($other->date)?->format('d M Y') ?? '—';

            $msg = sprintf(
                "Slot sudah terpakai oleh #%d (%s) pada %s %s–%s. Cek kalender dahulu sebelum approve.",
                $other->bookingroom_id,
                $other->meeting_title ?? '—',
                $cDate,
                $cStart,
                $cEnd
            );
            $this->dispatch('toast', type: 'warning', title: 'Jadwal Bentrok', message: $msg, duration: 6000);
            return;
        }

        DB::table('booking_rooms')
            ->where('bookingroom_id', $id)
            ->update([
                'status' => self::STATUS_APPROVED,
                'is_approve' => 1,
                'updated_at' => now(),
            ]);

        $this->dispatch('toast', type: 'success', message: 'Approved → On Going.');
        $this->refreshPage();
    }

    public function sendBack(int $id): void
    {
        DB::table('booking_rooms')
            ->where('bookingroom_id', $id)
            ->update([
                'status' => self::STATUS_PENDING,
                'is_approve' => 0,
                'updated_at' => now(),
            ]);

        $this->dispatch('toast', type: 'success', message: 'Sent back to Pending.');
        $this->refreshPage();
    }

    protected function baseQuery(string $status, ?int $isApprove = null)
    {
        $q = DB::table('booking_rooms')
            ->leftJoin('rooms', 'rooms.room_id', '=', 'booking_rooms.room_id')
            ->select([
                'booking_rooms.bookingroom_id',
                'booking_rooms.meeting_title',
                'booking_rooms.booking_type',
                'booking_rooms.status',
                'booking_rooms.is_approve',
                'booking_rooms.date',
                'booking_rooms.start_time',
                'booking_rooms.end_time',
                'booking_rooms.room_id',
                DB::raw('rooms.room_number as room_name'),
                'booking_rooms.online_provider',
                'booking_rooms.online_meeting_url',
                'booking_rooms.online_meeting_code',
            ])
            ->where('booking_rooms.status', $status);

        if (!is_null($isApprove)) {
            $q->where('booking_rooms.is_approve', $isApprove);
        }

        if ($this->q !== '') {
            $q->where('booking_rooms.meeting_title', 'like', '%' . $this->q . '%');
        }

        // Date filter
        if ($this->list_mode !== 'all' && $this->selected_date) {
            $q->whereDate('booking_rooms.date', $this->selected_date);
        }

        // Sorting
        if ($this->list_mode === 'oldest') {
            $q->orderBy('booking_rooms.date', 'asc')
                ->orderBy('booking_rooms.start_time', 'asc');
        } else {
            $q->orderBy('booking_rooms.date', 'desc')
                ->orderBy('booking_rooms.start_time', 'desc');
        }

        return $q;
    }

    public function updated($prop): void
    {
        if (in_array($prop, ['q', 'selected_date', 'list_mode'], true)) {
            $this->resetPage('pendingPage');
            $this->resetPage('ongoingPage');
        }
    }

    public function render()
    {
        $this->autoProgressToDone();

        $pending = $this->baseQuery(self::STATUS_PENDING, 0)
            ->paginate($this->perPage, ['*'], 'pendingPage');

        $onGoing = $this->baseQuery(self::STATUS_APPROVED, 1)
            ->paginate($this->perPage, ['*'], 'ongoingPage');

        return view('livewire.pages.receptionist.bookings-approval', [
            'pending' => $pending,
            'onGoing' => $onGoing,
        ]);
    }
}
