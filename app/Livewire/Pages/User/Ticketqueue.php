<?php

namespace App\Livewire\Pages\User;

use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.app')]
#[Title('Ticket Queue')]
class Ticketqueue extends Component
{
    public function render()
    {
        return view('livewire.pages.user.ticketqueue');
    }
}
