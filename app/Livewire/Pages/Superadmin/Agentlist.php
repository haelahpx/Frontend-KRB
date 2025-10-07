<?php

namespace App\Livewire\Pages\Superadmin;

use Livewire\Component;

#[Layout('layouts.superadmin')]
#[Title('Superadmin Dashboard')]class Agentlist extends Component
{
    public function render()
    {
        return view('livewire.pages.superadmin.agentlist');
    }
}
