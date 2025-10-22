<?php

namespace App\Livewire\Pages\Superadmin;

use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.superadmin')]
#[Title('Booking Vehicle')]
class Bookingvehicle extends Component
{
    public function render()
    {
        return view('livewire.pages.superadmin.bookingvehicle');
    }
}
