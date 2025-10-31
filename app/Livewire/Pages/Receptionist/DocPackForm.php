<?php

namespace App\Livewire\Pages\Receptionist;

use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Department;
use App\Models\Storage;
use App\Models\Delivery;
use Carbon\Carbon;

#[Layout('layouts.receptionist')]
#[Title('Doc/Pack Form')]
class DocPackForm extends Component
{
    public string $direction = 'taken'; // taken | deliver
    public string $itemType = 'package'; // package | document

    public ?int $departmentId = null;
    public ?int $userId = null;
    public string $senderText = '';
    public string $receiverText = '';
    public ?int $storageId = null;
    public string $itemName = '';

    /** Dropdown Data */
    public array $departments = [];
    public array $users = [];
    public array $storages = [];

    protected function rules(): array
    {
        $base = [
            'direction'    => ['required', 'in:taken,deliver'],
            'itemType'     => ['required', 'in:package,document'],
            'storageId'    => ['required', 'integer', 'exists:storages,storage_id'],
            'itemName'     => ['required', 'string', 'max:255'],
            'departmentId' => ['required', 'integer'],
            'userId'       => ['required', 'integer'],
        ];

        if ($this->direction === 'taken') {
            $base['senderText'] = ['required', 'string', 'max:255'];
        } else {
            $base['receiverText'] = ['required', 'string', 'max:255'];
        }

        return $base;
    }

    public function mount(): void
    {
        $companyId = Auth::user()->company_id;

        $this->departments = Department::where('company_id', $companyId)
            ->orderBy('department_name')
            ->get(['department_id as id', 'department_name as name'])
            ->toArray();

        $this->storages = Storage::where('company_id', $companyId)
            ->orderBy('name')
            ->get(['storage_id as id', 'name'])
            ->toArray();
    }

    public function updatedDepartmentId(): void
    {
        $this->userId = null;
        $this->loadUsers();
    }

    private function loadUsers(): void
    {
        $companyId = Auth::user()->company_id;

        if (!$this->departmentId) {
            $this->users = [];
            return;
        }

        $this->users = User::where('company_id', $companyId)
            ->where('department_id', $this->departmentId)
            ->orderBy('full_name')
            ->pluck('full_name', 'user_id')
            ->toArray();
    }

    public function updatedDirection(): void
    {
        $this->departmentId = null;
        $this->userId = null;
        $this->senderText = '';
        $this->receiverText = '';
        $this->users = [];
    }

    public function save(): void
    {
        $this->validate();

        $now = Carbon::now('Asia/Jakarta');

        if ($this->direction === 'taken') {
            $receiver = User::whereKey($this->userId)->value('full_name') ?? '—';
            $sender   = $this->senderText;
        } else {
            $sender   = User::whereKey($this->userId)->value('full_name') ?? '—';
            $receiver = $this->receiverText;
        }

        Delivery::create([
            'company_id'      => Auth::user()->company_id,
            'receptionist_id' => Auth::id(),
            'type'            => $this->itemType,
            'item_name'       => $this->itemName,
            'storage_id'      => $this->storageId,
            'nama_pengirim'   => $sender,
            'nama_penerima'   => $receiver,
            'status'          => 'pending',
            'direction'       => $this->direction, // ✅ Always included now
            'pengambilan'     => null,
            'pengiriman'      => null,
            'created_at'      => $now,
            'updated_at'      => $now,
        ]);

        $this->reset([
            'departmentId', 'userId', 'senderText', 'receiverText', 'storageId', 'itemName'
        ]);
        $this->users = [];

        $this->dispatch('toast', type: 'success', title: 'Tersimpan', message: 'Data berhasil disimpan.', duration: 3000);
    }

    public function render()
    {
        return view('livewire.pages.receptionist.docpackform');
    }
}
