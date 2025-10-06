<?php

namespace App\Livewire\Pages\User;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Illuminate\Support\Facades\Auth;
use App\Models\Ticket;
use App\Models\TicketComment;
use Illuminate\Validation\ValidationException;
use Throwable;

#[Layout('layouts.app')]
#[Title('Ticket Detail')]
class Ticketshow extends Component
{
    public Ticket $ticket;
    public string $newComment = '';

    public function mount(Ticket $ticket)
    {
        // Hanya pemilik tiket yang boleh melihat (ubah sesuai kebutuhan role)
        if ($ticket->user_id !== Auth::id()) {
            abort(403);
        }

        // Eager load minimal kolom + urutkan komentar seperti chat
        $this->ticket = $ticket->load([
            'department:department_id,department_name',
            'user:user_id,full_name',
            'comments' => fn ($q) => $q->orderBy('created_at', 'asc'),
            'comments.user:user_id,full_name',
        ]);
    }

    public function addComment()
    {
        try {
            $this->validate([
                'newComment' => ['required', 'string', 'min:3'],
            ]);

            $this->ticket->comments()->create([
                'user_id'      => Auth::id(),
                'comment_text' => $this->newComment,
            ]);

            // Reset input
            $this->reset('newComment');

            // Refresh komentar dengan urutan ascending
            $this->ticket->load([
                'comments' => fn ($q) => $q->orderBy('created_at', 'asc'),
                'comments.user:user_id,full_name',
            ]);

            // Toast sukses
            $this->dispatch('toast', type: 'success', title: 'Berhasil', message: 'Komentar berhasil ditambahkan.', duration: 3000);

        } catch (ValidationException $e) {
            // Ambil pesan pertama agar ringkas
            $first = collect($e->validator->errors()->all())->first() ?? 'Periksa kembali input Anda.';
            $this->dispatch('toast', type: 'error', title: 'Validasi Gagal', message: $first, duration: 3000);
            throw $e; // biarkan Livewire menandai field invalid

        } catch (Throwable $e) {
            // Error tak terduga
            $this->dispatch('toast', type: 'error', title: 'Gagal', message: 'Terjadi kesalahan tak terduga saat menambah komentar.', duration: 3000);
        }
    }

    public function render()
    {
        return view('livewire.pages.user.ticketshow');
    }
}
