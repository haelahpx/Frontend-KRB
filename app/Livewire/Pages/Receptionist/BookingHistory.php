<?php

namespace App\Livewire\Pages\Receptionist;

use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use App\Models\BookingRoom;
use Carbon\Carbon;

#[Layout('layouts.receptionist')]
#[Title('Booking History')]
class BookingHistory extends Component
{
    use WithPagination;

    protected string $paginationTheme = 'tailwind';

    public int $perDone = 5;
    public int $perRejected = 5;

    public bool $withTrashed = false;
    public string $qDone = '';
    public string $qRejected = '';

    public bool $showModal = false;
    public string $modalMode = 'create';
    public ?int $editingId = null;

    public array $form = [
        'booking_type' => 'meeting',
        'meeting_title' => '',
        'date' => '',
        'start_time' => '',
        'end_time' => '',
        'room_id' => null,
        'online_provider' => null,
        'notes' => '',
        'status' => 'completed',
    ];

    /** Tolerant sets that match your DB */
    private const DONE_SET = [3, '3', 'done', 'DONE', 'Done', 'completed', 'COMPLETED', 'Completed'];
    private const REJECTED_SET = [2, '2', 'rejected', 'REJECTED', 'Rejected'];

    protected string $tz = 'Asia/Jakarta';

    // ─────────────────────────────────────────────────────────────────────
    // NEW (safety): also auto-progress here in case History page opens first
    // ─────────────────────────────────────────────────────────────────────
    private function autoProgressToDone(): int
    {
        $now = Carbon::now($this->tz)->format('Y-m-d H:i:s');

        return DB::transaction(function () use ($now) {
            return BookingRoom::query()
                ->where('status', 'approved')
                ->whereNotNull('date')
                ->whereNotNull('end_time')
                ->whereRaw("TIMESTAMP(`date`, `end_time`) <= ?", [$now])
                ->update(['status' => 'completed']);
        });
    }

    /** pagination resets */
    public function updatedQDone(): void
    {
        $this->resetPage('pageDone');
    }
    public function updatedQRejected(): void
    {
        $this->resetPage('pageRejected');
    }
    public function updatedWithTrashed(): void
    {
        $this->resetPage('pageDone');
        $this->resetPage('pageRejected');
    }

    /** open Create modal */
    public function create(string $bookingType = 'meeting', string $status = 'completed'): void
    {
        $this->modalMode = 'create';
        $this->editingId = null;
        $now = Carbon::now($this->tz);
        $this->form = [
            'booking_type' => $bookingType,
            'meeting_title' => '',
            'date' => $now->toDateString(),
            'start_time' => $now->format('H:00'),
            'end_time' => $now->copy()->addHour()->format('H:00'),
            'room_id' => null,
            'online_provider' => in_array($bookingType, ['online_meeting', 'onlinemeeting'], true) ? 'zoom' : null,
            'notes' => '',
            'status' => $status,
        ];
        $this->showModal = true;
    }

    /** open Edit modal */
    public function edit(int $id): void
    {
        $row = $this->baseQuery()->withTrashed()->findOrFail($id);

        $this->modalMode = 'edit';
        $this->editingId = $row->getKey();

        $this->form = [
            'booking_type' => (string) ($row->booking_type ?? 'meeting'),
            'meeting_title' => (string) ($row->meeting_title ?? ''),
            'date' => (string) ($row->date ?? ''),
            'start_time' => (string) ($row->start_time ?? ''),
            'end_time' => (string) ($row->end_time ?? ''),
            'room_id' => $row->room_id,
            'online_provider' => (string) ($row->online_provider ?? ''),
            'notes' => (string) ($row->notes ?? ''),
            'status' => $this->normalizeDbStatus($row->status),
        ];

        $this->showModal = true;
    }

    /** create/update */
    public function save(): void
    {
        $data = $this->validateForm();
        $statusForDb = $data['status'];

        if ($this->modalMode === 'create') {
            BookingRoom::create([
                'booking_type' => $data['booking_type'],
                'meeting_title' => $data['meeting_title'],
                'date' => $data['date'],
                'start_time' => $data['start_time'],
                'end_time' => $data['end_time'],
                'room_id' => in_array($data['booking_type'], ['meeting', 'bookingroom'], true) ? $data['room_id'] : null,
                'online_provider' => in_array($data['booking_type'], ['online_meeting', 'onlinemeeting'], true) ? $data['online_provider'] : null,
                'notes' => $data['notes'],
                'status' => $statusForDb,
                'user_id' => Auth::id(),
            ]);
        } else {
            $row = $this->baseQuery()->withTrashed()->findOrFail($this->editingId);

            $row->update([
                'booking_type' => $data['booking_type'],
                'meeting_title' => $data['meeting_title'],
                'date' => $data['date'],
                'start_time' => $data['start_time'],
                'end_time' => $data['end_time'],
                'room_id' => in_array($data['booking_type'], ['meeting', 'bookingroom'], true) ? $data['room_id'] : null,
                'online_provider' => in_array($data['booking_type'], ['online_meeting', 'onlinemeeting'], true) ? $data['online_provider'] : null,
                'notes' => $data['notes'],
                'status' => $statusForDb,
            ]);
        }

        $this->showModal = false;

        if ($statusForDb === 'completed') $this->resetPage('pageDone');
        if ($statusForDb === 'rejected')  $this->resetPage('pageRejected');
    }

    /** soft delete */
    public function destroy(int $id): void
    {
        $row = $this->baseQuery()->findOrFail($id);
        $row->delete();
    }

    /** restore */
    public function restore(int $id): void
    {
        $row = $this->baseQuery()->onlyTrashed()->findOrFail($id);
        $row->restore();
    }

    /** optional hard delete */
    public function forceDestroy(int $id): void
    {
        $row = $this->baseQuery()->withTrashed()->findOrFail($id);
        $row->forceDelete();
    }

    private function baseQuery()
    {
        return BookingRoom::query();
    }

    private function normalizeDbStatus($status): string
    {
        if (in_array($status, self::DONE_SET, true))     return 'completed';
        if (in_array($status, self::REJECTED_SET, true)) return 'rejected';
        return 'completed';
    }

    private function validateForm(): array
    {
        $rules = [
            'form.booking_type' => ['required', 'string', 'max:50'],
            'form.meeting_title' => ['required', 'string', 'max:255'],
            'form.date' => ['nullable', 'date'],
            'form.start_time' => ['nullable', 'string', 'max:10'],
            'form.end_time' => ['nullable', 'string', 'max:10'],
            'form.room_id' => ['nullable', 'integer'],
            'form.online_provider' => ['nullable', Rule::in(['zoom', 'google_meet'])],
            'form.notes' => ['nullable', 'string', 'max:1000'],
            'form.status' => ['required', Rule::in(['completed', 'rejected'])],
        ];

        if (in_array($this->form['booking_type'], ['meeting', 'bookingroom'], true)) {
            $rules['form.room_id'] = ['required', 'integer'];
            $rules['form.online_provider'] = ['nullable'];
        } else {
            $rules['form.online_provider'] = ['required', Rule::in(['zoom', 'google_meet'])];
            $rules['form.room_id'] = ['nullable'];
        }

        $data = $this->validate($rules)['form'];

        foreach (['date', 'start_time', 'end_time', 'notes'] as $k) {
            if ($data[$k] === '') $data[$k] = null;
        }

        return $data;
    }

    /** lists */
    public function getDoneRowsProperty()
    {
        $q = $this->baseQuery()
            ->when(!$this->withTrashed, fn($qq) => $qq->whereNull('deleted_at'))
            ->when($this->withTrashed, fn($qq) => $qq->withTrashed())
            ->whereIn('status', self::DONE_SET)
            ->when(strlen($this->qDone) > 0, fn($qq) => $qq->where('meeting_title', 'like', '%' . $this->qDone . '%'))
            ->orderByRaw("COALESCE(`date`, '0000-01-01') DESC")
            ->orderByRaw("COALESCE(`start_time`, '00:00:00') DESC");

        return $q->paginate($this->perDone, ['*'], 'pageDone');
    }

    public function getRejectedRowsProperty()
    {
        return $this->baseQuery()
            ->when(!$this->withTrashed, fn($q) => $q->whereNull('deleted_at'))
            ->when($this->withTrashed, fn($q) => $q->withTrashed())
            ->whereIn('status', ['rejected'])
            ->when(strlen($this->qRejected) > 0, fn($q) => $q->where('meeting_title', 'like', '%' . $this->qRejected . '%'))
            ->orderByDesc('date')
            ->orderByDesc('start_time')
            ->paginate($this->perRejected, ['*'], 'pageRejected');
    }

    public function render()
    {
        // Safety net: ensure overdue approvals are already in History
        $this->autoProgressToDone();

        return view('livewire.pages.receptionist.booking-history', [
            'doneRows' => $this->doneRows,
            'rejectedRows' => $this->rejectedRows,
        ]);
    }
}
