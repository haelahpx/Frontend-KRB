<?php

namespace App\Livewire\Pages\User;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Illuminate\Support\Facades\Auth;
use App\Models\Department;
use App\Models\Ticket;

#[Layout('layouts.app')]
#[Title('Create Ticket')]
class CreateTicket extends Component
{
    // Form fields
    public string $subject = '';
    public string $priority = 'low';                 // lowercase
    public ?int $assigned_department_id = null;
    public string $description = '';

    // Display-only
    public string $requester_department = '-';

    // Dropdown data
    public array $departments = [];

    // NEW: daftar file TMP (JSON string dari blade)
    public string $temp_items_json = '[]';

    public function mount(): void
    {
        $user = Auth::user()->loadMissing(['department', 'company']);

        $this->requester_department   = optional($user->department)->department_name ?? '-';
        $this->assigned_department_id = $user->department_id;

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
            'priority'               => ['required', 'in:low,medium,high'],
            'assigned_department_id' => ['required', 'exists:departments,department_id'],
            'description'            => ['nullable', 'string', 'max:10000'],
            // NOTE: validasi lampiran dilakukan saat signature-temp (format & 10MB/file)
        ];
    }

    public function save()
    {
        $this->validate();

        $user = Auth::user()->loadMissing(['department', 'company']);

        // Simpan ticket persis seperti sebelumnya
        $ticket = Ticket::create([
            'company_id'     => $user->company_id,
            'requestdept_id' => $user->department_id,
            'department_id'  => $this->assigned_department_id,
            'user_id'        => $user->getKey(),
            'subject'        => $this->subject,
            'description'    => $this->description,
            'priority'       => $this->priority,
            'status'         => 'OPEN',
        ]);

        // NEW: finalize Cloudinary TMP -> final + insert ke ticket_attachments
        $items = json_decode($this->temp_items_json ?? '[]', true) ?? [];
        if (!empty($items)) {
            app(\App\Http\Controllers\AttachmentController::class)
                ->finalizeTemp(new \Illuminate\Http\Request([
                    'ticket_id' => $ticket->getKey(),
                    'items'     => $items,
                ]));
        }

        // Reset & redirect
        $this->reset(['subject', 'priority', 'assigned_department_id', 'description', 'temp_items_json']);
        session()->flash('success', 'Ticket created successfully.');
        return redirect()->route('ticketstatus');
    }

    public function render()
    {
        return view('livewire.pages.user.createticket');
    }
}
