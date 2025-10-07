<?php

namespace App\Livewire\Pages\Receptionist;

use App\Models\Package as PackageModel; // alias to avoid name collision
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;
use Carbon\Carbon;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;

#[Layout('layouts.receptionist')]
#[Title('Package')]
class Package extends Component
{
    use WithPagination;

    public $showModal = false;
    public ?int $editId = null;
    public ?string $createdAtDisplay = null;

    public array $form = [
        'package_name' => '',
        'nama_pengirim' => '',
        'nama_penerima' => '',
        'penyimpanan' => null,  
        'pengambilan' => null,  
    ];

    protected function rules(): array
    {
        return [
            'form.package_name' => ['required', 'string', 'max:255'],
            'form.nama_pengirim' => ['required', 'string', 'max:255'],
            'form.nama_penerima' => ['required', 'string', 'max:255'],
            'form.penyimpanan' => ['nullable', 'in:rak1,rak2,rak3'],
            'form.pengambilan' => ['nullable', 'date_format:Y-m-d\TH:i'],
        ];
    }

    public function openCreate(): void
    {
        $this->resetForm();
        $this->editId = null;
        $this->createdAtDisplay = now()->format('d M Y, H:i');
        $this->showModal = true;
    }

    public function openEdit(int $id): void
    {
        $pkg = PackageModel::query()
            ->where('company_id', Auth::user()->company_id)
            ->findOrFail($id);

        $this->editId = $pkg->package_id;

        $this->form = [
            'package_name' => $pkg->package_name,
            'nama_pengirim' => $pkg->nama_pengirim,
            'nama_penerima' => $pkg->nama_penerima,
            'penyimpanan' => $pkg->penyimpanan,
            'pengambilan' => $pkg->pengambilan?->format('Y-m-d\TH:i'),
        ];

        $this->createdAtDisplay = $pkg->created_at?->format('d M Y, H:i');
        $this->showModal = true;
    }

    public function save(): void
    {
        $this->validate();

        $pengambilanStr = $this->form['pengambilan'] ?? null;
        $pengambilan = $pengambilanStr ? Carbon::createFromFormat('Y-m-d\TH:i', $pengambilanStr) : null;
        $status = $pengambilan ? 'taken' : 'stored';

        $data = [
            'company_id' => Auth::user()->company_id,
            'receptionist_id' => Auth::id(),
            'package_name' => $this->form['package_name'],
            'nama_pengirim' => $this->form['nama_pengirim'],
            'nama_penerima' => $this->form['nama_penerima'],
            'penyimpanan' => $this->form['penyimpanan'] ?: null,
            'pengambilan' => $pengambilan,
            'status' => $status,
        ];

        if ($this->editId) {
            PackageModel::where('company_id', $data['company_id'])
                ->whereKey($this->editId)
                ->update($data);
        } else {
            PackageModel::create($data);
        }

        $this->showModal = false;
        $this->dispatch('toast', type: 'success', title: 'Ditambah', message: 'Paket ditambah.', duration: 3000);
        $this->resetPage();
        $this->resetForm();
        $this->resetPage();
    }

    public function delete(int $id): void
    {
        $pkg = \App\Models\Package::findOrFail($id);
        $pkg->delete();
        $this->dispatch('toast', type: 'success', title: 'Dihapus', message: 'Paket dihapus.', duration: 3000);
        $this->resetPage();
    }

    public function markDone(int $id): void
    {
        PackageModel::where('company_id', Auth::user()->company_id)
            ->whereKey($id)
            ->update([
                'status' => 'taken',
                'pengambilan' => now(),
            ]);

        $this->dispatch('toast', message: 'Moved to Done.');
    }

    public function markStored(int $id): void
    {
        PackageModel::where('company_id', Auth::user()->company_id)
            ->whereKey($id)
            ->update([
                'status' => 'stored',
                'pengambilan' => null,
            ]);

        $this->dispatch('toast', message: 'Moved back to On-going.');
    }

    public function closeModal(): void
    {
        $this->showModal = false;
    }

    private function resetForm(): void
    {
        $this->form = [
            'package_name' => '',
            'nama_pengirim' => '',
            'nama_penerima' => '',
            'penyimpanan' => null,
            'pengambilan' => null,
        ];
        $this->createdAtDisplay = null;
    }

    public function render()
    {
        $companyId = Auth::user()->company_id;

        $ongoing = PackageModel::query()
            ->with('receptionist')
            ->where('company_id', $companyId)
            ->where('status', 'stored')
            ->latest('created_at')
            ->paginate(8, pageName: 'ongoing');

        $done = PackageModel::query()
            ->with('receptionist')
            ->where('company_id', $companyId)
            ->where('status', 'taken')
            ->latest('pengambilan')
            ->paginate(8, pageName: 'done');

        return view('livewire.pages.receptionist.package', compact('ongoing', 'done'));
    }
}
