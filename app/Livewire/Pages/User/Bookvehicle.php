<?php

namespace App\Livewire\Pages\User;

use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class Bookvehicle extends Component
{
    use WithFileUploads;

    // form fields
    public $name;
    public $department_id;
    public $start_time;
    public $end_time;
    public $date_from;
    public $date_to;
    public $purpose;
    public $destination;
    public $odd_even_area = 'no';
    public $jenis_keperluan = null;
    public $agree_terms = false;
    public $has_sim_a = false;

    // optional vehicle
    public $vehicle_id;
    public $vehicles;
    public $hasVehicles = false;

    // uploads
    public $photo_before;
    public $photo_after;

    // collections
    public $departments;

    // sidebar data
    public $availability = [];
    public $recentBookings = [];

    public function mount()
    {
        // load vehicles if exists
        if (Schema::hasTable('vehicles')) {
            $this->hasVehicles = true;
            $this->vehicles = DB::table('vehicles')
                ->where('company_id', auth()->user()->company_id ?? 1)
                ->get();
        } else {
            $this->hasVehicles = false;
            $this->vehicles = collect();
        }

        // load departments if exists
        if (Schema::hasTable('departments')) {
            $this->departments = DB::table('departments')
                ->where('company_id', auth()->user()->company_id ?? 1)
                ->get();
        } else {
            $this->departments = collect();
        }

        // prepare sidebar
        $this->loadAvailability();
        $this->loadRecentBookings();
    }

    // Avoid constant-expression problems by using a method
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
            'odd_even_area' => ['nullable', Rule::in(['no','odd','even'])],
            'agree_terms' => 'accepted',
            'has_sim_a' => 'accepted',
            'vehicle_id' => 'nullable|integer',
            'photo_before' => 'nullable|image|max:5120',
            'photo_after' => 'nullable|image|max:5120',
        ];
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

        $beforePath = null;
        $afterPath = null;

        if ($this->photo_before) {
            $beforePath = $this->photo_before->store('bookings/photos', 'public');
        }
        if ($this->photo_after) {
            $afterPath = $this->photo_after->store('bookings/photos', 'public');
        }

        // Insert to DB (adjust column names if your schema differs)
        DB::table('vehicle_bookings')->insert([
            'company_id' => auth()->user()->company_id ?? 1,
            'user_id' => auth()->id(),
            'vehicle_id' => $this->vehicle_id ?: null,
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
            'photo_before' => $beforePath,
            'photo_after' => $afterPath,
            'status' => 'pending',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        session()->flash('success', 'Booking kendaraan berhasil dikirim dan berstatus PENDING.');

        // refresh sidebar recent bookings
        $this->loadRecentBookings();

        return redirect()->route('bookingstatus');
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

    protected function loadAvailability()
    {
        if (Schema::hasTable('vehicles')) {
            $vehicles = DB::table('vehicles')
                ->where('company_id', auth()->user()->company_id ?? 1)
                ->get();

            $this->availability = $vehicles->map(function ($v) {
                $status = $v->status ?? 'available';
                // safe fallback for plate column (your DB uses plate_number)
                $plate = $v->plate_number ?? ($v->license_plate ?? '');
                $label = ($v->vehicle_name ?? ($v->name ?? 'Kendaraan')) . ($plate ? " — {$plate}" : '');
                return [
                    'id' => $v->vehicle_id ?? $v->id,
                    'label' => $label,
                    'status' => $status,
                ];
            })->toArray();
        } else {
            // fallback placeholders
            $this->availability = [
                ['id' => 1, 'label' => 'Mobil Operasional A', 'status' => 'available'],
                ['id' => 2, 'label' => 'Avanza Operasional', 'status' => 'available'],
                ['id' => 3, 'label' => 'Motor Operasional', 'status' => 'maintenance'],
            ];
        }
    }

    protected function loadRecentBookings()
    {
        if (Schema::hasTable('vehicle_bookings')) {
            $this->recentBookings = DB::table('vehicle_bookings')
                ->where('company_id', auth()->user()->company_id ?? 1)
                ->orderBy('created_at', 'desc')
                ->limit(5)
                ->get();
        } else {
            $this->recentBookings = collect([
                (object)['name' => 'Rapat Tim A', 'date_from' => now()->addDay()->toDateString(), 'start_time' => '13:00', 'status' => 'pending'],
                (object)['name' => 'Survey Lokasi', 'date_from' => now()->addDays(2)->toDateString(), 'start_time' => '09:00', 'status' => 'pending'],
            ]);
        }
    }

    public function render()
    {
        return view('livewire.pages.user.bookvehicle', [
            'departments' => $this->departments,
            'vehicles' => $this->vehicles,
            'availability' => $this->availability,
            'recentBookings' => $this->recentBookings,
        ])->layout('layouts.app', ['title' => 'Book Vehicle']);
    }
}
