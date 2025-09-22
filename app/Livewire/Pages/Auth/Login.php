<?php

namespace App\Livewire\Pages\Auth;

use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\Attributes\Title;

#[Layout('layouts.auth')]
#[Title('Login')]
class Login extends Component
{
    public function render()
    {
        return view('livewire.pages.auth.login');
    }
}
