<?php

namespace App\Livewire\Pages\Receptionist;

use App\Models\BookingRoom;
use App\Models\Department;
use App\Models\Requirement;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.receptionist')]
#[Title('Meeting Schedule')]
class MeetingSchedule extends Component
{
    /** Static room map biar simpel */
    private const ROOMS = [
        'Ruangan 1' => 1,
        'Ruangan 2' => 2,
        'Ruangan 3' => 3,
    ];

    /** UI state */
    public ?int $editingId = null;
    public bool $showModal = false;

    /** Form terpadu */
    public array $form = [
        'meeting_title' => null,
        'location'      => null, // 'Ruangan 1'|'Ruangan 2'|'Ruangan 3'
        'department_id' => null,
        'date'          => null, // Y-m-d
        'time'          => null, // H:i
        'time_end'      => null, // H:i
        'participant'   => null,
        'notes'         => null,
        'requirements'  => [],   // array<int>
    ];

    /** Dropdown data */
    public array $departments = [];
    public array $requirementOptions = [];

    /** Buckets */
    public array $planned = [];
    public array $ongoing = [];
    public array $done    = [];

    /** cache id "Other" (opsional) */
    private ?int $otherRequirementId = null;

    public function mount(): void
    {
        // Seed wajib minimum (aman jika sudah ada)
        if (Requirement::count() === 0) {
            Requirement::upsertByName(['Video Conference', 'Projector', 'Whiteboard', 'Catering', 'Other']);
        }

        // Dropdown departments (scope by company)
        $dept = Department::query()
            ->when(Auth::user()?->company_id, fn ($q, $cid) => $q->where('company_id', $cid))
            ->orderBy('department_name')
            ->get(['department_id', 'department_name']);

        $this->departments = $dept->map(fn ($d) => [
            'department_id' => (int) $d->department_id,
            'name'          => (string) $d->department_name,
        ])->all();

        // Dropdown requirements
        $this->requirementOptions = Requirement::orderBy('name')
            ->get(['requirement_id', 'name'])
            ->map(fn ($r) => ['id' => (int) $r->requirement_id, 'name' => (string) $r->name])
            ->all();

        $this->otherRequirementId = $this->findOtherRequirementId();

        $this->reloadBuckets();
    }

    /** Validasi ringkas */
    protected function rules(): array
    {
        return [
            'form.meeting_title' => ['required', 'string', 'max:255'],
            'form.location'      => ['required', Rule::in(array_keys(self::ROOMS))],
            'form.department_id' => ['required', 'integer', 'exists:departments,department_id'],
            'form.date'          => ['required', 'date_format:Y-m-d'],
            'form.time'          => ['required', 'date_format:H:i'],
            'form.time_end'      => ['required', 'date_format:H:i', 'after:form.time'],
            'form.participant'   => ['required', 'integer', 'min:1'],
            'form.notes'         => ['nullable', 'string', 'max:1000'],
            'form.requirements'  => ['array'],
            'form.requirements.*'=> ['integer', 'exists:requirements,requirement_id'],
        ];
    }

    protected function messages(): array
    {
        return [
            'form.department_id.required' => 'Departemen wajib dipilih.',
            'form.department_id.exists'   => 'Departemen tidak valid.',
        ];
    }

    /** Create/Update disatukan */
    public function save(): void
    {
        $this->validate();
        $this->validateNotesIfOther();

        $uid = Auth::user()?->user_id ?? Auth::id();
        $cid = Auth::user()?->company_id;

        $roomId = $this->roomIdFromLabel($this->form['location']);
        if (!$roomId) {
            $this->dispatch('toast', type: 'error', title: 'Gagal', message: 'Ruangan tidak valid.');
            return;
        }

        $payload = [
            'room_id'             => $roomId,
            'company_id'          => $cid,
            'user_id'             => $uid,
            'department_id'       => (int) $this->form['department_id'],
            'meeting_title'       => (string) $this->form['meeting_title'],
            'date'                => (string) $this->form['date'],
            'number_of_attendees' => (int) $this->form['participant'],
            'start_time'          => $this->joinDateTime($this->form['date'], $this->form['time']),
            'end_time'            => $this->joinDateTime($this->form['date'], $this->form['time_end']),
            'special_notes'       => $this->composeSpecialNotes($this->form['requirements'], (string) ($this->form['notes'] ?? '')),
        ];

        if ($this->editingId) {
            BookingRoom::whereKey($this->editingId)->update($payload);
            $msg = 'Perubahan disimpan.';
        } else {
            BookingRoom::create($payload);
            $msg = 'Meeting berhasil ditambahkan.';
        }

        $this->resetForm();
        $this->reloadBuckets();
        $this->dispatch('toast', type: 'success', title: 'Sukses', message: $msg, duration: 2500);
    }

    /** Buka modal edit */
    public function openEdit(int $id): void
    {
        $row = BookingRoom::find($id);
        if (!$row) return;

        $meta = $this->parseSpecialNotes((string) ($row->special_notes ?? ''));

        $this->editingId        = $row->getKey();
        $this->form['meeting_title'] = $row->meeting_title;
        $this->form['location']      = $this->roomLabelFromId($row->room_id);
        $this->form['department_id'] = (int) $row->department_id;
        $this->form['date']          = $this->fmtDate($row->date);
        $this->form['time']          = $this->fmtTime($row->start_time);
        $this->form['time_end']      = $this->fmtTime($row->end_time);
        $this->form['notes']         = $meta['notes'] ?? null;
        $this->form['requirements']  = $meta['requirement_ids'] ?? [];

        $this->showModal = true;
    }

    public function closeEdit(): void
    {
        $this->showModal = false;
        $this->editingId = null;
    }

    /** Soft delete (model sudah SoftDeletes, jadi cukup delete()) */
    public function destroy(int $id): void
    {
        BookingRoom::whereKey($id)->delete(); // soft delete
        $this->reloadBuckets();
        $this->dispatch('toast', type: 'success', title: 'Dihapus', message: 'Meeting dihapus.', duration: 2500);
    }

    /** Ambil & kelompokkan item ke planned/ongoing/done */
    private function reloadBuckets(): void
    {
        $items = BookingRoom::query()
            ->when(Auth::user()?->company_id, fn ($q, $cid) => $q->where('company_id', $cid))
            ->orderBy('date')->orderBy('start_time')
            ->get();

        $this->planned = $this->ongoing = $this->done = [];

        foreach ($items as $r) {
            $meta = $this->parseSpecialNotes((string) ($r->special_notes ?? ''));

            $ui = [
                'id'            => $r->getKey(),
                'meeting_title' => $r->meeting_title,
                'location'      => $this->roomLabelFromId($r->room_id),
                'date'          => $this->fmtDate($r->date),
                'time'          => $this->fmtTime($r->start_time),
                'time_end'      => $this->fmtTime($r->end_time),
                'participant'   => (int) $r->number_of_attendees,
                'department_id' => (int) $r->department_id,
                'requirements'  => $meta['requirement_ids'] ?? [],
                'notes'         => $meta['notes'] ?? null,
            ];

            $status = $this->computeStatus($ui['date'], $ui['time'], $ui['time_end']);
            match ($status) {
                'planned' => $this->planned[] = $ui,
                'ongoing' => $this->ongoing[] = $ui,
                default   => $this->done[]    = $ui,
            };
        }
    }

    /** ------------ Helpers ringkas ------------ */

    private function validateNotesIfOther(): void
    {
        if ($this->otherRequirementId && in_array($this->otherRequirementId, $this->form['requirements'] ?? [], true)) {
            $this->validate(['form.notes' => 'required|string|min:2|max:1000']);
        }
    }

    private function resetForm(): void
    {
        $this->reset(['editingId', 'showModal']);
        $this->form = [
            'meeting_title' => null,
            'location'      => null,
            'department_id' => null,
            'date'          => null,
            'time'          => null,
            'time_end'      => null,
            'participant'   => null,
            'notes'         => null,
            'requirements'  => [],
        ];
        $this->resetValidation();
    }

    private function roomIdFromLabel(?string $label): ?int
    {
        return self::ROOMS[$label] ?? null;
    }

    private function roomLabelFromId(?int $id): ?string
    {
        $flip = array_flip(self::ROOMS);
        return $id ? ($flip[$id] ?? null) : null;
    }

    private function joinDateTime(string $date, string $time): string
    {
        return "{$date} {$time}:00"; // sesuai kolom DATETIME
    }

    private function fmtDate($v): ?string
    {
        if (!$v) return null;
        try { return Carbon::parse($v)->format('Y-m-d'); } catch (\Throwable) { return is_string($v) ? substr($v,0,10) : null; }
    }

    private function fmtTime($v): ?string
    {
        if (!$v) return null;
        try { return Carbon::parse($v)->format('H:i'); } catch (\Throwable) {
            return (is_string($v) && preg_match('/^\d{2}:\d{2}/', $v)) ? substr($v,0,5) : null;
        }
    }

    private function computeStatus(?string $date, ?string $start, ?string $end): string
    {
        if (!$date || !$start || !$end) return 'planned';

        try {
            $startAt = Carbon::parse("{$date} {$start}", config('app.timezone'));
            $endAt   = Carbon::parse("{$date} {$end}",   config('app.timezone'));
        } catch (\Throwable) {
            return 'planned';
        }

        $now = now(config('app.timezone'));
        if ($now->lt($startAt)) return 'planned';
        if ($now->between($startAt, $endAt, true)) return 'ongoing';
        return 'done';
    }

    /** Simpan requirement sebagai ID agar stabil */
    private function composeSpecialNotes(array $ids, string $notes): string
    {
        $clean = array_values(array_unique(array_map('intval', array_filter($ids, fn ($v) => $v !== null))));
        $tag = $clean ? '[REQID=' . implode(',', $clean) . ']' : '';
        return trim($tag . "\n" . trim($notes));
    }

    /** Baca kembali [REQID=1,2,3] (tetap support legacy [REQ=...]) */
    private function parseSpecialNotes(string $raw): array
    {
        $notes = $raw;
        $ids   = [];

        if (preg_match('/\[REQID=([\d,\s]+)\]/i', $notes, $m)) {
            $ids   = array_values(array_filter(array_map('intval', explode(',', $m[1]))));
            $notes = str_replace($m[0], '', $notes);
        } elseif (preg_match('/\[REQ=([a-z0-9 _\-,]+)\]/i', $notes, $m)) {
            $names = array_values(array_filter(array_map(fn ($x) => trim(strtolower($x)), explode(',', $m[1]))));
            if ($names) {
                $ids = Requirement::whereIn(\DB::raw('LOWER(name)'), $names)
                    ->pluck('requirement_id')->map(fn ($v) => (int) $v)->all();
            }
            $notes = str_replace($m[0], '', $notes);
        }

        return ['requirement_ids' => $ids, 'notes' => ltrim($notes)];
    }

    private function findOtherRequirementId(): ?int
    {
        $id = Requirement::whereRaw('LOWER(name) = ?', ['other'])->value('requirement_id');
        return $id ? (int) $id : null;
    }

    /** Auto-refresh opsional (dipanggil dari wire:poll bila ada) */
    public function tick(): void
    {
        try { $this->reloadBuckets(); } catch (\Throwable $e) {
            \Log::error('[MeetingSchedule tick] '.$e->getMessage());
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
            'roomOptions'        => array_keys(self::ROOMS),
        ]);
    }
}
