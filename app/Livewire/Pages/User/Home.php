<?php

namespace App\Livewire\Pages\User;

use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.app')]
#[Title('HomePage')]
class Home extends Component
{
    public function render()
    {
        return view('livewire.pages.user.home');
    }
}
