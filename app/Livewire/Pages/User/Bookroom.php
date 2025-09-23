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
    public $view = 'form'; // 'form' or 'calendar'
    public $selectedDate;
    public $currentWeek;
    
    // Form properties
    public $meeting_title = '';
    public $room_id = '';
    public $date = '';
    public $number_of_attendees = '';
    public $start_time = '';
    public $end_time = '';
    public $requirements = [];
    public $special_notes = '';

    public function mount()
    {
        $this->selectedDate = Carbon::now();
        $this->currentWeek = Carbon::now()->startOfWeek();
        $this->date = Carbon::now()->format('Y-m-d');
    }

    public function switchView($view)
    {
        $this->view = $view;
    }

    public function previousWeek()
    {
        $this->currentWeek = $this->currentWeek->copy()->subWeek();
    }

    public function nextWeek()
    {
        $this->currentWeek = $this->currentWeek->copy()->addWeek();
    }

    public function selectDate($date)
    {
        $this->selectedDate = Carbon::parse($date);
        $this->date = $this->selectedDate->format('Y-m-d');
    }

    public function submitBooking()
    {
        $this->validate([
            'meeting_title' => 'required|min:3',
            'room_id' => 'required',
            'date' => 'required|date',
            'number_of_attendees' => 'required|integer|min:1',
            'start_time' => 'required',
            'end_time' => 'required|after:start_time',
        ]);

        // Handle booking submission here
        session()->flash('message', 'Room booked successfully!');
        
        // Reset form
        $this->reset(['meeting_title', 'room_id', 'number_of_attendees', 'start_time', 'end_time', 'requirements', 'special_notes']);
    }

    private function getDummyRooms()
    {
        return [
            ['id' => 1, 'name' => 'Conference Room A', 'capacity' => 12, 'available' => true],
            ['id' => 2, 'name' => 'Board Room', 'capacity' => 20, 'available' => false],
            ['id' => 3, 'name' => 'Meeting Room B', 'capacity' => 8, 'available' => true],
            ['id' => 4, 'name' => 'Training Room', 'capacity' => 15, 'available' => true],
        ];
    }

    private function getDummyBookings()
    {
        return [
            [
                'id' => 1,
                'room_id' => 1,
                'meeting_title' => 'Team Standup',
                'date' => $this->currentWeek->copy()->format('Y-m-d'),
                'start_time' => '14:00:00',
                'end_time' => '15:00:00',
                'user_name' => 'John Doe'
            ],
            [
                'id' => 2,
                'room_id' => 2,
                'meeting_title' => 'Client Presentation',
                'date' => $this->currentWeek->copy()->addDay()->format('Y-m-d'),
                'start_time' => '10:00:00',
                'end_time' => '12:00:00',
                'user_name' => 'Jane Smith'
            ],
            [
                'id' => 3,
                'room_id' => 1,
                'meeting_title' => 'Weekly Review',
                'date' => $this->currentWeek->copy()->addDays(2)->format('Y-m-d'),
                'start_time' => '09:00:00',
                'end_time' => '10:30:00',
                'user_name' => 'Mike Johnson'
            ],
            [
                'id' => 4,
                'room_id' => 4,
                'meeting_title' => 'Training Session',
                'date' => $this->currentWeek->copy()->addDays(3)->format('Y-m-d'),
                'start_time' => '13:00:00',
                'end_time' => '16:00:00',
                'user_name' => 'Sarah Wilson'
            ],
            [
                'id' => 5,
                'room_id' => 3,
                'meeting_title' => 'Project Planning',
                'date' => $this->currentWeek->copy()->addDays(1)->format('Y-m-d'),
                'start_time' => '15:30:00',
                'end_time' => '17:00:00',
                'user_name' => 'David Brown'
            ]
        ];
    }

    private function getTimeSlots()
    {
        $slots = [];
        for ($hour = 8; $hour <= 18; $hour++) {
            $slots[] = sprintf('%02d:00', $hour);
            $slots[] = sprintf('%02d:30', $hour);
        }
        return $slots;
    }

    private function getBookingForSlot($roomId, $date, $timeSlot)
    {
        $bookings = $this->getDummyBookings();
        $slotTime = Carbon::parse($date . ' ' . $timeSlot);
        
        foreach ($bookings as $booking) {
            if ($booking['room_id'] == $roomId && $booking['date'] == $date) {
                $startTime = Carbon::parse($booking['date'] . ' ' . $booking['start_time']);
                $endTime = Carbon::parse($booking['date'] . ' ' . $booking['end_time']);
                
                if ($slotTime->between($startTime, $endTime->subMinute())) {
                    return $booking;
                }
            }
        }
        
        return null;
    }

    public function render()
    {
        $rooms = $this->getDummyRooms();
        $bookings = $this->getDummyBookings();
        $timeSlots = $this->getTimeSlots();
        
        // Generate week days
        $weekDays = [];
        for ($i = 0; $i < 7; $i++) {
            $weekDays[] = $this->currentWeek->copy()->addDays($i);
        }

        return view('livewire.pages.user.bookroom', compact('rooms', 'bookings', 'timeSlots', 'weekDays'));
    }
}