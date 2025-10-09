<?php

namespace App\Livewire\Pages\Admin;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use App\Models\User;
use App\Models\Role;

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
    public ?int $role_id = null;

    /** Edit (modal) */
    public bool $modalEdit = false;
    public ?int $editingId = null;
    public string $edit_full_name = '';
    public string $edit_email = '';
    public ?string $edit_phone_number = null;
    public ?string $edit_password = null; // optional
    public ?int $edit_role_id = null;

    /** Derived from auth (locked) */
    public int $company_id;
    public int $department_id;
    public string $company_name = '-';
    public string $department_name = '-';

    /** Options */
    /** @var array<array{id:int,name:string}> */
    public array $roles = [];

    protected $casts = [
        'modalEdit' => 'bool',
    ];

    /* ======================== Lifecycle ======================== */

    public function mount(): void
    {
        $auth = Auth::user()->loadMissing(['company', 'department']);

        $this->company_id = (int) ($auth->company_id ?? 0);
        $this->department_id = (int) ($auth->department_id ?? 0);
        $this->company_name = optional($auth->company)->company_name ?? '-';
        $this->department_name = optional($auth->department)->department_name ?? '-';

        // Roles dropdown: hide superadmin & receptionist
        $this->roles = Role::query()
            ->whereNotIn('name', ['superadmin', 'receptionist'])
            ->orderBy('name')
            ->get(['role_id as id', 'name'])
            ->map(fn($r) => ['id' => (int) $r->id, 'name' => (string) $r->name])
            ->toArray();
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }
    public function updatingRoleFilter(): void
    {
        $this->resetPage();
    }

    /* ======================== Validation ======================== */

    protected function createRules(): array
    {
        return [
            'full_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'unique:users,email'],
            'phone_number' => ['nullable', 'string', 'max:30'],
            'password' => ['required', 'string', 'min:6'],
            'role_id' => [
                'required',
                'integer',
                // Block receptionist & superadmin server-side
                Rule::exists('roles', 'role_id')
                    ->where(fn($q) => $q->whereNotIn('name', ['receptionist', 'superadmin'])),
            ],
        ];
    }

    protected function editRules(): array
    {
        $ignore = $this->editingId ? ",{$this->editingId},user_id" : '';
        return [
            'edit_full_name' => ['required', 'string', 'max:255'],
            'edit_email' => ['required', 'email', "unique:users,email{$ignore}"],
            'edit_phone_number' => ['nullable', 'string', 'max:30'],
            'edit_role_id' => [
                'required',
                'integer',
                // Block receptionist & superadmin server-side
                Rule::exists('roles', 'role_id')
                    ->where(fn($q) => $q->whereNotIn('name', ['receptionist', 'superadmin'])),
            ],
            // edit_password optional
        ];
    }

    /* ======================== Create ======================== */

    public function store(): void
    {
        $data = $this->validate($this->createRules());

        User::create([
            'full_name' => $data['full_name'],
            'email' => strtolower($data['email']),
            'phone_number' => $data['phone_number'] ?? null,
            'password' => bcrypt($this->password),
            'role_id' => (int) $data['role_id'],
            'company_id' => $this->company_id,    // lock to auth company
            'department_id' => $this->department_id, // lock to auth department
        ]);

        // ✅ Best UX: clear inputs, errors, and go back to first page (freshest on top)
        $this->resetCreateForm();
        $this->dispatch('toast', type: 'success', title: 'Dibuat', message: 'User dibuat.', duration: 3000);
        $this->resetPage(); // rerender & ensure the latest item is visible on page 1
    }

    private function resetCreateForm(): void
    {
        // clear form & errors
        $this->reset([
            'full_name',
            'email',
            'phone_number',
            'password',
            'role_id',
        ]);
        $this->resetValidation();
    }

    /* ======================== Edit ======================== */

    public function openEdit(int $id): void
    {
        $this->resetErrorBag();
        $this->resetValidation();

        $u = User::with(['role', 'department'])
            ->where('company_id', $this->company_id)
            ->where('department_id', $this->department_id)   // lock dept
            ->where('user_id', $id)
            ->firstOrFail();

        $targetRole = strtolower($u->role->name ?? '');

        // Rule: admin/superadmin can only be edited by themselves
        if (in_array($targetRole, ['admin', 'superadmin'], true) && $u->user_id !== Auth::id()) {
            $this->dispatch('toast', type: 'warning', title: 'Ditolak', message: 'Anda tidak bisa mengedit akun Admin lain.', duration: 4000);
            return;
        }

        $this->editingId = (int) $u->user_id;
        $this->edit_full_name = (string) $u->full_name;
        $this->edit_email = (string) $u->email;
        $this->edit_phone_number = $u->phone_number;
        $this->edit_role_id = $u->role_id;
        $this->edit_password = null;

        $this->modalEdit = true;
    }

    public function closeEdit(): void
    {
        $this->modalEdit = false;
        $this->editingId = null;
        $this->edit_full_name = '';
        $this->edit_email = '';
        $this->edit_phone_number = null;
        $this->edit_role_id = null;
        $this->edit_password = null;

        $this->resetErrorBag();
        $this->resetValidation();
    }

    public function update(): void
    {
        if (!$this->editingId)
            return;

        $u = User::with('role')
            ->where('company_id', $this->company_id)
            ->where('department_id', $this->department_id)
            ->where('user_id', $this->editingId)
            ->firstOrFail();

        $targetRole = strtolower($u->role->name ?? '');

        // Rule: admin/superadmin can only be updated by themselves
        if (in_array($targetRole, ['admin', 'superadmin'], true) && $u->user_id !== Auth::id()) {
            $this->dispatch('toast', type: 'warning', title: 'Ditolak', message: 'Anda tidak bisa mengedit akun Admin lain.', duration: 4000);
            return;
        }

        $data = $this->validate($this->editRules());

        $payload = [
            'full_name' => $data['edit_full_name'],
            'email' => strtolower($data['edit_email']),
            'phone_number' => $data['edit_phone_number'] ?? null,
            'role_id' => (int) $data['edit_role_id'],
            // department_id remains locked
        ];

        if (!empty($this->edit_password)) {
            $payload['password'] = bcrypt($this->edit_password);
        }

        $u->update($payload);

        $this->closeEdit();
        $this->dispatch('toast', type: 'success', title: 'Diupdate', message: 'User diupdate.', duration: 3000);
    }

    /* ======================== Delete ======================== */

    public function delete(int $id): void
    {
        $u = User::with('role')
            ->where('company_id', $this->company_id)
            ->where('department_id', $this->department_id)   // lock dept
            ->where('user_id', $id)
            ->first();

        if (!$u) {
            $this->dispatch('toast', type: 'warning', title: 'Tidak ditemukan', message: 'User tidak ditemukan.', duration: 3000);
            return;
        }

        $roleName = strtolower($u->role->name ?? '');

        // Block deleting admin/superadmin
        if (in_array($roleName, ['admin', 'superadmin'], true)) {
            $this->dispatch('toast', type: 'warning', title: 'Ditolak', message: 'Akun Admin tidak boleh dihapus.', duration: 4000);
            return;
        }

        // Block self-delete (optional but safer)
        if ($u->user_id === Auth::id()) {
            $this->dispatch('toast', type: 'warning', title: 'Ditolak', message: 'Tidak boleh menghapus akun sendiri.', duration: 4000);
            return;
        }

        $u->delete();

        if ($this->editingId === $id) {
            $this->closeEdit();
        }

        $this->dispatch('toast', type: 'success', title: 'Dihapus', message: 'User dihapus.', duration: 3000);
    }

    /* ======================== Render ======================== */

    public function render()
    {
        $users = User::query()
            ->with(['role', 'department'])
            ->where('company_id', $this->company_id)
            ->where('department_id', $this->department_id)
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
            ->select('users.*') // important for pagination & model hydration
            ->paginate(10);

        return view('livewire.pages.admin.usermanagement', [
            'users' => $users,
        ]);
    }
}
