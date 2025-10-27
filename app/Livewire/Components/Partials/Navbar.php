<?php

namespace App\Livewire\Components\Partials;

use Livewire\Component;

class Navbar extends Component
{
    public $showDropdown = false;

    public function toggleDropdown()
    {
        $this->showDropdown = !$this->showDropdown;
    }

    public function closeDropdown()
    {
        $this->showDropdown = false;
    }

    public function render()
    {
        return view('livewire.components.navbar');
    }
}