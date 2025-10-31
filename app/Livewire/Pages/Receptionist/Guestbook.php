<?php

namespace App\Livewire\Pages\Receptionist;

use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Models\Guestbook as GuestbookModel;

#[Layout('layouts.receptionist')]
#[Title('GuestBook')]
class Guestbook extends Component
{
    // ---- Form fields (unchanged) ----
    public $date, $jam_in, $name, $phone_number, $instansi, $keperluan, $petugas_penjaga;

    // ---- Compatibility props so old Livewire snapshots don't crash (unused here) ----
    public int $perLatest  = 5;   // compat only
    public int $perEntries = 5;   // compat only
    public $filter_date    = null; // compat only
    public string $q       = '';   // compat only
    public bool $showEdit  = false; // compat only
    public ?int $editId    = null;  // compat only
    public array $edit = [          // compat only
        'date'            => null,
        'jam_in'          => null,
        'jam_out'         => null,
        'name'            => null,
        'phone_number'    => null,
        'instansi'        => null,
        'keperluan'       => null,
        'petugas_penjaga' => null,
    ];

    public function mount(): void
    {
        $this->date = $this->date ?: now()->format('Y-m-d');
    }

    protected function rules(): array
    {
        return [
            'date'            => ['required', 'date'],
            'jam_in'          => ['required', 'date_format:H:i'],
            'name'            => ['required', 'string', 'max:255'],
            'phone_number'    => ['nullable', 'string', 'max:50'],
            'instansi'        => ['nullable', 'string', 'max:255'],
            'keperluan'       => ['nullable', 'string', 'max:255'],
            'petugas_penjaga' => ['required', 'string', 'max:255'],
        ];
    }

    // Normalizers (unchanged)
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

    // CREATE (unchanged)
    public function save(): void
    {
        $this->date   = $this->normalizeDate($this->date);
        $this->jam_in = $this->normalizeTime($this->jam_in);
        $this->validate();

        GuestbookModel::create([
            'company_id'       => $this->companyId(),
            'date'             => $this->date,
            'jam_in'           => $this->jam_in,
            'jam_out'          => null,
            'name'             => $this->name,
            'phone_number'     => $this->phone_number,
            'instansi'         => $this->instansi,
            'keperluan'        => $this->keperluan,
            'petugas_penjaga'  => $this->petugas_penjaga,
        ]);

        $this->reset(['jam_in', 'name', 'phone_number', 'instansi', 'keperluan', 'petugas_penjaga']);
        $this->dispatch('$refresh');
        $this->dispatch('toast', type: 'success', title: 'Ditambah', message: 'Guest ditambah.', duration: 3000);
        session()->flash('saved', true);
    }

    public function render()
    {
        return view('livewire.pages.receptionist.guestbook');
    }
}
