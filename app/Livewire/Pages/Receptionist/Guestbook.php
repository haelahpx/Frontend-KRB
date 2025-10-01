<?php

namespace App\Livewire\Pages\Receptionist;

use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use App\Models\Guestbook as GuestbookModel;

#[Layout('layouts.receptionist')]
#[Title('GuestBook')]
class Guestbook extends Component
{
    use WithPagination;

    public $date, $jam_in, $name, $phone_number, $instansi, $keperluan, $petugas_penjaga;
    public $filter_date;
    public $q = '';
    public bool $showEdit = false;
    public $editId = null;
    public $edit = [
        'date' => null,
        'jam_in' => null,
        'jam_out' => null,
        'name' => null,
        'phone_number' => null,
        'instansi' => null,
        'keperluan' => null,
        'petugas_penjaga' => null,
    ];

    public function mount(): void
    {
        $this->date = $this->date ?: now()->format('Y-m-d');
    }

    protected function rules(): array
    {
        return [
            'date' => ['required', 'date'],
            'jam_in' => ['required', 'date_format:H:i'],
            'name' => ['required', 'string', 'max:255'],
            'phone_number' => ['nullable', 'string', 'max:50'],
            'instansi' => ['nullable', 'string', 'max:255'],
            'keperluan' => ['nullable', 'string', 'max:255'],
            'petugas_penjaga' => ['required', 'string', 'max:255'],
        ];
    }

    protected function rulesEdit(): array
    {
        return [
            'edit.date' => ['required', 'date'],
            'edit.jam_in' => ['required', 'date_format:H:i'],
            'edit.jam_out' => ['nullable', 'date_format:H:i'],
            'edit.name' => ['required', 'string', 'max:255'],
            'edit.phone_number' => ['nullable', 'string', 'max:50'],
            'edit.instansi' => ['nullable', 'string', 'max:255'],
            'edit.keperluan' => ['nullable', 'string', 'max:255'],
            'edit.petugas_penjaga' => ['required', 'string', 'max:255'],
        ];
    }
    public function updatedDate($v): void
    {
        $this->date = $this->normalizeDate($v);
    }
    public function updatedEditDate($v): void
    {
        $this->edit['date'] = $this->normalizeDate($v);
    }
    public function updatedJamIn($v): void
    {
        $this->jam_in = $this->normalizeTime($v);
    }
    public function updatedEditJamIn($v): void
    {
        $this->edit['jam_in'] = $this->normalizeTime($v);
    }
    public function updatedEditJamOut($v): void
    {
        $this->edit['jam_out'] = $this->normalizeTime($v, true);
    }
    private function normalizeDate($v): ?string
    {
        if (!$v) return null;
        try {
            return Carbon::parse(str_replace('/', '-', $v))->format('Y-m-d');
        } catch (\Throwable $e) {
            return $v;
        }
    }
    private function normalizeTime($v, bool $nullable = false): ?string
    {
        if ($nullable && $v === '') return null;
        if (!$v) return null;
        try {
            return Carbon::parse($v)->format('H:i');
        } catch (\Throwable $e) {
            return $v;
        }
    }
    private function companyId()
    {
        return optional(Auth::user())->company_id;
    }
    private function findOwnedOrFail(int $id): GuestbookModel
    {
        return GuestbookModel::whereKey($id)
            ->where('company_id', $this->companyId())
            ->firstOrFail();
    }
    public function save(): void
    {
        $this->date   = $this->normalizeDate($this->date);
        $this->jam_in = $this->normalizeTime($this->jam_in);
        $this->validate();
        GuestbookModel::create([
            'company_id'       => $this->companyId(),   
            'date'             => $this->date,
            'jam_in'           => $this->jam_in,
            'jam_out'          => null, 
            'name'             => $this->name,
            'phone_number'     => $this->phone_number,
            'instansi'         => $this->instansi,
            'keperluan'        => $this->keperluan,
            'petugas_penjaga'  => $this->petugas_penjaga,
        ]);
        $this->reset(['jam_in', 'name', 'phone_number', 'instansi', 'keperluan', 'petugas_penjaga']);
        $this->dispatch('$refresh');
        $this->dispatch('toast', type: 'success', title: 'Ditambah', message: 'Guest ditambah.', duration: 3000);
        session()->flash('saved', true);
    }
    public function openEdit(int $id): void
    {
        $row = $this->findOwnedOrFail($id);
        $this->editId = $row->getKey();
        $this->edit = [
            'date'            => $row->date ? Carbon::parse($row->date)->format('Y-m-d') : null,
            'jam_in'          => $row->jam_in ? Carbon::parse($row->jam_in)->format('H:i') : null,
            'jam_out'         => $row->jam_out ? Carbon::parse($row->jam_out)->format('H:i') : null,
            'name'            => $row->name,
            'phone_number'    => $row->phone_number,
            'instansi'        => $row->instansi,
            'keperluan'       => $row->keperluan,
            'petugas_penjaga' => $row->petugas_penjaga,
        ];
        $this->resetValidation();
        $this->showEdit = true;
    }
    public function saveEdit(): void
    {
        $this->validate($this->rulesEdit());
        $row = $this->findOwnedOrFail($this->editId);
        $row->update([
            'date'            => $this->edit['date'],
            'jam_in'          => $this->edit['jam_in'],
            'jam_out'         => $this->edit['jam_out'] ?: null,
            'name'            => $this->edit['name'],
            'phone_number'    => $this->edit['phone_number'],
            'instansi'        => $this->edit['instansi'],
            'keperluan'       => $this->edit['keperluan'],
            'petugas_penjaga' => $this->edit['petugas_penjaga'],
        ]);
        $this->showEdit = false;
        $this->dispatch('toast', type: 'success', title: 'Update', message: 'Guest diedit.', duration: 3000);
        $this->dispatch('$refresh');
    }
    public function delete(int $id): void
    {
        $row = $this->findOwnedOrFail($id);
        $row->delete();
        $this->dispatch('toast', type: 'success', title: 'Dihapus', message: 'Guest dihapus.', duration: 3000);
        $this->dispatch('$refresh');
    }
    public function setJamKeluarNow(int $id): void
    {
        $row = $this->findOwnedOrFail($id);
        if ($row->jam_out) {
            $this->dispatch('notify', type: 'warning', message: 'Jam keluar sudah diisi.');
            return;
        }
        $now = Carbon::now(config('app.timezone', 'Asia/Jakarta'))->format('H:i');
        $row->update(['jam_out' => $now]);
        $this->dispatch('notify', type: 'success', message: "Keluar: {$now}. Dipindah ke Riwayat Kunjungan.");
        $this->dispatch('$refresh');
    }
    public function getTodayLatestProperty()
    {
        return GuestbookModel::where('company_id', $this->companyId())
            ->whereDate('date', now()->toDateString())
            ->whereNull('jam_out')
            ->latest('created_at')
            ->take(10)
            ->get();
    }
    public function getEntriesProperty()
    {
        $q = GuestbookModel::query()
            ->where('company_id', $this->companyId())
            ->whereNotNull('jam_out');
        if ($this->filter_date) {
            $q->whereDate('date', $this->filter_date);
        }
        if ($this->q) {
            $term = '%' . $this->q . '%';
            $q->where(function ($w) use ($term) {
                $w->where('name', 'like', $term)
                ->orWhere('phone_number', 'like', $term)
                ->orWhere('instansi', 'like', $term)
                ->orWhere('keperluan', 'like', $term)
                ->orWhere('petugas_penjaga', 'like', $term);
            });
        }
        return $q->latest('created_at')->paginate(10);
    }
    public function getServerClockProperty(): string
    {
        return Carbon::now(config('app.timezone', 'Asia/Jakarta'))->format('H:i:s');
    }
    public function closeEdit(): void
    {
        $this->showEdit = false;
        $this->resetValidation();
    }
    public function render()
    {
        return view('livewire.pages.receptionist.guestbook', [
            'todayLatest' => $this->todayLatest,
            'entries'     => $this->entries,
        ]);
    }
}
