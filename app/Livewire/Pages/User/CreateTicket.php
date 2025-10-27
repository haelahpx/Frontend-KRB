<?php

namespace App\Livewire\Pages\User;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Illuminate\Support\Facades\Auth;
use App\Models\Department;
use App\Models\Ticket;
use Illuminate\Validation\ValidationException;
use Throwable;

#[Layout('layouts.app')]
#[Title('Create Ticket')]
class CreateTicket extends Component
{
    // Form fields
    public string $subject = '';
    public string $priority = 'low'; // must be: low|medium|high
    public ?int $assigned_department_id = null;
    public string $description = '';

    // Display-only
    public string $requester_department = '-';

    // Dropdown data
    public array $departments = [];

    // TMP files (JSON from blade)
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
        ];
    }

    public function save()
    {
        try {
            $this->validate();

            $user = Auth::user()->loadMissing(['department', 'company']);

            // Create ticket
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

            // Finalize temp attachments (best-effort)
            $items = json_decode($this->temp_items_json ?? '[]', true) ?? [];
            if (!empty($items)) {
                try {
                    app(\App\Http\Controllers\AttachmentController::class)
                        ->finalizeTemp(new \Illuminate\Http\Request([
                            'ticket_id' => $ticket->getKey(),
                            'items'     => $items,
                        ]));
                } catch (Throwable $e) {
                    $this->dispatch('toast', type: 'warning', title: 'Lampiran', message: 'Beberapa lampiran gagal diproses.', duration: 3000);
                }
            }

            // Reset form
            $this->reset(['subject', 'priority', 'assigned_department_id', 'description', 'temp_items_json']);

            // Success toast
            $this->dispatch('toast', type: 'success', title: 'Berhasil', message: 'Tiket berhasil dibuat.', duration: 3000);
            session()->flash('toast', [
                'type'    => 'success',
                'title'   => 'Berhasil',
                'message' => 'Tiket berhasil dibuat.',
                'duration'=> 3000,
            ]);

            return redirect()->route('ticketstatus');

        } catch (ValidationException $e) {
            $first = collect($e->validator->errors()->all())->first() ?? 'Periksa kembali input Anda.';
            $this->dispatch('toast', type: 'error', title: 'Validasi Gagal', message: $first, duration: 3000);
            throw $e;
        } catch (Throwable $e) {
            $this->dispatch('toast', type: 'error', title: 'Gagal', message: 'Terjadi kesalahan tak terduga.', duration: 3000);
            return;
        }
    }

    public function render()
    {
        // keep your existing blade path
        return view('livewire.pages.user.createticket');
    }
}
