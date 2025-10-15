<?php

namespace App\Livewire\Pages\User;

use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;

#[Layout('layouts.app')]
#[Title('BookVehicle')]

class Bookvehicle extends Component
{
    use WithFileUploads;

    // form fields (sesuai GForm)
    public $name;
    public $department_id;
    public $pukul; // optional combined time label, we keep time fields separately
    public $start_time;
    public $end_time;
    public $date_from;
    public $date_to;
    public $purpose; // Keperluan (short)
    public $destination; // Tujuan Lokasi
    public $odd_even_area = 'no'; // 'odd', 'even', 'no'
    public $jenis_keperluan = null; // e.g. 'dinas', 'kunjungan', etc.
    public $agree_terms = false;
    public $has_sim_a = false; // konfirmasi SIM A

    // files (before/after)
    public $photo_before;
    public $photo_after;

    // vehicles loaded (if exists)
    public $vehicle_id;
    public $vehicles;
    public $hasVehicles = false;

    protected function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'department_id' => 'required|integer',
            'date_from' => 'required|date',
            'date_to' => 'required|date|after_or_equal:date_from',
            'start_time' => 'required',
            'end_time' => 'nullable',
            'destination' => 'required|string|max:255',
            'purpose' => 'required|string|max:500',
            'jenis_keperluan' => ['nullable', 'string', 'max:100'],
            'odd_even_area' => ['nullable', Rule::in(['no', 'odd', 'even'])],
            'agree_terms' => 'accepted',
            'has_sim_a' => 'accepted',
            'vehicle_id' => 'nullable|integer',
            'photo_before' => 'nullable|image|max:5120', // max 5MB
            'photo_after' => 'nullable|image|max:5120',
        ];
    }

    // options for jenis keperluan (adjust jika mau)
    public $jenisOptions = [
        'dinas' => 'Dinas',
        'kunjungan' => 'Kunjungan',
        'logistik' => 'Logistik / Pengiriman',
        'lainnya' => 'Lainnya',
    ];

    public function mount()
    {
        // load vehicles if table exists
        if (Schema::hasTable('vehicles')) {
            $this->hasVehicles = true;
            $this->vehicles = DB::table('vehicles')->where('company_id', auth()->user()->company_id ?? 1)->get();
        } else {
            $this->hasVehicles = false;
            $this->vehicles = collect();
        }

        // load departments if exists (for select)
        if (Schema::hasTable('departments')) {
            $this->departments = DB::table('departments')->where('company_id', auth()->user()->company_id ?? 1)->get();
        } else {
            $this->departments = collect();
        }
    }

    public function updatedPhotoBefore()
    {
        $this->validateOnly('photo_before');
    }

    public function updatedPhotoAfter()
    {
        $this->validateOnly('photo_after');
    }

    public function submit()
    {
        $this->validate();

        // store uploaded images temporarily (if any) to storage/app/public/tmp_bookings
        $storedBefore = null;
        $storedAfter = null;
        if ($this->photo_before) {
            $storedBefore = $this->photo_before->store('tmp_bookings', 'public');
        }
        if ($this->photo_after) {
            $storedAfter = $this->photo_after->store('tmp_bookings', 'public');
        }

        // assemble draft
        $draft = [
            'name' => $this->name,
            'department_id' => $this->department_id,
            'date_from' => $this->date_from,
            'date_to' => $this->date_to,
            'start_time' => $this->start_time,
            'end_time' => $this->end_time,
            'destination' => $this->destination,
            'purpose' => $this->purpose,
            'jenis_keperluan' => $this->jenis_keperluan,
            'odd_even_area' => $this->odd_even_area,
            'vehicle_id' => $this->vehicle_id,
            'photo_before' => $storedBefore,
            'photo_after' => $storedAfter,
            'has_sim_a' => $this->has_sim_a ? 1 : 0,
            'agree_terms' => $this->agree_terms ? 1 : 0,
            'saved_at' => now()->toDateTimeString(),
        ];

        $drafts = session('bookvehicle.drafts', []);
        $drafts[] = $draft;
        session(['bookvehicle.drafts' => $drafts]);

        session()->flash('success', 'Booking disimpan sebagai draft (session).');

        $this->resetForm();
    }

    public function resetForm()
    {
        $this->name = null;
        $this->department_id = null;
        $this->start_time = null;
        $this->end_time = null;
        $this->date_from = null;
        $this->date_to = null;
        $this->purpose = null;
        $this->destination = null;
        $this->jenis_keperluan = null;
        $this->odd_even_area = 'no';
        $this->agree_terms = false;
        $this->has_sim_a = false;
        $this->vehicle_id = null;
        $this->photo_before = null;
        $this->photo_after = null;
    }

    public function clearDrafts()
    {
        session()->forget('bookvehicle.drafts');
        session()->flash('success', 'Semua draft dihapus.');
    }

    public function render()
    {
        return view('livewire.pages.user.bookvehicle', [
            'departments' => $this->departments ?? collect()
        ]);
    }
}
