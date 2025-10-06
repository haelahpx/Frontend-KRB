<?php

namespace App\Livewire\Pages\Superadmin;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Carbon\Carbon;

// Aliases Model
use App\Models\BookingRoom as BR;
use App\Models\Room as RM;
use App\Models\Department as DP;
use App\Models\Requirement;

#[Layout('layouts.superadmin')]
#[Title('Booking Room')]
class Bookingroom extends Component
{
    use WithPagination;

    // UI state
    public string $search = '';
    public string $departmentFilter = '';
    public int    $perPage = 10;
    public string $sortBy  = 'start_time';
    public string $sortDir = 'desc';

    protected $queryString = [
        'search'           => ['except' => ''],
        'departmentFilter' => ['except' => ''],
        'perPage'          => ['except' => 10],
        'sortBy'           => ['except' => 'start_time'],
        'sortDir'          => ['except' => 'desc'],
    ];

    // Modal & edit state (EDIT ONLY)
    public bool  $modal = false;
    public ?int  $editingId = null;

    // Form fields
    public $room_id;
    public $department_id;
    public $meeting_title;
    public $date;
    public $number_of_attendees;
    public $start_time;
    public $end_time;
    public $special_notes;

    // Requirement checklist in modal
    public array $selectedRequirements = [];
    public $allRequirements = []; // [{requirement_id, name}]

    // Lookups for list cards
    public array $roomLookup = [];
    public array $deptLookup = [];

    private array $sortable = ['meeting_title', 'date', 'start_time', 'end_time', 'number_of_attendees'];

    protected function rules(): array
    {
        return [
            'room_id'              => ['required', 'integer', Rule::exists('rooms', 'room_id')],
            'department_id'        => ['required', 'integer', Rule::exists('departments', 'department_id')],
            'meeting_title'        => ['required', 'string', 'max:255'],
            'date'                 => ['required', 'date'],
            'number_of_attendees'  => ['required', 'integer', 'min:1'],
            'start_time'           => ['required', 'date'],
            'end_time'             => ['required', 'date', 'after:start_time'],
            'special_notes'        => ['nullable', 'string'],
            'selectedRequirements' => ['array'],
        ];
    }

    public function mount(): void
    {
        $companyId = Auth::user()->company_id;

        // Display column for Room
        $roomNameCol = Schema::hasColumn('rooms', 'name') ? 'name'
            : (Schema::hasColumn('rooms', 'room_name') ? 'room_name'
                : (Schema::hasColumn('rooms', 'room_number') ? 'room_number' : null));
        if (!$roomNameCol) {
            throw new \RuntimeException('Rooms table needs a display column (name / room_name / room_number).');
        }

        $rooms = RM::where('company_id', $companyId)
            ->orderBy($roomNameCol)
            ->get(['room_id', "$roomNameCol as name"]);
        $this->roomLookup = $rooms->pluck('name', 'room_id')->toArray();

        // Display column for Department
        $deptNameCol = Schema::hasColumn('departments', 'name') ? 'name'
            : (Schema::hasColumn('departments', 'department_name') ? 'department_name' : null);
        if (!$deptNameCol) {
            throw new \RuntimeException('Departments table needs a display column (name / department_name).');
        }

        $departments = DP::where('company_id', $companyId)
            ->orderBy($deptNameCol)
            ->get(['department_id', "$deptNameCol as name"]);
        $this->deptLookup = $departments->pluck('name', 'department_id')->toArray();

        // Load requirement master (global)
        $this->allRequirements = Requirement::orderBy('name')->get(['requirement_id', 'name']);

        if (!in_array($this->sortBy, $this->sortable, true)) $this->sortBy = 'start_time';
        $this->sortDir = $this->sortDir === 'asc' ? 'asc' : 'desc';
    }

    // Pagination refreshers
    public function updatingSearch()
    {
        $this->resetPage();
    }
    public function updatingDepartmentFilter()
    {
        $this->resetPage();
    }
    public function updatingPerPage()
    {
        $this->resetPage();
    }

    public function sort(string $field): void
    {
        if (!in_array($field, $this->sortable, true)) return;
        if ($this->sortBy === $field) {
            $this->sortDir = $this->sortDir === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortBy = $field;
            $this->sortDir = 'asc';
        }
        $this->resetPage();
    }

    /* ---------- Edit-only Modal ---------- */
    public function openEdit(int $id): void
    {
        $companyId = Auth::user()->company_id;
        $data = BR::where('company_id', $companyId)->findOrFail($id);

        $this->editingId = $data->bookingroom_id;

        $this->room_id             = $data->room_id;
        $this->department_id       = $data->department_id;
        $this->meeting_title       = $data->meeting_title;
        $this->date                = $data->date;
        $this->number_of_attendees = $data->number_of_attendees;
        $this->start_time          = Carbon::parse($data->start_time)->format('Y-m-d\TH:i');
        $this->end_time            = Carbon::parse($data->end_time)->format('Y-m-d\TH:i');
        $this->special_notes       = $data->special_notes;

        // Load selected requirements (pivot)
        $this->selectedRequirements = DB::table('booking_requirements')
            ->where('bookingroom_id', $id)
            ->pluck('requirement_id')
            ->toArray();

        $this->modal = true;
        $this->resetErrorBag();
    }

    public function closeModal(): void
    {
        $this->modal = false;
        $this->resetErrorBag();
    }

    /* ---------- Update & Delete only ---------- */
    public function update(): void
    {
        $this->validate();

        $companyId = Auth::user()->company_id;
        $row = BR::where('company_id', $companyId)->findOrFail($this->editingId);

        // Update booking data
        $row->update([
            'room_id'             => $this->room_id,
            'department_id'       => $this->department_id,
            'meeting_title'       => $this->meeting_title,
            'date'                => $this->date,
            'number_of_attendees' => $this->number_of_attendees,
            'start_time'          => Carbon::parse($this->start_time)->toDateTimeString(),
            'end_time'            => Carbon::parse($this->end_time)->toDateTimeString(),
            'special_notes'       => $this->special_notes,
        ]);

        // Sync requirements (pivot)
        DB::table('booking_requirements')
            ->where('bookingroom_id', $row->bookingroom_id)
            ->delete();

        $now = now();
        foreach ($this->selectedRequirements as $reqId) {
            DB::table('booking_requirements')->insert([
                'bookingroom_id' => $row->bookingroom_id,
                'requirement_id' => $reqId,
                'created_at'     => $now,
                'updated_at'     => $now,
            ]);
        }

        $this->closeModal();
        $this->resetForm();
        session()->flash('success', 'Booking updated (requirements included).');
    }

    public function delete(int $id): void
    {
        $companyId = Auth::user()->company_id;
        $row = BR::where('company_id', $companyId)->findOrFail($id);
        $row->delete();

        // clean pivot
        DB::table('booking_requirements')->where('bookingroom_id', $id)->delete();

        session()->flash('success', 'Booking deleted.');
        $this->resetPage();
    }

    private function resetForm(): void
    {
        $this->reset([
            'editingId',
            'room_id',
            'department_id',
            'meeting_title',
            'date',
            'number_of_attendees',
            'start_time',
            'end_time',
            'special_notes',
            'selectedRequirements',
        ]);
    }

    public function render()
    {
        $companyId = Auth::user()->company_id;

        // Base query
        $bookings = BR::query()
            ->select([
                'booking_rooms.*',
                DB::raw("COALESCE(u.full_name, '') as user_full_name"),
            ])
            ->leftJoin('users as u', 'u.user_id', '=', 'booking_rooms.user_id')
            ->where('booking_rooms.company_id', $companyId)
            ->when($this->departmentFilter, fn($q) => $q->where('booking_rooms.department_id', $this->departmentFilter))
            ->when($this->search !== '', function ($q) {
                $s = "%{$this->search}%";
                $q->where(function ($qq) use ($s) {
                    $qq->where('booking_rooms.meeting_title', 'like', $s)
                        ->orWhere('booking_rooms.special_notes', 'like', $s);
                });
            })
            ->orderBy($this->sortBy, $this->sortDir)
            ->paginate($this->perPage);

        // Build requirements map for the page (avoid queries in Blade)
        $ids = $bookings->pluck('bookingroom_id')->all();

        $requirementsMap = [];
        if (!empty($ids)) {
            $rows = DB::table('booking_requirements as br')
                ->join('requirements as r', 'r.requirement_id', '=', 'br.requirement_id')
                ->whereIn('br.bookingroom_id', $ids)
                ->orderBy('r.name')
                ->get(['br.bookingroom_id', 'r.name']);

            foreach ($rows as $row) {
                $requirementsMap[$row->bookingroom_id][] = $row->name;
            }
        }

        return view('livewire.pages.superadmin.bookingroom', [
            'bookings'         => $bookings,
            'roomLookup'       => $this->roomLookup,
            'deptLookup'       => $this->deptLookup,
            'requirementsMap'  => $requirementsMap, // bookingroom_id => [names...]
            'allRequirements'  => $this->allRequirements, // for modal checklist
        ]);
    }
}
