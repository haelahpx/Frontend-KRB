<?php

namespace App\Livewire\Pages\User;

use Livewire\Component;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;

#[Layout('layouts.app')]
#[Title('BookVehicle')]
class Bookvehicle extends Component
{
    public $vehicle_id;
    public $date;
    public $departure_time;
    public $return_time;
    public $destination;
    public $number_of_passengers = 1;
    public $purpose;

    public $vehicles = [];
    public $hasVehicles = false;

    protected $rules = [
        'date' => 'required|date',
        'departure_time' => 'required',
        'destination' => 'required|string|max:255',
        'number_of_passengers' => 'required|integer|min:1',
        'purpose' => 'nullable|string|max:500',
    ];

    public function mount()
    {
        if (Schema::hasTable('vehicles')) {
            $this->hasVehicles = true;
            $this->vehicles = DB::table('vehicles')->select('*')->get();
        } else {
            $this->hasVehicles = false;
            $this->vehicles = collect();
        }
    }

    public function submit()
    {
        $this->validate();

        $draft = [
            'vehicle_id' => $this->vehicle_id,
            'date' => $this->date,
            'departure_time' => $this->departure_time,
            'return_time' => $this->return_time,
            'destination' => $this->destination,
            'number_of_passengers' => $this->number_of_passengers,
            'purpose' => $this->purpose,
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
        $this->vehicle_id = null;
        $this->date = null;
        $this->departure_time = null;
        $this->return_time = null;
        $this->destination = null;
        $this->number_of_passengers = 1;
        $this->purpose = null;
    }

    public function render()
    {
        return view('livewire.pages.user.bookvehicle');
    }
}
