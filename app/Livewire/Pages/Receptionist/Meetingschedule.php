<?php

namespace App\Livewire\Pages\Receptionist;

use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.receptionist')]
#[Title('Meeting Schedule')]
class MeetingSchedule extends Component
{
    public array $items = [];
    public $date, $time, $time_end, $participant, $department, $with, $location, $notes, $status = 'planned';
    public ?int $editingId = null;
    public bool $modalEdit = false;
    public string $saveState = 'idle';
    public ?string $savedAt = null;
    public function mount(): void
    {
        $this->items = session('ms_items', $this->seed());
    }
    private function seed(): array
    {
        return [
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
    }
    private function persist(): void
    {
        session()->put('ms_items', $this->items);
    }
    protected function rules(): array
    {
        return [
            'date' => 'required|date',
            'time' => 'required|date_format:H:i',
            'time_end' => 'required|date_format:H:i|after:time',
            'participant' => 'required|integer|min:1',
            'department' => 'required|string',
            'with' => 'nullable|string|max:200',
            'location' => 'required|in:Ruangan 1,Ruangan 2,Ruangan 3',
            'notes' => 'nullable|string|max:1000',
            'status' => 'required|in:planned,ongoing,done',
        ];
    }
    private function nextId(): int
    {
        return empty($this->items) ? 1 : (max(array_column($this->items, 'id')) + 1);
    }
    private function findIndex(int $id): ?int
    {
        foreach ($this->items as $i => $m) {
            if ($m['id'] === $id)
                return $i;
        }
        return null;
    }
    private function resetForm(): void
    {
        $this->reset([
            'date',
            'time',
            'time_end',
            'participant',
            'department',
            'with',
            'location',
            'notes',
            'status',
            'editingId'
        ]);
        $this->status = 'planned';
    }
    public function save(): void
    {
        $this->validate();
        $this->items[] = [
            'id' => $this->nextId(),
            'date' => $this->date,
            'time' => $this->time,
            'time_end' => $this->time_end,
            'participant' => (int) $this->participant,
            'department' => $this->department,
            'with' => $this->with,
            'location' => $this->location,
            'notes' => $this->notes,
            'status' => $this->status,
        ];
        $this->persist();
        $this->resetForm();
        $this->resetValidation();
        $this->saveState = 'saved';
        $this->savedAt = now()->toIso8601String();
        $this->dispatch('notify', type: 'success', message: 'Meeting ditambahkan.');
    }
    public function openEdit(int $id): void
    {
        $idx = $this->findIndex($id);
        if ($idx === null)
            return;
        $m = $this->items[$idx];
        $this->editingId = $id;
        $this->date = $m['date'];
        $this->time = $m['time'];
        $this->time_end = $m['time_end'] ?? null;
        $this->participant = $m['participant'];
        $this->department = $m['department'] ?? null;
        $this->with = $m['with'];
        $this->location = $m['location'];
        $this->notes = $m['notes'];
        $this->status = $m['status'];
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
        $idx = $this->findIndex($this->editingId);
        if ($idx === null)
            return;
        $this->items[$idx] = [
            'id' => $this->editingId,
            'date' => $this->date,
            'time' => $this->time,
            'time_end' => $this->time_end,
            'participant' => (int) $this->participant,
            'department' => $this->department,
            'with' => $this->with,
            'location' => $this->location,
            'notes' => $this->notes,
            'status' => $this->status,
        ];
        $this->persist();
        $this->modalEdit = false;
        $this->resetForm();
        $this->resetValidation();
        $this->dispatch('notify', type: 'success', message: 'Perubahan disimpan.');
    }
    public function destroy(int $id): void
    {
        $this->items = array_values(array_filter($this->items, fn($m) => $m['id'] !== $id));
        $this->persist();
        $this->dispatch('notify', type: 'success', message: 'Meeting dihapus.');
    }
    public function toPlanned(int $id): void
    {
        $idx = $this->findIndex($id);
        if ($idx === null)
            return;
        $this->items[$idx]['status'] = 'planned';
        $this->persist();
    }
    public function toOngoing(int $id): void
    {
        $idx = $this->findIndex($id);
        if ($idx === null)
            return;
        $this->items[$idx]['status'] = 'ongoing';
        $this->persist();
    }
    public function toDone(int $id): void
    {
        $idx = $this->findIndex($id);
        if ($idx === null)
            return;
        $this->items[$idx]['status'] = 'done';
        $this->persist();
    }
    public function getPlannedProperty(): array
    {
        $rows = array_filter($this->items, fn($m) => $m['status'] === 'planned');
        usort($rows, fn($a, $b) => ($a['date'] <=> $b['date']) ?: ($a['time'] <=> $b['time']));
        return array_values($rows);
    }
    public function getOngoingProperty(): array
    {
        $rows = array_filter($this->items, fn($m) => $m['status'] === 'ongoing');
        usort($rows, fn($a, $b) => ($a['date'] <=> $b['date']) ?: ($a['time'] <=> $b['time']));
        return array_values($rows);
    }
    public function getDoneProperty(): array
    {
        $rows = array_filter($this->items, fn($m) => $m['status'] === 'done');
        usort($rows, fn($a, $b) => ($a['date'] <=> $b['date']) ?: ($a['time'] <=> $b['time']));
        return array_values($rows);
    }
    public function tick(): void
    {
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
        ]);
    }
}
