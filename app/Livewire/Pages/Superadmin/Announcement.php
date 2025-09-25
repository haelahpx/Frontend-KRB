<?php

namespace App\Livewire\Pages\Superadmin;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\WithPagination;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth; // ⬅️ tambahkan
use App\Models\Announcement as AnnouncementModel;

#[Layout('layouts.superadmin')]
#[Title('announcement')]
class Announcement extends Component
{
    use WithPagination;

    // UI State
    public bool $formVisible = false;
    public bool $editMode = false;
    public bool $showDeleteConfirm = false;

    // Form fields
    public ?int $announcementId = null;
    public ?int $company_id = null;
    public string $description = '';
    public ?string $event_at = null; // 'Y-m-d\TH:i'

    // Filters & sorting
    public string $search = '';
    public string $sortField = 'created_at';
    public string $sortDirection = 'desc';

    // ⚠️ company_id kita set sendiri dari Auth, jadi tidak wajib dari user input
    protected $rules = [
        'company_id'  => 'sometimes|integer',
        'description' => 'required|string|max:255',
        'event_at'    => 'nullable|date',
    ];

    protected $listeners = ['refreshAnnouncements' => '$refresh'];

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function sortBy(string $field): void
    {
        $allowedSort = ['announcements_id', 'company_id', 'event_at', 'created_at'];
        if (!in_array($field, $allowedSort, true)) return;

        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = 'asc';
        }

        $this->resetPage();
    }

    public function mount(): void
    {
        // Set default company dari user login
        $this->company_id = Auth::user()?->company_id;
    }

    /* ---------- Create / Edit flow (inline form) ---------- */

    public function toggleForm(): void
    {
        if ($this->editMode) {
            $this->formVisible = true;
            return;
        }
        $this->formVisible = !$this->formVisible;

        if ($this->formVisible && !$this->editMode) {
            $this->resetForm();
            $this->company_id = Auth::user()?->company_id; // pastikan terisi saat create
        }
    }

    public function startCreate(): void
    {
        $this->resetForm();
        $this->editMode = false;
        $this->formVisible = true;
        $this->company_id = Auth::user()?->company_id; // ⬅️ set dari Auth
    }

    public function startEdit(int $id): void
    {
        $announcement = AnnouncementModel::findOrFail($id);

        $this->announcementId = $announcement->getKey();
        // Boleh tampilkan company_id yang tersimpan, tapi saat save kita tetap override dari Auth
        $this->company_id     = $announcement->company_id;
        $this->description    = $announcement->description;
        $this->event_at       = $announcement->event_at
            ? $announcement->event_at->format('Y-m-d\TH:i')
            : null;

        $this->editMode    = true;
        $this->formVisible = true;
        $this->resetValidation();
    }

    public function save(): void
    {
        // Paksa company_id dari user login (mengabaikan input user)
        $this->company_id = Auth::user()?->company_id;

        $this->validate();

        $payload = [
            'company_id'  => $this->company_id, // ⬅️ nilai final dari Auth
            'description' => $this->description,
            'event_at'    => $this->event_at ? Carbon::parse($this->event_at) : null,
        ];

        if ($this->editMode && $this->announcementId) {
            AnnouncementModel::findOrFail($this->announcementId)->update($payload);
            session()->flash('message', 'Announcement updated successfully.');
        } else {
            AnnouncementModel::create($payload);
            session()->flash('message', 'Announcement created successfully.');
        }

        $this->cancelForm();
    }

    public function cancelForm(): void
    {
        $this->formVisible = false;
        $this->editMode = false;
        $this->resetForm();
        $this->resetValidation();
    }

    private function resetForm(): void
    {
        $this->announcementId = null;
        // Jangan reset company_id ke null—biarkan tetap dari Auth saat create
        $this->description = '';
        $this->event_at = null;
    }

    /* ---------- Delete flow ---------- */

    public function confirmDelete(int $id): void
    {
        $this->announcementId = $id;
        $this->showDeleteConfirm = true;
    }

    public function cancelDelete(): void
    {
        $this->showDeleteConfirm = false;
        $this->announcementId = null;
    }

    public function delete(): void
    {
        AnnouncementModel::findOrFail($this->announcementId)->delete();
        session()->flash('message', 'Announcement deleted successfully.');
        $this->showDeleteConfirm = false;
        $this->announcementId = null;

        if ($this->editMode) {
            $this->cancelForm();
        }
    }

    public function render()
    {
        $userCompanyId = Auth::user()?->company_id;

        $announcements = AnnouncementModel::query()
            // (Opsional) batasi hanya company milik user
            ->when($userCompanyId, fn($q) => $q->where('company_id', $userCompanyId))
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('description', 'like', "%{$this->search}%")
                        ->orWhere('company_id', 'like', "%{$this->search}%")
                        ->orWhere('announcements_id', 'like', "%{$this->search}%");
                });
            })
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate(10);

        return view('livewire.pages.superadmin.announcement', compact('announcements'));
    }
}
