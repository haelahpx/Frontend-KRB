<?php

namespace App\Livewire\Pages\Superadmin;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use App\Models\User;
use App\Models\Role;
use App\Models\Department;

#[Layout('layouts.superadmin')]
#[Title('Account Settings')]
class Account extends Component
{
    use WithPagination;

    protected $paginationTheme = 'tailwind';

    // Filters
    public string $search = '';
    public string $roleFilter = '';

    // Create form (inline)
    public string $full_name = '';
    public string $email = '';
    public ?string $phone_number = null;
    public ?string $password = null;
    public ?int $role_id = null;
    public ?int $department_id = null;

    // Edit (modal) state
    public bool $modalEdit = false;
    public ?int $editingId = null;
    public string $edit_full_name = '';
    public string $edit_email = '';
    public ?string $edit_phone_number = null;
    public ?string $edit_password = null; // optional
    public ?int $edit_role_id = null;
    public ?int $edit_department_id = null;

    // Derived from auth
    public int $company_id;
    public string $company_name = '-';

    // Options
    /** @var array<array{id:int,name:string}> */
    public array $roles = [];
    /** @var \Illuminate\Support\Collection */
    public $departments;

    // Role & Department mapping targets
    public ?int $roleReceptionistId = null;
    public ?int $roleSuperadminId  = null;
    public ?int $deptAdministrationId = null;
    public ?int $deptExecutiveId      = null;

    protected $casts = [
        'modalEdit' => 'bool',
    ];

    /* ======================== Lifecycle ======================== */

    public function mount(): void
    {
        $auth = Auth::user()->loadMissing('company');
        $this->company_id   = (int) ($auth->company_id ?? 0);
        $this->company_name = optional($auth->company)->company_name ?? '-';

        // roles dropdown
        $this->roles = Role::orderBy('name')
            ->get(['role_id as id', 'name'])
            ->map(fn($r) => ['id' => (int) $r->id, 'name' => (string) $r->name])
            ->toArray();

        // Simpan role_id spesifik
        $this->roleReceptionistId = Role::where('name', 'receptionist')->value('role_id');
        $this->roleSuperadminId   = Role::where('name', 'superadmin')->value('role_id');

        // Muat departments & ambil target IDs
        $this->loadDepartments();
        $this->deptAdministrationId = Department::where('company_id', $this->company_id)
            ->where('department_name', 'Administration')
            ->value('department_id');

        $this->deptExecutiveId = Department::where('company_id', $this->company_id)
            ->where('department_name', 'Executive')
            ->value('department_id');
    }

    public function updatingSearch(): void { $this->resetPage(); }
    public function updatingRoleFilter(): void { $this->resetPage(); }

    private function loadDepartments(): void
    {
        $this->departments = Department::query()
            ->when($this->company_id, fn($q) => $q->where('company_id', $this->company_id))
            ->orderBy('department_name')
            ->get(['department_id', 'department_name']);
    }

    /* ======================== Validation ======================== */

    protected function createRules(): array
    {
        return [
            'full_name'     => ['required', 'string', 'max:255'],
            'email'         => [
                'required',
                'email',
                Rule::unique('users', 'email')->whereNull('deleted_at'), // abaikan yang sudah soft-deleted
            ],
            'phone_number'  => ['nullable', 'string', 'max:30'],
            'password'      => ['required', 'string', 'min:6'],
            'role_id'       => ['required', 'integer', Rule::exists('roles', 'role_id')],
            'department_id' => [
                'required',
                'integer',
                Rule::exists('departments', 'department_id')->where('company_id', $this->company_id),
            ],
        ];
    }

    protected function editRules(): array
    {
        $ignoreId = $this->editingId ?? null;

        return [
            'edit_full_name'     => ['required', 'string', 'max:255'],
            'edit_email'         => [
                'required',
                'email',
                Rule::unique('users', 'email')
                    ->ignore($ignoreId, 'user_id')
                    ->whereNull('deleted_at'),
            ],
            'edit_phone_number'  => ['nullable', 'string', 'max:30'],
            'edit_role_id'       => ['required', 'integer', Rule::exists('roles', 'role_id')],
            'edit_department_id' => [
                'required',
                'integer',
                Rule::exists('departments', 'department_id')->where('company_id', $this->company_id),
            ],
            // edit_password opsional
        ];
    }

    /* ======================== Helpers ======================== */

    private function mapDeptForRole(?int $roleId): ?int
    {
        if (!$roleId) return null;

        if ($this->roleReceptionistId && $roleId === $this->roleReceptionistId) {
            return $this->deptAdministrationId; // receptionist -> Administration
        }
        if ($this->roleSuperadminId && $roleId === $this->roleSuperadminId) {
            return $this->deptExecutiveId; // superadmin -> Executive
        }
        return null; // role lain tidak dipaksa
    }

    private function isSuperadminRole(?int $roleId): bool
    {
        return $roleId !== null && $this->roleSuperadminId !== null && (int)$roleId === (int)$this->roleSuperadminId;
    }

    /* ======================== Reactive hooks ======================== */

    // Create form
    public function updatedRoleId($value): void
    {
        $mapped = $this->mapDeptForRole((int) $value);
        if ($mapped) $this->department_id = $mapped;
    }

    // Edit form
    public function updatedEditRoleId($value): void
    {
        $mapped = $this->mapDeptForRole((int) $value);
        if ($mapped) $this->edit_department_id = $mapped;
    }

    /* ======================== Create ======================== */

    public function store(): void
    {
        if ($this->role_id) {
            $forced = $this->mapDeptForRole((int) $this->role_id);
            if ($forced) $this->department_id = $forced;
        }

        $data = $this->validate($this->createRules());

        // jaga-jaga
        $forced = $this->mapDeptForRole((int) $data['role_id']);
        if ($forced) $data['department_id'] = $forced;

        User::create([
            'full_name'     => $data['full_name'],
            'email'         => strtolower($data['email']),
            'phone_number'  => $data['phone_number'] ?? null,
            'password'      => bcrypt($this->password),
            'role_id'       => (int) $data['role_id'],
            'company_id'    => $this->company_id,
            'department_id' => (int) $data['department_id'],
        ]);

        $this->resetCreateForm();
        $this->dispatch('toast', type: 'success', title: 'Dibuat', message: 'User dibuat.', duration: 3000);
        $this->resetPage();
    }

    private function resetCreateForm(): void
    {
        $this->full_name = '';
        $this->email = '';
        $this->phone_number = null;
        $this->password = null;
        $this->role_id = null;
        $this->department_id = null;
        $this->resetValidation();
    }

    /* ======================== Edit ======================== */

    public function openEdit(int $id): void
    {
        $this->resetErrorBag();
        $this->resetValidation();

        $u = User::where('company_id', $this->company_id)
            ->where('user_id', $id)
            ->firstOrFail();

        // 🔒 Superadmin tidak bisa edit Superadmin lain
        if ($this->isSuperadminRole($u->role_id) && $u->user_id !== Auth::id()) {
            $this->dispatch('toast', type: 'warning', title: 'Ditolak', message: 'Tidak boleh mengedit Superadmin lain.', duration: 3000);
            return;
        }

        $this->editingId          = $u->user_id;
        $this->edit_full_name     = (string) $u->full_name;
        $this->edit_email         = (string) $u->email;
        $this->edit_phone_number  = $u->phone_number;
        $this->edit_role_id       = $u->role_id;
        $this->edit_department_id = $u->department_id;
        $this->edit_password      = null;

        $this->modalEdit = true;
    }

    public function closeEdit(): void
    {
        $this->modalEdit = false;
        $this->editingId          = null;
        $this->edit_full_name     = '';
        $this->edit_email         = '';
        $this->edit_phone_number  = null;
        $this->edit_role_id       = null;
        $this->edit_department_id = null;
        $this->edit_password      = null;

        $this->resetErrorBag();
        $this->resetValidation();
    }

    public function update(): void
    {
        if (!$this->editingId) return;

        $u = User::where('company_id', $this->company_id)
            ->where('user_id', $this->editingId)
            ->firstOrFail();

        // 🔒 Superadmin tidak bisa edit Superadmin lain (kecuali dirinya)
        if ($this->isSuperadminRole($u->role_id) && $u->user_id !== Auth::id()) {
            $this->dispatch('toast', type: 'warning', title: 'Ditolak', message: 'Tidak boleh mengedit Superadmin lain.', duration: 3000);
            return;
        }

        // Paksa mapping sebelum validasi
        if ($this->edit_role_id) {
            $forced = $this->mapDeptForRole((int) $this->edit_role_id);
            if ($forced) $this->edit_department_id = $forced;
        }

        $data = $this->validate($this->editRules());

        // jaga-jaga lagi
        $forced = $this->mapDeptForRole((int) $data['edit_role_id']);
        if ($forced) $data['edit_department_id'] = $forced;

        $payload = [
            'full_name'     => $data['edit_full_name'],
            'email'         => strtolower($data['edit_email']),
            'phone_number'  => $data['edit_phone_number'] ?? null,
            'role_id'       => (int) $data['edit_role_id'],
            'department_id' => (int) $data['edit_department_id'],
        ];
        if (!empty($this->edit_password)) {
            $payload['password'] = bcrypt($this->edit_password);
        }

        $u->update($payload);

        $this->closeEdit();
        $this->dispatch('toast', type: 'success', title: 'Diupdate', message: 'User diupdate.', duration: 3000);
    }

    /* ======================== Delete (Soft Delete only) ======================== */

    public function delete(int $id): void
    {
        $u = User::where('company_id', $this->company_id)
            ->where('user_id', $id)
            ->firstOrFail();

        // 🔒 Superadmin tidak bisa menghapus Superadmin lain (kecuali dirinya sendiri, kalau mau boleh — tapi di sini juga kita larang demi aman)
        if ($this->isSuperadminRole($u->role_id)) {
            // blokir semua penghapusan akun superadmin (termasuk diri sendiri) — kalau ingin izinkan hapus dirinya sendiri, ganti kondisi ke: $u->user_id !== Auth::id()
            $this->dispatch('toast', type: 'warning', title: 'Ditolak', message: 'Tidak boleh menghapus akun Superadmin.', duration: 3000);
            return;
        }

        // Soft delete (model User menggunakan SoftDeletes)
        $u->delete();

        if ($this->editingId === $id) {
            $this->closeEdit();
        }

        $this->dispatch('toast', type: 'success', title: 'Dihapus', message: 'User dihapus (soft delete).', duration: 3000);
    }

    /* ======================== Render ======================== */

    public function render()
    {
        $users = User::with(['role', 'department'])
            ->where('company_id', $this->company_id)
            // default Eloquent: tidak menampilkan yang sudah soft-deleted
            ->when($this->search, function ($q) {
                $s = trim($this->search);
                $q->where(function ($qq) use ($s) {
                    $qq->where('full_name', 'like', "%{$s}%")
                        ->orWhere('email', 'like', "%{$s}%");
                });
            })
            ->when($this->roleFilter, fn($q) => $q->where('role_id', (int) $this->roleFilter))
            ->orderByDesc('user_id')
            ->paginate(10);

        return view('livewire.pages.superadmin.account', [
            'users'       => $users,
            'departments' => $this->departments,
        ]);
    }
}
