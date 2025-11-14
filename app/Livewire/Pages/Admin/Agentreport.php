<?php

namespace App\Livewire\Pages\Admin;

use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.admin')]
#[Title('Admin - Agent Report')]
class Agentreport extends Component
{
    public function render()
    {
        return view('livewire.pages.admin.agentreport');
    }
}
