<?php

namespace App\Livewire\Pages\Superadmin;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\WithPagination;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use App\Models\Information as InformationModel;

#[Layout('layouts.superadmin')]
#[Title('information')]
class Information extends Component
{
    use WithPagination;

    public bool $formVisible = false;
    public bool $editMode = false;
    public bool $showDeleteConfirm = false;

    public ?int $informationId = null;
    public ?int $company_id = null;
    public string $description = '';
    public ?string $event_at = null;

    public string $search = '';
    public string $sortField = 'created_at';
    public string $sortDirection = 'desc';

    protected $rules = [
        'company_id'  => 'sometimes|integer',
        'description' => 'required|string|max:255',
        'event_at'    => 'nullable|date',
    ];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function sortBy(string $field): void
    {
        $allowed = ['information_id', 'company_id', 'event_at', 'created_at'];
        if (!in_array($field, $allowed)) return;

        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = 'asc';
        }
        $this->resetPage();
    }

    public function mount(): void
    {
        $this->company_id = Auth::user()?->company_id;
    }

    public function toggleForm(): void
    {
        if ($this->editMode) {
            $this->formVisible = true;
            return;
        }
        $this->formVisible = !$this->formVisible;

        if ($this->formVisible && !$this->editMode) {
            $this->resetForm();
            $this->company_id = Auth::user()?->company_id;
        }
    }

    public function startEdit(int $id): void
    {
        $info = InformationModel::findOrFail($id);

        $this->informationId = $info->getKey();
        $this->company_id    = $info->company_id;
        $this->description   = $info->description;
        $this->event_at      = $info->event_at?->format('Y-m-d\TH:i');

        $this->editMode    = true;
        $this->formVisible = true;
        $this->resetValidation();
    }

    public function save(): void
    {
        $this->company_id = Auth::user()?->company_id;
        $this->validate();

        $payload = [
            'company_id'  => $this->company_id,
            'description' => $this->description,
            'event_at'    => $this->event_at ? Carbon::parse($this->event_at) : null,
        ];

        if ($this->editMode && $this->informationId) {
            InformationModel::findOrFail($this->informationId)->update($payload);
            session()->flash('message', 'Information updated successfully.');
        } else {
            InformationModel::create($payload);
            session()->flash('message', 'Information created successfully.');
        }

        $this->cancelForm();
    }

    public function cancelForm(): void
    {
        $this->formVisible = false;
        $this->editMode = false;
        $this->resetForm();
        $this->resetValidation();
    }

    private function resetForm(): void
    {
        $this->informationId = null;
        $this->description = '';
        $this->event_at = null;
    }

    public function confirmDelete(int $id): void
    {
        $this->informationId = $id;
        $this->showDeleteConfirm = true;
    }

    public function cancelDelete(): void
    {
        $this->showDeleteConfirm = false;
        $this->informationId = null;
    }

    public function delete(): void
    {
        InformationModel::findOrFail($this->informationId)->delete();
        session()->flash('message', 'Information deleted successfully.');
        $this->showDeleteConfirm = false;
        $this->informationId = null;

        if ($this->editMode) $this->cancelForm();
    }

    public function render()
    {
        $companyId = Auth::user()?->company_id;

        $information = InformationModel::query()
            ->when($companyId, fn($q) => $q->where('company_id', $companyId))
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('description', 'like', "%{$this->search}%")
                        ->orWhere('company_id', 'like', "%{$this->search}%")
                        ->orWhere('information_id', 'like', "%{$this->search}%");
                });
            })
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate(10);

        return view('livewire.pages.superadmin.information', compact('information'));
    }
}
