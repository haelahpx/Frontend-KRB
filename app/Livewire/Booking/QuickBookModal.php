<?php

namespace App\Livewire\Booking;

use Livewire\Component;
use Livewire\Attributes\On;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\Room;
use App\Models\BookingRoom;
use App\Models\Requirement;

class QuickBookModal extends Component
{
    public bool $show = false;

    public $room_id = '';
    public string $date = '';
    public string $start_time = '';
    public string $end_time = '';
    public string $meeting_title = '';
    public $number_of_attendees = '';
    public array $requirements = [];
    public string $special_notes = '';

    protected string $tz = 'Asia/Jakarta';
    protected int $slotMinutes = 30;
    protected int $leadMinutes = 15;
    public string $minStart = '00:00';

    #[On('open-quick-book')]
    public function open(int $roomId, string $ymd, string $time): void
    {
        $this->resetErrorBag();
        $this->requirements = [];
        $this->special_notes = '';
        $this->meeting_title = '';
        $this->number_of_attendees = '';

        $this->room_id    = $roomId;
        $this->date       = $ymd;
        $this->start_time = $time;
        $this->end_time   = Carbon::createFromFormat('H:i', $time)->addMinutes($this->slotMinutes)->format('H:i');

        $this->updateMinStart();
        $this->show = true;
    }

    public function close(): void
    {
        $this->show = false;
    }

    protected function updateMinStart(): void
    {
        $now = Carbon::now($this->tz);
        if ($this->date === $now->toDateString()) {
            $this->minStart = $now->format('H:i');
            if ($this->start_time < $this->minStart) {
                $bumped = $this->roundUpToSlot($now->copy()->addMinutes($this->leadMinutes));
                $this->start_time = $bumped->format('H:i');
                $this->end_time   = $bumped->copy()->addMinutes($this->slotMinutes)->format('H:i');
                $this->dispatch('toast', type: 'info', message: "Start time diupdate ke {$this->start_time} karena waktu sebelumnya sudah terlewat.");
            }
        } else {
            $this->minStart = '00:00';
        }
    }

    protected function roundUpToSlot(Carbon $time): Carbon
    {
        $minute = (int) $time->minute;
        $extra  = $this->slotMinutes - ($minute % $this->slotMinutes);
        if ($extra === $this->slotMinutes) $extra = 0;
        return $time->copy()->addMinutes($extra)->setSecond(0);
    }

    public function updatedDate(): void
    {
        $this->updateMinStart();
    }

    public function submit(): void
    {
        $this->validate([
            'meeting_title'        => 'required|string|min:3',
            'room_id'              => 'required|integer|exists:rooms,room_id',
            'date'                 => 'required|date',
            'number_of_attendees'  => 'required|integer|min:1',
            'start_time'           => 'required|date_format:H:i',
            'end_time'             => 'required|date_format:H:i|after:start_time',
            'special_notes'        => 'nullable|string|max:1000',
            'requirements'         => 'array',
        ]);

        $now = Carbon::now($this->tz);
        if ($this->date < $now->toDateString()) {
            $this->dispatch('toast', type: 'error', message: 'Tidak bisa booking ke tanggal yang sudah lewat.');
            return;
        }
        if ($this->date === $now->toDateString() && $this->start_time < $now->format('H:i')) {
            $this->dispatch('toast', type: 'error', message: 'Start time tidak boleh di masa lalu.');
            return;
        }

        $startDt = Carbon::createFromFormat('Y-m-d H:i', "{$this->date} {$this->start_time}", $this->tz);
        $endDt   = Carbon::createFromFormat('Y-m-d H:i', "{$this->date} {$this->end_time}",   $this->tz);

        $overlap = BookingRoom::query()
            ->where('room_id', $this->room_id)
            ->where('date', $this->date)
            ->where('start_time', '<', $endDt)
            ->where('end_time',   '>', $startDt)
            ->exists();

        if ($overlap) {
            $this->dispatch('toast', type: 'error', message: 'Slot waktu sudah terpakai.');
            return;
        }

        DB::transaction(function () use ($startDt, $endDt) {
            $booking = BookingRoom::create([
                'room_id'              => (int)$this->room_id,
                'company_id'           => Auth::user()->company_id ?? 1,
                'user_id'              => Auth::id() ?? 1,
                'department_id'        => Auth::user()->department_id ?? null,
                'meeting_title'        => $this->meeting_title,
                'date'                 => $this->date,
                'number_of_attendees'  => (int)$this->number_of_attendees,
                'start_time'           => $startDt,
                'end_time'             => $endDt,
                'special_notes'        => $this->special_notes,
            ]);

            if (!empty($this->requirements)) {
                $ids = Requirement::upsertByName($this->requirements);
                $booking->requirements()->sync($ids);
            }
        });

        $this->dispatch('toast', type: 'success', message: 'Booking berhasil dari kalender.');
        $this->dispatch('booking-created');
        $this->close();
    }

    public function render()
    {
        return view('livewire.booking.quick-book-modal', [
            'roomName' => Room::where('room_id', $this->room_id)->value('room_number'),
        ]);
    }
}
