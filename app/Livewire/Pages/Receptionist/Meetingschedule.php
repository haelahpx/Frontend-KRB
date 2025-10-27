<?php

namespace App\Livewire\Pages\Receptionist;

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

    /** OFFLINE form state */
    public array $form = [
        'meeting_title' => null,
        'room_id'       => null,
        'department_id' => null,
        'date'          => null,
        'time'          => null,
        'time_end'      => null,
        'participant'   => null,
        'notes'         => null,
        'requirements'  => [],
    ];

    /** OFFLINE user dropdown + search */
    public ?int $offline_user_id = null;
    public array $usersByDeptOffline = [];
    public string $userQueryOffline = '';

    /** ONLINE form state */
    public string $online_meeting_title = '';
    public string $online_platform = 'google_meet';
    public ?string $online_date = null;
    public ?string $online_start_time = null;
    public ?string $online_end_time = null;
    public ?int $online_department_id = null;
    public ?int $online_user_id = null;
    public array $usersByDept = [];
    public string $userQueryOnline = '';

    /** Shared lookups / flags */
    public bool $googleConnected = false;
    public array $departments = [];
    public array $requirementOptions = [];
    public array $rooms = [];

    /** Department search inputs (front-end filtered) */
    public string $deptQueryOffline = '';
    public string $deptQueryOnline  = '';

    private ?int $otherRequirementId = null;

    public function mount(): void
    {
        // Optional seeding helper
        if (Requirement::count() === 0 && method_exists(Requirement::class, 'upsertByName')) {
            Requirement::upsertByName(['Video Conference', 'Projector', 'Whiteboard', 'Catering', 'Other']);
        }

        $this->loadDepartments();
        $this->loadRooms();
        $this->loadRequirements();

        $this->otherRequirementId = $this->findOtherRequirementId();
        $this->googleConnected    = $this->detectGoogleConnected();

        // Initial users (if department already preselected)
        if ($this->online_department_id) {
            $this->loadUsersForDept((int) $this->online_department_id);
        }
        if (!empty($this->form['department_id'])) {
            $this->loadUsersForDeptOffline((int) $this->form['department_id']);
        }
    }

    /* ===================== Lookups ===================== */

    protected function loadDepartments(): void
    {
        $nameCol = $this->pickColumn('departments', ['department_name', 'name'], 'department_name');
        $pkCol   = $this->pickColumn('departments', ['department_id', 'id'], 'department_id');

        $dept = DB::table('departments')
            ->selectRaw("$pkCol as id, $nameCol as label")
            ->when(Auth::user()?->company_id, fn($q, $cid) => $q->where('company_id', $cid))
            ->orderBy($nameCol)
            ->get();

        $this->departments = $dept->map(fn($d) => [
            'id'   => (int) $d->id,
            'name' => (string) $d->label,
        ])->all();
    }

    protected function loadRooms(): void
    {
        $pkCol    = $this->pickColumn('rooms', ['room_id', 'id'], 'room_id');
        $labelCol = $this->pickColumn('rooms', ['room_name', 'room_number', 'name'], 'room_number');

        $rooms = DB::table('rooms')
            ->selectRaw("$pkCol as id, $labelCol as label")
            ->when(Auth::user()?->company_id, fn($q, $cid) => $q->where('company_id', $cid))
            ->orderBy($labelCol)
            ->get();

        $this->rooms = $rooms->map(fn($r) => [
            'id'   => (int) $r->id,
            'name' => (string) $r->label,
        ])->all();
    }

    protected function loadRequirements(): void
    {
        $idCol = $this->pickColumn('requirements', ['requirement_id', 'id'], 'requirement_id');
        $nmCol = $this->pickColumn('requirements', ['name', 'requirement_name'], 'name');

        $this->requirementOptions = DB::table('requirements')
            ->selectRaw("$idCol as id, $nmCol as name")
            ->orderBy($nmCol)
            ->get()
            ->map(fn($r) => ['id' => (int)$r->id, 'name' => (string)$r->name])
            ->all();
    }

    /* ===================== Users loading ===================== */

    /** Base: full list for a department (no search) */
    protected function loadUsersForDept(?int $deptId): void
    {
        $this->usersByDept = $this->queryUsersForDropdown($deptId, null);
    }

    /** Base: full list for a department (no search) */
    protected function loadUsersForDeptOffline(?int $deptId): void
    {
        $this->usersByDeptOffline = $this->queryUsersForDropdown($deptId, null);
    }

    /**
     * DB query for users with optional search (prefix-first).
     * If $search is provided, results are ordered with prefix matches first.
     */
    private function queryUsersForDropdown(?int $deptId, ?string $search, int $limit = 50): array
    {
        if (!$deptId) return [];

        $pk   = $this->pickColumn('users', ['user_id', 'id'], 'user_id');
        $name = $this->pickColumn('users', ['name', 'full_name', 'fullname'], 'name');

        $cid  = Auth::user()?->company_id;

        $q = DB::table('users')
            ->selectRaw("$pk as id, $name as label")
            ->where('department_id', $deptId);

        if ($cid && Schema::hasColumn('users', 'company_id')) {
            $q->where('company_id', $cid);
        }

        if ($search !== null && $search !== '') {
            $needle = trim($search);

            // Order prefix matches first, then contains, then alphabetically
            $q->where(function ($qq) use ($name, $needle) {
                $qq->where($name, 'like', "%{$needle}%");
            })
              ->orderByRaw("CASE WHEN $name LIKE ? THEN 0 ELSE 1 END", ["{$needle}%"])
              ->orderBy($name)
              ->limit($limit);
        } else {
            $q->orderBy($name)->limit($limit);
        }

        return $q->get()
            ->map(fn($u) => ['id' => (int)$u->id, 'name' => (string)$u->label])
            ->all();
    }

    /* ===================== Livewire watchers ===================== */

    public function updatedOnlineDepartmentId($val): void
    {
        $deptId = (int) ($val ?: 0);

        // If user is typing, fetch filtered from DB; otherwise full list
        if (trim($this->userQueryOnline) !== '') {
            $this->usersByDept = $this->queryUsersForDropdown($deptId, $this->userQueryOnline);
        } else {
            $this->loadUsersForDept($deptId);
        }

        if ($this->online_user_id && !collect($this->usersByDept)->pluck('id')->contains((int)$this->online_user_id)) {
            $this->online_user_id = null;
        }
    }

    public function updatedFormDepartmentId($val): void
    {
        $deptId = (int) ($val ?: 0);

        if (trim($this->userQueryOffline) !== '') {
            $this->usersByDeptOffline = $this->queryUsersForDropdown($deptId, $this->userQueryOffline);
        } else {
            $this->loadUsersForDeptOffline($deptId);
        }

        if ($this->offline_user_id && !collect($this->usersByDeptOffline)->pluck('id')->contains((int)$this->offline_user_id)) {
            $this->offline_user_id = null;
        }
    }

    /** Search-as-you-type (OFFLINE) */
    public function updatedUserQueryOffline($q): void
    {
        $deptId = (int) ($this->form['department_id'] ?: 0);
        if ($deptId === 0) { $this->usersByDeptOffline = []; return; }

        $this->usersByDeptOffline = $this->queryUsersForDropdown($deptId, $q);

        // Optional auto-select when exact match is typed
        $first = $this->usersByDeptOffline[0] ?? null;
        if ($first && mb_strtolower($first['name']) === mb_strtolower(trim($q))) {
            $this->offline_user_id = $first['id'];
        }
    }

    /** Search-as-you-type (ONLINE) */
    public function updatedUserQueryOnline($q): void
    {
        $deptId = (int) ($this->online_department_id ?: 0);
        if ($deptId === 0) { $this->usersByDept = []; return; }

        $this->usersByDept = $this->queryUsersForDropdown($deptId, $q);

        $first = $this->usersByDept[0] ?? null;
        if ($first && mb_strtolower($first['name']) === mb_strtolower(trim($q))) {
            $this->online_user_id = $first['id'];
        }
    }

    /* ===================== Validation & helpers ===================== */

    protected function rules(): array
    {
        $deptPk = $this->pickColumn('departments', ['department_id', 'id'], 'department_id');
        $roomPk = $this->pickColumn('rooms', ['room_id', 'id'], 'room_id');

        return [
            'form.meeting_title' => ['required', 'string', 'max:255'],
            'form.room_id'       => ['required', 'integer', "exists:rooms,{$roomPk}"],
            'form.department_id' => ['required', 'integer', "exists:departments,{$deptPk}"],
            'form.date'          => ['required', 'date_format:Y-m-d'],
            'form.time'          => ['required', 'date_format:H:i'],
            'form.time_end'      => ['required', 'date_format:H:i', 'after:form.time'],
            'form.participant'   => ['required', 'integer', 'min:1'],
            'form.notes'         => ['nullable', 'string', 'max:1000'],
            'form.requirements'  => ['array'],
            'form.requirements.*'=> ['integer'],
        ];
    }

    protected function hasRoomOverlap(
        int $roomId,
        string $ymd,
        string $startAt,
        string $endAt,
        ?int $excludeId = null
    ): bool {
        $pendingApproved = ['pending', 'approved', 0, 1, '0', '1', 'PENDING', 'APPROVED'];

        return DB::table('booking_rooms')
            ->where('room_id', $roomId)
            ->where('date', $ymd)
            ->whereIn('status', $pendingApproved)
            ->when($excludeId, fn($q) => $q->where('bookingroom_id', '!=', $excludeId))
            ->where('start_time', '<', $endAt)
            ->where('end_time',   '>', $startAt)
            ->exists();
    }

    private function toDateTime(string $ymd, string $hm): string
    {
        return Carbon::createFromFormat('Y-m-d H:i', "$ymd $hm", $this->tz)->format('Y-m-d H:i:s');
    }

    public function saveOffline(): void
    {
        $this->validate();
        $this->validateNotesIfOther();

        $cid    = Auth::user()?->company_id;
        $authId = Auth::user()?->user_id ?? Auth::id();

        try {
            $startAt = $this->toDateTime((string)$this->form['date'], (string)$this->form['time']);
            $endAt   = $this->toDateTime((string)$this->form['date'], (string)$this->form['time_end']);
        } catch (\Throwable) {
            session()->flash('toast', ['type' => 'error', 'title' => 'Format waktu tidak valid', 'message' => 'Periksa tanggal/jam.']);
            $this->js('window.location.reload()');
            return;
        }

        if ($endAt <= $startAt) {
            session()->flash('toast', ['type' => 'error', 'title' => 'Waktu salah', 'message' => 'Jam selesai harus setelah jam mulai.']);
            $this->js('window.location.reload()');
            return;
        }

        if ($this->hasRoomOverlap((int)$this->form['room_id'], (string)$this->form['date'], $startAt, $endAt, $this->editingId)) {
            $humanStart = Carbon::parse($startAt, $this->tz)->format('d M Y H:i');
            $humanEnd   = Carbon::parse($endAt,   $this->tz)->format('H:i');
            session()->flash('toast', [
                'type' => 'warning',
                'title' => 'Slot Sudah Terpakai',
                'message' => "Waktu {$humanStart}–{$humanEnd} untuk ruangan yang dipilih sudah di-booking. Cek kalender dulu.",
            ]);
            $this->js('window.location.reload()');
            return;
        }

        DB::table('booking_rooms')->insert([
            'room_id'              => (int)$this->form['room_id'],
            'company_id'           => $cid,
            'user_id'              => $authId,
            'department_id'        => (int)$this->form['department_id'],
            'meeting_title'        => (string)$this->form['meeting_title'],
            'date'                 => (string)$this->form['date'],
            'start_time'           => $startAt,
            'end_time'             => $endAt,
            ...(Schema::hasColumn('booking_rooms', 'number_of_attendees')
                ? ['number_of_attendees' => (int)$this->form['participant']]
                : (Schema::hasColumn('booking_rooms', 'participant') ? ['participant' => (int)$this->form['participant']] : [])
            ),
            ...(Schema::hasColumn('booking_rooms', 'special_notes')
                ? ['special_notes' => $this->composeSpecialNotes($this->form['requirements'], (string)($this->form['notes'] ?? ''))]
                : (Schema::hasColumn('booking_rooms', 'notes') ? ['notes' => $this->composeSpecialNotes($this->form['requirements'], (string)($this->form['notes'] ?? ''))] : [])
            ),
            'booking_type'         => 'meeting',
            'status'               => self::INITIAL_STATUS,
            ...(Schema::hasColumn('booking_rooms', 'is_approve') ? ['is_approve' => 0] : []),
            'created_at'           => now(),
            'updated_at'           => now(),
            ...(Schema::hasColumn('booking_rooms', 'assigned_user_id') && $this->offline_user_id
                ? ['assigned_user_id' => (int)$this->offline_user_id]
                : []
            ),
        ]);

        $this->resetOfflineForm();

        session()->flash('toast', ['type' => 'success', 'title' => 'Sukses', 'message' => 'Booking disimpan (pending approval).']);
        $this->js('window.location.reload()');
    }

    public function saveOnline(): void
    {
        $data = $this->validate([
            'online_meeting_title' => ['required', 'string', 'max:255'],
            'online_platform'      => ['required', Rule::in(['google_meet', 'zoom'])],
            'online_date'          => ['required', 'date_format:Y-m-d'],
            'online_start_time'    => ['required', 'date_format:H:i'],
            'online_end_time'      => ['required', 'date_format:H:i', 'after:online_start_time'],
            'online_department_id' => ['nullable', 'integer'],
            'online_user_id'       => ['nullable', 'integer'],
        ]);

        $cid = Auth::user()?->company_id;
        $uid = Auth::user()?->user_id ?? Auth::id();

        try {
            $startAt = $this->toDateTime($data['online_date'], $data['online_start_time']);
            $endAt   = $this->toDateTime($data['online_date'], $data['online_end_time']);
        } catch (\Throwable) {
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
            'company_id'             => $cid,
            'user_id'                => $uid,
            'department_id'          => $this->online_department_id,
            'meeting_title'          => $data['online_meeting_title'],
            'booking_type'           => 'online_meeting',
            'status'                 => self::INITIAL_STATUS,
            ...(Schema::hasColumn('booking_rooms', 'is_approve') ? ['is_approve' => 0] : []),
            'date'                   => $data['online_date'],
            'start_time'             => $startAt,
            'end_time'               => $endAt,
            'online_provider'        => $data['online_platform'],
            'online_meeting_url'     => null,
            'online_meeting_code'    => null,
            'online_meeting_password'=> null,
            'created_at'             => now(),
            'updated_at'             => now(),
            ...(Schema::hasColumn('booking_rooms', 'assigned_user_id') && $this->online_user_id
                ? ['assigned_user_id' => (int)$this->online_user_id]
                : []
            ),
        ]);

        $this->resetOnlineForm();

        session()->flash('toast', ['type' => 'success', 'title' => 'Sukses', 'message' => 'Online meeting disimpan.']);
        $this->js('window.location.reload()');
    }

    public function updated($name): void
    {
        // (Keep your overlap checker etc. if you had it here)
        if ($name === 'online_department_id') {
            $this->updatedOnlineDepartmentId($this->online_department_id);
        }
        if ($name === 'form.department_id') {
            $this->updatedFormDepartmentId($this->form['department_id']);
        }
    }

    /* ===================== Utilities ===================== */

    protected function pickColumn(string $table, array $candidates, string $fallback): string
    {
        foreach ($candidates as $col) {
            if (Schema::hasColumn($table, $col)) return $col;
        }
        return $fallback;
    }

    protected function detectGoogleConnected(): bool
    {
        try {
            if (App::bound('App\Services\GoogleMeetService')) {
                $svc = App::make('App\Services\GoogleMeetService');
                if (method_exists($svc, 'connected'))   return (bool) $svc->connected();
                if (method_exists($svc, 'isConnected')) return (bool) $svc->isConnected();
            }
        } catch (\Throwable) {}
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
        $idCol = $this->pickColumn('requirements', ['requirement_id', 'id'], 'requirement_id');
        $nmCol = $this->pickColumn('requirements', ['name', 'requirement_name'], 'name');

        $row = DB::table('requirements')
            ->select($idCol . ' as id', $nmCol . ' as name')
            ->whereRaw('LOWER('.$nmCol.') = ?', ['other'])
            ->first();

        return $row ? (int) $row->id : null;
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
            'room_id'       => null,
            'department_id' => null,
            'date'          => null,
            'time'          => null,
            'time_end'      => null,
            'participant'   => null,
            'notes'         => null,
            'requirements'  => [],
        ];
        $this->offline_user_id    = null;
        $this->usersByDeptOffline = [];
        $this->userQueryOffline   = '';
        $this->resetValidation();
    }

    private function resetOnlineForm(): void
    {
        $this->online_meeting_title = '';
        $this->online_platform      = 'google_meet';
        $this->online_date          = null;
        $this->online_start_time    = null;
        $this->online_end_time      = null;
        $this->online_department_id = null;
        $this->online_user_id       = null;
        $this->usersByDept          = [];
        $this->userQueryOnline      = '';
        $this->resetValidation();
    }

    /* ===================== Render ===================== */

    private function filterItemsByName(array $items, string $q, int $max = 50): array
    {
        $q = trim((string)$q);
        if ($q === '') {
            usort($items, fn($a, $b) => strnatcasecmp($a['name'] ?? '', $b['name'] ?? ''));
            return array_slice(array_values($items), 0, $max);
        }

        $qLower = mb_strtolower($q);
        $prefix = array_values(array_filter($items, fn($r) => isset($r['name']) && mb_stripos($r['name'], $qLower) === 0));
        $extra  = array_values(array_filter($items, fn($r) => isset($r['name']) && mb_stripos($r['name'], $qLower) !== false && mb_stripos($r['name'], $qLower) !== 0));

        usort($prefix, fn($a,$b)=>strnatcasecmp($a['name'],$b['name']));
        usort($extra,  fn($a,$b)=>strnatcasecmp($a['name'],$b['name']));
        return array_slice(array_merge($prefix, $extra), 0, $max);
    }

    public function render()
    {
        // Departments are filtered on the client side (array filter)
        $departmentsOffline = $this->filterItemsByName($this->departments, $this->deptQueryOffline, 50);
        $departmentsOnline  = $this->filterItemsByName($this->departments, $this->deptQueryOnline, 50);

        // Users are already DB-filtered as you type, so just pass through
        $usersOfflineFiltered = $this->usersByDeptOffline;
        $usersOnlineFiltered  = $this->usersByDept;

        return view('livewire.pages.receptionist.meetingschedule', [
            'departments'        => $this->departments,
            'departmentsOffline' => $departmentsOffline,
            'departmentsOnline'  => $departmentsOnline,
            'requirementOptions' => $this->requirementOptions,
            'rooms'              => $this->rooms,
            'usersByDept'        => $usersOnlineFiltered,
            'usersByDeptOffline' => $usersOfflineFiltered,
            'googleConnected'    => $this->googleConnected,
            'editingId'          => $this->editingId,
            'form'               => $this->form,
            'online_platform'    => $this->online_platform,
        ]);
    }
}
