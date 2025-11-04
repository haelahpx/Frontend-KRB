<?php

namespace App\Livewire\Pages\Receptionist;

use App\Models\Delivery;
use App\Models\Department;
use App\Models\User as UserModel;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.receptionist')]
#[Title('Documents & Packages — Status')]
class DocPackStatus extends Component
{
    use WithPagination;

    protected string $paginationTheme = 'tailwind';

    // Filters
    public string $q = '';
    public ?string $selectedDate = null;   // 'YYYY-MM-DD' (created_at)
    public string $dateMode = 'semua';     // 'semua' | 'terbaru' | 'terlama'
    public string $type = 'all';           // 'all' | 'document' | 'package'
    public ?int $departmentId = null;
    public ?int $userId = null;
    public string $departmentQ = '';
    public string $userQ = '';

    // Pagination per box
    public int $perPending = 5;
    public int $perStored = 5;

    // Edit modal (optional)
    public bool $showEdit = false;
    public ?int $editId = null;
    public array $edit = [
        'item_name'     => null,
        'nama_pengirim' => null,
        'nama_penerima' => null,
        'catatan'       => null,
    ];

    protected $rules = [
        'edit.item_name'     => 'nullable|string|max:255',
        'edit.nama_pengirim' => 'nullable|string|max:255',
        'edit.nama_penerima' => 'nullable|string|max:255',
        'edit.catatan'       => 'nullable|string|max:5000',
    ];

    public function updated($name): void
    {
        if ($name === 'departmentId') {
            $this->userId = null;
        }

        if (
            in_array($name, [
                'q',
                'selectedDate',
                'dateMode',
                'type',
                'departmentId',
                'userId',
                'departmentQ',
                'userQ',
            ], true)
        ) {
            $this->resetPage('pending');
            $this->resetPage('stored');
        }
    }

    private function base()
    {
        return Delivery::query()->byCompany(Auth::user()->company_id ?? null);
    }

    private function applySharedFilters($q)
    {
        if ($this->type !== 'all') {
            $q->where('type', $this->type);
        }

        if ($this->selectedDate) {
            $q->whereDate('created_at', $this->selectedDate);
        }

        if ($this->departmentId && Schema::hasColumn('deliveries', 'department_id')) {
            $q->where('department_id', $this->departmentId);
        }

        if (trim($this->departmentQ) !== '' && Schema::hasColumn('deliveries', 'department_id')) {
            $deptIds = Department::query()
                ->where('company_id', Auth::user()->company_id ?? null)
                ->whereNull('deleted_at')
                ->where('department_name', 'like', '%' . trim($this->departmentQ) . '%')
                ->pluck('department_id');

            $deptIds->isNotEmpty() ? $q->whereIn('department_id', $deptIds) : $q->whereRaw('0=1');
        }

        if ($this->userId && Schema::hasColumn('deliveries', 'receptionist_id')) {
            $q->where('receptionist_id', $this->userId);
        }

        if (trim($this->userQ) !== '' && Schema::hasColumn('deliveries', 'receptionist_id')) {
            $userIds = UserModel::query()
                ->where('company_id', Auth::user()->company_id ?? null)
                ->whereNull('deleted_at')
                ->when($this->departmentId, fn($qq) => $qq->where('department_id', $this->departmentId))
                ->where('full_name', 'like', '%' . trim($this->userQ) . '%')
                ->pluck('user_id');

            $userIds->isNotEmpty() ? $q->whereIn('receptionist_id', $userIds) : $q->whereRaw('0=1');
        }

        if (trim($this->q) !== '') {
            $term = '%' . trim($this->q) . '%';
            $q->where(function ($qq) use ($term) {
                $qq->where('item_name', 'like', $term)
                    ->orWhere('nama_pengirim', 'like', $term)
                    ->orWhere('nama_penerima', 'like', $term)
                    ->orWhereHas('receptionist', function ($u) use ($term) {
                        $u->where('full_name', 'like', $term);
                    });
            });
        }

        if ($this->dateMode === 'terbaru')
            $q->latest('created_at');
        elseif ($this->dateMode === 'terlama')
            $q->oldest('created_at');

        return $q;
    }

    /** Pending set (still at receptionist, not stored yet) */
    public function getPendingProperty()
    {
        $q = $this->base()->where('status', 'pending');
        $this->applySharedFilters($q)->latest('created_at');

        return $q->with('receptionist')->paginate($this->perPending, pageName: 'pending');
    }

    /** Stored set (already stored to a slot) */
    public function getStoredProperty()
    {
        $q = $this->base()->where('status', 'stored');
        $this->applySharedFilters($q)->latest('created_at');

        return $q->with('receptionist')->paginate($this->perStored, pageName: 'stored');
    }

    /* ===== Pending box actions ===== */

    public function openEdit(int $id): void
    {
        $row = $this->base()->findOrFail($id);
        $this->editId = $row->delivery_id ?? $row->id ?? $id;
        $this->edit = [
            'item_name'     => $row->item_name,
            'nama_pengirim' => $row->nama_pengirim,
            'nama_penerima' => $row->nama_penerima,
            'catatan'       => $row->catatan,
        ];
        $this->showEdit = true;
    }

    public function saveEdit(): void
    {
        if (!$this->editId)
            return;

        $this->validate();

        $row = $this->base()->findOrFail($this->editId);
        $row->fill([
            'item_name'     => $this->edit['item_name'],
            'nama_pengirim' => $this->edit['nama_pengirim'],
            'nama_penerima' => $this->edit['nama_penerima'],
            'catatan'       => $this->edit['catatan'],
        ])->save();

        $this->showEdit = false;
        $this->editId = null;
        $this->resetPage('pending');
        $this->dispatch('toast', type: 'success', title: 'Saved', message: 'Information successfully saved.', duration: 3000);
    }

    public function storeItem(int $id): void
    {
        $row = $this->base()->where('status', 'pending')->findOrFail($id);
        $row->status = 'stored';
        $row->save();

        $this->resetPage('pending');
        $this->resetPage('stored');
        $this->dispatch('toast', type: 'success', title: 'Stored', message: 'Item successfully stored.', duration: 3000);
    }

    /* ===== Direction helpers & Stored actions ===== */

    private function getDirectionFor(Delivery $row): string
    {
        if (Schema::hasColumn('deliveries', 'direction')) {
            $dir = (string) $row->direction;
            if ($dir === 'deliver' || $dir === 'taken') {
                return $dir;
            }
        }
        return $this->inferDirection($row);
    }

    private function inferDirection(Delivery $row): string
    {
        $companyId = Auth::user()->company_id ?? null;

        $pengirim = trim((string) $row->nama_pengirim);
        $penerima = trim((string) $row->nama_penerima);

        $isPengirimUser  = false;
        $isPenerimaUser  = false;

        if ($pengirim !== '') {
            $isPengirimUser = UserModel::query()
                ->where('company_id', $companyId)
                ->whereRaw('LOWER(TRIM(full_name)) = ?', [mb_strtolower($pengirim)])
                ->exists();
        }
        if ($penerima !== '') {
            $isPenerimaUser = UserModel::query()
                ->where('company_id', $companyId)
                ->whereRaw('LOWER(TRIM(full_name)) = ?', [mb_strtolower($penerima)])
                ->exists();
        }

        if ($isPenerimaUser && !$isPengirimUser)
            return 'taken';
        if ($isPengirimUser && !$isPenerimaUser)
            return 'deliver';

        return ($row->type === 'document') ? 'deliver' : 'taken';
    }

    /** Stored → final (delivered/taken) based on direction column */
    public function finalizeItem(int $id): void
    {
        $row = $this->base()->where('status', 'stored')->findOrFail($id);
        $dir = $this->getDirectionFor($row); // 'deliver' | 'taken'

        $when = now();

        if ($dir === 'deliver') {
            $row->status = 'delivered';
            if (Schema::hasColumn('deliveries', 'pengiriman')) {
                $row->pengiriman = $when;
            }
        } else {
            $row->status = 'taken';
            if (Schema::hasColumn('deliveries', 'pengambilan')) {
                $row->pengambilan = $when;
            }
        }

        $row->save();

        $this->resetPage('stored');
        $this->dispatch('toast', type: 'success', title: 'Done', message: 'Item successfully finalized.', duration: 3000);
    }


    public function render()
    {
        $companyId = Auth::user()->company_id ?? null;

        $departments = Department::query()
            ->where('company_id', $companyId)
            ->whereNull('deleted_at')
            ->when(trim($this->departmentQ) !== '', fn($q) =>
                $q->where('department_name', 'like', '%' . trim($this->departmentQ) . '%'))
            ->orderBy('department_name')
            ->get(['department_id', 'department_name']);

        $users = UserModel::query()
            ->where('company_id', $companyId)
            ->when($this->departmentId, fn($q) => $q->where('department_id', $this->departmentId))
            ->when(trim($this->userQ) !== '', fn($q) =>
                $q->where('full_name', 'like', '%' . trim($this->userQ) . '%'))
            ->whereNull('deleted_at')
            ->orderBy('full_name')
            ->get(['user_id', 'full_name']);

        $storedDirections = collect($this->stored->items())
            ->mapWithKeys(function ($row) {
                $dir = $this->getDirectionFor($row); // 'deliver' | 'taken'
                return [($row->delivery_id ?? $row->id) => $dir];
            })
            ->toArray();

        return view('livewire.pages.receptionist.docpackstatus', [
            'pending'         => $this->pending,
            'stored'          => $this->stored,
            'storedDirections'=> $storedDirections,
            'departments'     => $departments,
            'users'           => $users,
        ]);
    }
}
