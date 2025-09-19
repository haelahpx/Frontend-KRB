<?php

namespace App\Livewire\Pages;

use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class CreateTicket extends Component
{
    public function render()
    {
        return view('livewire.pages.user.createticket');
    }
}
