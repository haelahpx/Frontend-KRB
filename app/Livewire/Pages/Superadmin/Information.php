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
#[Title('Information')]
class Information extends Component
{
    use WithPagination;

    // Derived from Auth
    public ?int $company_id = null;

    // Table filter & sorting
    public string $search = '';
    public string $sortField = 'created_at';
    public string $sortDirection = 'desc';

    // Create form fields
    public string $description = '';
    public ?string $event_at = null;

    // Edit modal state & fields
    public bool $modalEdit = false;
    public ?int $edit_id = null;
    public string $edit_description = '';
    public ?string $edit_event_at = null;

    // Validation rules for the create form
    protected function rules(): array
    {
        return [
            'description' => 'required|string|max:255',
            'event_at'    => 'nullable|date',
        ];
    }

    // Validation rules for the edit modal
    protected function editRules(): array
    {
        return [
            'edit_description' => 'required|string|max:255',
            'edit_event_at'    => 'nullable|date',
        ];
    }

    public function mount(): void
    {
        // Set default company from the logged-in user
        $this->company_id = Auth::user()?->company_id;
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    /**
     * Create new information.
     */
    public function store(): void
    {
        $this->validate();

        InformationModel::create([
            'company_id'  => $this->company_id, // Get company from authenticated user
            'description' => $this->description,
            'event_at'    => $this->event_at ? Carbon::parse($this->event_at) : null,
        ]);

        $this->reset('description', 'event_at');
        $this->dispatch('toast', type: 'success', title: 'Dibuat', message: 'Information berhasil dibuat.', duration: 3000);
        $this->resetPage();
    }

    /**
     * Open and prepare the edit modal.
     */
    public function openEdit(int $id): void
    {
        $info = InformationModel::where('company_id', $this->company_id)
            ->findOrFail($id);

        $this->edit_id = $info->getKey();
        $this->edit_description = $info->description;
        $this->edit_event_at = $info->event_at ? $info->event_at->format('Y-m-d\TH:i') : null;

        $this->modalEdit = true;
        $this->resetErrorBag();
    }

    /**
     * Close the edit modal and reset its state.
     */
    public function closeEdit(): void
    {
        $this->modalEdit = false;
        $this->reset('edit_id', 'edit_description', 'edit_event_at');
    }

    /**
     * Update the information from the edit modal.
     */
    public function update(): void
    {
        $this->validate($this->editRules());

        $info = InformationModel::where('company_id', $this->company_id)
            ->findOrFail($this->edit_id);

        $info->update([
            'description' => $this->edit_description,
            'event_at'    => $this->edit_event_at ? Carbon::parse($this->edit_event_at) : null,
        ]);

        $this->closeEdit();
        $this->dispatch('toast', type: 'success', title: 'Diupdate', message: 'Information berhasil diupdate.', duration: 3000);
        $this->resetPage();
    }

    /**
     * Delete an information entry.
     */
    public function delete(int $id): void
    {
        InformationModel::where('company_id', $this->company_id)
            ->findOrFail($id)
            ->delete();

        $this->resetPage();
        $this->dispatch('toast', type: 'success', title: 'Dihapus', message: 'Information berhasil dihapus.', duration: 3000);
    }

    public function render()
    {
        $information = InformationModel::query()
            ->where('company_id', $this->company_id)
            ->when($this->search, function ($query) {
                $query->where('description', 'like', "%{$this->search}%");
            })
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate(10);

        return view('livewire.pages.superadmin.information', compact('information'));
    }
}
