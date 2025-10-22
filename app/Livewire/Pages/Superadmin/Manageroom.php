<?php
// app/Livewire/Pages/Superadmin/Manageroom.php

namespace App\Livewire\Pages\Superadmin;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use App\Models\Room;

#[Layout('layouts.superadmin')]
#[Title('Manage Room')]
class Manageroom extends Component
{
    use WithPagination;

    public int $companyId;

    // Create
    public string $room_number = '';
    public string $search = '';

    // Edit modal
    public bool $roomModal = false;
    public ?int $room_edit_id = null;
    public string $room_edit_number = '';

    public function mount(): void
    {
        $this->companyId = (int) (Auth::user()->company_id ?? 0);
    }

    public function updatingSearch(): void
    {
        $this->resetPage(pageName: 'roomsPage');
    }

    protected function roomCreateRules(): array
    {
        return [
            'room_number' => [
                'required', 'string', 'max:255',
                Rule::unique('rooms', 'room_number')
                    ->where(fn($q) => $q->where('company_id', $this->companyId)),
            ],
        ];
    }

    protected function roomEditRules(): array
    {
        return [
            'room_edit_number' => [
                'required', 'string', 'max:255',
                Rule::unique('rooms', 'room_number')
                    ->where(fn($q) => $q->where('company_id', $this->companyId))
                    ->ignore($this->room_edit_id, 'room_id'),
            ],
        ];
    }

    // Create
    public function roomStore(): void
    {
        $this->validate($this->roomCreateRules());

        Room::create([
            'company_id'  => $this->companyId,
            'room_number' => $this->room_number,
        ]);

        $this->room_number = '';
        session()->flash('success', 'Room berhasil dibuat.');
        $this->resetPage(pageName: 'roomsPage');
    }

    // Edit
    public function roomOpenEdit(int $id): void
    {
        $r = Room::where('company_id', $this->companyId)->findOrFail($id);
        $this->room_edit_id     = $r->room_id;
        $this->room_edit_number = $r->room_number;
        $this->roomModal        = true;
        $this->resetErrorBag();
    }

    public function roomCloseEdit(): void
    {
        $this->roomModal = false;
        $this->reset('room_edit_id', 'room_edit_number');
        $this->resetErrorBag();
    }

    public function roomUpdate(): void
    {
        $this->validate($this->roomEditRules());

        $r = Room::where('company_id', $this->companyId)->findOrFail($this->room_edit_id);
        $r->update(['room_number' => $this->room_edit_number]);

        $this->roomCloseEdit();
        session()->flash('success', 'Room berhasil diupdate.');
    }

    // Soft Delete
    public function roomDelete(int $id): void
    {
        $room = Room::where('company_id', $this->companyId)->findOrFail($id);
        $room->delete(); // soft delete only

        if ($this->room_edit_id === $id) {
            $this->roomCloseEdit();
        }

        session()->flash('success', 'Room berhasil dipindahkan ke arsip (soft deleted).');
        $this->resetPage(pageName: 'roomsPage');
    }

    // Query
    public function getRoomsProperty()
    {
        return Room::query()
            ->where('company_id', $this->companyId)
            ->when($this->search !== '', fn($q) => $q->where('room_number', 'like', "%{$this->search}%"))
            ->orderByDesc('created_at')
            ->paginate(10, pageName: 'roomsPage');
    }

    public function render()
    {
        return view('livewire.pages.superadmin.manageroom', [
            'rooms' => $this->rooms,
        ]);
    }
}
