<?php

namespace App\Livewire\Pages\Receptionist;

use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Models\Guestbook as GuestbookModel;

#[Layout('layouts.receptionist')]
#[Title('GuestBook History')]
class GuestbookHistory extends Component
{
    use WithPagination;

    protected string $paginationTheme = 'tailwind';

    // Per-page controls
    public int $perLatest = 5;   // for "Kunjungan Terbaru"
    public int $perEntries = 5;   // for "Riwayat Kunjungan"

    // Filters for history box
    public ?string $filter_date = null;  // YYYY-MM-DD
    public string $q = '';

    // Sorting (follow BookingsApproval pattern)
    public string $dateMode = 'semua'; // semua | terbaru | terlama

    // Include soft-deleted rows in history list
    public bool $withTrashed = false;

    // Edit modal state
    public bool $showEdit = false;
    public ?int $editId = null;

    public array $edit = [
        'date' => null,
        'jam_in' => null,
        'jam_out' => null,
        'name' => null,
        'phone_number' => null,
        'instansi' => null,
        'keperluan' => null,
        'petugas_penjaga' => null,
    ];

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

    /** ==== Normalizers for edit ==== */
    public function updatedEditDate($v): void
    {
        $this->edit['date'] = $this->normalizeDate($v);
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
        if (!$v)
            return null;
        try {
            return Carbon::parse(str_replace('/', '-', $v))->format('Y-m-d');
        } catch (\Throwable) {
            return $v;
        }
    }

    private function normalizeTime($v, bool $nullable = false): ?string
    {
        if ($nullable && $v === '')
            return null;
        if (!$v)
            return null;
        try {
            return Carbon::parse($v)->format('H:i');
        } catch (\Throwable) {
            return $v;
        }
    }

    private function companyId(): ?int
    {
        return Auth::user()?->company_id;
    }

    /** Pastikan data milik company yang sama; tidak ikut yang sudah di-trashed kecuali withTrashed() */
    private function findOwnedOrFail(int $id): GuestbookModel
    {
        return GuestbookModel::withTrashed()
            ->whereKey($id)
            ->where('company_id', $this->companyId())
            ->firstOrFail();
    }

    /** ==== Reset the correct paginator when filters change ==== */
    public function updatingQ(): void
    {
        $this->resetPage('entriesPage');
    }
    public function updatingFilterDate(): void
    {
        $this->resetPage('entriesPage');
    }
    public function updatedWithTrashed(): void
    {
        $this->resetPage('entriesPage');
    }
    public function updatedDateMode(): void
    {
        $this->resetPage('entriesPage');
    }
    public function updatedPerLatest(): void
    {
        $this->resetPage('latestPage');
    }
    public function updatedPerEntries(): void
    {
        $this->resetPage('entriesPage');
    }

    /** Sorting helper like BookingsApproval */
    private function sortingDirection(): string
    {
        return $this->dateMode === 'terlama' ? 'ASC' : 'DESC';
    }

    /** ==== Computed props with pagination ==== */

    /** Kunjungan hari ini yang BELUM keluar, paginated (independent page name) */
    public function getLatestProperty()
    {
        $q = GuestbookModel::where('company_id', $this->companyId())
            ->whereDate('date', now()->toDateString())
            ->whereNull('jam_out');

        // Order newer first
        $q->orderByDesc('created_at');

        return $q->paginate($this->perLatest, ['*'], 'latestPage');
    }

    /** Riwayat kunjungan (sudah keluar), with soft delete toggle, paginated (independent page name) */
    public function getEntriesProperty()
    {
        $q = GuestbookModel::query()
            ->where('company_id', $this->companyId())
            ->whereNotNull('jam_out');

        // withTrashed toggle
        if ($this->withTrashed) {
            $q->withTrashed();
        } else {
            $q->whereNull('deleted_at');
        }

        if ($this->filter_date) {
            $q->whereDate('date', $this->filter_date);
        }

        if ($this->q !== '') {
            $term = '%' . $this->q . '%';
            $q->where(function ($w) use ($term) {
                $w->where('name', 'like', $term)
                    ->orWhere('phone_number', 'like', $term)
                    ->orWhere('instansi', 'like', $term)
                    ->orWhere('keperluan', 'like', $term)
                    ->orWhere('petugas_penjaga', 'like', $term);
            });
        }

        // Sort by date+jam_in similar to BookingsApproval (dateMode)
        $dir = $this->sortingDirection();
        $dtExpr = "COALESCE(
            CASE WHEN `jam_out` REGEXP '^[0-9]{2}:' THEN CONCAT(`date`, ' ', `jam_out`) ELSE CONCAT(`date`, ' ', `jam_in`) END,
            CONCAT(`date`, ' 00:00:00')
        )";
        $q->orderByRaw("$dtExpr $dir")->orderByDesc('created_at');

        return $q->paginate($this->perEntries, ['*'], 'entriesPage');
    }

    public function getServerClockProperty(): string
    {
        return Carbon::now(config('app.timezone', 'Asia/Jakarta'))->format('H:i:s');
    }

    /** ====== Actions for history (edit/delete/restore) ====== */
    public function openEdit(int $id): void
    {
        $row = $this->findOwnedOrFail($id);

        $this->editId = $row->getKey();
        $this->edit = [
            'date' => $row->date ? Carbon::parse($row->date)->format('Y-m-d') : null,
            'jam_in' => $row->jam_in ? Carbon::parse($row->jam_in)->format('H:i') : null,
            'jam_out' => $row->jam_out ? Carbon::parse($row->jam_out)->format('H:i') : null,
            'name' => $row->name,
            'phone_number' => $row->phone_number,
            'instansi' => $row->instansi,
            'keperluan' => $row->keperluan,
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
            'date' => $this->edit['date'],
            'jam_in' => $this->edit['jam_in'],
            'jam_out' => $this->edit['jam_out'] ?: null,
            'name' => $this->edit['name'],
            'phone_number' => $this->edit['phone_number'],
            'instansi' => $this->edit['instansi'],
            'keperluan' => $this->edit['keperluan'],
            'petugas_penjaga' => $this->edit['petugas_penjaga'],
        ]);

        $this->showEdit = false;
        $this->dispatch('toast', type: 'success', title: 'Update', message: 'Guest diedit.', duration: 3000);
        $this->dispatch('$refresh');
    }

    /** Keluar sekarang (set jam_out real-time) */
    public function setJamKeluarNow(int $id): void
    {
        $row = $this->findOwnedOrFail($id);
        $row->update([
            'jam_out' => Carbon::now()->format('H:i'),
        ]);
        $this->dispatch('toast', type: 'success', title: 'Keluar', message: 'Jam keluar diset sekarang.', duration: 2500);
        $this->dispatch('$refresh');
    }

    /** SOFT DELETE */
    public function delete(int $id): void
    {
        $row = $this->findOwnedOrFail($id);
        $row->delete();

        // If page becomes empty after deletion, go back one page
        $entries = $this->getEntriesProperty();
        if ($entries->isEmpty() && $entries->currentPage() > 1) {
            $this->setPage($entries->currentPage() - 1, 'entriesPage');
        }

        $this->dispatch('toast', type: 'success', title: 'Dihapus', message: 'Guest dihapus (soft delete).', duration: 3000);
        $this->dispatch('$refresh');
    }

    public function restore(int $id): void
    {
        $row = GuestbookModel::onlyTrashed()
            ->where('company_id', $this->companyId())
            ->whereKey($id)
            ->first();

        if ($row) {
            $row->restore();
            $this->dispatch('toast', type: 'success', title: 'Pulihkan', message: 'Guest dipulihkan.', duration: 2500);
            $this->dispatch('$refresh');
        }
    }

    public function destroyForever(int $id): void
    {
        $row = GuestbookModel::onlyTrashed()
            ->where('company_id', $this->companyId())
            ->whereKey($id)
            ->first();

        if ($row) {
            $row->forceDelete();
            $this->dispatch('toast', type: 'success', title: 'Hapus Permanen', message: 'Guest dihapus permanen.', duration: 2500);
            $this->dispatch('$refresh');
        }
    }

    public function closeEdit(): void
    {
        $this->showEdit = false;
        $this->resetValidation();
    }

    public function render()
    {
        return view('livewire.pages.receptionist.guestbookhistory', [
            'latest' => $this->latest,
            'entries' => $this->entries,
        ]);
    }
}
