<?php

namespace App\Livewire\Pages\Receptionist;

use App\Models\BookingRoom;
use App\Models\Requirement;
use Carbon\Carbon;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.receptionist')]
#[Title('Meeting Schedule')]
class MeetingSchedule extends Component
{
    private const INITIAL_STATUS = 'pending';

    private string $tz = 'Asia/Jakarta';

    public ?int $editingId = null;
    public array $form = [
        'meeting_title' => null,
        'room_id' => null,
        'department_id' => null,
        'date' => null,
        'time' => null,
        'time_end' => null,
        'participant' => null,
        'notes' => null,
        'requirements' => [],
    ];

    public string $online_meeting_title = '';
    public string $online_platform = 'google_meet';
    public ?string $online_date = null;
    public ?string $online_start_time = null;
    public ?string $online_end_time = null;
    public ?int $online_department_id = null;
    public ?int $online_user_id = null;
    public bool $googleConnected = false;
    public array $departments = [];
    public array $usersByDept = [];
    public array $requirementOptions = [];
    public array $rooms = [];

    public string $deptQueryOffline = '';
    public string $deptQueryOnline = '';

    private ?int $otherRequirementId = null;

    public function mount(): void
    {
        if (Requirement::count() === 0 && method_exists(Requirement::class, 'upsertByName')) {
            Requirement::upsertByName(['Video Conference', 'Projector', 'Whiteboard', 'Catering', 'Other']);
        }

        $this->loadDepartments();
        $this->loadRooms();
        $this->loadRequirements();

        $this->otherRequirementId = $this->findOtherRequirementId();
        $this->googleConnected = $this->detectGoogleConnected();

        if ($this->online_department_id) {
            $this->loadUsersForDept((int) $this->online_department_id);
        }
    }

    protected function loadDepartments(): void
    {
        $nameCol = $this->pickColumn('departments', ['department_name', 'name'], 'department_name');
        $pkCol = $this->pickColumn('departments', ['department_id', 'id'], 'department_id');

        $dept = DB::table('departments')
            ->selectRaw("$pkCol as id, $nameCol as label")
            ->when(Auth::user()?->company_id, fn($q, $cid) => $q->where('company_id', $cid))
            ->orderBy($nameCol)
            ->get();

        $this->departments = $dept->map(fn($d) => [
            'id' => (int) $d->id,
            'name' => (string) $d->label,
        ])->all();
    }

    protected function loadRooms(): void
    {
        $pkCol = $this->pickColumn('rooms', ['room_id', 'id'], 'room_id');
        $labelCol = $this->pickColumn('rooms', ['room_name', 'room_number', 'name'], 'room_number');

        $rooms = DB::table('rooms')
            ->selectRaw("$pkCol as id, $labelCol as label")
            ->when(Auth::user()?->company_id, fn($q, $cid) => $q->where('company_id', $cid))
            ->orderBy($labelCol)
            ->get();

        $this->rooms = $rooms->map(fn($r) => [
            'id' => (int) $r->id,
            'name' => (string) $r->label,
        ])->all();
    }

    protected function loadRequirements(): void
    {
        $this->requirementOptions = Requirement::orderBy('name')
            ->get(['requirement_id', 'name'])
            ->map(fn($r) => ['id' => (int) $r->requirement_id, 'name' => (string) $r->name])
            ->all();
    }

    protected function loadUsersForDept(?int $deptId): void
    {
        $this->usersByDept = [];
        if (!$deptId)
            return;

        $pk = $this->pickColumn('users', ['user_id', 'id'], 'user_id');
        $name = $this->pickColumn('users', ['name', 'full_name', 'fullname'], 'name');

        $q = DB::table('users')
            ->selectRaw("$pk as id, $name as label")
            ->where('department_id', $deptId);

        if ($cid = Auth::user()?->company_id) {
            if (Schema::hasColumn('users', 'company_id')) {
                $q->where('company_id', $cid);
            }
        }

        $this->usersByDept = $q->orderBy($name)->get()
            ->map(fn($u) => ['id' => (int) $u->id, 'name' => (string) $u->label])->all();
    }

    public function updatedOnlineDepartmentId($val): void
    {
        $this->loadUsersForDept((int) ($val ?: 0));
        if ($this->online_user_id && !collect($this->usersByDept)->pluck('id')->contains((int) $this->online_user_id)) {
            $this->online_user_id = null;
        }
    }


    protected function rules(): array
    {
        $deptPk = $this->pickColumn('departments', ['department_id', 'id'], 'department_id');
        $roomPk = $this->pickColumn('rooms', ['room_id', 'id'], 'room_id');

        return [
            'form.meeting_title' => ['required', 'string', 'max:255'],
            'form.room_id' => ['required', "integer", "exists:rooms,{$roomPk}"],
            'form.department_id' => ['required', 'integer', "exists:departments,{$deptPk}"],
            'form.date' => ['required', 'date_format:Y-m-d'],
            'form.time' => ['required', 'date_format:H:i'],
            'form.time_end' => ['required', 'date_format:H:i', 'after:form.time'],
            'form.participant' => ['required', 'integer', 'min:1'],
            'form.notes' => ['nullable', 'string', 'max:1000'],
            'form.requirements' => ['array'],
            'form.requirements.*' => ['integer', 'exists:requirements,requirement_id'],
        ];
    }

    protected function hasRoomOverlap(int $roomId, string $ymd, string $startAt, string $endAt, ?int $excludeId = null): bool
    {
        $pendingApproved = ['pending', 'approved', 0, 1, '0', '1', 'PENDING', 'APPROVED'];

        return BookingRoom::query()
            ->where('room_id', $roomId)
            ->where('date', $ymd)
            ->whereIn('status', $pendingApproved)
            ->when($excludeId, fn($q) => $q->where('bookingroom_id', '!=', $excludeId))
            ->where('start_time', '<', $endAt)
            ->where('end_time', '>', $startAt)
            ->exists();
    }

    public function saveOffline(): void
    {
        $this->validate();
        $this->validateNotesIfOther();

        $uid = Auth::user()?->user_id ?? Auth::id();
        $cid = Auth::user()?->company_id;
        $roomId = (int) $this->form['room_id'];

        try {
            $startAt = Carbon::createFromFormat('Y-m-d H:i', "{$this->form['date']} {$this->form['time']}", $this->tz)
                ->format('Y-m-d H:i:s');
            $endAt = Carbon::createFromFormat('Y-m-d H:i', "{$this->form['date']} {$this->form['time_end']}", $this->tz)
                ->format('Y-m-d H:i:s');
        } catch (\Throwable $e) {
            session()->flash('toast', ['type' => 'error', 'title' => 'Format waktu tidak valid', 'message' => 'Periksa tanggal/jam.']);
            $this->js('window.location.reload()');
            return;
        }

        if ($endAt <= $startAt) {
            session()->flash('toast', ['type' => 'error', 'title' => 'Waktu salah', 'message' => 'Jam selesai harus setelah jam mulai.']);
            $this->js('window.location.reload()');
            return;
        }

        if ($this->hasRoomOverlap($roomId, (string) $this->form['date'], $startAt, $endAt, $this->editingId)) {
            $humanStart = Carbon::parse($startAt, $this->tz)->format('d M Y H:i');
            $humanEnd = Carbon::parse($endAt, $this->tz)->format('H:i');
            session()->flash('toast', [
                'type' => 'warning',
                'title' => 'Slot Sudah Terpakai',
                'message' => "Waktu {$humanStart}–{$humanEnd} untuk ruangan yang dipilih sudah di-booking. Cek kalender dulu.",
            ]);
            $this->js('window.location.reload()');
            return;
        }

        BookingRoom::create([
            'room_id' => $roomId,
            'company_id' => $cid,
            'user_id' => $uid,
            'department_id' => (int) $this->form['department_id'],
            'meeting_title' => (string) $this->form['meeting_title'],
            'date' => (string) $this->form['date'],
            'number_of_attendees' => (int) $this->form['participant'],
            'start_time' => $startAt,
            'end_time' => $endAt,
            'special_notes' => $this->composeSpecialNotes($this->form['requirements'], (string) ($this->form['notes'] ?? '')),
            'booking_type' => 'meeting',
            'status' => self::INITIAL_STATUS,
            'is_approve' => 0,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->resetOfflineForm();

        session()->flash('toast', [
            'type' => 'success',
            'title' => 'Sukses',
            'message' => 'Booking disimpan (pending approval). Halaman diperbarui.',
        ]);

        $this->js('window.location.reload()');
    }

    public function saveOnline(): void
    {
        $data = $this->validate([
            'online_meeting_title' => ['required', 'string', 'max:255'],
            'online_platform' => ['required', Rule::in(['google_meet', 'zoom'])],
            'online_date' => ['required', 'date_format:Y-m-d'],
            'online_start_time' => ['required', 'date_format:H:i'],
            'online_end_time' => ['required', 'date_format:H:i', 'after:online_start_time'],
            'online_department_id' => ['nullable', 'integer'],
            'online_user_id' => ['nullable', 'integer'],
        ], [], [
            'online_meeting_title' => 'judul meeting',
            'online_platform' => 'platform',
            'online_date' => 'tanggal',
            'online_start_time' => 'waktu mulai',
            'online_end_time' => 'waktu selesai',
        ]);

        $cid = Auth::user()?->company_id;
        $uid = Auth::user()?->user_id ?? Auth::id();

        try {
            $startAt = Carbon::createFromFormat('Y-m-d H:i', "{$data['online_date']} {$data['online_start_time']}", $this->tz)
                ->format('Y-m-d H:i:s');
            $endAt = Carbon::createFromFormat('Y-m-d H:i', "{$data['online_date']} {$data['online_end_time']}", $this->tz)
                ->format('Y-m-d H:i:s');
        } catch (\Throwable $e) {
            session()->flash('toast', ['type' => 'error', 'title' => 'Format waktu tidak valid', 'message' => 'Periksa tanggal/jam.']);
            $this->js('window.location.reload()');
            return;
        }

        if ($endAt <= $startAt) {
            session()->flash('toast', ['type' => 'error', 'title' => 'Waktu salah', 'message' => 'Jam selesai harus setelah jam mulai.']);
            $this->js('window.location.reload()');
            return;
        }

        DB::table('booking_rooms')->insert([
            'company_id' => $cid,
            'user_id' => $uid,
            'department_id' => $this->online_department_id,
            'meeting_title' => $data['online_meeting_title'],
            'booking_type' => 'online_meeting',
            'status' => self::INITIAL_STATUS,
            'is_approve' => 0,
            'date' => $data['online_date'],
            'start_time' => $startAt,
            'end_time' => $endAt,
            'online_provider' => $data['online_platform'],
            'online_meeting_url' => null,
            'online_meeting_code' => null,
            'online_meeting_password' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->resetOnlineForm();

        session()->flash('toast', [
            'type' => 'success',
            'title' => 'Sukses',
            'message' => 'Online meeting disimpan. Halaman diperbarui.',
        ]);

        $this->js('window.location.reload()');
    }

    public function updated($name): void
    {
        if (in_array($name, ['form.room_id', 'form.date', 'form.time', 'form.time_end'], true)) {
            try {
                if ($this->form['room_id'] && $this->form['date'] && $this->form['time'] && $this->form['time_end']) {
                    $startAt = Carbon::createFromFormat('Y-m-d H:i', "{$this->form['date']} {$this->form['time']}", $this->tz)->format('Y-m-d H:i:s');
                    $endAt = Carbon::createFromFormat('Y-m-d H:i', "{$this->form['date']} {$this->form['time_end']}", $this->tz)->format('Y-m-d H:i:s');

                    if ($endAt > $startAt && $this->hasRoomOverlap((int) $this->form['room_id'], (string) $this->form['date'], $startAt, $endAt, $this->editingId)) {
                        $this->dispatch('toast', type: 'warning', title: 'Perhatian', message: 'Slot ini tampak bentrok. Cek kalender dulu ya.');
                    }
                }
            } catch (\Throwable $e) {
            }
        }
    }

    protected function pickColumn(string $table, array $candidates, string $fallback): string
    {
        foreach ($candidates as $col)
            if (Schema::hasColumn($table, $col))
                return $col;
        return $fallback;
    }

    protected function detectGoogleConnected(): bool
    {
        try {
            if (App::bound('App\Services\GoogleMeetService')) {
                $svc = App::make('App\Services\GoogleMeetService');
                if (method_exists($svc, 'connected'))
                    return (bool) $svc->connected();
                if (method_exists($svc, 'isConnected'))
                    return (bool) $svc->isConnected();
            }
        } catch (\Throwable $e) {
        }
        return false;
    }

    private function composeSpecialNotes(array $ids, string $notes): string
    {
        $cleanIds = array_values(array_unique(array_map('intval', array_filter($ids, fn($v) => $v !== null))));
        $tag = $cleanIds ? '[REQID=' . implode(',', $cleanIds) . ']' : '';
        return trim($tag . "\n" . trim($notes));
    }

    private function findOtherRequirementId(): ?int
    {
        $id = Requirement::whereRaw('LOWER(name)=?', ['other'])->value('requirement_id');
        return $id ? (int) $id : null;
    }

    private function validateNotesIfOther(): void
    {
        if ($this->otherRequirementId && in_array($this->otherRequirementId, $this->form['requirements'] ?? [], true)) {
            $this->validate(['form.notes' => 'required|string|min:2|max:1000']);
        }
    }

    private function resetOfflineForm(): void
    {
        $this->editingId = null;
        $this->form = [
            'meeting_title' => null,
            'room_id' => null,
            'department_id' => null,
            'date' => null,
            'time' => null,
            'time_end' => null,
            'participant' => null,
            'notes' => null,
            'requirements' => [],
        ];
        $this->resetValidation();
    }

    private function resetOnlineForm(): void
    {
        $this->online_meeting_title = '';
        $this->online_platform = 'google_meet';
        $this->online_date = null;
        $this->online_start_time = null;
        $this->online_end_time = null;
        $this->online_department_id = null;
        $this->online_user_id = null;
        $this->usersByDept = [];
        $this->resetValidation();
    }

    public function render()
    {
        $filter = fn(array $items, string $query) =>
            array_values(array_filter(
                $items,
                fn($d) =>
                $query === '' || str_contains(strtolower($d['name']), strtolower($query))
            ));

        $departmentsOffline = $filter($this->departments, $this->deptQueryOffline);
        $departmentsOnline = $filter($this->departments, $this->deptQueryOnline);

        return view('livewire.pages.receptionist.meetingschedule', [
            'departments' => $this->departments,
            'departmentsOffline' => $departmentsOffline,
            'departmentsOnline' => $departmentsOnline,
            'requirementOptions' => $this->requirementOptions,
            'rooms' => $this->rooms,
            'usersByDept' => $this->usersByDept,
            'googleConnected' => $this->googleConnected,
            'editingId' => $this->editingId,
            'form' => $this->form,
            'online_platform' => $this->online_platform,
        ]);
    }
}
