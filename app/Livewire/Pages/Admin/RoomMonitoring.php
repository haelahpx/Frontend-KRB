<?php

namespace App\Livewire\Pages\Admin;

use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\ValidationException;
use App\Models\BookingRoom;
use App\Models\Department;
use App\Models\Room;
use Carbon\Carbon;

#[Layout('layouts.admin')]
#[Title('Room Monitoring')]
class RoomMonitoring extends Component
{
    use WithPagination;

    public string $q = '';
    public ?string $date_from = null;   // Y-m-d
    public ?string $date_to   = null;   // Y-m-d
    public ?int $room_id = null;
    public ?int $department_id = null;
    public ?string $status = null;      // planned|ongoing|done
    public int $perPage = 10;

    // UI-bound dd/mm/yyyy
    public ?string $date_from_ui = null;
    public ?string $date_to_ui   = null;

    public array $rooms = [];
    public array $departments = [];

    public bool $showDetail = false;
    public array $detail = [];

    protected string $tz = 'Asia/Jakarta';

    protected $queryString = [
        'q'             => ['except' => ''],
        'date_from'     => ['except' => null],
        'date_to'       => ['except' => null],
        'room_id'       => ['except' => null],
        'department_id' => ['except' => null],
        'status'        => ['except' => null],
        'perPage'       => ['except' => 10],
    ];

    public function mount(): void
    {
        $this->department_id = $this->department_id ?: (Auth::user()->department_id ?? null);
        $cid = Auth::user()->company_id ?? null;

        // Rooms dropdown with defensive cols
        $roomDisplayCol = collect(['room_number', 'room_name', 'name'])
            ->first(fn($col) => Schema::hasColumn('rooms', $col)) ?? 'room_id';

        $roomSelect = array_values(array_unique(array_filter([
            'room_id',
            Schema::hasColumn('rooms', 'room_number') ? 'room_number' : null,
            Schema::hasColumn('rooms', 'room_name') ? 'room_name' : null,
            Schema::hasColumn('rooms', 'name') ? 'name' : null,
        ])));

        $this->rooms = Room::query()
            ->when($cid, fn($q) => $q->where('company_id', $cid))
            ->orderBy($roomDisplayCol)
            ->get($roomSelect)
            ->map(function ($r) use ($roomDisplayCol) {
                $display = $r->{$roomDisplayCol} ?? $r->room_id;
                if ($roomDisplayCol === 'room_id') $display = 'Room #'.$r->room_id;
                return ['id' => (int)$r->room_id, 'name' => (string)$display];
            })
            ->all();

        // Departments dropdown
        $deptDisplayCol = collect(['department_name', 'name'])
            ->first(fn($col) => Schema::hasColumn('departments', $col)) ?? 'department_id';

        $deptSelect = array_values(array_unique(array_filter([
            'department_id',
            Schema::hasColumn('departments','department_name') ? 'department_name' : null,
            Schema::hasColumn('departments','name') ? 'name' : null,
        ])));

        $this->departments = Department::query()
            ->when($cid, fn($q) => $q->where('company_id', $cid))
            ->orderBy($deptDisplayCol)
            ->get($deptSelect)
            ->map(function ($d) use ($deptDisplayCol) {
                $display = $d->{$deptDisplayCol} ?? $d->department_id;
                if ($deptDisplayCol === 'department_id') $display = 'Dept #'.$d->department_id;
                return ['id' => (int)$d->department_id, 'name' => (string)$display];
            })
            ->all();

        // Default range = this week
        if (!$this->date_from || !$this->date_to) {
            $today = Carbon::now($this->tz);
            $this->date_from = $today->copy()->startOfWeek()->format('Y-m-d');
            $this->date_to   = $today->copy()->endOfWeek()->format('Y-m-d');
        }
        $this->date_from_ui = $this->date_from ? Carbon::parse($this->date_from)->format('d/m/Y') : null;
        $this->date_to_ui   = $this->date_to   ? Carbon::parse($this->date_to)->format('d/m/Y')   : null;
    }

    public function updating($field): void
    {
        if (in_array($field, ['q','room_id','department_id','status','perPage','date_from_ui','date_to_ui'], true)) {
            $this->resetPage();
        }
    }

    private function sanitizeDdMmYyyy(?string $val): ?string
    {
        if (!$val) return null;
        $v = preg_replace('~[^0-9/]~', '', $val) ?? '';
        $v = preg_replace('~/+~', '/', $v);
        if (!preg_match('~^(\d{2})/(\d{2})/(\d{4})$~', $v, $m)) return null;
        if (!checkdate((int)$m[2], (int)$m[1], (int)$m[3])) return null;
        return sprintf('%04d-%02d-%02d', $m[3], $m[2], $m[1]); // Y-m-d
    }

    public function updatedDateFromUi($val): void
    {
        $this->date_from = $this->sanitizeDdMmYyyy($val);
        if ($val !== null && $this->date_from === null) {
            throw ValidationException::withMessages(['date_from_ui' => 'Invalid date format. Use dd/mm/yyyy']);
        }
    }

    public function updatedDateToUi($val): void
    {
        $this->date_to = $this->sanitizeDdMmYyyy($val);
        if ($val !== null && $this->date_to === null) {
            throw ValidationException::withMessages(['date_to_ui' => 'Invalid date format. Use dd/mm/yyyy']);
        }
    }

    protected function computeStatus(?string $date, ?string $start, ?string $end): string
    {
        if (!$date || !$start || !$end) return 'planned';
        try {
            $now = Carbon::now($this->tz);
            $startAt = Carbon::parse("$date $start", $this->tz);
            $endAt   = Carbon::parse("$date $end", $this->tz);
            if ($now->lt($startAt)) return 'planned';
            if ($now->betweenIncluded($startAt, $endAt)) return 'ongoing';
            return 'done';
        } catch (\Throwable) {
            return 'planned';
        }
    }

    protected function rows()
    {
        $cid = Auth::user()->company_id ?? null;
        $now = Carbon::now($this->tz)->format('Y-m-d H:i:s');

        $roomCols = ['room_id'];
        if (Schema::hasColumn('rooms', 'room_number')) $roomCols[] = 'room_number';
        if (Schema::hasColumn('rooms', 'room_name'))   $roomCols[] = 'room_name';

        $deptCols = ['department_id'];
        if (Schema::hasColumn('departments', 'department_name')) $deptCols[] = 'department_name';

        $userCols = ['user_id', 'email'];
        if (Schema::hasColumn('users', 'full_name')) $userCols[] = 'full_name';

        $base = BookingRoom::query()
            ->when($cid, fn($q) => $q->where('company_id', $cid))
            ->when($this->department_id, fn($q) => $q->where('department_id', $this->department_id))
            ->when($this->room_id, fn($q) => $q->where('room_id', $this->room_id))
            ->when($this->date_from, fn($q) => $q->where('date', '>=', $this->date_from))
            ->when($this->date_to, fn($q) => $q->where('date', '<=', $this->date_to))
            ->when($this->q !== '', function ($q) {
                $like = "%{$this->q}%";
                $q->where(function ($qq) use ($like) {
                    $qq->where('meeting_title', 'like', $like)
                       ->orWhere('special_notes', 'like', $like);
                });
            })
            ->when($this->status === 'planned', fn($q) => $q->whereRaw("TIMESTAMP(`date`,`start_time`) > ?", [$now]))
            ->when($this->status === 'ongoing', fn($q) => $q->whereRaw("? BETWEEN TIMESTAMP(`date`,`start_time`) AND TIMESTAMP(`date`,`end_time`)", [$now]))
            ->when($this->status === 'done',    fn($q) => $q->whereRaw("TIMESTAMP(`date`,`end_time`) < ?", [$now]))
            ->with([
                'room:'.implode(',', $roomCols),
                'department:'.implode(',', $deptCols),
                'user:'.implode(',', $userCols),
            ])
            ->orderBy('date')
            ->orderBy('start_time');

        $paginator = $base->paginate($this->perPage);

        $items = $paginator->getCollection()->map(function ($r) {
            $date  = $r->date ? Carbon::parse($r->date)->format('Y-m-d') : null;
            $start = $r->start_time ? Carbon::parse($r->start_time)->format('H:i') : null;
            $end   = $r->end_time ? Carbon::parse($r->end_time)->format('H:i') : null;

            $room = $r->room;
            $roomLabel = $room?->room_number
                ?? ($room?->room_name ?? ('Room #'.$r->room_id));

            $status = $this->computeStatus($date, $start, $end);

            return [
                'id'           => (int)$r->bookingroom_id,
                'meeting_title'=> (string)($r->meeting_title ?? ''),
                'room'         => (string)$roomLabel,
                'department'   => (string)($r->department->department_name ?? '—'),
                'requested_by' => (string)($r->user->full_name ?? $r->user->email ?? '—'),
                'date'         => $date,
                'start'        => $start,
                'end'          => $end,
                'attendees'    => (int)($r->number_of_attendees ?? 0),
                'notes'        => (string)($r->special_notes ?? ''),
                'status'       => $status,
            ];
        });

        $paginator->setCollection($items);
        return $paginator;
    }

    public function setQuickRange(string $range): void
    {
        $now = Carbon::now($this->tz);

        if ($range === 'today') {
            $this->date_from = $now->toDateString();
            $this->date_to   = $now->toDateString();
            $this->date_from_ui = $now->format('d/m/Y');
            $this->date_to_ui   = $now->format('d/m/Y');
            session()->flash('info', 'Filter applied: Today');
        } elseif ($range === 'week') {
            $this->date_from = $now->copy()->startOfWeek()->format('Y-m-d');
            $this->date_to   = $now->copy()->endOfWeek()->format('Y-m-d');
            $this->date_from_ui = Carbon::parse($this->date_from)->format('d/m/Y');
            $this->date_to_ui   = Carbon::parse($this->date_to)->format('d/m/Y');
            session()->flash('info', 'Filter applied: This week');
        } else {
            $this->date_from = null;
            $this->date_to   = null;
            $this->date_from_ui = null;
            $this->date_to_ui   = null;
            session()->flash('info', 'Filter cleared: All dates');
        }

        $this->resetPage();
    }

    public function openDetail(int $id): void
    {
        $cid = Auth::user()->company_id ?? null;

        $roomCols = ['room_id'];
        if (Schema::hasColumn('rooms', 'room_number')) $roomCols[] = 'room_number';
        if (Schema::hasColumn('rooms', 'room_name'))   $roomCols[] = 'room_name';

        $deptCols = ['department_id'];
        if (Schema::hasColumn('departments', 'department_name')) $deptCols[] = 'department_name';

        $userCols = ['user_id', 'email'];
        if (Schema::hasColumn('users', 'full_name')) $userCols[] = 'full_name';

        $row = BookingRoom::query()
            ->when($cid, fn($q) => $q->where('company_id', $cid))
            ->where('bookingroom_id', $id)
            ->with([
                'room:'.implode(',', $roomCols),
                'department:'.implode(',', $deptCols),
                'user:'.implode(',', $userCols),
                'requirements:id,name',
            ])
            ->first();

        if (!$row) {
            session()->flash('error', 'Data tidak ditemukan.');
            return;
        }

        $date  = $row->date ? Carbon::parse($row->date)->format('Y-m-d') : null;
        $start = $row->start_time ? Carbon::parse($row->start_time)->format('H:i') : null;
        $end   = $row->end_time ? Carbon::parse($row->end_time)->format('H:i') : null;

        $roomLabel = $row->room?->room_number
            ?? ($row->room?->room_name ?? ('Room #'.$row->room_id));

        $roomMeta = [];
        if (!empty($row->room?->room_number)) $roomMeta[] = 'Number: '.$row->room->room_number;
        if (!empty($row->room?->room_name))   $roomMeta[] = 'Name: '.$row->room->room_name;

        $status = $this->computeStatus($date, $start, $end);
        $reqs = $row->requirements?->pluck('name')->filter()->values()->all() ?? [];

        $detail = [
            'id'            => (int)$row->bookingroom_id,
            'meeting_title' => (string)($row->meeting_title ?? ''),
            'status'        => $status,
            'room'          => (string)$roomLabel,
            'room_meta'     => count($roomMeta) ? implode(' • ', $roomMeta) : null,
            'department'    => (string)($row->department->department_name ?? '—'),
            'requested_by'  => (string)($row->user->full_name ?? $row->user->email ?? '—'),
            'date'          => $date ? Carbon::parse($date)->format('d M Y') : '—',
            'start'         => $start ?? '—',
            'end'           => $end ?? '—',
            'attendees'     => (int)($row->number_of_attendees ?? 0),
            'notes'         => (string)($row->special_notes ?? ''),
            'requirements'  => $reqs,
        ];

        $summary = [];
        $summary[] = 'Meeting: ' . ($detail['meeting_title'] ?: '-');
        $summary[] = 'Room: ' . ($detail['room'] ?: '-') . (!empty($detail['room_meta']) ? ' ('.$detail['room_meta'].')' : '');
        $summary[] = 'Date: ' . ($detail['date'] ?: '-') . ' ' . ($detail['start'] ?: '-') . '–' . ($detail['end'] ?: '-');
        $summary[] = 'Dept: ' . ($detail['department'] ?: '-');
        $summary[] = 'Requested By: ' . ($detail['requested_by'] ?: '-');
        $summary[] = 'Attendees: ' . ($detail['attendees'] ?: '-');
        $summary[] = 'Requirements: ' . (count($reqs) ? implode(', ', $reqs) : '-');
        $summary[] = 'Status: ' . (isset($detail['status']) ? strtoupper($detail['status']) : '-');
        $summary[] = 'Notes: ' . ($detail['notes'] ?: '-');

        $detail['summary_lines'] = $summary;

        $this->detail = $detail;
        $this->showDetail = true;
    }

    public function closeDetail(): void
    {
        $this->showDetail = false;
        $this->detail = [];
    }

    public function render()
    {
        $lockedDeptName = collect($this->departments)->firstWhere('id', $this->department_id)['name'] ?? '—';

        // keep your existing blade path
        return view('livewire.pages.admin.roommonitoring', [
            'rows'           => $this->rows(),
            'rooms'          => $this->rooms,
            'departments'    => $this->departments,
            'lockedDeptName' => $lockedDeptName,
        ]);
    }
}
