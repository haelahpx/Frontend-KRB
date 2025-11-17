<?php

namespace App\Livewire\Pages\Admin;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use App\Models\User;
use App\Models\Role;
use App\Models\Department;

#[Layout('layouts.admin')]
#[Title('Admin - User Management')]
class Usermanagement extends Component
{
    use WithPagination;

    protected string $paginationTheme = 'tailwind';

    /** Filters */
    public string $search = '';
    public string $roleFilter = '';

    /** Create form */
    public string $full_name = '';
    public string $email = '';
    public ?string $phone_number = null;
    public ?string $password = null;
    public ?string $role_key = null;

    /** Edit (modal) */
    public bool $modalEdit = false;
    public ?int $editingId = null;
    public string $edit_full_name = '';
    public string $edit_email = '';
    public ?string $edit_phone_number = null;
    public ?string $edit_password = null; // optional
    public ?string $edit_role_key = null;

    /** Derived from auth */
    public int $company_id;
    public ?int $primary_department_id = null;
    public string $company_name = '-';
    public string $department_name = '-'; // label yg mengikuti selection

    /** Department switcher */
    /** @var array<int, array{id:int,name:string}> */
    public array $departmentOptions = []; // renamed variable
    public ?int $selected_department_id = null;  // pilihan aktif
    public bool $showSwitcher = false;  // control visibility of the department switcher

    /** Options */
    /** @var array<int, array{id:int,name:string}> */
    public array $roles = [];
    public array $roleOptions = [];

    protected $casts = [
        'modalEdit' => 'bool',
    ];

    /* ======================== Lifecycle ======================== */

    public function mount(): void
    {
        $auth = Auth::user()->loadMissing(['company', 'department']);

        $this->company_id = (int)($auth->company_id ?? 0);
        $this->primary_department_id = $auth->department_id ?: null;
        $this->company_name = optional($auth->company)->company_name ?? '-';

        // load switcher options
        $this->loadUserDepartments();

        // pilih default: primary -> first option -> null
        $this->selected_department_id = $this->primary_department_id
            ?: ($this->departmentOptions[0]['id'] ?? null);

        $this->department_name = $this->resolveDeptName($this->selected_department_id)
            ?: (optional($auth->department)->department_name ?? '-');

        // Roles dropdown: hide superadmin & receptionist
        $this->roles = Role::query()
            ->whereNotIn('name', ['superadmin', 'receptionist'])
            ->orderBy('name')
            ->get(['role_id as id', 'name'])
            ->map(fn($r) => ['id' => (int) $r->id, 'name' => (string) $r->name])
            ->toArray();

        $options = [];
        foreach ($this->roles as $role) {
            if (strtolower($role['name']) === 'user') {
                $options[] = ['key' => $role['id'] . '_no', 'name' => 'User'];
                $options[] = ['key' => $role['id'] . '_yes', 'name' => 'User (Agent)'];
            } else {
                $options[] = ['key' => $role['id'] . '_no', 'name' => $role['name']];
            }
        }
        $this->roleOptions = $options;
    }

    protected function loadUserDepartments(): void
    {
        $user = Auth::user();

        $rows = DB::table('user_departments as ud')
            ->join('departments as d', 'd.department_id', '=', 'ud.department_id')
            ->where('ud.user_id', $user->user_id)
            ->orderBy('d.department_name')
            ->get(['d.department_id as id', 'd.department_name as name']);

        $this->departmentOptions = $rows->map(fn($r) => [
            'id' => (int)$r->id,
            'name' => (string)$r->name
        ])->values()->all();

        $this->showSwitcher = true; // Show switcher if departments are loaded

        // fallback jika pivot kosong namun user punya primary
        if (empty($this->departmentOptions) && $this->primary_department_id) {
            $name = Department::where('department_id', $this->primary_department_id)->value('department_name') ?? 'Unknown';
            $this->departmentOptions = [
                ['id' => (int)$this->primary_department_id, 'name' => (string)$name]
            ];

            $this->showSwitcher = false; // Hide switcher if no department options are available
        }
    }

    protected function resolveDeptName(?int $deptId): string
    {
        if (!$deptId) return '-';
        foreach ($this->departmentOptions as $opt) {
            if ($opt['id'] === (int)$deptId) return $opt['name'];
        }
        return Department::where('department_id', $deptId)->value('department_name') ?? '-';
    }

    public function resetToPrimaryDepartment(): void
    {
        if ($this->primary_department_id) {
            $this->selected_department_id = $this->primary_department_id;
            $this->department_name = $this->resolveDeptName($this->selected_department_id);
            $this->resetPage();
        }
    }

    // Livewire 3 naming tolerance
    public function updatedSelectedDepartment_id(): void { $this->updatedSelectedDepartmentId(); }
    public function updatedSelectedDepartmentId(): void
    {
        $allowed = collect($this->departmentOptions)->pluck('id')->all();
        $id = (int) $this->selected_department_id;

        if (!in_array($id, $allowed, true)) {
            $this->selected_department_id = $this->primary_department_id ?: ($this->departmentOptions[0]['id'] ?? null);
            $id = (int)$this->selected_department_id;
        }
        $this->department_name = $this->resolveDeptName($id);
        $this->resetPage();
    }

    public function updatingSearch(): void { $this->resetPage(); }
    public function updatingRoleFilter(): void { $this->resetPage(); }

    /* ======================== Validation ======================== */

    protected function createRules(): array
    {
        return [
            'full_name'    => ['required', 'string', 'max:255'],
            'email'        => ['required', 'email', 'unique:users,email,NULL,user_id,deleted_at,NULL'],
            'phone_number' => ['nullable', 'string', 'max:30'],
            'password'     => ['required', 'string', 'min:6'],
            'role_key'      => [
                'required',
                'string',
                Rule::in(array_column($this->roleOptions, 'key'))
            ],
        ];
    }

    protected function editRules(): array
    {
        $id = $this->editingId ?? 'NULL';
        return [
            'edit_full_name'    => ['required', 'string', 'max:255'],
            'edit_email'        => ["required", "email", "unique:users,email,{$id},user_id,deleted_at,NULL"],
            'edit_phone_number' => ['nullable', 'string', 'max:30'],
            'edit_role_key'      => [
                'required',
                'string',
                Rule::in(array_column($this->roleOptions, 'key'))
            ],
        ];
    }

    /* ======================== Create ======================== */

    public function store(): void
    {
        $data = $this->validate($this->createRules());

        // pakai departemen TERPILIH (fallback primary)
        $deptId = $this->selected_department_id ?: $this->primary_department_id;

        [$roleId, $isAgent] = explode('_', $data['role_key']);

        User::create([
            'full_name'     => $data['full_name'],
            'email'         => strtolower($data['email']),
            'phone_number'  => $data['phone_number'] ?? null,
            'password'      => $this->password,
            'role_id'       => (int) $roleId,
            'is_agent'      => $isAgent,
            'company_id'    => $this->company_id,
            'department_id' => $deptId,
        ]);

        $this->resetCreateForm();
        $this->dispatch('toast', type: 'success', title: 'Dibuat', message: 'User dibuat.', duration: 3000);
        $this->resetPage();
    }

    private function resetCreateForm(): void
    {
        $this->reset([
            'full_name',
            'email',
            'phone_number',
            'password',
            'role_key',
        ]);
        $this->resetValidation();
    }

    /* ======================== Edit ======================== */

    public function openEdit(int $id): void
    {
        $this->resetErrorBag();
        $this->resetValidation();

        $deptId = $this->selected_department_id ?: $this->primary_department_id;

        $u = User::with(['role', 'department'])
            ->where('company_id', $this->company_id)
            ->where('department_id', $deptId) // lock ke dept terpilih
            ->where('user_id', $id)
            ->firstOrFail();

        $targetRole = strtolower($u->role->name ?? '');

        // Aturan: admin/superadmin hanya boleh edit dirinya sendiri
        if (in_array($targetRole, ['admin', 'superadmin'], true) && $u->user_id !== Auth::id()) {
            $this->dispatch('toast', type: 'warning', title: 'Ditolak', message: 'Anda tidak bisa mengedit akun Admin lain.', duration: 4000);
            return;
        }

        $this->editingId         = (int) $u->user_id;
        $this->edit_full_name    = (string) $u->full_name;
        $this->edit_email        = (string) $u->email;
        $this->edit_phone_number = $u->phone_number;
        $this->edit_role_key     = $u->role_id . '_' . $u->is_agent;
        $this->edit_password     = null;

        $this->modalEdit = true;
    }

    public function closeEdit(): void
    {
        $this->modalEdit        = false;
        $this->editingId        = null;
        $this->edit_full_name   = '';
        $this->edit_email       = '';
        $this->edit_phone_number= null;
        $this->edit_role_key    = null;
        $this->edit_password    = null;

        $this->resetErrorBag();
        $this->resetValidation();
    }

    public function update(): void
    {
        if (!$this->editingId) return;

        $deptId = $this->selected_department_id ?: $this->primary_department_id;

        $u = User::with('role')
            ->where('company_id', $this->company_id)
            ->where('department_id', $deptId)
            ->where('user_id', $this->editingId)
            ->firstOrFail();

        $targetRole = strtolower($u->role->name ?? '');

        if (in_array($targetRole, ['admin', 'superadmin'], true) && $u->user_id !== Auth::id()) {
            $this->dispatch('toast', type: 'warning', title: 'Ditolak', message: 'Anda tidak bisa mengedit akun Admin lain.', duration: 4000);
            return;
        }

        $data = $this->validate($this->editRules());

        [$roleId, $isAgent] = explode('_', $data['edit_role_key']);

        $payload = [
            'full_name'    => $data['edit_full_name'],
            'email'        => strtolower($data['edit_email']),
            'phone_number' => $data['edit_phone_number'] ?? null,
            'role_id'      => (int) $roleId,
            'is_agent'     => $isAgent,
        ];

        if (!empty($this->edit_password)) {
            $payload['password'] = $this->edit_password; // auto-hash by model cast
        }

        $u->update($payload);

        $this->closeEdit();
        $this->dispatch('toast', type: 'success', title: 'Diupdate', message: 'User diupdate.', duration: 3000);
    }

    /* ======================== Delete (Soft Delete) ======================== */

    public function delete(int $id): void
    {
        $deptId = $this->selected_department_id ?: $this->primary_department_id;

        $u = User::with('role')
            ->where('company_id', $this->company_id)
            ->where('department_id', $deptId)
            ->where('user_id', $id)
            ->first();

        if (!$u) {
            $this->dispatch('toast', type: 'warning', title: 'Tidak ditemukan', message: 'User tidak ditemukan.', duration: 3000);
            return;
        }

        $roleName = strtolower($u->role->name ?? '');

        if (in_array($roleName, ['admin', 'superadmin'], true)) {
            $this->dispatch('toast', type: 'warning', title: 'Ditolak', message: 'Akun Admin tidak boleh dihapus.', duration: 4000);
            return;
        }

        if ($u->user_id === Auth::id()) {
            $this->dispatch('toast', type: 'warning', title: 'Ditolak', message: 'Tidak boleh menghapus akun sendiri.', duration: 4000);
            return;
        }

        $u->delete();

        if ($this->editingId === $id) {
            $this->closeEdit();
        }

        $this->dispatch('toast', type: 'success', title: 'Dihapus', message: 'User dihapus (soft delete).', duration: 3000);
    }

    /* ===== Opsional: restore & force delete di Trash ===== */

    public function restore(int $id): void
    {
        $deptId = $this->selected_department_id ?: $this->primary_department_id;

        $u = User::onlyTrashed()
            ->where('company_id', $this->company_id)
            ->where('department_id', $deptId)
            ->where('user_id', $id)
            ->first();

        if (!$u) {
            $this->dispatch('toast', type: 'warning', title: 'Tidak ditemukan', message: 'User tidak ditemukan di Trash.', duration: 3000);
            return;
        }

        $u->restore();
        $this->dispatch('toast', type: 'success', title: 'Dipulihkan', message: 'User dipulihkan.', duration: 3000);
    }

    public function destroy(int $id): void
    {
        $deptId = $this->selected_department_id ?: $this->primary_department_id;

        $u = User::onlyTrashed()
            ->where('company_id', $this->company_id)
            ->where('department_id', $deptId)
            ->where('user_id', $id)
            ->first();

        if (!$u) {
            $this->dispatch('toast', type: 'warning', title: 'Tidak ditemukan', message: 'User tidak ditemukan di Trash.', duration: 3000);
            return;
        }

        $u->forceDelete();
        $this->dispatch('toast', type: 'success', title: 'Dihapus Permanen', message: 'User dihapus permanen.', duration: 3000);
    }

    /* ======================== Render ======================== */

    public function render()
    {
        $deptId = $this->selected_department_id ?: $this->primary_department_id;

        $users = User::query()
            ->with(['role', 'department'])
            ->where('company_id', $this->company_id)
            ->when($deptId, fn($q) => $q->where('department_id', $deptId))
            ->whereHas('role', fn($q) => $q->where('name', '!=', 'superadmin'))
            ->leftJoin('roles', 'roles.role_id', '=', 'users.role_id')
            ->when($this->search, function ($q) {
                $s = trim($this->search);
                $q->where(function ($qq) use ($s) {
                    $qq->where('users.full_name', 'like', "%{$s}%")
                       ->orWhere('users.email', 'like', "%{$s}%");
                });
            })
            ->when($this->roleFilter, fn($q) => $q->where('users.role_id', (int) $this->roleFilter))
            ->orderByRaw("CASE WHEN LOWER(roles.name) = 'admin' THEN 0 ELSE 1 END")
            ->orderByDesc('users.user_id')
            ->select('users.*')
            ->paginate(10);

        return view('livewire.pages.admin.usermanagement', [
            'users' => $users,
        ]);
    }
}
