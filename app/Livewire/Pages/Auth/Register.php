<?php

namespace App\Livewire\Pages\Auth;

use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.auth')]
class Register extends Component
{
    public function render()
    {
        return view('livewire.pages.auth.register');
    }
}
