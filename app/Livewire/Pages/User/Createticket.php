<?php

namespace App\Livewire\Pages\User;

use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\Attributes\Title;

#[Layout('layouts.app')]
#[Title('CreateTicket')]
class CreateTicket extends Component
{
    public function render()
    {
        return view('livewire.pages.user.createticket');
    }
}
