<?php
// app/Livewire/Pages/Superadmin/Managerequirement.php

namespace App\Livewire\Pages\Superadmin;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Illuminate\Validation\Rule;
use App\Models\Requirement;

#[Layout('layouts.superadmin')]
#[Title('Manage Requirement')]
class Managerequirement extends Component
{
    use WithPagination;

    // Create
    public string $req_name = '';
    public string $req_search = '';

    // Edit modal
    public bool $reqModal = false;
    public ?int $req_edit_id = null;
    public string $req_edit_name = '';

    public function updatingReqSearch(): void
    {
        $this->resetPage(pageName: 'reqsPage');
    }

    protected function reqCreateRules(): array
    {
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
                Rule::unique('requirements', 'name')->ignore($this->req_edit_id, 'requirement_id'),
            ],
        ];
    }

    // Create
    public function reqStore(): void
    {
        $this->validate($this->reqCreateRules());
        Requirement::create(['name' => $this->req_name]);
        $this->req_name = '';
        session()->flash('success', 'Requirement berhasil dibuat.');
        $this->resetPage(pageName: 'reqsPage');
    }

    // Edit
    public function reqOpenEdit(int $id): void
    {
        $q = Requirement::findOrFail($id);
        $this->req_edit_id   = $q->requirement_id;
        $this->req_edit_name = $q->name;
        $this->reqModal      = true;
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

    public function render()
    {
        return view('livewire.pages.superadmin.managerequirement', [
            'requirements' => $this->requirements,
        ]);
    }
}
