<?php

namespace App\Livewire\Pages\Superadmin;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Role;
use App\Models\Department;

#[Layout('layouts.superadmin')]
#[Title('Account Settings')]
class Account extends Component
{
    use WithPagination;

    // filters & table
    public string $search = '';
    public string $roleFilter = '';

    // form
    public ?int $userId = null;
    public string $full_name = '';
    public string $email = '';
    public ?string $phone_number = null;
    public ?string $password = null;          // only for create or when changing
    public ?int $role_id = null;
    public ?int $department_id = null;

    // derived from auth
    public int $company_id;
    public string $company_name = '-';

    // options
    public array $roles = [];
    /** @var \Illuminate\Support\Collection */
    public $departments;

    // UI state
    public bool $isEdit = false;
    public bool $showModal = false; // <— controls modal

    protected function rules(): array
    {
        // unique email by user_id PK
        $ignoreId = $this->userId ?? 'NULL';
        return [
            'full_name'     => ['required', 'string', 'max:255'],
            'email'         => ['required', 'email', "unique:users,email,{$ignoreId},user_id"],
            'phone_number'  => ['nullable', 'string', 'max:30'],
            'role_id'       => ['required', 'integer', 'exists:roles,role_id'],
            'department_id' => ['required', 'integer', 'exists:departments,department_id'],
            'company_id'    => ['required', 'integer', 'exists:companies,company_id'],
        ];
    }

    public function mount(): void
    {
        $auth = Auth::user()->loadMissing('company');
        $this->company_id   = (int) $auth->company_id;
        $this->company_name = optional($auth->company)->company_name ?? '-';

        $this->roles = Role::orderBy('name')
            ->get(['role_id as id', 'name'])
            ->toArray();

        $this->loadDepartments();
    }

    public function updatingSearch() { $this->resetPage(); }
    public function updatingRoleFilter() { $this->resetPage(); }

    private function loadDepartments(): void
    {
        $this->departments = Department::where('company_id', $this->company_id)
            ->orderBy('department_name')
            ->get(['department_id', 'department_name']);
    }

    public function render()
    {
        $users = User::with(['role', 'department'])
            ->where('company_id', $this->company_id)
            ->when($this->search, function ($q) {
                $q->where(function ($qq) {
                    $qq->where('full_name', 'like', "%{$this->search}%")
                       ->orWhere('email', 'like', "%{$this->search}%");
                });
            })
            ->when($this->roleFilter, fn($q) => $q->where('role_id', $this->roleFilter))
            ->orderByDesc('user_id')
            ->paginate(10);

        return view('livewire.pages.superadmin.account', compact('users'));
    }

    /* ========= Modal Actions ========= */

    public function openCreate(): void
    {
        $this->resetForm();
        $this->isEdit = false;
        $this->showModal = true;
    }

    public function openEdit(int $id): void
    {
        $u = User::where('company_id', $this->company_id)
            ->where('user_id', $id)
            ->firstOrFail();

        $this->isEdit        = true;
        $this->userId        = $u->user_id;
        $this->full_name     = $u->full_name;
        $this->email         = $u->email;
        $this->phone_number  = $u->phone_number;
        $this->role_id       = $u->role_id;
        $this->department_id = $u->department_id;

        $this->password = null; // don't prefill
        $this->showModal = true;
    }

    public function closeModal(): void
    {
        $this->showModal = false;
    }

    /* ========= CRUD ========= */

    public function store(): void
    {
        $this->validate(array_merge($this->rules(), [
            'password' => ['required', 'string', 'min:6'],
        ]));

        User::create([
            'full_name'     => $this->full_name,
            'email'         => strtolower($this->email),
            'phone_number'  => $this->phone_number,
            'password'      => bcrypt($this->password),
            'role_id'       => $this->role_id,
            'company_id'    => $this->company_id,
            'department_id' => $this->department_id,
        ]);

        session()->flash('message', 'User created.');
        $this->closeModal();
        $this->resetForm();
    }

    public function update(): void
    {
        $this->validate();

        $u = User::where('company_id', $this->company_id)
            ->where('user_id', $this->userId)
            ->firstOrFail();

        $data = [
            'full_name'     => $this->full_name,
            'email'         => strtolower($this->email),
            'phone_number'  => $this->phone_number,
            'role_id'       => $this->role_id,
            'department_id' => $this->department_id,
        ];

        if (!empty($this->password)) {
            $data['password'] = bcrypt($this->password);
        }

        $u->update($data);

        session()->flash('message', 'User updated.');
        $this->closeModal();
        $this->resetForm();
    }

    public function delete(int $id): void
    {
        User::where('company_id', $this->company_id)
            ->where('user_id', $id)
            ->delete();

        session()->flash('message', 'User deleted.');
    }

    private function resetForm(): void
    {
        $this->userId = null;
        $this->full_name = '';
        $this->email = '';
        $this->phone_number = '';
        $this->password = null;
        $this->role_id = null;
        $this->department_id = null;
        $this->isEdit = false;
        // jangan tutup modal di sini — biar bisa dipakai openCreate/openEdit
    }
}
