<?php

namespace App\Livewire\Pages\Superadmin;

use App\Http\Controllers\VehicleAttachmentController;
use App\Models\Vehicle as VehicleModel;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.superadmin')]
#[Title('Vehicles')]
class Vehicle extends Component
{
    use WithPagination;

    protected string $paginationTheme = 'tailwind';

    public int $company_id = 0;

    public string $search = '';
    public string $categoryFilter = '';
    public string $activeFilter = '';
    public bool $showTrashed = false;

    public string $name = '';
    public string $category = 'car';
    public string $plate_number = '';
    public ?string $year = null;
    public ?string $notes = null;
    public bool $is_active = true;

    public string $temp_items_json = '[]';

    public bool $modalEdit = false;
    public ?int $edit_id = null;
    public string $edit_name = '';
    public string $edit_category = 'car';
    public string $edit_plate_number = '';
    public ?string $edit_year = null;
    public ?string $edit_notes = null;
    public bool $edit_is_active = true;
    public ?string $current_image = null;
    public string $edit_temp_items_json = '[]';

    public const CATEGORIES = ['car', 'pickup', 'motorcycle', 'other'];

    public function mount(): void
    {
        $this->company_id = (int) (Auth::user()->company_id ?? 0);
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }
    public function updatedCategoryFilter()
    {
        $this->resetPage();
    }
    public function updatedActiveFilter()
    {
        $this->resetPage();
    }
    public function updatedShowTrashed()
    {
        $this->resetPage();
    }

    protected function createRules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'category' => ['required', Rule::in(self::CATEGORIES)],
            'plate_number' => [
                'required',
                'string',
                'max:32',
                Rule::unique('vehicles', 'plate_number')
                    ->where(fn($q) => $q->where('company_id', $this->company_id))
                    ->whereNull('deleted_at'),
            ],
            'year' => ['nullable', 'string', 'max:8'],
            'notes' => ['nullable', 'string'],
            'is_active' => ['boolean'],
        ];
    }

    protected function editRules(): array
    {
        return [
            'edit_name' => ['required', 'string', 'max:255'],
            'edit_category' => ['required', Rule::in(self::CATEGORIES)],
            'edit_plate_number' => [
                'required',
                'string',
                'max:32',
                Rule::unique('vehicles', 'plate_number')
                    ->ignore($this->edit_id, 'vehicle_id')
                    ->where(fn($q) => $q->where('company_id', $this->company_id))
                    ->whereNull('deleted_at'),
            ],
            'edit_year' => ['nullable', 'string', 'max:8'],
            'edit_notes' => ['nullable', 'string'],
            'edit_is_active' => ['boolean'],
        ];
    }

    public function store(): void
    {
        $this->validate($this->createRules());

        $vehicle = VehicleModel::create([
            'company_id' => $this->company_id,
            'name' => trim($this->name),
            'category' => $this->category,
            'plate_number' => strtoupper(trim($this->plate_number)),
            'year' => $this->year ? trim($this->year) : null,
            'notes' => $this->notes,
            'is_active' => (bool) $this->is_active,
        ]);

        $items = json_decode($this->temp_items_json ?: '[]', true) ?? [];
        if (!empty($items)) {
            try {
                app(VehicleAttachmentController::class)->finalize(new \Illuminate\Http\Request([
                    'vehicle_id' => $vehicle->getKey(),
                    'items' => $items,
                ]));
            } catch (\Throwable $e) {
                $this->dispatch('toast', type: 'warning', title: 'Gambar', message: 'Gambar gagal diproses.', duration: 3000);
            }
        }

        $this->reset(['name', 'category', 'plate_number', 'year', 'notes', 'is_active', 'temp_items_json']);
        $this->category = 'car';
        $this->is_active = true;

        $this->dispatch('toast', type: 'success', title: 'Created', message: 'Vehicle created successfully.', duration: 3000);
        $this->resetPage();
    }

    public function openEdit(int $id): void
    {
        $row = VehicleModel::withTrashed()
            ->where('company_id', $this->company_id)
            ->findOrFail($id);

        $this->edit_id = $row->vehicle_id;
        $this->edit_name = $row->name ?? '';
        $this->edit_category = $row->category ?? 'car';
        $this->edit_plate_number = $row->plate_number ?? '';
        $this->edit_year = $row->year;
        $this->edit_notes = $row->notes;
        $this->edit_is_active = (bool) $row->is_active;
        $this->current_image = $row->image;
        $this->edit_temp_items_json = '[]';

        $this->modalEdit = true;
        $this->resetErrorBag();
    }

    public function closeEdit(): void
    {
        $this->modalEdit = false;
        $this->reset(
            'edit_id',
            'edit_name',
            'edit_category',
            'edit_plate_number',
            'edit_year',
            'edit_notes',
            'edit_is_active',
            'current_image',
            'edit_temp_items_json'
        );
    }

    public function update(): void
    {
        if (is_null($this->edit_id))
            return;

        $this->validate($this->editRules());

        VehicleModel::where('company_id', $this->company_id)
            ->where('vehicle_id', $this->edit_id)
            ->update([
                'name' => trim($this->edit_name),
                'category' => $this->edit_category,
                'plate_number' => strtoupper(trim($this->edit_plate_number)),
                'year' => $this->edit_year ? trim($this->edit_year) : null,
                'notes' => $this->edit_notes,
                'is_active' => (bool) $this->edit_is_active,
            ]);

        $items = json_decode($this->edit_temp_items_json ?: '[]', true) ?? [];
        if (!empty($items)) {
            try {
                app(VehicleAttachmentController::class)->finalize(new \Illuminate\Http\Request([
                    'vehicle_id' => $this->edit_id,
                    'items' => $items,
                ]));
            } catch (\Throwable $e) {
                $this->dispatch('toast', type: 'warning', title: 'Gambar', message: 'Gambar baru gagal diproses.', duration: 3000);
            }
        }

        $this->closeEdit();
        $this->dispatch('toast', type: 'success', title: 'Updated', message: 'Vehicle updated.', duration: 3000);
    }

    public function delete(int $id): void
    {
        $row = VehicleModel::where('company_id', $this->company_id)->findOrFail($id);
        $row->delete();
        $this->dispatch('toast', type: 'success', title: 'Trashed', message: 'Vehicle moved to trash.', duration: 2500);
        $this->resetPage();
    }

    public function restore(int $id): void
    {
        $row = VehicleModel::withTrashed()
            ->where('company_id', $this->company_id)
            ->findOrFail($id);

        if ($row->trashed()) {
            $row->restore();
            $this->dispatch('toast', type: 'success', title: 'Restored', message: 'Vehicle restored.', duration: 2500);
        }
    }

    public function render()
    {
        $query = VehicleModel::query()->where('company_id', $this->company_id);

        if ($this->showTrashed)
            $query->onlyTrashed();

        if ($this->search !== '') {
            $s = trim($this->search);
            $query->where(function ($q) use ($s) {
                $q->where('name', 'like', "%{$s}%")
                    ->orWhere('plate_number', 'like', "%{$s}%")
                    ->orWhere('year', 'like', "%{$s}%");
            });
        }

        if ($this->categoryFilter !== '')
            $query->where('category', $this->categoryFilter);
        if ($this->activeFilter !== '')
            $query->where('is_active', $this->activeFilter === 'active');

        $rows = $query->orderBy('name')->paginate(10);

        return view('livewire.pages.superadmin.vehicle', [
            'rows' => $rows,
            'categories' => self::CATEGORIES,
        ]);
    }
}
