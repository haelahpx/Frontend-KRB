<?php

namespace App\Livewire\Pages\User;

use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class Profile extends Component
{
    public function render()
    {
        return view('livewire.pages.user.profile');
    }
}
