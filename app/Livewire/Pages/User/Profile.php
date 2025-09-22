<?php

namespace App\Livewire\Pages\User;

use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\Attributes\Title;
#[Layout('layouts.app')]
#[Title('Profile')]
class Profile extends Component
{
    public function render()
    {
        return view('livewire.pages.user.profile');
    }
}
