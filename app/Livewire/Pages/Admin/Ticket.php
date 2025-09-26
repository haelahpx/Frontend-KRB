<?php

namespace App\Livewire\Pages\Admin;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use App\Models\Ticket as TicketModel;
use App\Models\User;
use App\Models\Role;                // <-- pastikan ada model Role (PK: role_id, kolom name)
use App\Models\TicketAssignment;
use Carbon\Carbon;

#[Layout('layouts.admin')]
#[Title('Admin - Ticket')]
class Ticket extends Component
{
    use WithPagination;

    public ?int $selectedTicketId = null;
    public ?int $selectedAgentId  = null;

    // akan diisi id role untuk "user" saat mount
    protected ?int $userRoleId = null;

    public function mount(): void
    {
        // Ambil role_id untuk role bernama "user"
        $this->userRoleId = Role::where('name', 'user')->value('role_id');

        if (!$this->userRoleId) {
            session()->flash('error', 'Role "user" tidak ditemukan. Cek tabel    roles (kolom name & role_id).');
        }
    }

    protected function rules(): array
    {
        $deptId = Auth::user()->department_id;

        return [
            'selectedAgentId' => [
                'required',
                'integer',
                // Validasi: agent harus ada di tabel users.user_id DAN memenuhi dept & role = user
                Rule::exists('users', 'user_id')
                    ->where(fn($q) => $q->where('department_id', $deptId)
                        ->where('role_id', $this->userRoleId)),
            ],
        ];
    }

    public function assignAgent(int $ticketId): void
    {
        $this->validate();

        $deptId = Auth::user()->department_id;

        // Pastikan ticket memang milik departemen admin
        $ticket = TicketModel::where('ticket_id', $ticketId)
            ->where('department_id', $deptId)
            ->firstOrFail();

        // Simpan/Update penugasan (1 ticket 1 assignment; kalau mau multi, ganti ke create saja)
        TicketAssignment::updateOrCreate(
            ['ticket_id' => $ticketId],
            ['agent_id' => $this->selectedAgentId, 'assigned_at' => Carbon::now()]
        );

        // Update status ticket
        $ticket->status = 'ASSIGNED'; // atau 'IN_PROGRESS' sesuai flow-mu
        $ticket->save();

        session()->flash('message', 'Agent berhasil di-assign.');
        $this->reset(['selectedAgentId', 'selectedTicketId']);
    }

    public function render()
    {
        $deptId = Auth::user()->department_id;

        $tickets = TicketModel::where('department_id', $deptId)
            ->orderByDesc('created_at')
            ->paginate(10);

        // List agen: role "user" + satu departemen
        $agents = User::where('department_id', $deptId)
            ->where('role_id', $this->userRoleId)
            ->orderBy('full_name')
            ->get(['user_id', 'full_name']); // pilih kolom yang dipakai

        return view('livewire.pages.admin.ticket', compact('tickets', 'agents'));
    }
}
