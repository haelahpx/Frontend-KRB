<?php

namespace App\Livewire\Pages\Receptionist;

use App\Models\Documents as DocumentModel;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.receptionist')]
#[Title('Documents')]
class Documents extends Component
{
    use WithPagination;

    protected string $paginationTheme = 'tailwind';
    protected $queryString = ['q', 'filter_date'];

    // === form fields (matched to your columns) ===
    public $document_name, $nama_pengirim, $nama_penerima;
    public $type = 'document';
    public $penyimpanan;
    public $pengambilan_date, $pengambilan_time; // combined into pengambilan (datetime)
    public $status = 'pending';

    // filters/ui
    public $filter_date;
    public $q = '';
    public bool $showEdit = false;
    public $editId = null;

    // edit buffer
    public $edit = [
        'document_name'    => null,
        'nama_pengirim'    => null,
        'nama_penerima'    => null,
        'type'             => 'document',
        'penyimpanan'      => null,
        'pengambilan_date' => null,
        'pengambilan_time' => null,
        'pengiriman'       => null, // datetime-local
        'status'           => 'pending',
    ];

    private function companyId()
    {
        return optional(Auth::user())->company_id;
    }

    private function findOwnedOrFail(int $id): DocumentModel
    {
        $row = DocumentModel::whereKey($id)
            ->where('company_id', $this->companyId())
            ->first();

        if (!$row) {
            throw new ModelNotFoundException('Document not found or not owned.');
        }
        return $row;
    }

    private function combineDateTime(?string $date, ?string $time): ?Carbon
    {
        if (empty($date) && empty($time)) return null;
        if (empty($date)) return null;
        $time = $time ?: '00:00';
        return Carbon::createFromFormat('Y-m-d H:i', "{$date} {$time}", config('app.timezone', 'Asia/Jakarta'));
    }

    protected function rules(): array
    {
        return [
            'document_name'    => ['required', 'string', 'max:255'],
            'nama_pengirim'    => ['nullable', 'string', 'max:255'],
            'nama_penerima'    => ['nullable', 'string', 'max:255'],
            'type'             => ['required', 'in:document,invoice,etc'],
            'penyimpanan'      => ['nullable', 'string', 'max:50'],
            'pengambilan_date' => ['nullable', 'date'],
            'pengambilan_time' => ['nullable', 'date_format:H:i'],
            'status'           => ['required', 'in:pending,taken,delivered'],
        ];
    }

    protected function rulesEdit(): array
    {
        return [
            'edit.document_name'    => ['required', 'string', 'max:255'],
            'edit.nama_pengirim'    => ['nullable', 'string', 'max:255'],
            'edit.nama_penerima'    => ['nullable', 'string', 'max:255'],
            'edit.type'             => ['required', 'in:document,invoice,etc'],
            'edit.penyimpanan'      => ['nullable', 'string', 'max:50'],
            'edit.pengambilan_date' => ['nullable', 'date'],
            'edit.pengambilan_time' => ['nullable', 'date_format:H:i'],
            'edit.pengiriman'       => ['nullable', 'date'],
            'edit.status'           => ['required', 'in:pending,taken,delivered'],
        ];
    }

    public function updatedQ(): void { $this->resetPage(); }
    public function updatedFilterDate(): void { $this->resetPage(); }

    public function save(): void
    {
        $data = $this->validate();

        // nullify empty string inputs
        foreach (['penyimpanan'] as $k) {
            if (!array_key_exists($k, $data) || $data[$k] === '') $data[$k] = null;
        }

        $pengambilan = $this->combineDateTime($data['pengambilan_date'] ?? null, $data['pengambilan_time'] ?? null);
        $statusInput  = $data['status'] ?? 'pending';

        $now        = Carbon::now(config('app.timezone', 'Asia/Jakarta'));
        $pengiriman = ($statusInput === 'delivered') ? $now : null;

        $payload = [
            'company_id'      => $this->companyId(),
            // ✅ your table has 'receptionist_id' (NOT user_id / department_id)
            'receptionist_id' => Auth::id(),
            'document_name'   => $data['document_name'],
            'nama_pengirim'   => $data['nama_pengirim'] ?? null,
            'nama_penerima'   => $data['nama_penerima'] ?? null,
            'type'            => $data['type'],
            'penyimpanan'     => $data['penyimpanan'] ?? null,
            'pengambilan'     => $pengambilan,
            'pengiriman'      => $pengiriman,
            'status'          => $statusInput,
        ];

        DocumentModel::create($payload);

        $this->resetForm();
        session()->flash('saved', true);

        $this->dispatch('notify', type: 'success', message: match($statusInput) {
            'delivered' => 'Dokumen langsung masuk Riwayat (Delivered).',
            'taken'     => 'Dokumen disimpan ke kotak Taken.',
            default     => 'Dokumen disimpan ke kotak Pending.',
        });

        $this->dispatch('$refresh');
    }

    public function openEdit(int $id): void
    {
        $r = $this->findOwnedOrFail($id);
        $this->editId = $r->getKey();

        $this->edit = [
            'document_name'    => $r->document_name,
            'nama_pengirim'    => $r->nama_pengirim,
            'nama_penerima'    => $r->nama_penerima,
            'type'             => $r->type,
            'penyimpanan'      => $r->penyimpanan,
            'pengambilan_date' => optional($r->pengambilan)?->format('Y-m-d'),
            'pengambilan_time' => optional($r->pengambilan)?->format('H:i'),
            'pengiriman'       => optional($r->pengiriman)?->format('Y-m-d\TH:i'),
            'status'           => $r->status,
        ];

        $this->resetValidation();
        $this->showEdit = true;
    }

    public function saveEdit(): void
    {
        $this->validate($this->rulesEdit());
        $row = $this->findOwnedOrFail($this->editId);

        $pengambilan = $this->combineDateTime(
            $this->edit['pengambilan_date'] ?? null,
            $this->edit['pengambilan_time'] ?? null
        );

        $desiredStatus = $this->edit['status'] ?? 'pending';

        if ($desiredStatus !== 'delivered') {
            $pengiriman = null;
            $status = $desiredStatus;
        } else {
            $pengiriman = !empty($this->edit['pengiriman'])
                ? Carbon::parse($this->edit['pengiriman'], config('app.timezone', 'Asia/Jakarta'))
                : Carbon::now(config('app.timezone', 'Asia/Jakarta'));
            $status = 'delivered';
        }

        $row->update([
            'document_name' => $this->edit['document_name'],
            'nama_pengirim' => $this->edit['nama_pengirim'],
            'nama_penerima' => $this->edit['nama_penerima'],
            'type'          => $this->edit['type'],
            'penyimpanan'   => $this->edit['penyimpanan'] !== '' ? $this->edit['penyimpanan'] : null,
            'pengambilan'   => $pengambilan,
            'pengiriman'    => $pengiriman,
            'status'        => $status,
        ]);

        $this->showEdit = false;
        $this->dispatch('notify', type: 'success', message: 'Perubahan disimpan. Posisi kartu diperbarui sesuai status.');
        $this->dispatch('$refresh');
    }

    public function setSudahDikirim(int $id): void
    {
        $row = $this->findOwnedOrFail($id);

        if ($row->pengiriman) {
            $this->dispatch('notify', type: 'warning', message: 'Dokumen sudah dikirim.');
            return;
        }

        $now = Carbon::now(config('app.timezone', 'Asia/Jakarta'));

        $row->update([
            'pengiriman' => $now,
            'status'     => 'delivered',
        ]);

        $this->dispatch('notify', type: 'success', message: "Dikirim: {$now->format('d M Y H:i')}. Pindah ke Riwayat.");
        $this->dispatch('$refresh');
    }

    public function setPengambilanNow(): void
    {
        $now = Carbon::now(config('app.timezone', 'Asia/Jakarta'));
        $this->pengambilan_date = $now->format('Y-m-d');
        $this->pengambilan_time = $now->format('H:i');
        $this->dispatch('notify', type: 'info', message: 'Pengambilan di-set ke waktu saat ini.');
    }

    public function setEditPengambilanNow(): void
    {
        $now = Carbon::now(config('app.timezone', 'Asia/Jakarta'));
        $this->edit['pengambilan_date'] = $now->format('Y-m-d');
        $this->edit['pengambilan_time'] = $now->format('H:i');
        $this->dispatch('notify', type: 'info', message: 'Pengambilan (edit) di-set ke waktu saat ini.');
    }

    public function closeEdit(): void
    {
        $this->showEdit = false;
        $this->resetValidation();
    }

    public function delete(int $id): void
    {
        $this->findOwnedOrFail($id)->delete();
        $this->dispatch('notify', type: 'success', message: 'Dokumen dihapus.');
        $this->dispatch('$refresh');
    }

    private function resetForm(): void
    {
        $this->reset([
            'document_name',
            'nama_pengirim',
            'nama_penerima',
            'type',
            'penyimpanan',
            'pengambilan_date',
            'pengambilan_time',
            'status',
            'editId',
            'showEdit',
            'q',
            'filter_date',
        ]);

        $this->type   = 'document';
        $this->status = 'pending';
    }

    // ===== Lists =====
    public function getPendingListProperty()
    {
        return DocumentModel::forCompany($this->companyId())
            ->where('status', 'pending')
            ->whereNull('pengiriman')
            ->latest('created_at')
            ->take(50)
            ->get();
    }

    public function getTakenListProperty()
    {
        return DocumentModel::forCompany($this->companyId())
            ->where('status', 'taken')
            ->whereNull('pengiriman')
            ->latest('created_at')
            ->take(50)
            ->get();
    }

    public function getEntriesProperty()
    {
        $q = DocumentModel::forCompany($this->companyId())
            ->whereNotNull('pengiriman'); // delivered only

        if ($this->filter_date) {
            $q->whereDate('pengambilan', $this->filter_date);
        }

        $q->search($this->q);

        return $q->latest('pengiriman')->paginate(10);
    }

    public function getServerClockProperty(): string
    {
        return Carbon::now(config('app.timezone', 'Asia/Jakarta'))->format('H:i:s');
    }

    public function render()
    {
        return view('livewire.pages.receptionist.documents', [
            'pendingList' => $this->pendingList,
            'takenList'   => $this->takenList,
            'entries'     => $this->entries,
        ]);
    }
}
