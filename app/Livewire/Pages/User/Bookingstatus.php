<?php

namespace App\Livewire\Pages\User;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Illuminate\Support\Carbon;

#[Layout('layouts.app')]
#[Title('Booked Rooms')]
class bookingstatus extends Component
{
    public ?string $q = null;         
    public ?string $date_from = null;  
    public ?string $date_to = null;    
    public array $bookings = [
        ['id' => 1, 'title' => 'Team Standup', 'room_name' => 'Conference Room A', 'start_time' => '2025-09-24 09:00:00', 'end_time' => '2025-09-24 10:00:00', 'status' => 'booked'],
        ['id' => 2, 'title' => 'Client Presentation', 'room_name' => 'Board Room', 'start_time' => '2025-09-25 13:00:00', 'end_time' => '2025-09-25 14:30:00', 'status' => 'booked'],
        ['id' => 3, 'title' => 'Weekly Review', 'room_name' => 'Conference Room A', 'start_time' => '2025-09-26 08:30:00', 'end_time' => '2025-09-26 09:30:00', 'status' => 'cancelled'],
        ['id' => 4, 'title' => 'Design Sync', 'room_name' => 'Meeting Room B', 'start_time' => '2025-09-27 14:00:00', 'end_time' => '2025-09-27 15:00:00', 'status' => 'booked'],
        ['id' => 5, 'title' => 'Training Session', 'room_name' => 'Training Room', 'start_time' => '2025-09-28 10:00:00', 'end_time' => '2025-09-28 12:00:00', 'status' => 'pending'],
    ];

    public function mount(): void
    {
        $this->date_from ??= Carbon::today()->toDateString();
        $this->date_to ??= Carbon::today()->addDays(14)->toDateString();
    }
    public function clearFilters(): void
    {
        $this->q = null;
        $this->date_from = Carbon::today()->toDateString();
        $this->date_to = Carbon::today()->addDays(14)->toDateString();
    }
    public function getBookedProperty(): array
    {
        $from = Carbon::parse($this->date_from)->startOfDay();
        $to = Carbon::parse($this->date_to)->endOfDay();

        return collect($this->bookings)
            ->where('status', 'booked')
            ->filter(fn($b) => Carbon::parse($b['end_time'])->gte($from)
                && Carbon::parse($b['start_time'])->lte($to))
            ->when($this->q, function ($c) {
                $t = mb_strtolower(trim($this->q));
                return $c->filter(fn($b) => str_contains(mb_strtolower($b['title']), $t)
                    || str_contains(mb_strtolower($b['room_name']), $t));
            })
            ->sortBy('start_time')
            ->values()
            ->all();
    }

    public function render()
    {
        return view('livewire.pages.user.bookingstatus');
    }
}
