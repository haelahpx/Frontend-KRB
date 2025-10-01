<?php

namespace App\Livewire\Pages\Superadmin;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use App\Models\Department as DepartmentModel;

#[Layout('layouts.superadmin')]
#[Title('Department')]
class Department extends Component
{
    use WithPagination;

    // Derived from Auth
    public int $company_id;
    public string $company_name = '-';

    // Table filter
    public string $search = '';

    // Create form
    public string $department_name = '';

    // Edit modal state
    public bool $modalEdit = false;
    public ?int $edit_id = null;
    public string $edit_department_name = '';

    protected function rules(): array
    {
        // Unique per company
        return [
            'department_name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('departments', 'department_name')
                    ->where(fn($q) => $q->where('company_id', $this->company_id))
            ],
        ];
    }

    protected function editRules(): array
    {
        return [
            'edit_department_name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('departments', 'department_name')
                    ->where(fn($q) => $q->where('company_id', $this->company_id))
                    ->ignore($this->edit_id, 'department_id'),
            ],
        ];
    }

    public function mount(): void
    {
        $user = Auth::user();
        $this->company_id = (int) $user->company_id;
        // kalau ada relasi company() di User:
        $this->company_name = method_exists($user, 'company') && $user->company
            ? ($user->company->company_name ?? '-')
            : '-';
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    /** Create */
    public function store(): void
    {
        $this->validate();

        DepartmentModel::create([
            'company_id'      => $this->company_id, // ambil dari Auth
            'department_name' => $this->department_name,
        ]);

        $this->reset('department_name');
        $this->dispatch('toast', type: 'success', title: 'Dibuat', message: 'department berhasil dibuat.', duration: 3000);
        $this->resetPage();
    }

    /** Open Edit Modal */
    public function openEdit(int $departmentId): void
    {
        $row = DepartmentModel::where('company_id', $this->company_id)
            ->where('department_id', $departmentId)
            ->firstOrFail();

        $this->edit_id = $row->department_id;
        $this->edit_department_name = $row->department_name;
        $this->modalEdit = true;
    }

    public function closeEdit(): void
    {
        $this->modalEdit = false;
        $this->reset('edit_id', 'edit_department_name');
        $this->resetErrorBag();
    }

    /** Update */
    public function update(): void
    {
        $this->validate($this->editRules());

        DepartmentModel::where('company_id', $this->company_id)
            ->where('department_id', $this->edit_id)
            ->update([
                'department_name' => $this->edit_department_name,
            ]);

        $this->closeEdit();
        $this->dispatch('toast', type: 'success', title: 'Diperbarui', message: 'Perubahan disimpan.', duration: 3000);
    }

    /** Delete (confirm native browser) */
    public function delete(int $departmentId): void
    {
        DepartmentModel::where('company_id', $this->company_id)
            ->where('department_id', $departmentId)
            ->delete();

        $this->dispatch('toast', type: 'success', title: 'Dihapus', message: 'Department dihapus.', duration: 3000);
        $this->resetPage();
    }

    public function render()
    {
        $rows = DepartmentModel::where('company_id', $this->company_id)
            ->when(
                $this->search !== '',
                fn($q) =>
                $q->where('department_name', 'like', "%{$this->search}%")
            )
            ->orderBy('department_name')
            ->paginate(10);

        return view('livewire.pages.superadmin.department', compact('rows'));
    }
}
