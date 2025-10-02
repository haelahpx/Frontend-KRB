<?php

namespace App\Livewire\Pages\Superadmin;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use App\Models\Room;
use App\Models\Requirement;

#[Layout('layouts.superadmin')]
#[Title('Booking Room')]
class Bookingroom extends Component
{
    use WithPagination;

    // ===== Context
    public int $companyId;

    // ===== Rooms (create)
    public string $room_number = '';
    public string $search = '';

    // ===== Rooms (edit modal)
    public bool $roomModal = false;
    public ?int $room_edit_id = null;
    public string $room_edit_number = '';

    // ===== Requirements (create)
    public string $req_name = '';
    public string $req_search = '';

    // ===== Requirements (edit modal)
    public bool $reqModal = false;
    public ?int $req_edit_id = null;
    public string $req_edit_name = '';

    public function mount(): void
    {
        $this->companyId = (int) (Auth::user()->company_id ?? 0);
    }

    // ===== Pagination search reset (match two lists)
    public function updatingSearch(): void { $this->resetPage(pageName: 'roomsPage'); }
    public function updatingReqSearch(): void { $this->resetPage(pageName: 'reqsPage'); }

    // ===== Validation
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

    protected function reqCreateRules(): array
    {
        // Requirement tidak pakai company_id (global)
        return [
            'req_name' => [
                'required', 'string', 'max:255',
                Rule::unique('requirements', 'name'),
            ],
        ];
    }

    protected function reqEditRules(): array
    {
        return [
            'req_edit_name' => [
                'required', 'string', 'max:255',
                Rule::unique('requirements', 'name')
                    ->ignore($this->req_edit_id, 'requirement_id'),
            ],
        ];
    }

    // ===== Rooms: Create/List
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

    public function roomOpenEdit(int $id): void
    {
        $r = Room::where('company_id', $this->companyId)->findOrFail($id);
        $this->room_edit_id = $r->room_id;
        $this->room_edit_number = $r->room_number;
        $this->roomModal = true;
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

    public function roomDelete(int $id): void
    {
        Room::where('company_id', $this->companyId)->findOrFail($id)->delete();
        if ($this->room_edit_id === $id) $this->roomCloseEdit();

        session()->flash('success', 'Room berhasil dihapus.');
        $this->resetPage(pageName: 'roomsPage');
    }

    public function getRoomsProperty()
    {
        return Room::query()
            ->where('company_id', $this->companyId)
            ->when($this->search !== '', fn($q) => $q->where('room_number', 'like', "%{$this->search}%"))
            ->orderByDesc('created_at')
            ->paginate(10, pageName: 'roomsPage');
    }

    // ===== Requirements: Create/List
    public function reqStore(): void
    {
        $this->validate($this->reqCreateRules());

        Requirement::create(['name' => $this->req_name]);

        $this->req_name = '';
        session()->flash('success', 'Requirement berhasil dibuat.');
        $this->resetPage(pageName: 'reqsPage');
    }

    public function reqOpenEdit(int $id): void
    {
        $q = Requirement::findOrFail($id);
        $this->req_edit_id = $q->requirement_id;
        $this->req_edit_name = $q->name;
        $this->reqModal = true;
        $this->resetErrorBag();
    }

    public function reqCloseEdit(): void
    {
        $this->reqModal = false;
        $this->reset('req_edit_id', 'req_edit_name');
        $this->resetErrorBag();
    }

    public function reqUpdate(): void
    {
        $this->validate($this->reqEditRules());

        $q = Requirement::findOrFail($this->req_edit_id);
        $q->update(['name' => $this->req_edit_name]);

        $this->reqCloseEdit();
        session()->flash('success', 'Requirement berhasil diupdate.');
    }

    public function reqDelete(int $id): void
    {
        Requirement::findOrFail($id)->delete();
        if ($this->req_edit_id === $id) $this->reqCloseEdit();

        session()->flash('success', 'Requirement berhasil dihapus.');
        $this->resetPage(pageName: 'reqsPage');
    }

    public function getRequirementsProperty()
    {
        return Requirement::query()
            ->when($this->req_search !== '', fn($q) => $q->where('name', 'like', "%{$this->req_search}%"))
            ->orderBy('name')
            ->paginate(10, pageName: 'reqsPage');
    }

    // ===== Render
    public function render()
    {
        return view('livewire.pages.superadmin.bookingroom', [
            'rooms'        => $this->rooms,
            'requirements' => $this->requirements,
        ]);
    }
}
