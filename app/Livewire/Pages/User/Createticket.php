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
    public string $priority = 'LOW';
    public ?int $assigned_department_id = null; // departemen tujuan (assigned to)
    public string $description = '';

    // Display-only (dept user login)
    public string $requester_department = '-';

    // Uploads
    /** @var \Livewire\Features\SupportFileUploads\TemporaryUploadedFile[] */
    public array $attachments = [];

    // Dropdown data
    public array $departments = [];

    public function mount(): void
    {
        $user = Auth::user()->loadMissing(['department', 'company']);

        // Info dept user yang login (display only)
        $this->requester_department = optional($user->department)->department_name ?? '-';

        // Default assign-to = dept user sendiri (boleh diganti)
        $this->assigned_department_id = $user->department_id;

        // Ambil semua department (opsional: filter by company)
        $this->departments = Department::query()
            ->when($user->company_id, fn ($q) => $q->where('company_id', $user->company_id))
            ->orderBy('department_name')
            ->get(['id', 'department_name'])
            ->toArray();
    }

    protected function rules(): array
    {
        return [
            'subject'                => ['required', 'string', 'max:255'],
            'priority'               => ['required', 'in:LOW,MEDIUM,HIGH,CRITICAL'],
            'assigned_department_id' => ['nullable', 'exists:departments,id'],
            'description'            => ['nullable', 'string', 'max:5000'],
            'attachments.*'          => ['file', 'max:10240', 'mimes:jpg,jpeg,png,pdf,doc,docx'],
        ];
    }

    public function save()
    {
        $this->validate();

        $user = Auth::user();

        // Simpan ticket
        $ticket = Ticket::create([
            'company_id'    => $user->company_id,
            'department_id' => $this->assigned_department_id, // departemen tujuan (assigned to)
            'user_id'       => $user->id,
            'subject'       => $this->subject,
            'description'   => $this->description,
            'priority'      => $this->priority,
            'status'        => 'OPEN',
        ]);

        // Simpan attachments (jika ada)
        foreach ($this->attachments as $file) {
            $path = $file->store('tickets/'.$ticket->id, 'public');
            TicketAttachment::create([
                'ticket_id'   => $ticket->id,
                'file_url'    => Storage::disk('public')->url($path),
                'file_type'   => $file->getClientOriginalExtension(),
                'uploaded_by' => $user->id,
            ]);
        }

        // Reset form
        $this->reset(['subject', 'priority', 'assigned_department_id', 'description', 'attachments']);

        session()->flash('success', 'Ticket created successfully.');

        return redirect()->route('ticketstatus');
    }

    public function render()
    {
        // Karena Blade akses $this->..., kita tidak perlu pass data di sini
        return view('livewire.pages.user.createticket');
    }
}
