<?php

namespace App\Livewire\Pages\Admin;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;

#[Layout('layouts.admin')]
#[Title('Admin-Dashboard')]
class Dashboard extends Component
{
    public function render()
    {
        return view('livewire.pages.admin.dashboard');
    }
}
