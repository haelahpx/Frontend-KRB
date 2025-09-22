<?php

namespace App\Livewire\Pages\User;

use Livewire\Attributes\Layout;
use Livewire\Component;
use Carbon\Carbon;
use Livewire\Attributes\Title;

#[Layout('layouts.app')]
#[Title('BookRoom')]
class Bookroom extends Component
{
    public $rooms = [];
    public $recentBookings = [];

    public function mount()
    {
        // Dummy rooms
        $this->rooms = [
            (object)[ 'name' => 'Conference Room A', 'capacity' => 20, 'is_available' => true ],
            (object)[ 'name' => 'Board Room 1', 'capacity' => 12, 'is_available' => false ],
            (object)[ 'name' => 'Meeting Room 2', 'capacity' => 8, 'is_available' => true ],
        ];

        // Dummy bookings
        $this->recentBookings = [
            (object)[
                'title' => 'Weekly Standup',
                'date' => Carbon::now()->subDay(),
                'room' => (object)['name' => 'Conference Room A'],
            ],
            (object)[
                'title' => 'Project Planning',
                'date' => Carbon::now()->subHours(5),
                'room' => (object)['name' => 'Board Room 1'],
            ],
        ];
    }

    public function render()
    {
        return view('livewire.pages.user.bookroom', [
            'rooms' => $this->rooms,
            'recentBookings' => $this->recentBookings,
        ]);
    }
}
