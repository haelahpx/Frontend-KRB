<?php

namespace App\Livewire\Pages\Receptionist;

use Carbon\Carbon;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.receptionist')]
#[Title('Bookings Approval')]
class BookingsApproval extends Component
{
    use WithPagination;

    protected string $paginationTheme = 'tailwind';

    public string $q = '';
    public string $filter = 'pending';

    public string $meeting_title = '';
    public string $platform = 'google_meet';
    public ?string $date = null;
    public ?string $start_time = null;
    public ?string $end_time = null;
    public ?int $selected_department_id = null;
    public ?int $selected_user_id = null;

    public array $departments = [];
    public array $filteredUsers = [];

    public bool $showEdit = false;
    public ?int $editingId = null;

    public bool $googleConnected = false;

    public function mount(): void
    {
        $deptPk = $this->pickColumn('departments', ['department_id', 'id'], 'department_id');
        $deptLabel = $this->pickColumn('departments', ['name', 'department_name', 'dept_name', 'nama'], 'department_name');

        $this->departments = DB::table('departments')
            ->selectRaw("$deptPk as id, $deptLabel as label")
            ->orderBy($deptLabel, 'asc')
            ->get()
            ->map(fn($d) => ['id' => (int) $d->id, 'name' => (string) $d->label]) // map to ['id','name'] for Blade
            ->all();

        $this->googleConnected = $this->detectGoogleConnected();
    }

    protected function pickColumn(string $table, array $candidates, string $fallback): string
    {
        foreach ($candidates as $col) {
            if (Schema::hasColumn($table, $col))
                return $col;
        }
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

    public function getRowsProperty()
    {
        $q = DB::table('booking_rooms')
            ->select([
                'bookingroom_id',
                'meeting_title',
                'booking_type',
                'status',
                'date',
                'start_time',
                'end_time',
                'online_provider',
                'online_meeting_url',
                'online_meeting_code',
                'online_meeting_password',
            ]);

        if ($this->q !== '') {
            $q->where('meeting_title', 'like', '%' . $this->q . '%');
        }

        if ($this->filter !== 'all') {
            $q->where('status', $this->filter);
        }

        $q->orderByDesc('date')->orderBy('start_time');

        return $q->paginate(10);
    }

    public function createOnlineMeeting(): void
    {
        $data = $this->validate([
            'meeting_title' => ['required', 'string', 'max:255'],
            'platform' => ['required', Rule::in(['google_meet', 'zoom'])],
            'date' => ['required', 'date'],
            'start_time' => ['required', 'date_format:H:i'],
            'end_time' => ['required', 'date_format:H:i', 'after:start_time'],
            'selected_department_id' => ['nullable', 'integer'],
            'selected_user_id' => ['nullable', 'integer'],
        ], [], [
            'meeting_title' => 'judul meeting',
            'platform' => 'platform',
            'date' => 'tanggal',
            'start_time' => 'waktu mulai',
            'end_time' => 'waktu selesai',
        ]);

        $payload = [
            'meeting_title' => $data['meeting_title'],
            'booking_type' => 'online_meeting',
            'status' => 'approved',
            'date' => $data['date'],
            'start_time' => $data['start_time'],
            'end_time' => $data['end_time'],
            'online_provider' => $data['platform'],
            'online_meeting_url' => null,
            'online_meeting_code' => null,
            'online_meeting_password' => null,
            'department_id' => $this->selected_department_id,
            'user_id' => $this->selected_user_id,
            'created_at' => now(),
            'updated_at' => now(),
        ];

        try {
            if ($data['platform'] === 'google_meet' && $this->googleConnected && App::bound('App\Services\GoogleMeetService')) {
                $svc = App::make('App\Services\GoogleMeetService');
                if (method_exists($svc, 'createMeeting')) {
                    $meeting = $svc->createMeeting([
                        'title' => $data['meeting_title'],
                        'start_at' => Carbon::parse($data['date'] . ' ' . $data['start_time'], 'Asia/Jakarta'),
                        'end_at' => Carbon::parse($data['date'] . ' ' . $data['end_time'], 'Asia/Jakarta'),
                        'user_id' => $this->selected_user_id,
                        'dept_id' => $this->selected_department_id,
                    ]);
                    $payload['online_meeting_url'] = $meeting['url'] ?? null;
                    $payload['online_meeting_code'] = $meeting['code'] ?? null;
                    $payload['online_meeting_password'] = $meeting['password'] ?? null;
                }
            }

            if ($data['platform'] === 'zoom' && App::bound('App\Services\ZoomService')) {
                $zoom = App::make('App\Services\ZoomService');
                if (method_exists($zoom, 'createMeeting')) {
                    $meeting = $zoom->createMeeting(
                        $data['meeting_title'],
                        Carbon::parse($data['date'] . ' ' . $data['start_time'], 'Asia/Jakarta'),
                        Carbon::parse($data['date'] . ' ' . $data['end_time'], 'Asia/Jakarta')
                    );
                    $payload['online_meeting_url'] = $meeting['url'] ?? null;
                    $payload['online_meeting_code'] = $meeting['code'] ?? null;
                    $payload['online_meeting_password'] = $meeting['password'] ?? null;
                }
            }
        } catch (\Throwable $e) {

        }

        DB::table('booking_rooms')->insert($payload);

        // Reset create form
        $this->reset([
            'meeting_title',
            'date',
            'start_time',
            'end_time',
            'selected_department_id',
            'selected_user_id',
        ]);
        $this->platform = 'google_meet';

        if (method_exists($this, 'resetPage'))
            $this->resetPage();
        $this->dispatch('toast', type: 'success', message: 'Online meeting dibuat & di-approve.');
    }

    protected function calcDurationMinutes(string $start, string $end): int
    {
        try {
            $s = Carbon::createFromFormat('H:i', $start);
            $e = Carbon::createFromFormat('H:i', $end);
            return max(0, $e->diffInMinutes($s));
        } catch (\Throwable $e) {
            return 60;
        }
    }

    public function approve(int $bookingroomId): void
    {
        DB::table('booking_rooms')
            ->where('bookingroom_id', $bookingroomId)
            ->update(['status' => 'approved', 'updated_at' => now()]);

        $this->dispatch('toast', type: 'success', message: 'Booking approved.');
        if (method_exists($this, 'resetPage'))
            $this->resetPage();
    }

    public function reject(int $bookingroomId): void
    {
        DB::table('booking_rooms')
            ->where('bookingroom_id', $bookingroomId)
            ->update(['status' => 'rejected', 'updated_at' => now()]);

        $this->dispatch('toast', type: 'success', message: 'Booking rejected.');
        if (method_exists($this, 'resetPage'))
            $this->resetPage();
    }

    public function openEdit(int $bookingroomId): void
    {
        $row = DB::table('booking_rooms')
            ->where('bookingroom_id', $bookingroomId)
            ->first();

        if (!$row) {
            $this->dispatch('toast', type: 'error', message: 'Data booking tidak ditemukan.');
            return;
        }

        $this->editingId = $bookingroomId;
        $this->meeting_title = (string) ($row->meeting_title ?? '');
        $this->platform = (string) ($row->online_provider ?? 'google_meet');
        $this->date = $row->date ?? null;
        $this->start_time = $row->start_time ?? null;
        $this->end_time = $row->end_time ?? null;
        $this->selected_department_id = isset($row->department_id) ? (int) $row->department_id : null;
        $this->selected_user_id = isset($row->user_id) ? (int) $row->user_id : null;

        $this->loadUsersForDepartment($this->selected_department_id);

        $this->showEdit = true;
    }

    public function closeEdit(): void
    {
        $this->reset([
            'showEdit',
            'editingId',
            'meeting_title',
            'platform',
            'date',
            'start_time',
            'end_time',
            'selected_department_id',
            'selected_user_id',
            'filteredUsers',
        ]);
        $this->platform = 'google_meet';
    }

    public function update(): void
    {
        if (!$this->editingId) {
            $this->dispatch('toast', type: 'error', message: 'Tidak ada data yang diedit.');
            return;
        }

        $data = $this->validate([
            'meeting_title' => ['required', 'string', 'max:255'],
            'platform' => ['required', Rule::in(['google_meet', 'zoom'])],
            'date' => ['required', 'date'],
            'start_time' => ['required', 'date_format:H:i'],
            'end_time' => ['required', 'date_format:H:i', 'after:start_time'],
            'selected_department_id' => ['nullable', 'integer'],
            'selected_user_id' => ['nullable', 'integer'],
        ], [], [
            'meeting_title' => 'judul meeting',
            'platform' => 'platform',
            'date' => 'tanggal',
            'start_time' => 'waktu mulai',
            'end_time' => 'waktu selesai',
        ]);

        DB::table('booking_rooms')
            ->where('bookingroom_id', $this->editingId)
            ->update([
                'meeting_title' => $data['meeting_title'],
                'online_provider' => $data['platform'],
                'date' => $data['date'],
                'start_time' => $data['start_time'],
                'end_time' => $data['end_time'],
                'department_id' => $this->selected_department_id,
                'user_id' => $this->selected_user_id,
                'updated_at' => now(),
            ]);

        $this->dispatch('toast', type: 'success', message: 'Perubahan disimpan.');
        $this->closeEdit();
        if (method_exists($this, 'resetPage'))
            $this->resetPage();
    }

    public function updatedSelectedDepartmentId($value): void
    {
        $this->loadUsersForDepartment($value);
        $this->selected_user_id = null;
    }

    protected function loadUsersForDepartment($departmentId): void
    {
        if (!$departmentId) {
            $this->filteredUsers = [];
            return;
        }

        $userPk = $this->pickColumn('users', ['user_id', 'id'], 'user_id');
        $nameCol = $this->pickColumn('users', ['name', 'fullname', 'full_name'], 'name');

        $this->filteredUsers = DB::table('users')
            ->selectRaw("$userPk as id, $nameCol as label")
            ->where('department_id', $departmentId)
            ->orderBy($nameCol)
            ->get()
            ->map(fn($u) => ['id' => (int) $u->id, 'name' => (string) $u->label])
            ->all();
    }

    public function render()
    {
        return view('livewire.pages.receptionist.bookings-approval', [
            'rows' => $this->rows,
        ]);
    }
}
