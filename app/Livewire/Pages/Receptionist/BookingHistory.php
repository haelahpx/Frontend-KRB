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
use App\Models\Room;
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

    // unified search
    public string $q = '';

    public ?string $selectedDate = null;   // 'YYYY-MM-DD'
    public string $dateMode = 'semua';     // 'semua' | 'terbaru' | 'terlama'

    public bool $showModal = false;
    public string $modalMode = 'create';
    public ?int $editingId = null;

    public array $rooms = [];

    public array $form = [
        'booking_type'    => 'meeting',
        'meeting_title'   => '',
        'date'            => '',
        'start_time'      => '',
        'end_time'        => '',
        'room_id'         => null,
        'online_provider' => null,
        'notes'           => '',
        'status'          => 'completed',
    ];

    private const DONE_SET     = [3, '3', 'done', 'DONE', 'Done', 'completed', 'COMPLETED', 'Completed'];
    private const REJECTED_SET = [2, '2', 'rejected', 'REJECTED', 'Rejected'];

    protected string $tz = 'Asia/Jakarta';

    public function mount(): void
    {
        $this->rooms = Room::select('room_id', 'room_number')
            ->orderBy('room_number')
            ->get()
            ->map(fn ($r) => ['id' => (int) $r->room_id, 'name' => (string) $r->room_number])
            ->toArray();
    }

    private function autoProgressToDone(): int
    {
        $now = Carbon::now($this->tz)->toDateTimeString();

        return DB::transaction(function () use ($now) {
            return BookingRoom::query()
                ->where('status', 'approved')
                ->whereNotNull('end_time')
                ->where('end_time', '<=', $now)
                ->update(['status' => 'completed']);
        });
    }

    // unified search resets both paginations
    public function updatedQ(): void
    {
        $this->resetPage('pageDone');
        $this->resetPage('pageRejected');
    }

    public function updatedWithTrashed(): void
    {
        $this->resetPage('pageDone'); $this->resetPage('pageRejected');
    }
    public function updatedSelectedDate(): void
    {
        $this->resetPage('pageDone'); $this->resetPage('pageRejected');
    }
    public function updatedDateMode(): void
    {
        $this->resetPage('pageDone'); $this->resetPage('pageRejected');
    }

    public function create(string $bookingType = 'meeting', string $status = 'completed'): void
    {
        $this->modalMode = 'create';
        $this->editingId = null;

        $now = Carbon::now($this->tz);
        $this->form = [
            'booking_type'    => $bookingType,
            'meeting_title'   => '',
            'date'            => $now->toDateString(),
            'start_time'      => $now->format('H:00'),
            'end_time'        => $now->copy()->addHour()->format('H:00'),
            'room_id'         => null,
            'online_provider' => in_array($bookingType, ['online_meeting', 'onlinemeeting'], true) ? 'zoom' : null,
            'notes'           => '',
            'status'          => $status,
        ];
        $this->showModal = true;
    }

    public function edit(int $id): void
    {
        $row = $this->baseQuery()->withTrashed()->findOrFail($id);

        $this->modalMode = 'edit';
        $this->editingId = $row->getKey();

        $this->form = [
            'booking_type'    => (string) ($row->booking_type ?? 'meeting'),
            'meeting_title'   => (string) ($row->meeting_title ?? ''),
            'date'            => (string) ($row->date ?? ''),
            'start_time'      => (string) ($row->start_time ?? ''),
            'end_time'        => (string) ($row->end_time ?? ''),
            'room_id'         => $row->room_id,
            'online_provider' => (string) ($row->online_provider ?? ''),
            'notes'           => (string) ($row->notes ?? ''),
            'status'          => $this->normalizeDbStatus($row->status),
        ];

        $this->showModal = true;
    }

    public function save(): void
    {
        $data = $this->validateForm();
        $statusForDb = $data['status'];

        if ($this->modalMode === 'create') {
            BookingRoom::create([
                'booking_type'    => $data['booking_type'],
                'meeting_title'   => $data['meeting_title'],
                'date'            => $data['date'],
                'start_time'      => $data['start_time'],
                'end_time'        => $data['end_time'],
                'room_id'         => in_array($data['booking_type'], ['meeting', 'bookingroom'], true) ? $data['room_id'] : null,
                'online_provider' => in_array($data['booking_type'], ['online_meeting', 'onlinemeeting'], true) ? $data['online_provider'] : null,
                'notes'           => $data['notes'],
                'status'          => $statusForDb,
                'user_id'         => Auth::id(),
            ]);
        } else {
            $row = $this->baseQuery()->withTrashed()->findOrFail($this->editingId);
            $row->update([
                'booking_type'    => $data['booking_type'],
                'meeting_title'   => $data['meeting_title'],
                'date'            => $data['date'],
                'start_time'      => $data['start_time'],
                'end_time'        => $data['end_time'],
                'room_id'         => in_array($data['booking_type'], ['meeting', 'bookingroom'], true) ? $data['room_id'] : null,
                'online_provider' => in_array($data['booking_type'], ['online_meeting', 'onlinemeeting'], true) ? $data['online_provider'] : null,
                'notes'           => $data['notes'],
                'status'          => $statusForDb,
            ]);
        }

        $this->showModal = false;
        $this->dispatch('toast', type: 'success', title: 'Disimpan', message: 'Booking berhasil disimpan.', duration: 3000);
        if ($statusForDb === 'completed') $this->resetPage('pageDone');
        if ($statusForDb === 'rejected')  $this->resetPage('pageRejected');
    }

    public function destroy(int $id): void
    {
        $row = $this->baseQuery()->findOrFail($id);
        $row->delete();
        $this->dispatch('toast', type: 'success', title: 'Dihapus', message: 'Booking dihapus.', duration: 3000);
        $this->fixEmptyPageAfterChange();

    }

    public function restore(int $id): void
    {
        $row = $this->baseQuery()->onlyTrashed()->findOrFail($id);
        $row->restore();
        $this->dispatch('toast', type: 'success', title: 'Dipulihkan', message: 'Booking dipulihkan.', duration: 3000);
        $this->fixEmptyPageAfterChange();
    }

    public function forceDestroy(int $id): void
    {
        $row = $this->baseQuery()->withTrashed()->findOrFail($id);
        $row->forceDelete();

        $this->fixEmptyPageAfterChange();
        $this->dispatch('toast', type: 'success', title: 'Dihapus Permanen', message: 'Booking dihapus permanen.', duration: 3000);
    }

    private function fixEmptyPageAfterChange(): void
    {
        $done = $this->getDoneRowsProperty();
        if ($done->isEmpty() && $done->currentPage() > 1) {
            $this->setPage($done->currentPage() - 1, 'pageDone');
        }

        $rej = $this->getRejectedRowsProperty();
        if ($rej->isEmpty() && $rej->currentPage() > 1) {
            $this->setPage($rej->currentPage() - 1, 'pageRejected');
        }
    }

    private function baseQuery()
    {
        return BookingRoom::query()->with('room');
    }

    private function normalizeDbStatus($status): string
    {
        if (in_array($status, self::DONE_SET, true))     return 'completed';
        if (in_array($status, self::REJECTED_SET, true)) return 'rejected';
        return 'completed';
    }

    private function validateForm(): array
    {
        $isRoomType = in_array($this->form['booking_type'] ?? null, ['meeting', 'bookingroom'], true);

        $rules = [
            'form.booking_type'    => ['required', 'string', 'max:50'],
            'form.meeting_title'   => ['required', 'string', 'max:255'],
            'form.date'            => ['nullable', 'date'],
            'form.start_time'      => ['nullable', 'string', 'max:10'],
            'form.end_time'        => ['nullable', 'string', 'max:10'],
            'form.room_id'         => [$isRoomType ? 'required' : 'nullable', 'integer', 'exists:rooms,room_id'],
            'form.online_provider' => [$isRoomType ? 'nullable' : 'required', Rule::in(['zoom', 'google_meet'])],
            'form.notes'           => ['nullable', 'string', 'max:1000'],
            'form.status'          => ['required', Rule::in(['completed', 'rejected'])],
        ];

        $data = $this->validate($rules)['form'];

        foreach (['date', 'start_time', 'end_time', 'notes'] as $k) {
            if (($data[$k] ?? null) === '') $data[$k] = null;
        }

        return $data;
    }

    public function getDoneRowsProperty()
    {
        $q = $this->baseQuery()
            ->when(!$this->withTrashed, fn ($qq) => $qq->whereNull('deleted_at'))
            ->when($this->withTrashed, fn ($qq) => $qq->withTrashed())
            ->whereIn('status', self::DONE_SET)
            ->when($this->q !== '', fn ($qq) => $qq->where('meeting_title', 'like', '%' . $this->q . '%'))
            ->when($this->selectedDate, fn ($qq) => $qq->whereDate('date', $this->selectedDate))
            ->when($this->dateMode === 'terbaru', fn ($qq) => $qq->orderByDesc('date')->orderByDesc('start_time'))
            ->when($this->dateMode === 'terlama', fn ($qq) => $qq->orderBy('date')->orderBy('start_time'))
            ->when($this->dateMode === 'semua', fn ($qq) => $qq->orderByRaw("COALESCE(`date`, '0000-01-01') DESC")->orderByRaw("COALESCE(`start_time`, '00:00:00') DESC"));

        return $q->paginate($this->perDone, ['*'], 'pageDone');
    }

    public function getRejectedRowsProperty()
    {
        $q = $this->baseQuery()
            ->when(!$this->withTrashed, fn ($qq) => $qq->whereNull('deleted_at'))
            ->when($this->withTrashed, fn ($qq) => $qq->withTrashed())
            ->whereIn('status', ['rejected'])
            ->when($this->q !== '', fn ($qq) => $qq->where('meeting_title', 'like', '%' . $this->q . '%'))
            ->when($this->selectedDate, fn ($qq) => $qq->whereDate('date', $this->selectedDate))
            ->when($this->dateMode === 'terbaru', fn ($qq) => $qq->orderByDesc('date')->orderByDesc('start_time'))
            ->when($this->dateMode === 'terlama', fn ($qq) => $qq->orderBy('date')->orderBy('start_time'))
            ->when($this->dateMode === 'semua', fn ($qq) => $qq->orderByDesc('date')->orderByDesc('start_time'));

        return $q->paginate($this->perRejected, ['*'], 'pageRejected');
    }

    public function render()
    {
        $this->autoProgressToDone();

        return view('livewire.pages.receptionist.booking-history', [
            'doneRows'     => $this->doneRows,
            'rejectedRows' => $this->rejectedRows,
            'rooms'        => $this->rooms,
        ]);
    }
}
