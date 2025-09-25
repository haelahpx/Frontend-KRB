<?php

namespace App\Livewire\Pages\User;

use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Models\Department;
use App\Models\Ticket;
use App\Models\TicketAttachment;

#[Layout('layouts.app')]
#[Title('Create Ticket')]
class CreateTicket extends Component
{
    use WithFileUploads;

    // Form fields
    public string $subject = '';
    public string $priority = 'low';                 // <- lowercase sesuai enum baru
    public ?int $assigned_department_id = null;      // departemen tujuan (assigned to)
    public string $description = '';

    // Display-only (dept user login)
    public string $requester_department = '-';

    /** @var \Livewire\Features\SupportFileUploads\TemporaryUploadedFile[] */
    public array $attachments = [];

    // Dropdown data: [{ department_id, department_name }]
    public array $departments = [];

    public function mount(): void
    {
        $user = Auth::user()->loadMissing(['department', 'company']);

        // Info dept user yang login (display only)
        $this->requester_department = optional($user->department)->department_name ?? '-';

        // Default assign-to = dept user sendiri (boleh diganti)
        $this->assigned_department_id = $user->department_id;

        // Ambil semua department (filter by company jika ada)
        $this->departments = Department::query()
            ->when($user->company_id, fn ($q) => $q->where('company_id', $user->company_id))
            ->orderBy('department_name', 'asc')
            ->get(['department_id', 'department_name'])
            ->toArray();
    }

    protected function rules(): array
    {
        return [
            'subject'                => ['required', 'string', 'max:255'],
            'priority'               => ['required', 'in:low,medium,high'],            // <- lowercase only
            'assigned_department_id' => ['required', 'exists:departments,department_id'],
            'description'            => ['nullable', 'string', 'max:10000'],
            'attachments.*'          => ['file', 'max:10240', 'mimes:jpg,jpeg,png,pdf,doc,docx'],
        ];
    }

    public function save()
    {
        $this->validate();

        $user = Auth::user()->loadMissing(['department', 'company']);

        // Simpan ticket (per skema baru)
        $ticket = Ticket::create([
            'company_id'     => $user->company_id,
            'requestdept_id' => $user->department_id,          // departemen si pembuat tiket
            'department_id'  => $this->assigned_department_id, // departemen tujuan (assigned)
            'user_id'        => $user->getKey(),
            'subject'        => $this->subject,
            'description'    => $this->description,
            'priority'       => $this->priority,               // low/medium/high
            'status'         => 'OPEN',
        ]);

        // Simpan attachments (tanpa uploaded_by)
        foreach ($this->attachments as $file) {
            $path = $file->store('tickets/' . $ticket->getKey(), 'public');

            TicketAttachment::create([
                'ticket_id' => $ticket->getKey(),
                'file_url'  => Storage::disk('public')->url($path),
                'file_type' => $file->getClientOriginalExtension(),
                // 'created_at' dibiarkan: DB pakai CURRENT_TIMESTAMP
            ]);
        }

        // Reset form
        $this->reset(['subject', 'priority', 'assigned_department_id', 'description', 'attachments']);

        session()->flash('success', 'Ticket created successfully.');

        return redirect()->route('ticketstatus');
    }

    public function render()
    {
        // NOTE: view name harus sesuai file: resources/views/livewire/pages/user/createticket.blade.php
        return view('livewire.pages.user.createticket');
    }
}
