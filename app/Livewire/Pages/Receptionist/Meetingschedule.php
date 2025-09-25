<?php

namespace App\Livewire\Pages\Receptionist;

use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.receptionist')]
#[Title('Meeting Schedule')]
class MeetingSchedule extends Component
{
    public array $items = [
        [
            'id' => 1,
            'date' => '2025-09-26',
            'time' => '10:00',
            'time_end' => '11:00',
            'participant' => 6,
            'department' => 'IT',
            'with' => 'PT Sinar Abadi',
            'location' => 'Ruangan 1',
            'notes' => 'Bawa dokumen kontrak',
            'status' => 'planned',
        ],
        [
            'id' => 2,
            'date' => '2025-09-27',
            'time' => '14:30',
            'time_end' => '15:30',
            'participant' => 10,
            'department' => 'Operasional',
            'with' => 'Tim IT',
            'location' => 'Ruangan 2',
            'notes' => 'Update progres sprint',
            'status' => 'planned',
        ],
        [
            'id' => 3,
            'date' => '2025-09-28',
            'time' => '09:00',
            'time_end' => '10:00',
            'participant' => 3,
            'department' => 'Riset',
            'with' => 'Sekretariat',
            'location' => 'Ruangan 3',
            'notes' => 'Cek legal wording',
            'status' => 'done',
        ],
    ];

    // form fields
    public $date, $time, $time_end, $participant, $department, $with, $location, $notes;

    // filters / state
    public $statusFilter = 'all';
    public $q = '';
    public ?int $editingId = null;

    protected $rules = [
        'date' => 'required|date',
        'time' => 'required|date_format:H:i',
        'time_end' => 'required|date_format:H:i|after:time',
        'participant' => 'required|integer|min:1',
        'department' => 'required|string',
        'with' => 'nullable|string|max:200',
        'location' => 'required|in:Ruangan 1,Ruangan 2,Ruangan 3',
        'notes' => 'nullable|string|max:1000',
    ];

    public function save(): void
    {
        $this->validate();

        $nextId = empty($this->items) ? 1 : (max(array_column($this->items, 'id')) + 1);

        $this->items[] = [
            'id' => $nextId,
            'date' => $this->date,
            'time' => $this->time,
            'time_end' => $this->time_end,
            'participant' => (int) $this->participant,
            'department' => $this->department,
            'with' => $this->with,
            'location' => $this->location,
            'notes' => $this->notes,
            'status' => 'planned',
        ];

        $this->reset(['date', 'time', 'time_end', 'participant', 'department', 'with', 'location', 'notes']);
        $this->dispatch('notify', type: 'success', message: 'Meeting added.');
    }

    public function edit(int $id): void
    {
        $m = collect($this->items)->firstWhere('id', $id);
        if (!$m)
            return;

        $this->editingId = $id;
        $this->date = $m['date'];
        $this->time = $m['time'];
        $this->time_end = $m['time_end'] ?? null;
        $this->participant = $m['participant'];
        $this->department = $m['department'] ?? null;
        $this->with = $m['with'];
        $this->location = $m['location'];
        $this->notes = $m['notes'];
    }

    public function update(): void
    {
        if (!$this->editingId)
            return;

        $this->validate();

        foreach ($this->items as &$m) {
            if ($m['id'] === $this->editingId) {
                $m['date'] = $this->date;
                $m['time'] = $this->time;
                $m['time_end'] = $this->time_end;
                $m['participant'] = (int) $this->participant;
                $m['department'] = $this->department;
                $m['with'] = $this->with;
                $m['location'] = $this->location;
                $m['notes'] = $this->notes;
                break;
            }
        }
        unset($m);

        $this->editingId = null;
        $this->reset(['date', 'time', 'time_end', 'participant', 'department', 'with', 'location', 'notes']);
        $this->dispatch('notify', type: 'success', message: 'Meeting updated.');
    }

    public function destroy(int $id): void
    {
        $this->items = array_values(array_filter($this->items, fn($m) => $m['id'] !== $id));
        $this->dispatch('notify', type: 'success', message: 'Meeting deleted.');
    }

    public function markDone(int $id): void
    {
        foreach ($this->items as &$m) {
            if ($m['id'] === $id) {
                $m['status'] = 'done';
                break;
            }
        }
        unset($m);
    }

    public function markPlanned(int $id): void
    {
        foreach ($this->items as &$m) {
            if ($m['id'] === $id) {
                $m['status'] = 'planned';
                break;
            }
        }
        unset($m);
    }

    public function cancelEdit(): void
    {
        $this->editingId = null;
        $this->reset(['date', 'time', 'time_end', 'participant', 'department', 'with', 'location', 'notes']);
    }

    public function getRowsProperty(): array
    {
        $rows = $this->items;

        if ($this->q) {
            $q = mb_strtolower($this->q);
            $rows = array_filter($rows, function ($m) use ($q) {
                return str_contains(mb_strtolower((string) $m['participant']), $q)
                    || str_contains(mb_strtolower($m['with'] ?? ''), $q)
                    || str_contains(mb_strtolower($m['location'] ?? ''), $q)
                    || str_contains(mb_strtolower($m['notes'] ?? ''), $q)
                    || str_contains(mb_strtolower($m['department'] ?? ''), $q);
            });
        }

        if ($this->statusFilter !== 'all') {
            $rows = array_filter($rows, fn($m) => $m['status'] === $this->statusFilter);
        }

        usort($rows, fn($a, $b) => ($a['date'] <=> $b['date']) ?: ($a['time'] <=> $b['time']));
        return array_values($rows);
    }

    public function render()
    {
        return view('livewire.pages.receptionist.meetingschedule', [
            'rows' => $this->rows,
        ]);
    }
}
