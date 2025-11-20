<?php

namespace App\Livewire\Pages\Receptionist;

use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Models\Guestbook as GuestbookModel;

// TODO: Ganti baris ini dengan Model milik Anda sendiri (Misal: App\Models\Divisi, App\Models\Pegawai)
use App\Models\Department; 
use App\Models\User;

#[Layout('layouts.receptionist')]
#[Title('GuestBook')]
class Guestbook extends Component
{
    // Form fields yang diisi user
    public $name;
    public $phone_number;
    public $instansi;
    public $keperluan;
    
    // Field baru (Nullable / Optional)
    public $department_id;
    public $user_id;

    // Data Lists untuk Dropdown
    public $departments_list = [];
    public $users_list = [];

    // Field internal (diisi otomatis)
    public $date;
    public $jam_in;
    public $petugas_penjaga;

    // ---- Compatibility props ----
    public int $perLatest  = 5; 
    public int $perEntries = 5; 
    public $filter_date    = null;
    public string $q       = '';   
    public bool $showEdit  = false;
    public ?int $editId    = null; 
    public array $edit = [          
        'date'            => null,
        'jam_in'          => null,
        'jam_out'         => null,
        'name'            => null,
        'phone_number'    => null,
        'instansi'        => null,
        'keperluan'       => null,
        'petugas_penjaga' => null,
        'department_id'   => null,
        'user_id'         => null,
    ];

    public function mount(): void
    {
        $this->date = $this->date ?: now()->format('Y-m-d');

        // Load list departemen saat halaman dibuka (berdasarkan company user yg login)
        if ($compId = $this->companyId()) {
            // SESUAIKAN: Pastikan 'Department' di bawah ini match dengan nama Class model yang Anda import di atas
            $this->departments_list = Department::where('company_id', $compId)->get();
        } else {
            $this->departments_list = [];
        }
    }

    // Hook: Ketika department_id berubah, load user yang sesuai
    public function updatedDepartmentId($value)
    {
        // Reset user yang dipilih sebelumnya
        $this->user_id = null; 

        if ($value) {
            // SESUAIKAN: Pastikan 'User' di bawah ini match dengan nama Class model yang Anda import di atas
            // Pastikan juga kolom 'department_id' sesuai dengan struktur tabel user Anda
            $this->users_list = User::where('department_id', $value)->get();
        } else {
            $this->users_list = [];
        }
    }

    protected function rules(): array
    {
        return [
            'name'          => ['required', 'string', 'max:255'],
            'phone_number'  => ['nullable', 'string', 'max:50'],
            'instansi'      => ['nullable', 'string', 'max:255'],
            'keperluan'     => ['nullable', 'string', 'max:255'],
            // SESUAIKAN: Ganti 'departments' dan 'users' di bawah ini dengan nama TABEL di database Anda
            'department_id' => ['nullable', 'exists:departments,id'], 
            'user_id'       => ['nullable', 'exists:users,id'],       
        ];
    }

    public function updatedDate($v): void  { $this->date   = $this->normalizeDate($v); }
    public function updatedJamIn($v): void { $this->jam_in = $this->normalizeTime($v); }

    private function normalizeDate($v): ?string
    {
        if (!$v) return null;
        try { return Carbon::parse(str_replace('/', '-', $v))->format('Y-m-d'); }
        catch (\Throwable) { return $v; }
    }

    private function normalizeTime($v, bool $nullable = false): ?string
    {
        if ($nullable && $v === '') return null;
        if (!$v) return null;
        try { return Carbon::parse($v)->format('H:i'); }
        catch (\Throwable) { return $v; }
    }

    private function companyId(): ?int
    {
        return Auth::user()?->company_id;
    }

    public function save(): void
    {
        $now = Carbon::now(config('app.timezone', 'Asia/Jakarta'));
        $this->date   = $now->toDateString();
        $this->jam_in = $now->format('H:i');

        $user = Auth::user();
        $this->petugas_penjaga = $user?->full_name ?? $user?->name ?? 'Petugas Receptionist';

        $this->validate();

    
        // Reset form
        $this->reset(['name', 'phone_number', 'instansi', 'keperluan', 'department_id', 'user_id']);
        // Reset list user karena dept kosong lagi
        $this->users_list = []; 

        $this->dispatch('$refresh');
        $this->dispatch('toast', type: 'success', title: 'Ditambah', message: 'Guest ditambah.', duration: 3000);
        session()->flash('saved', true);
    }

    public function render()
    {
        return view('livewire.pages.receptionist.guestbook');
    }
}