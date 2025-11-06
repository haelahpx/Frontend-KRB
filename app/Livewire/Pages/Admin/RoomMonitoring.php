<?php

namespace App\Livewire\Pages\Admin;

use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\BookingRoom;
use App\Models\Department;

#[Layout('layouts.admin')]
#[Title('History Room Booking')]
class RoomMonitoring extends Component
{
    // list limits
    public int $limitOffline = 10;
    public int $limitOnline  = 10;

    public ?string $search = null;

    // header + switcher
    public string $company_name = '-';
    public string $department_name = '-';
    public array  $deptOptions = [];          // [['id'=>..,'name'=>..], ...]
    public ?int   $selected_department_id = null;
    public ?int   $primary_department_id  = null;

    public bool $showSwitcher = false;    

    public function mount(): void
    {
        $user = Auth::user()->loadMissing(['company', 'department']);
        $this->company_name = optional($user->company)->company_name ?? '-';
        $this->primary_department_id = $user->department_id ?: null;

        $this->loadUserDepartments();

        // default selection
        if (!$this->selected_department_id) {
            $this->selected_department_id = $this->primary_department_id
                ?: ($this->deptOptions[0]['id'] ?? null);
        }
        $this->department_name = $this->resolveDeptName($this->selected_department_id);
    }

    protected function loadUserDepartments(): void
    {
        $user = Auth::user();

        $rows = DB::table('user_departments as ud')
            ->join('departments as d', 'd.department_id', '=', 'ud.department_id')
            ->where('ud.user_id', $user->user_id)
            ->orderBy('d.department_name')
            ->get(['d.department_id as id', 'd.department_name as name']);

        $this->deptOptions = $rows->map(fn($r) => ['id' => (int)$r->id, 'name' => (string)$r->name])->values()->all();

        $this->showSwitcher = true; 

        if (empty($this->deptOptions) && $this->primary_department_id) {
            $name = Department::where('department_id', $this->primary_department_id)->value('department_name') ?? 'Unknown';
            $this->deptOptions = [['id' => (int)$this->primary_department_id, 'name' => (string)$name]];

            $this->showSwitcher = false; 
        }
    }

    protected function resolveDeptName(?int $deptId): string
    {
        if (!$deptId) return '-';
        foreach ($this->deptOptions as $opt) {
            if ($opt['id'] === (int)$deptId) return $opt['name'];
        }
        return Department::where('department_id', $deptId)->value('department_name') ?? '-';
    }

    public function resetToPrimaryDepartment(): void
    {
        if ($this->primary_department_id) {
            $this->selected_department_id = $this->primary_department_id;
            $this->department_name = $this->resolveDeptName($this->selected_department_id);
        }
    }

    // Livewire may call either depending on version
    public function updatedSelectedDepartment_id(): void { $this->updatedSelectedDepartmentId(); }
    public function updatedSelectedDepartmentId(): void
    {
        $allowed = collect($this->deptOptions)->pluck('id')->all();
        $id = (int) $this->selected_department_id;

        if (!in_array($id, $allowed, true)) {
            $this->selected_department_id = $this->primary_department_id ?: ($this->deptOptions[0]['id'] ?? null);
            $id = (int) $this->selected_department_id;
        }
        $this->department_name = $this->resolveDeptName($id);
    }

    public function loadMore(string $side = 'offline'): void
    {
        if ($side === 'online') $this->limitOnline += 10;
        else $this->limitOffline += 10;
    }

    protected function baseHistoryQuery()
    {
        $companyId = Auth::user()?->company_id;
        $deptId    = $this->selected_department_id ?: $this->primary_department_id;

        return BookingRoom::query()
            ->with(['room'])
            ->where('company_id', $companyId)
            ->when($deptId, fn($q) => $q->where('department_id', $deptId)) // filter by selected dept
            ->where('end_time', '<', now()) // only past bookings
            ->when($this->search, function ($q, $s) {
                $q->where(function ($qq) use ($s) {
                    $qq->where('meeting_title', 'like', "%{$s}%")
                       ->orWhere('special_notes', 'like', "%{$s}%");
                });
            })
            ->orderByDesc('end_time');
    }

    public function render()
    {
        $base = $this->baseHistoryQuery();

        $offline = (clone $base)
            ->where('booking_type', 'meeting')
            ->limit($this->limitOffline)
            ->get();

        $online = (clone $base)
            ->where('booking_type', 'online_meeting')
            ->limit($this->limitOnline)
            ->get();

        return view('livewire.pages.admin.roommonitoring', [
            'offline' => $offline,
            'online'  => $online,
        ]);
    }
}
