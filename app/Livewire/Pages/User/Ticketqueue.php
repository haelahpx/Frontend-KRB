<?php

namespace App\Livewire\Pages\User;

use App\Models\Ticket;
use App\Models\TicketAssignment;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Illuminate\Support\Facades\DB;

#[Layout('layouts.app')]
#[Title('Ticket Center')]
class Ticketqueue extends Component
{
    use WithPagination;

    public string $tab = 'queue';

    public string $search = '';
    public string $status = '';
    public string $priority = '';
    public string $claimPriority = '';

    protected $queryString = [
        'tab'           => ['except' => 'queue'],
        'search'        => ['except' => ''],
        'status'        => ['except' => ''],
        'priority'      => ['except' => ''],
        'claimPriority' => ['except' => ''],
    ];

    public function updatingSearch() { $this->resetPage('qpage'); }
    public function updatingStatus() { $this->resetPage('qpage'); }
    public function updatingPriority() { $this->resetPage('qpage'); }
    public function updatingClaimPriority() { $this->resetPage('cpage'); }
    public function updatedTab()
    {
        $this->resetPage('qpage');
        $this->resetPage('cpage');
    }

    protected function queueQuery()
    {
        $user = auth()->user();

        return Ticket::with(['attachments', 'requester'])
            ->where('company_id', $user->company_id)
            ->where('department_id', $user->department_id)
            ->where('user_id', '!=', $user->user_id)
            ->where('status', '!=', 'RESOLVED')
            ->whereDoesntHave('assignments', function ($q) {
                $q->whereNull('deleted_at');
            })
            ->when($this->search, function ($q) {
                $q->where(function ($q2) {
                    $q2->where('subject', 'like', "%{$this->search}%")
                        ->orWhere('description', 'like', "%{$this->search}%");
                });
            })
            ->when($this->status !== '', fn($q) => $q->where('status', $this->status))
            ->when($this->priority !== '', fn($q) => $q->where('priority', $this->priority))
            ->orderByDesc('created_at');
    }

    protected function claimsQuery()
    {
        $user = auth()->user();

        return TicketAssignment::query()
            ->with(['ticket.attachments', 'ticket.requester'])
            ->where('user_id', $user->user_id)
            ->whereNull('deleted_at')
            ->whereRelation('ticket', 'status', '!=', 'CLOSED')
            ->when($this->claimPriority !== '', fn ($q) =>
                $q->whereHas('ticket', fn ($qt) => $qt->where('priority', $this->claimPriority))
            )
            ->orderByDesc('created_at');
    }

    public function claim(int $ticketId)
    {
        $user = auth()->user();

        $alreadyClaimed = TicketAssignment::where('ticket_id', $ticketId)
            ->whereNull('deleted_at')
            ->exists();

        if ($alreadyClaimed) {
            $this->dispatch('notify', type: 'warning', message: 'Tiket sudah diklaim orang lain.');
            return;
        }

        DB::transaction(function () use ($ticketId, $user) {
            TicketAssignment::create([
                'ticket_id' => $ticketId,
                'user_id'   => $user->user_id,
            ]);

            Ticket::where('ticket_id', $ticketId)->update([
                'status'     => 'IN_PROGRESS',
                'updated_at' => now(),
            ]);
        });

        $this->resetPage('qpage');
        $this->dispatch('notify', type: 'success', message: 'Tiket berhasil di-claim.');
    }

    public function render()
    {
        $tickets = $this->tab === 'queue'
            ? $this->queueQuery()->paginate(10, ['*'], 'qpage')
            : collect();

        $claims = $this->tab === 'claims'
            ? $this->claimsQuery()->paginate(12, ['*'], 'cpage')
            : collect();

        return view('livewire.pages.user.ticketqueue', compact('tickets', 'claims'));
    }
}
