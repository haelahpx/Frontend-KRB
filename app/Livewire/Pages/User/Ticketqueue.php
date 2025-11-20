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

    /**
     * Kanban columns for My Claims tab
     */
    public array $kanbanColumns = [
        'OPEN'        => 'Open',
        'IN_PROGRESS' => 'In Progress',
        'RESOLVED'    => 'Resolved',
    ];

    protected $queryString = [
        'tab'           => ['except' => 'queue'],
        'search'        => ['except' => ''],
        'status'        => ['except' => ''],
        'priority'      => ['except' => ''],
        'claimPriority' => ['except' => ''],
    ];

    public function updatingSearch(): void
    {
        $this->resetPage('qpage');
    }

    public function updatingStatus(): void
    {
        $this->resetPage('qpage');
    }

    public function updatingPriority(): void
    {
        $this->resetPage('qpage');
    }

    public function updatedTab(): void
    {
        $this->resetPage('qpage');
    }

    /**
     * Auto-close tickets:
     * All tickets with status RESOLVED for >= 1 day become CLOSED
     */
    protected function autoCloseResolvedTickets(): void
    {
        Ticket::where('status', 'RESOLVED')
            ->where('updated_at', '<=', now()->subDay())
            ->update([
                'status'     => 'CLOSED',
                'updated_at' => now(),
            ]);
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
            ->when($this->status !== '', fn ($q) => $q->where('status', $this->status))
            ->when($this->priority !== '', fn ($q) => $q->where('priority', $this->priority))
            ->orderByDesc('created_at');
    }

    protected function claimsQuery()
    {
        $user = auth()->user();

        return TicketAssignment::query()
            ->with(['ticket.attachments', 'ticket.requester'])
            ->where('user_id', $user->user_id)
            ->whereNull('deleted_at')
            // closed tickets are not shown in My Claims
            ->whereRelation('ticket', 'status', '!=', 'CLOSED')
            ->when(
                $this->claimPriority !== '',
                fn ($q) => $q->whereHas(
                    'ticket',
                    fn ($qt) => $qt->where('priority', $this->claimPriority)
                )
            )
            ->orderByDesc('created_at');
    }

    public function claim(int $ticketId): void
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

    /**
     * Called by Kanban drag & drop on My Claims tab
     */
    public function moveClaim(int $ticketId, string $newStatus): void
    {
        $user = auth()->user();

        if (! in_array($newStatus, ['OPEN', 'IN_PROGRESS', 'RESOLVED', 'CLOSED'], true)) {
            return;
        }

        $assignment = TicketAssignment::query()
            ->where('ticket_id', $ticketId)
            ->where('user_id', $user->user_id)
            ->whereNull('deleted_at')
            ->first();

        if (! $assignment) {
            $this->dispatch('notify', type: 'error', message: 'Tiket tidak ditemukan atau bukan milik Anda.');
            return;
        }

        DB::transaction(function () use ($ticketId, $newStatus) {
            Ticket::where('ticket_id', $ticketId)->update([
                'status'     => $newStatus,
                'updated_at' => now(),
            ]);
        });

        $this->dispatch('notify', type: 'success', message: 'Status tiket diperbarui.');
    }

    public function render()
    {
        // Auto-close old RESOLVED tickets on each page load
        $this->autoCloseResolvedTickets();

        $tickets = $this->tab === 'queue'
            ? $this->queueQuery()->paginate(10, ['*'], 'qpage')
            : collect();

        // Kanban: we use all claimed tickets (no pagination)
        $claims = $this->tab === 'claims'
            ? $this->claimsQuery()->get()
            : collect();

        return view('livewire.pages.user.ticketqueue', compact('tickets', 'claims'));
    }
}
