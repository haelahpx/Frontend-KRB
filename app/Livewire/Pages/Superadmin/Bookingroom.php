<?php

namespace App\Livewire\Pages\Superadmin;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Room;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;

#[Layout('layouts.superadmin')]
#[Title('Booking Room')]
class Bookingroom extends Component
{
    use WithPagination;

    public $room_id, $room_number;
    public $isEdit = false;

    protected $rules = [
        'room_number' => 'required|string|max:255',
    ];

    public function save()
    {
        $this->validate();

        Room::create([
            'company_id'  => Auth::user()->company_id,
            'room_number' => $this->room_number,
        ]);

        $this->resetInput();
        session()->flash('success', 'Room created successfully!');
    }

    public function edit($id)
    {
        $room = Room::findOrFail($id);
        $this->room_id    = $room->room_id;
        $this->room_number = $room->room_number;
        $this->isEdit     = true;
    }

    public function update()
    {
        $this->validate();

        $room = Room::findOrFail($this->room_id);
        $room->update([
            'room_number' => $this->room_number,
        ]);

        $this->resetInput();
        session()->flash('success', 'Room updated successfully!');
    }

    public function delete($id)
    {
        Room::findOrFail($id)->delete();
        session()->flash('success', 'Room deleted successfully!');
    }

    private function resetInput()
    {
        $this->room_id = null;
        $this->room_number = '';
        $this->isEdit = false;
    }

    public function render()
    {
        $rooms = Room::where('company_id', Auth::user()->company_id)->paginate(10);
        return view('livewire.pages.superadmin.bookingroom', compact('rooms'));
    }
}
