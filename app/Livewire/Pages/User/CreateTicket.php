<?php

namespace App\Livewire\Pages\User;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Models\Department;
use App\Models\Ticket;
use Illuminate\Validation\ValidationException;
use Throwable;

#[Layout('layouts.app')]
#[Title('Create Ticket')]
class CreateTicket extends Component
{
    public string $subject = '';
    public string $priority = 'low';
    public ?int $assigned_department_id = null;
    public string $description = '';

    public string $requester_department = '-';
    public array $departments = [];
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

            $ticket = Ticket::create([
                'company_id'     => $user->company_id,
                'requestdept_id' => $user->department_id,   // pastikan kolom ini ada
                'department_id'  => $this->assigned_department_id,
                'user_id'        => $user->getKey(),
                'subject'        => $this->subject,
                'description'    => $this->description,
                'priority'       => $this->priority,
                'status'         => 'OPEN',                 // pastikan enum/status valid
            ]);

            // finalize lampiran (best-effort)
            $items = [];
            try {
                $decoded = json_decode($this->temp_items_json ?? '[]', true, 512, JSON_THROW_ON_ERROR);
                $items = is_array($decoded) ? $decoded : [];
            } catch (\JsonException $je) {
                $items = [];
            }

            if ($items) {
                try {
                    app(\App\Http\Controllers\AttachmentController::class)
                        ->finalizeTemp(new \Illuminate\Http\Request([
                            'ticket_id' => $ticket->getKey(),
                            'items'     => $items,
                        ]));
                } catch (Throwable $e) {
                    Log::warning('Attachment finalizeTemp failed', [
                        'ticket_id' => $ticket->getKey(),
                        'err' => $e->getMessage()
                    ]);
                    $this->dispatch('toast', type: 'warning', title: 'Lampiran', message: 'Beberapa lampiran gagal diproses.', duration: 3000);
                }
            }

            // reset (kembalikan dept assignment ke dept user)
            $userDeptId = $user->department_id;
            $this->reset(['subject', 'priority', 'assigned_department_id', 'description', 'temp_items_json']);
            $this->priority = 'low';
            $this->assigned_department_id = $userDeptId;
            $this->temp_items_json = '[]';

            $this->dispatch('toast', type: 'success', title: 'Berhasil', message: 'Tiket berhasil dibuat.', duration: 3000);
            session()->flash('toast', [
                'type'    => 'success',
                'title'   => 'Berhasil',
                'message' => 'Tiket berhasil dibuat.',
                'duration'=> 3000,
            ]);

            // TETAP: redirect ke route 'ticketstatus'
            return redirect()->route('ticketstatus');

        } catch (ValidationException $e) {
            $first = collect($e->validator->errors()->all())->first() ?? 'Periksa kembali input Anda.';
            $this->dispatch('toast', type: 'error', title: 'Validasi Gagal', message: $first, duration: 3000);
            throw $e;
        } catch (Throwable $e) {
            Log::error('CreateTicket.save failed', [
                'err' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            $msg = app()->environment('local') ? $e->getMessage() : 'Terjadi kesalahan tak terduga.';
            $this->dispatch('toast', type: 'error', title: 'Gagal', message: $msg, duration: 3500);
            return;
        }
    }

    public function render()
    {
        return view('livewire.pages.user.createticket');
    }
}
