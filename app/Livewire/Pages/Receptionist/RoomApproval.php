<?php

namespace App\Livewire\Pages\Receptionist;

use App\Models\BookingRoom;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.receptionist')]
#[Title('Room Approval')]
class RoomApproval extends Component
{
    public array $pending = [];
    public array $ongoing = [];

    public ?int $rejectId = null;
    public string $reject_reason = ''; // kept for UI only; not saved (no column)

    public function mount(): void
    {
        $this->reloadBuckets();
    }

    public function approve(int $id): void
    {
        $row = BookingRoom::company(Auth::user()?->company_id)->find($id);
        if (!$row) return;

        if ($row->status !== BookingRoom::ST_PENDING) {
            $this->dispatch('toast', type: 'info', message: 'Booking sudah diproses.');
            $this->reloadBuckets();
            return;
        }

        $row->update([
            'status'      => BookingRoom::ST_APPROVED,
            'is_approve'  => true,
            'approved_by' => Auth::user()?->user_id ?? Auth::id(),
        ]);

        $this->dispatch('toast', type: 'success', message: 'Booking disetujui.');
        $this->reloadBuckets();
    }

    public function askReject(int $id): void
    {
        $this->rejectId = $id;
        $this->reject_reason = '';
    }

    public function reject(): void
    {
        if (!$this->rejectId) return;

        $row = BookingRoom::company(Auth::user()?->company_id)->find($this->rejectId);
        if ($row && $row->status === BookingRoom::ST_PENDING) {
            $row->update([
                'status'      => BookingRoom::ST_REJECTED,
                'is_approve'  => false,
                'approved_by' => Auth::user()?->user_id ?? Auth::id(),
            ]);
            $this->dispatch('toast', type: 'success', message: 'Booking ditolak.');
        }

        $this->rejectId = null;
        $this->reject_reason = '';
        $this->reloadBuckets();
    }

    // Poller
    public function tick(): void
    {
        try {
            $this->reloadBuckets();
        } catch (\Throwable $e) {
            \Log::error('[RoomApproval tick] '.$e->getMessage());
        }
    }

    private function reloadBuckets(): void
    {
        $cid = Auth::user()?->company_id;
        $now = now(config('app.timezone'));

        // Pending list
        $pend = BookingRoom::company($cid)
            ->pending()
            ->orderBy('date')->orderBy('start_time')
            ->get();

        // Ongoing = approved and within current time window
        $ongo = BookingRoom::company($cid)
            ->approved()
            ->where('start_time', '<=', $now)
            ->where('end_time', '>=', $now)
            ->orderBy('date')->orderBy('start_time')
            ->get();

        $this->pending = $pend->map(fn($r) => $this->uiMap($r))->all();
        $this->ongoing = $ongo->map(fn($r) => $this->uiMap($r))->all();
    }

    private function uiMap(BookingRoom $r): array
    {
        return [
            'id'            => $r->getKey(),
            'meeting_title' => $r->meeting_title,
            'room'          => (string)($r->room?->room_number ?? $r->room_id),
            'date'          => Carbon::parse($r->date)->format('d M Y'),
            'time'          => Carbon::parse($r->start_time)->format('H:i'),
            'time_end'      => Carbon::parse($r->end_time)->format('H:i'),
            'participants'  => (int) $r->number_of_attendees,
            'status'        => $r->status,
        ];
    }

    public function render()
    {
        return view('livewire.pages.receptionist.room-approval', [
            'pending' => $this->pending,
            'ongoing' => $this->ongoing,
        ]);
    }
}
