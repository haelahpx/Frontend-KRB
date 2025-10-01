<?php

namespace App\Livewire\Pages\Receptionist;

use App\Models\BookingRoom;
use App\Models\Department;
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

    public array $requirements = [];
    public string $saveState = 'idle';
    public ?string $savedAt = null;

    public array $planned = [];
    public array $ongoing = [];
    public array $done = [];

    public array $departments = [];
    public function mount(): void
    {
        $deptQuery = Department::query();

        if ($cid = optional(Auth::user())->company_id) {
            $deptQuery->where('company_id', $cid);
        }

        $this->departments = $deptQuery
            ->orderBy('department_name')
            ->get(['department_id', 'department_name'])
            ->map(fn($d) => [
                'department_id' => $d->department_id,
                'name' => $d->department_name,
            ])
            ->all();

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
            'requirements.*' => [Rule::in(['video', 'projector', 'whiteboard', 'catering', 'other'])],
        ];
    }

    protected function messages(): array
    {
        return [
            'date.before_or_equal' => 'Tanggal terlalu jauh di masa depan. Maksimal 9999-12-31.',
            'date.after_or_equal' => 'Tanggal terlalu jauh di masa lalu. Minimal 1000-01-01.',
            'department_id.required' => 'Departemen wajib dipilih.',
            'department_id.exists' => 'Departemen tidak valid.',
        ];
    }

    protected function validateNotesIfOther(): void
    {
        if (in_array('other', $this->requirements ?? [], true)) {
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


    public function save(): \Symfony\Component\HttpFoundation\Response
    {
        $this->validate();
        $this->validateNotesIfOther();

        $this->date = substr((string) $this->date, 0, 10);

        BookingRoom::create([
            'room_id' => $this->mapRoomId($this->location),
            'company_id' => optional(Auth::user())->company_id,
            'user_id' => optional(Auth::user())->user_id ?? optional(Auth::user())->id,
            'department_id' => (int) $this->department_id,
            'meeting_title' => $this->meeting_title,
            'date' => $this->date,
            'number_of_attendees' => (int) $this->participant,
            'start_time' => $this->combineDateTime($this->date, $this->time),
            'end_time' => $this->combineDateTime($this->date, $this->time_end),
            'special_notes' => $this->composeSpecialNotes($this->requirements, (string) $this->notes),
        ]);

        $this->resetForm();
        $this->resetValidation();

        return redirect()->to(url()->current());
    }

    public function openEdit(int $id): void
    {
        $row = BookingRoom::whereKey($id)->first();
        if (!$row)
            return;

        $meta = $this->parseSpecialNotes((string) $row->special_notes);

        $this->editingId = $row->getKey();
        $this->meeting_title = $row->meeting_title;
        $this->location = $this->mapRoomName($row->room_id);
        $this->department_id = $row->department_id;
        $this->date = optional($row->date)->format('Y-m-d');
        $this->participant = $row->number_of_attendees;
        $this->time = optional($row->start_time)->format('H:i');
        $this->time_end = optional($row->end_time)->format('H:i');
        $this->notes = $meta['notes'] ?? null;
        $this->requirements = $meta['requirements'] ?? [];

        $this->modalEdit = true;
    }

    public function closeEdit(): void
    {
        $this->modalEdit = false;
        $this->editingId = null;
    }

    public function update(): void
    {
        if (!$this->editingId)
            return;

        $this->validate();
        $this->validateNotesIfOther();

        $this->date = substr((string) $this->date, 0, 10);

        $row = BookingRoom::whereKey($this->editingId)->first();
        if (!$row)
            return;

        $row->update([
            'room_id' => $this->mapRoomId($this->location),
            'meeting_title' => $this->meeting_title,
            'department_id' => (int) $this->department_id,
            'date' => $this->date,
            'number_of_attendees' => (int) $this->participant,
            'start_time' => $this->combineDateTime($this->date, $this->time),
            'end_time' => $this->combineDateTime($this->date, $this->time_end),
            'special_notes' => $this->composeSpecialNotes($this->requirements, (string) $this->notes),
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
            ->when(optional(Auth::user())->company_id, fn($qq, $cid) => $qq->where('company_id', $cid))
            ->orderBy('date')
            ->orderBy('start_time');

        $all = $q->get();

        $this->planned = [];
        $this->ongoing = [];
        $this->done = [];

        foreach ($all as $r) {
            $meta = $this->parseSpecialNotes((string) $r->special_notes);
            $status = $this->computeStatus(
                $r->date?->format('Y-m-d'),
                $r->start_time?->format('H:i'),
                $r->end_time?->format('H:i')
            );

            $ui = [
                'id' => $r->getKey(),
                'meeting_title' => $r->meeting_title,
                'location' => $this->mapRoomName($r->room_id),
                'date' => optional($r->date)->format('Y-m-d'),
                'time' => optional($r->start_time)->format('H:i'),
                'time_end' => optional($r->end_time)->format('H:i'),
                'participant' => $r->number_of_attendees,
                'department_id' => $r->department_id,
                'requirements' => $meta['requirements'] ?? [],
                'notes' => $meta['notes'] ?? null,
            ];

            if ($status === 'planned') {
                $this->planned[] = $ui;
            } elseif ($status === 'ongoing') {
                $this->ongoing[] = $ui;
            } else {
                $this->done[] = $ui;
            }
        }
    }


    private function combineDateTime(string $date, string $time): string
    {
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

    private function parseSpecialNotes(string $raw): array
    {
        $requirements = [];
        $notes = $raw;

        if (preg_match('/\[STATUS=(planned|ongoing|done)\]/i', $notes, $m)) {
            $notes = str_replace($m[0], '', $notes);
        }

        if (preg_match('/\[REQ=([a-z,]+)\]/i', $notes, $m)) {
            $requirements = array_values(array_filter(array_map('trim', explode(',', strtolower($m[1])))));
            $notes = str_replace($m[0], '', $notes);
        }

        $notes = ltrim($notes);
        return compact('requirements', 'notes');
    }

    private function composeSpecialNotes(array $requirements, string $notes): string
    {
        $tags = [];

        $reqs = array_values(array_unique(array_filter(
            $requirements,
            fn($r) => in_array($r, ['video', 'projector', 'whiteboard', 'catering', 'other'], true)
        )));
        if (!empty($reqs)) {
            $tags[] = "[REQ=" . implode(',', $reqs) . "]";
        }

        $prefix = implode(' ', $tags);
        return trim($prefix . "\n" . trim($notes));
    }

    private function computeStatus(?string $date, ?string $start, ?string $end): string
    {
        if (!$date || !$start || !$end)
            return 'planned';

        $tz = config('app.timezone', 'UTC');
        $now = now($tz);
        $startAt = \Carbon\Carbon::createFromFormat('Y-m-d H:i', "{$date} {$start}", $tz);
        $endAt = \Carbon\Carbon::createFromFormat('Y-m-d H:i', "{$date} {$end}", $tz);

        if ($now->lt($startAt))
            return 'planned';
        if ($now->betweenIncluded($startAt, $endAt))
            return 'ongoing';
        return 'done';
    }

    public function tick(): void
    {
        $this->reloadBuckets();

        if ($this->saveState === 'saved' && $this->savedAt) {
            if (now()->diffInMilliseconds(\Carbon\Carbon::parse($this->savedAt)) >= 1500) {
                $this->saveState = 'idle';
            }
        }
    }

    public function render()
    {
        return view('livewire.pages.receptionist.meetingschedule', [
            'planned' => $this->planned,
            'ongoing' => $this->ongoing,
            'done' => $this->done,
            'departments' => $this->departments,
        ]);
    }
}
