<?php

namespace App\Livewire\Pages\Admin;

use Livewire\Component;
use Livewire\Attributes\Title;

#[Title('Dashboard')]
class Dashboard extends Component
{
    public function render()
    {
        return view('livewire.pages.admin.dashboard');
    }
}
