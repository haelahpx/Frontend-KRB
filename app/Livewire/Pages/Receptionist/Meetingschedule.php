<?php

namespace App\Livewire\Pages\Receptionist;

use App\Models\BookingRoom;
use App\Models\Department;
use App\Models\Requirement;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.receptionist')]
#[Title('Meeting Schedule')]
class MeetingSchedule extends Component
{
    public ?int $editingId = null;
    public bool $modalEdit = false;

    public $meeting_title;
    public $location;
    public $department_id;
    public $date;
    public $participant;
    public $time;
    public $time_end;
    public $notes;

    /** @var int[] requirement IDs */
    public array $requirements = [];

    /** @var array<int,array{id:int,name:string}> */
    public array $requirementOptions = [];

    public string $saveState = 'idle';
    public ?string $savedAt = null;

    public array $planned = [];
    public array $ongoing = [];
    public array $done = [];
    public array $departments = [];

    // to temporarily stop polling if a render error happened
    public bool $polling = true;

    /** cache: id “Other” kalau ada */
    private ?int $otherRequirementId = null;

    public function mount(): void
    {
        // Seed minimal kalau kosong (opsional aman)
        if (Requirement::count() === 0) {
            Requirement::upsertByName(['Video Conference','Projector','Whiteboard','Catering','Other']);
        }

        // Load Departments (scoped by company)
        $deptQuery = Department::query();
        if ($cid = optional(Auth::user())->company_id) {
            $deptQuery->where('company_id', $cid);
        }
        $this->departments = $deptQuery
            ->orderBy('department_name')
            ->get(['department_id', 'department_name'])
            ->map(fn ($d) => [
                'department_id' => $d->department_id,
                'name' => $d->department_name,
            ])->all();

        // Load Requirement options
        $this->requirementOptions = Requirement::orderBy('name')
            ->get(['requirement_id','name'])
            ->map(fn($r) => ['id' => (int)$r->requirement_id, 'name' => (string)$r->name])
            ->all();

        $this->otherRequirementId = $this->findOtherRequirementId();

        $this->reloadBuckets();
    }

    protected function rules(): array
    {
        return [
            'meeting_title' => ['required', 'string', 'max:255'],
            'location' => ['required', Rule::in(['Ruangan 1', 'Ruangan 2', 'Ruangan 3'])],
            'department_id' => ['required', 'integer', 'exists:departments,department_id'],
            'date' => ['required', 'date_format:Y-m-d', 'after_or_equal:1000-01-01', 'before_or_equal:9999-12-31'],
            'participant' => ['required', 'integer', 'min:1'],
            'time' => ['required', 'date_format:H:i'],
            'time_end' => ['required', 'date_format:H:i', 'after:time'],
            'notes' => ['nullable', 'string', 'max:1000'],
            'requirements' => ['array'],
            'requirements.*' => ['integer', 'exists:requirements,requirement_id'],
        ];
    }

    protected function messages(): array
    {
        return [
            'date.before_or_equal' => 'Tanggal terlalu jauh di masa depan. Maksimal 9999-12-31.',
            'date.after_or_equal'  => 'Tanggal terlalu jauh di masa lalu. Minimal 1000-01-01.',
            'department_id.required' => 'Departemen wajib dipilih.',
            'department_id.exists'   => 'Departemen tidak valid.',
        ];
    }

    protected function validateNotesIfOther(): void
    {
        if ($this->otherRequirementId && in_array($this->otherRequirementId, $this->requirements, true)) {
            $this->validate(['notes' => 'required|string|min:2|max:1000']);
        }
    }

    private function resetForm(): void
    {
        $this->reset([
            'editingId',
            'modalEdit',
            'meeting_title',
            'location',
            'department_id',
            'date',
            'participant',
            'time',
            'time_end',
            'notes',
            'requirements',
        ]);
        $this->requirements = [];
    }

    public function save(): void
    {
        $this->validate();
        $this->validateNotesIfOther();

        $this->date = substr((string) $this->date, 0, 10);

        $rid = $this->mapRoomId($this->location);
        if ($rid === null) throw new \RuntimeException('Invalid room selection.');

        BookingRoom::create([
            'room_id'             => $rid,
            'company_id'          => optional(Auth::user())->company_id,
            'user_id'             => optional(Auth::user())->user_id ?? optional(Auth::user())->id,
            'department_id'       => (int) $this->department_id,
            'meeting_title'       => $this->meeting_title,
            'date'                => $this->date,
            'number_of_attendees' => (int) $this->participant,
            'start_time'          => $this->combineDateTime($this->date, $this->time),     // if TIME col: $this->time.':00'
            'end_time'            => $this->combineDateTime($this->date, $this->time_end), // if TIME col: $this->time_end.':00'
            'special_notes'       => $this->composeSpecialNotes($this->requirements, (string) $this->notes),
        ]);

        $this->resetForm();
        $this->resetValidation();
        $this->reloadBuckets();

        $this->dispatch('toast', type: 'success', title: 'Tersimpan', message: 'Meeting berhasil ditambahkan.', duration: 2500);
    }

    public function openEdit(int $id): void
    {
        $row = BookingRoom::whereKey($id)->first();
        if (!$row) return;

        $meta = $this->parseSpecialNotes((string) ($row->special_notes ?? ''));

        $this->editingId     = $row->getKey();
        $this->meeting_title = $row->meeting_title;
        $this->location      = $this->mapRoomName($row->room_id);
        $this->department_id = $row->department_id;
        $this->date          = $this->safeDateYmd($row->date);
        $this->participant   = (int) $row->number_of_attendees;
        $this->time          = $this->safeTimeHi($row->start_time);
        $this->time_end      = $this->safeTimeHi($row->end_time);
        $this->notes         = $meta['notes'] ?? null;
        /** @var int[] */
        $this->requirements  = $meta['requirement_ids'] ?? [];

        $this->modalEdit = true;
    }

    public function closeEdit(): void
    {
        $this->modalEdit = false;
        $this->editingId = null;
    }

    public function update(): void
    {
        if (!$this->editingId) return;

        $this->validate();
        $this->validateNotesIfOther();

        $this->date = substr((string) $this->date, 0, 10);

        $row = BookingRoom::whereKey($this->editingId)->first();
        if (!$row) return;

        $rid = $this->mapRoomId($this->location);
        if ($rid === null) throw new \RuntimeException('Invalid room selection.');

        $row->update([
            'room_id'             => $rid,
            'meeting_title'       => $this->meeting_title,
            'department_id'       => (int) $this->department_id,
            'date'                => $this->date,
            'number_of_attendees' => (int) $this->participant,
            'start_time'          => $this->combineDateTime($this->date, $this->time),
            'end_time'            => $this->combineDateTime($this->date, $this->time_end),
            'special_notes'       => $this->composeSpecialNotes($this->requirements, (string) $this->notes),
        ]);

        $this->modalEdit = false;
        $this->resetForm();
        $this->resetValidation();
        $this->reloadBuckets();

        $this->dispatch('toast', type: 'success', title: 'Tersimpan', message: 'Perubahan disimpan.', duration: 3000);
    }

    public function destroy(int $id): void
    {
        BookingRoom::whereKey($id)->delete();
        $this->reloadBuckets();

        $this->dispatch('toast', type: 'success', title: 'Dihapus', message: 'Meeting dihapus.', duration: 3000);
    }

    private function reloadBuckets(): void
    {
        $q = BookingRoom::query()
            ->when(optional(Auth::user())->company_id, fn ($qq, $cid) => $qq->where('company_id', $cid))
            ->orderBy('date')
            ->orderBy('start_time');

        $all = $q->get();

        $this->planned = $this->ongoing = $this->done = [];

        foreach ($all as $r) {
            $meta  = $this->parseSpecialNotes((string) ($r->special_notes ?? ''));
            $date  = $this->safeDateYmd($r->date);
            $start = $this->safeTimeHi($r->start_time);
            $end   = $this->safeTimeHi($r->end_time);

            $ui = [
                'id'            => $r->getKey(),
                'meeting_title' => $r->meeting_title,
                'location'      => $this->mapRoomName($r->room_id),
                'date'          => $date,
                'time'          => $start,
                'time_end'      => $end,
                'participant'   => (int) $r->number_of_attendees,
                'department_id' => (int) $r->department_id,
                // pilihan requirement (ID) bila kamu mau render chip di list
                'requirements'  => $meta['requirement_ids'] ?? [],
                'notes'         => $meta['notes'] ?? null,
            ];

            $status = $this->computeStatus($date, $start, $end);

            if ($status === 'planned')      $this->planned[] = $ui;
            elseif ($status === 'ongoing')  $this->ongoing[] = $ui;
            else                            $this->done[]    = $ui;
        }
    }

    private function combineDateTime(string $date, string $time): string
    {
        // If DATETIME: simpan "Y-m-d H:i:00"; jika kolom TIME, simpan "$time:00".
        return "{$date} {$time}:00";
    }

    private function mapRoomId(?string $label): ?int
    {
        return match ($label) {
            'Ruangan 1' => 1,
            'Ruangan 2' => 2,
            'Ruangan 3' => 3,
            default => null,
        };
    }

    private function mapRoomName(?int $id): ?string
    {
        return match ($id) {
            1 => 'Ruangan 1',
            2 => 'Ruangan 2',
            3 => 'Ruangan 3',
            default => null,
        };
    }

    /**
     * Parse special_notes:
     * - New format: [REQID=1,2,3]
     * - Legacy format (supported): [REQ=video,projector]
     */
    private function parseSpecialNotes(string $raw): array
    {
        $notes = $raw;
        $ids = [];

        // strip status tag jika ada
        if (preg_match('/\[STATUS=(planned|ongoing|done)\]/i', $notes, $m)) {
            $notes = str_replace($m[0], '', $notes);
        }

        // New: [REQID=1,2,3]
        if (preg_match('/\[REQID=([\d,\s]+)\]/i', $notes, $m)) {
            $ids = array_values(array_filter(array_map('intval', explode(',', $m[1]))));
            $notes = str_replace($m[0], '', $notes);
        }
        // Legacy: [REQ=name,name]
        elseif (preg_match('/\[REQ=([a-z0-9 _\-,]+)\]/i', $notes, $m)) {
            $names = array_values(array_filter(array_map(fn($x)=>trim(strtolower($x)), explode(',', $m[1]))));
            if ($names) {
                $ids = Requirement::whereIn(\DB::raw('LOWER(name)'), $names)->pluck('requirement_id')->map(fn($v)=>(int)$v)->all();
            }
            $notes = str_replace($m[0], '', $notes);
        }

        $notes = ltrim($notes);

        return [
            'requirement_ids' => $ids,
            'notes'           => $notes,
        ];
    }

    private function composeSpecialNotes(array $requirementIds, string $notes): string
    {
        // Simpan hanya sebagai ID agar stabil
        $cleanIds = array_values(array_unique(array_map('intval', array_filter($requirementIds, fn($v)=>$v !== null))));
        $tag = $cleanIds ? '[REQID='.implode(',', $cleanIds).']' : '';

        return trim($tag . "\n" . trim($notes));
    }

    private function safeDateYmd($value): ?string
    {
        if (!$value) return null;
        try { return \Carbon\Carbon::parse($value)->format('Y-m-d'); }
        catch (\Throwable $e) { return is_string($value) ? substr($value, 0, 10) : null; }
    }

    private function safeTimeHi($value): ?string
    {
        if (!$value) return null;
        try { return \Carbon\Carbon::parse($value)->format('H:i'); }
        catch (\Throwable $e) {
            if (is_string($value) && preg_match('/^\d{2}:\d{2}/', $value)) return substr($value, 0, 5);
            return null;
        }
    }

    private function computeStatus(?string $date, ?string $start, ?string $end): string
    {
        if (!$date || !$start || !$end) return 'planned';

        $tz = config('app.timezone', 'UTC');

        try {
            $startAt = \Carbon\Carbon::parse("{$date} {$start}", $tz);
            $endAt   = \Carbon\Carbon::parse("{$date} {$end}", $tz);
        } catch (\Throwable $e) {
            return 'planned';
        }

        $now = now($tz);

        if ($now->lt($startAt)) return 'planned';
        if ($now->between($startAt, $endAt, true)) return 'ongoing';
        return 'done';
    }

    public function tick(): void
    {
        if (!$this->polling) return;
        try {
            $this->reloadBuckets();
        } catch (\Throwable $e) {
            \Log::error('[MeetingSchedule tick] '.$e->getMessage(), ['trace' => $e->getTraceAsString()]);
            $this->polling = false;
            $this->dispatch('toast', type: 'error', title: 'Auto-refresh paused',
                message: 'Terjadi error saat refresh. Cek log untuk detail.', duration: 4000);
        }
    }

    public function render()
    {
        return view('livewire.pages.receptionist.meetingschedule', [
            'planned'            => $this->planned,
            'ongoing'            => $this->ongoing,
            'done'               => $this->done,
            'departments'        => $this->departments,
            'requirementOptions' => $this->requirementOptions,
            'otherId'            => $this->otherRequirementId,
        ]);
    }

    private function findOtherRequirementId(): ?int
    {
        $other = Requirement::whereRaw('LOWER(name) = ?', ['other'])->value('requirement_id');
        return $other ? (int)$other : null;
    }
}
