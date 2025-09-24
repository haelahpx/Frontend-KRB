<?php

namespace App\Livewire\Pages\Receptionist;

use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Carbon;
use App\Models\Guestbook as GuestbookModel;

#[Layout('layouts.receptionist')]
#[Title('Buku Tamu')]
class Guestbook extends Component
{
    use WithPagination;

    // form fields
    public ?string $date = null;
    public ?string $name = null;
    public ?string $phone_number = null;
    public ?string $jam_in = null;
    public ?string $jam_out = null;
    public ?string $instansi = null;
    public ?string $keperluan = null;
    public ?string $petugas_penjaga = null;
    

    // filters
    public ?string $filter_date = null;
    public ?string $q = null;

    protected function rules(): array
    {
        return [
            'date'             => ['required', 'date'],
            'name'             => ['required', 'string', 'max:120'],
            'phone_number'            => ['nullable', 'string', 'max:30'],
            'jam_in'           => ['required', 'date_format:H:i'],
            'jam_out'          => ['nullable', 'date_format:H:i', 'after_or_equal:jam_in'],
            'instansi'         => ['nullable', 'string', 'max:120'],
            'keperluan'        => ['nullable', 'string', 'max:200'],
            'petugas_penjaga'  => ['required', 'string', 'max:120'],
        ];
    }

    public function mount(): void
    {
        $today = Carbon::today()->toDateString();
        $this->date = $today;
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
        GuestbookModel::create($data);

        // reset some fields (biar enak input beruntun)
        $this->name = $this->phone_number = $this->instansi = $this->keperluan = $this->petugas_penjaga = null;
        $this->jam_in = now()->format('H:i');
        $this->jam_out = null;

        $this->dispatch('toast', type: 'success', title: 'Tersimpan', message: 'Buku tamu ditambahkan.');
    }

    public function render()
    {
        $entries = GuestbookModel::query()
            ->when($this->filter_date, fn($q) => $q->whereDate('date', $this->filter_date))
            ->when($this->q, function ($q) {
                $term = '%' . trim($this->q) . '%';
                $q->where(function ($qq) use ($term) {
                    $qq->where('name', 'like', $term)
                        ->orWhere('no_hp', 'like', $term)
                        ->orWhere('instansi', 'like', $term)
                        ->orWhere('keperluan', 'like', $term)
                        ->orWhere('petugas_penjaga', 'like', $term);
                });
            })
            ->orderByDesc('created_at')
            ->paginate(10);

        $todayLatest = GuestbookModel::whereDate('date', Carbon::today())->latest()->take(5)->get();

        return view('livewire.pages.receptionist.guestbook', compact('entries', 'todayLatest'));
    }
}
