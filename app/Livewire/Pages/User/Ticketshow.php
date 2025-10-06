<?php

namespace App\Livewire\Pages\User;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Illuminate\Support\Facades\Auth;
use App\Models\Ticket;
use App\Models\TicketComment;

#[Layout('layouts.app')]
#[Title('Ticket Detail')]
class Ticketshow extends Component
{
    public Ticket $ticket;
    public string $newComment = '';

    public function mount(Ticket $ticket)
    {
        // Only owner can see (adjust if admins/receptionists also need access)
        if ($ticket->user_id !== Auth::id()) {
            abort(403);
        }

        // Eager load with minimal columns + sorted comments for chat
        $this->ticket = $ticket->load([
            'department:department_id,department_name',
            'user:user_id,full_name',
            'comments' => fn ($q) => $q->orderBy('created_at', 'asc'),
            'comments.user:user_id,full_name',
        ]);
    }

    public function addComment()
    {
        $this->validate([
            'newComment' => 'required|string|min:3',
        ]);

        $this->ticket->comments()->create([
            'user_id'      => Auth::id(),
            'comment_text' => $this->newComment,
        ]);

        // Reset input and refresh comments (keep ascending order)
        $this->reset('newComment');

        $this->ticket->load([
            'comments' => fn ($q) => $q->orderBy('created_at', 'asc'),
            'comments.user:user_id,full_name',
        ]);
    }

    public function render()
    {
        return view('livewire.pages.user.ticketshow');
    }
}
