<?php

namespace App\Livewire\Pages\Admin;

use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Throwable;
use Carbon\Carbon;

use App\Models\Information as InformationModel;
use App\Models\BookingRoom;

#[Layout('layouts.admin')]
#[Title('Admin - Information')]
class Information extends Component
{
    use WithPagination;

    // ====== UI STATE ======
    public string $mode = 'index'; // index|create|edit
    public ?int $editingId = null;

    // ====== PAGINATION ======
    protected string $paginationTheme = 'tailwind';
    public int $perPageInfo = 10;  // information list
    public int $perPageReq  = 6;   // requests (offline/online)

    // ====== FILTERS (information list) ======
    public ?string $search = null;

    // ====== FORM FIELDS (create / edit single info) ======
    public string $description = '';
    public string $event_at   = ''; // Y-m-d\TH:i

    // ====== BROADCAST (own department only) ======
    public string $broadcast_description = '';
    public string $broadcast_event_at    = '';

    // ====== REQUEST→INFORM MODAL ======
    public bool  $informModal = false;
    public ?int  $informBookingId = null;

    // ====== HEADER (hero) ======
    public string $company_name    = '-';
    public string $department_name = '-';

    public function mount(): void
    {
        try {
            $auth = Auth::user()->loadMissing(['company', 'department']);
            $this->company_name    = optional($auth->company)->company_name ?? '-';
            $this->department_name = optional($auth->department)->department_name ?? '-';

            $this->resetForm();
            $this->resetBroadcastForm();
        } catch (Throwable $e) {
            $this->dispatch('toast', type: 'error', title: 'Error', message: 'Gagal memuat header.', duration: 5000);
            Log::error('Information mount error', ['m' => $e->getMessage()]);
        }
    }

    private function resetForm(): void
    {
        $this->editingId   = null;
        $this->description = '';
        $this->event_at    = now()->format('Y-m-d\TH:i');
    }

    private function resetBroadcastForm(): void
    {
        $this->broadcast_description = '';
        $this->broadcast_event_at    = now()->format('Y-m-d\TH:i');
    }

    private function rules(): array
    {
        return [
            'description' => ['required','string'],
            'event_at'    => ['required','date'],
        ];
    }

    private function broadcastRules(): array
    {
        return [
            'broadcast_description' => ['required','string'],
            'broadcast_event_at'    => ['required','date'],
        ];
    }

    // ========= CRUD (Information table) =========
    public function create(): void
    {
        $this->resetForm();
        $this->mode = 'create';
    }

    public function edit(int $id): void
    {
        $row = InformationModel::where('information_id', $id)
            ->where('company_id', Auth::user()->company_id)
            ->where('department_id', Auth::user()->department_id)
            ->firstOrFail();

        $this->editingId   = $row->information_id;
        $this->description = (string) $row->description;
        $this->event_at    = optional($row->event_at)->format('Y-m-d\TH:i') ?? now()->format('Y-m-d\TH:i');

        $this->mode = 'edit';
    }

    public function store(): void
    {
        $data = $this->validate($this->rules());
        $user = Auth::user();

        if (!$user->department_id) {
            $this->dispatch('toast', type: 'error', title: 'Gagal', message: 'Akun Anda belum memiliki department.', duration: 5000);
            return;
        }

        InformationModel::create([
            'company_id'    => $user->company_id,
            'department_id' => $user->department_id,
            'description'   => $data['description'],
            'event_at'      => Carbon::parse($data['event_at']),
        ]);

        $this->dispatch('toast', type: 'success', title: 'Created', message: 'Information created.', duration: 3500);
        $this->mode = 'index';
        $this->resetForm();
        $this->resetPage('infoPage');
    }

    public function update(): void
    {
        $this->validate($this->rules());

        $user = Auth::user();
        $row = InformationModel::where('information_id', $this->editingId)
            ->where('company_id', $user->company_id)
            ->where('department_id', $user->department_id)
            ->firstOrFail();

        $row->fill([
            'company_id'    => $user->company_id,
            'department_id' => $user->department_id,
            'description'   => $this->description,
            'event_at'      => Carbon::parse($this->event_at),
        ])->save();

        $this->dispatch('toast', type: 'success', title: 'Updated', message: 'Information updated.', duration: 3500);
        $this->mode = 'index';
        $this->resetForm();
    }

    public function destroy(int $id): void
    {
        $row = InformationModel::where('information_id', $id)
            ->where('company_id', Auth::user()->company_id)
            ->where('department_id', Auth::user()->department_id)
            ->firstOrFail();

        $row->delete();

        $this->dispatch('toast', type: 'success', title: 'Deleted', message: 'Information deleted.', duration: 3500);
        $this->resetPage('infoPage');
    }

    public function cancel(): void
    {
        $this->mode = 'index';
        $this->resetForm();
        $this->resetBroadcastForm();
        $this->informModal = false;
        $this->informBookingId = null;
    }

    public function openBroadcast(): void
    {
        $this->resetBroadcastForm();
        // Tidak perlu view khusus; quick form ada di hero bar
    }

    public function submitBroadcast(): void
    {
        $data = $this->validate($this->broadcastRules());
        $user = Auth::user();

        if (!$user->department_id) {
            $this->dispatch('toast', type: 'error', title: 'Gagal', message: 'Akun Anda belum memiliki department.', duration: 5000);
            return;
        }

        try {
            InformationModel::create([
                'company_id'    => $user->company_id,
                'department_id' => $user->department_id,
                'description'   => $data['broadcast_description'],
                'event_at'      => Carbon::parse($data['broadcast_event_at'])->format('Y-m-d H:i:s'),
            ]);

            $this->dispatch('toast', type: 'success', title: 'Broadcast Sent', message: 'Information broadcast to your department.', duration: 3500);
            $this->resetBroadcastForm();
            $this->resetPage('infoPage');

        } catch (Throwable $e) {
            $this->dispatch('toast', type: 'error', title: 'Error', message: 'Gagal mengirim broadcast.', duration: 5000);
            Log::error('Information submitBroadcast error', ['m' => $e->getMessage()]);
        }
    }

    // ========= REQUEST → INFORM (own department only) =========
    public function openInformModal(int $bookingId): void
    {
        try {
            $this->informBookingId = $bookingId;
            $this->informModal = true;
        } catch (Throwable $e) {
            $this->dispatch('toast', type: 'error', title: 'Error', message: 'Gagal membuka modal.', duration: 5000);
            Log::error('Information openInformModal error', ['m' => $e->getMessage()]);
        }
    }

    public function closeInformModal(): void
    {
        $this->informModal = false;
        $this->informBookingId = null;
    }

    public function submitInform(): void
    {
        $this->validate([
            'informBookingId' => 'required|integer',
        ]);

        $user = Auth::user();
        if (!$user->department_id) {
            $this->dispatch('toast', type: 'error', title: 'Gagal', message: 'Akun Anda belum memiliki department.', duration: 5000);
            return;
        }

        $booking = BookingRoom::query()
            ->with(['user','room','department'])
            ->where('bookingroom_id', $this->informBookingId)
            ->firstOrFail();

        $companyId = $user->company_id;
        $deptId    = $user->department_id;
        $eventAt   = Carbon::parse($booking->date.' '.$booking->start_time)->format('Y-m-d H:i:s');
        $desc      = $this->composeDescription($booking);

        try {
            DB::transaction(function () use ($companyId, $deptId, $eventAt, $desc, $booking) {
                InformationModel::create([
                    'company_id'    => $companyId,
                    'department_id' => $deptId,
                    'description'   => $desc,
                    'event_at'      => $eventAt,
                ]);
                $booking->requestinformation = 'inform';
                $booking->save();
            });

            $this->closeInformModal();
            $this->resetPage('offlinePage');
            $this->resetPage('onlinePage');

            $this->dispatch('toast', type: 'success', title: 'Sent', message: 'Information sent to your department.', duration: 3500);

        } catch (Throwable $e) {
            $this->dispatch('toast', type: 'error', title: 'Error', message: 'Gagal mengirim informasi.', duration: 5000);
            Log::error('Information submitInform error', ['m' => $e->getMessage()]);
        }
    }

    protected function composeDescription(BookingRoom $b): string
    {
        $title  = $b->meeting_title ?: 'Meeting';
        $date   = Carbon::parse($b->date)->translatedFormat('d M Y');
        $start  = Carbon::parse($b->start_time)->format('H:i');
        $end    = Carbon::parse($b->end_time)->format('H:i');
        $by     = optional($b->user)->name ?: 'Unknown';
        $dept   = optional($b->department)->department_name ?: '-';
        $notes  = trim((string) $b->special_notes) ?: '-';
        $attend = (string) ($b->number_of_attendees ?? '-');

        if ($b->booking_type === 'online_meeting') {
            $provider = strtoupper((string) $b->online_provider ?: '-');
            $url      = trim((string) $b->online_meeting_url ?: '-');
            $code     = trim((string) $b->online_meeting_code ?: '-');
            $pass     = trim((string) $b->online_meeting_password ?: '-');

            return implode("\n", [
                "{$title} — ONLINE MEETING",
                "Tanggal/Jam : {$date} {$start}-{$end}",
                "Requester    : {$by} (Dept: {$dept})",
                "Peserta      : {$attend}",
                "Provider     : {$provider}",
                "Join Link    : {$url}",
                "Meeting Code : {$code}",
                "Password     : {$pass}",
                "Catatan      : {$notes}",
            ]);
        }

        $room = optional($b->room)->name ?: 'Room';
        return implode("\n", [
            "{$title} — OFFLINE MEETING",
            "Tanggal/Jam : {$date} {$start}-{$end}",
            "Ruangan     : {$room}",
            "Requester   : {$by} (Dept: {$dept})",
            "Peserta     : {$attend}",
            "Catatan     : {$notes}",
        ]);
    }

    // ========= RENDER =========
    public function render()
    {
        $user      = Auth::user();
        $companyId = $user->company_id;
        $deptId    = $user->department_id;

        // Request lists (company-scope; action push ke own dept)
        $offline = BookingRoom::query()
            ->with(['room','user','department'])
            ->where('company_id', $companyId)
            ->whereNull('deleted_at')
            ->where('booking_type', 'meeting')
            ->where('requestinformation', 'request')
            ->where('status', 'approved')
            ->orderByDesc('date')->orderByDesc('start_time')
            ->paginate($this->perPageReq, ['*'], 'offlinePage');

        $online = BookingRoom::query()
            ->with(['room','user','department'])
            ->where('company_id', $companyId)
            ->whereNull('deleted_at')
            ->where('booking_type', 'online_meeting')
            ->where('requestinformation', 'request')
            ->where('status', 'approved')
            ->orderByDesc('date')->orderByDesc('start_time')
            ->paginate($this->perPageReq, ['*'], 'onlinePage');

        // Information table: ONLY own department
        $rows = InformationModel::query()
            ->where('company_id', $companyId)
            ->where('department_id', $deptId)
            ->when($this->search, fn($q) =>
                $q->where('description', 'like', '%'.$this->search.'%')
            )
            ->orderByDesc('event_at')
            ->paginate($this->perPageInfo, ['*'], 'infoPage');

        return view('livewire.pages.admin.information', [
            'offline' => $offline,
            'online'  => $online,
            'rows'    => $rows,
        ]);
    }
}
