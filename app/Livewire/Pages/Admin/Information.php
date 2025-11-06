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
use App\Models\Department;

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

    // ====== FORM FIELDS ======
    public string $description = '';
    public string $event_at   = ''; // Y-m-d\TH:i

    // ====== BROADCAST (own department only; now uses selected dept) ======
    public string $broadcast_description = '';
    public string $broadcast_event_at    = '';

    // ====== REQUEST→INFORM MODAL ======
    public bool  $informModal = false;
    public ?int  $informBookingId = null;

    // ====== HEADER (hero) & DEPT SWITCHER ======
    public string $company_name    = '-';
    public string $department_name = '-'; // resolved from selected
    public array  $deptOptions     = [];  // [ ['id'=>1,'name'=>'...'], ... ]
    public ?int   $selected_department_id = null; // live switch
    public ?int   $primary_department_id  = null; // users.department_id

    public bool $showSwitcher = false;          

    public function mount(): void
    {
        try {
            $auth = Auth::user()->loadMissing(['company', 'department']);
            $this->company_name = optional($auth->company)->company_name ?? '-';

            // Primary dari users.department_id
            $this->primary_department_id = $auth->department_id ?: null;

            // Muat daftar departemen yang dimiliki user (pivot user_departments)
            $this->loadUserDepartments();

            // Set selected: prioritas primary; fallback ke pertama di list
            if (!$this->selected_department_id) {
                $this->selected_department_id = $this->primary_department_id
                    ?: ($this->deptOptions[0]['id'] ?? null);
            }

            $this->department_name = $this->resolveDeptName($this->selected_department_id);

            $this->resetForm();
            $this->resetBroadcastForm();

        } catch (Throwable $e) {
            $this->dispatch('toast', type: 'error', title: 'Error', message: 'Gagal memuat header.', duration: 5000);
            Log::error('Information mount error', ['m' => $e->getMessage()]);
        }
    }

    protected function loadUserDepartments(): void
    {
        $user = Auth::user();

        $rows = DB::table('user_departments as ud')
            ->join('departments as d', 'd.department_id', '=', 'ud.department_id')
            ->where('ud.user_id', $user->user_id) // PK kamu pakai user_id
            ->orderBy('d.department_name')
            ->get(['d.department_id as id', 'd.department_name as name']);

        $this->deptOptions = $rows->map(fn($r) => ['id' => (int)$r->id, 'name' => (string)$r->name])->values()->all();

        $this->showSwitcher = true; 

        // kalau user tidak punya pivot apapun, tetapi punya primary, masukkan primary supaya tetap bisa jalan
        if (empty($this->deptOptions) && $this->primary_department_id) {
            $name = Department::where('department_id', $this->primary_department_id)->value('department_name') ?? 'Unknown';
            $this->deptOptions = [['id' => (int)$this->primary_department_id, 'name' => (string)$name]];

            $this->showSwitcher = false;
        }
    }

    protected function resolveDeptName(?int $deptId): string
    {
        if (!$deptId) return '-';
        foreach ($this->deptOptions as $opt) {
            if ($opt['id'] === (int)$deptId) {
                return $opt['name'];
            }
        }
        // fallback query (kalau tidak ada di list karena perubahan data)
        return Department::where('department_id', $deptId)->value('department_name') ?? '-';
    }

    public function resetToPrimaryDepartment(): void
    {
        if ($this->primary_department_id) {
            $this->selected_department_id = $this->primary_department_id;
            $this->department_name = $this->resolveDeptName($this->selected_department_id);
            $this->resetPaginationForAll();
            $this->dispatch('toast', type: 'success', title: 'Switched', message: 'Kembali ke primary department.', duration: 2000);
        }
    }

    public function updatedSelectedDepartment_id(): void
    {
        // Livewire camelCase <-> snakeCase guard; jika method ini tidak terpanggil di versimu,
        // gunakan updatedSelectedDepartmentId() di bawah
        $this->updatedSelectedDepartmentId();
    }

    public function updatedSelectedDepartmentId(): void
    {
        $id = (int) $this->selected_department_id;

        // Validasi: hanya boleh memilih dari deptOptions
        $allowed = collect($this->deptOptions)->pluck('id')->all();
        if (!in_array($id, $allowed, true)) {
            // fallback ke primary atau first
            $this->selected_department_id = $this->primary_department_id ?: ($this->deptOptions[0]['id'] ?? null);
            $id = (int) $this->selected_department_id;
        }

        $this->department_name = $this->resolveDeptName($id);
        $this->resetPaginationForAll();
    }

    protected function currentDeptId(): ?int
    {
        return $this->selected_department_id ?: $this->primary_department_id;
    }

    protected function resetPaginationForAll(): void
    {
        $this->resetPage('infoPage');
        $this->resetPage('offlinePage');
        $this->resetPage('onlinePage');
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
        $user = Auth::user();
        $deptId = $this->currentDeptId();

        $row = InformationModel::where('information_id', $id)
            ->where('company_id', $user->company_id)
            ->where('department_id', $deptId)
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
        $deptId = $this->currentDeptId();

        if (!$deptId) {
            $this->dispatch('toast', type: 'error', title: 'Gagal', message: 'Tidak ada departemen terpilih.', duration: 5000);
            return;
        }

        InformationModel::create([
            'company_id'    => $user->company_id,
            'department_id' => $deptId,
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

        $user   = Auth::user();
        $deptId = $this->currentDeptId();

        $row = InformationModel::where('information_id', $this->editingId)
            ->where('company_id', $user->company_id)
            ->where('department_id', $deptId)
            ->firstOrFail();

        $row->fill([
            'company_id'    => $user->company_id,
            'department_id' => $deptId,
            'description'   => $this->description,
            'event_at'      => Carbon::parse($this->event_at),
        ])->save();

        $this->dispatch('toast', type: 'success', title: 'Updated', message: 'Information updated.', duration: 3500);
        $this->mode = 'index';
        $this->resetForm();
    }

    public function destroy(int $id): void
    {
        $user   = Auth::user();
        $deptId = $this->currentDeptId();

        $row = InformationModel::where('information_id', $id)
            ->where('company_id', $user->company_id)
            ->where('department_id', $deptId)
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
    }

    public function submitBroadcast(): void
    {
        $data  = $this->validate($this->broadcastRules());
        $user  = Auth::user();
        $deptId = $this->currentDeptId();

        if (!$deptId) {
            $this->dispatch('toast', type: 'error', title: 'Gagal', message: 'Tidak ada departemen terpilih.', duration: 5000);
            return;
        }

        try {
            InformationModel::create([
                'company_id'    => $user->company_id,
                'department_id' => $deptId,
                'description'   => $data['broadcast_description'],
                'event_at'      => Carbon::parse($data['broadcast_event_at'])->format('Y-m-d H:i:s'),
            ]);

            $this->dispatch('toast', type: 'success', title: 'Broadcast Sent', message: 'Information broadcast ke departemen terpilih.', duration: 3500);
            $this->resetBroadcastForm();
            $this->resetPage('infoPage');

        } catch (Throwable $e) {
            $this->dispatch('toast', type: 'error', title: 'Error', message: 'Gagal mengirim broadcast.', duration: 5000);
            Log::error('Information submitBroadcast error', ['m' => $e->getMessage()]);
        }
    }

    // ========= REQUEST → INFORM =========
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

        $user    = Auth::user();
        $deptId  = $this->currentDeptId();

        if (!$deptId) {
            $this->dispatch('toast', type: 'error', title: 'Gagal', message: 'Tidak ada departemen terpilih.', duration: 5000);
            return;
        }

        $booking = BookingRoom::query()
            ->with(['user','room','department'])
            ->where('bookingroom_id', $this->informBookingId)
            ->firstOrFail();

        $companyId = $user->company_id;
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

            $this->dispatch('toast', type: 'success', title: 'Sent', message: 'Information dikirim ke departemen terpilih.', duration: 3500);

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
        $deptId    = $this->currentDeptId();

        // Request lists (company-scope; action push ke dept terpilih)
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

        // Information table: ONLY department terpilih
        $rows = InformationModel::query()
            ->where('company_id', $companyId)
            ->when($deptId, fn($q) => $q->where('department_id', $deptId))
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
