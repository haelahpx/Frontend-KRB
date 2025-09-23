<?php

namespace App\Livewire\Pages\Auth;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;

use App\Models\User;
use App\Models\Company;
use App\Models\Department;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

#[Layout('layouts.auth')]
#[Title('Register')]
class Register extends Component
{
    // Form fields
    public string $full_name = '';
    public string $email = '';
    public string $phone_number = '';
    public string $password = '';
    public string $password_confirmation = '';

    // Selects
    public ?int $company_id = null;
    public ?int $department_id = null;

    // Options to render in <select>
    public array $companies = [];
    public array $departments = [];

    public function mount(): void
    {
        if (Auth::check()) {
            redirect()->route('home')->send();
        }

        // Pakai company_name sebagai label, alias ke 'name' agar view tetap sama
        $this->companies = Company::query()
            ->orderBy('company_name')
            ->get(['id', 'company_name as name'])
            ->map(fn ($c) => ['id' => (int) $c->id, 'name' => (string) $c->name])
            ->toArray();

        $this->departments = [];
    }

    // Saat company berubah -> load departments milik company tsb
    public function updatedCompanyId($value): void
    {
        $this->department_id = null;

        if (! $value) {
            $this->departments = [];
            return;
        }

        $this->departments = Department::query()
            ->where('company_id', $value)
            ->orderBy('department_name')
            ->get(['id', 'department_name as name'])
            ->map(fn ($d) => ['id' => (int) $d->id, 'name' => (string) $d->name])
            ->toArray();
    }

    protected function rules(): array
    {
        return [
            'full_name'        => ['required','string','min:3'],
            'email'            => ['required','email','max:255','unique:users,email'],
            'phone_number'     => ['nullable','string','max:30'], // ubah ke 'required' kalau wajib
            'password'         => ['required','confirmed','min:6'],
            'company_id'       => ['nullable','exists:companies,id'],   // ubah ke 'required' kalau wajib
            'department_id'    => ['nullable','exists:departments,id'], // ubah ke 'required' kalau wajib
        ];
    }

    public function register()
    {
        $data = $this->validate();

        $user = User::create([
            'full_name'     => $data['full_name'],
            'email'         => Str::lower($data['email']),
            'phone_number'  => $data['phone_number'] ?: null,
            'password'      => $data['password'], // auto-hash via casts di model User
            'company_id'    => $this->company_id,
            'department_id' => $this->department_id,
        ]);

        Auth::login($user, remember: true);
        session()->regenerate();

        $this->dispatch('toast', type:'success', title:'Success!', message:'Account created successfully.', duration:4000);

        return redirect()->route('home');
    }

    public function render()
    {
        return view('livewire.pages.auth.register');
    }
}
