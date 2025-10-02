<?php

namespace App\Livewire\Pages\Superadmin;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;

#[Layout('layouts.superadmin')]
#[Title('Ticket Support')]
class Ticketsupport extends Component
{
    public function render()
    {
        return view('livewire.pages.superadmin.ticketsupport');
    }
}
