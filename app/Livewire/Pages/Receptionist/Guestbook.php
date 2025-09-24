<?php

namespace App\Livewire\Pages\Receptionist;

use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Carbon;
use App\Models\Guestbook as GuestbookModel; // ⬅️ alias the model

#[Layout('layouts.receptionist')]
#[Title('Buku Tamu')]
class Guestbook extends Component
{
    use WithPagination;

    public ?string $tanggal = null;
    public ?string $nama = null;
    public ?string $no_hp = null;
    public ?string $jam_in = null;
    public ?string $jam_out = null;
    public ?string $instansi = null;
    public ?string $keperluan = null;
    public ?string $petugas_penjaga = null;
    public ?string $filter_date = null;
    public ?string $q = null;

    protected function rules(): array
    {
        return [
            'tanggal' => ['required', 'date'],
            'nama' => ['required', 'string', 'max:120'],
            'no_hp' => ['nullable', 'string', 'max:30'],
            'jam_in' => ['required', 'date_format:H:i'],
            'jam_out' => ['nullable', 'date_format:H:i', 'after_or_equal:jam_in'],
            'instansi' => ['nullable', 'string', 'max:120'],
            'keperluan' => ['nullable', 'string', 'max:200'],
            'petugas_penjaga' => ['required', 'string', 'max:120'],
        ];
    }

    public function mount(): void
    {
        $today = Carbon::today()->toDateString();
        $this->tanggal = $today;
        $this->jam_in = now()->format('H:i');
        $this->filter_date = $today;
    }

    public function updating($field): void
    {
        if (in_array($field, ['filter_date', 'q'], true)) {
            $this->resetPage();
        }
    }

    public function save(): void
    {
        $data = $this->validate();
        GuestbookModel::create($data); // ⬅️ use the alias

        // reset some fields
        $this->nama = $this->no_hp = $this->instansi = $this->keperluan = $this->petugas_penjaga = null;
        $this->jam_in = now()->format('H:i');
        $this->jam_out = null;

        $this->dispatch('toast', type: 'success', title: 'Tersimpan', message: 'Buku tamu ditambahkan.');
    }

    public function render()
    {
        $entries = GuestbookModel::query() // ⬅️ use the alias
            ->when($this->filter_date, fn($q) => $q->whereDate('tanggal', $this->filter_date))
            ->when($this->q, function ($q) {
                $term = '%' . trim($this->q) . '%';
                $q->where(function ($qq) use ($term) {
                    $qq->where('nama', 'like', $term)
                        ->orWhere('no_hp', 'like', $term)
                        ->orWhere('instansi', 'like', $term)
                        ->orWhere('keperluan', 'like', $term)
                        ->orWhere('petugas_penjaga', 'like', $term);
                });
            })
            ->orderByDesc('created_at')
            ->paginate(10);

        // you referenced $todayLatest in the view — provide it here
        $todayLatest = GuestbookModel::whereDate('tanggal', Carbon::today())->latest()->take(5)->get();

        return view('livewire.pages.receptionist.guestbook', compact('entries', 'todayLatest'));
    }
}
