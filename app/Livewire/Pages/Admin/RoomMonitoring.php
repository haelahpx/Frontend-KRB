<?php

namespace App\Livewire\Pages\Admin;

use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\BookingRoom;
use App\Models\Department;
use Livewire\WithPagination;

#[Layout('layouts.admin')]
#[Title('History Room Booking')]
class RoomMonitoring extends Component
{
    use WithPagination;

    // List limits
    public int $perPage = 10;
    public ?string $search = null;
    public ?string $statusFilter = '';

    // PROPERTY ADDED TO FIX Undefined variable $sortDirection ERROR
    public string $sortDirection = 'desc'; // Default to newest first

    // Header + switcher
    public string $company_name = '-';
    public string $department_name = '-';
    public array  $deptOptions = [];
    public ?int   $selected_department_id = null;
    public ?int   $primary_department_id  = null;
    public bool $showSwitcher = false;

    // --- MODAL PROPERTIES ---
    public bool $showDetailModal = false;
    public ?int $selectedBookingId = null;
    public ?BookingRoom $selectedBookingDetail = null;
    // ------------------------

    // --- SOFT DELETE PROPERTIES ---
    public bool $showDeleteConfirmModal = false;
    public ?int $bookingToDeleteId = null;
    // ------------------------------------

    // --- COMPUTED PROPERTY TO GET DETAIL ---
    /**
     * Retrieves the selected BookingRoom model instance with necessary relationships.
     */
    public function getBookingDetailProperty(): ?BookingRoom
    {
        if ($this->selectedBookingId) {
            // Include soft-deleted records for detail viewing
            return BookingRoom::withTrashed()
                ->with(['room', 'requirements'])
                ->find($this->selectedBookingId);
        }
        return null;
    }

    /**
     * Open the detail modal and fetch the selected booking detail.
     * FIX: Accepts string and casts internally to prevent Livewire BindingResolutionException.
     */
    public function openDetailModal(string $bookingId): void
    {
        $id = (int) $bookingId;
        Log::info('Attempting to open detail modal for ID: ' . $id);

        $this->selectedBookingId = $id;
        $this->selectedBookingDetail = $this->getBookingDetailProperty();

        if ($this->selectedBookingDetail) {
            $this->showDetailModal = true;
            Log::info('Detail modal opened successfully for ID: ' . $id);
        } else {
            Log::error('Failed to find booking detail for ID: ' . $id);
            $this->dispatch(
                'toast',
                type: 'error',
                title: 'Error',
                message: 'Gagal memuat detail booking (ID: ' . $id . ' tidak ditemukan).',
                duration: 5000
            );
            $this->showDetailModal = false;
        }
    }


    /**
     * Close the detail modal.
     */
    public function closeDetailModal(): void
    {
        $this->showDetailModal = false;
        $this->selectedBookingId = null;
        $this->selectedBookingDetail = null;
    }

    // --- SOFT DELETE METHODS ---

    /**
     * Open the delete confirmation modal.
     * FIX: Accepts string and casts internally to prevent Livewire BindingResolutionException.
     */
    public function openDeleteConfirmModal(string $bookingId): void
    {
        // Cast bookingId to integer to avoid issues with type mismatch
        $id = (int) $bookingId;

        // Check if the booking exists and is not already soft-deleted
        $booking = BookingRoom::find($id);

        if ($booking) {
            $this->bookingToDeleteId = $id;
            $this->showDeleteConfirmModal = true;
        } else {
            $this->dispatch(
                'toast',
                type: 'warning',
                title: 'Perhatian',
                message: 'Booking ID ' . $id . ' sudah dihapus.',
                duration: 5000
            );
        }
    }


    /**
     * Close the delete confirmation modal.
     */
    public function closeDeleteConfirmModal(): void
    {
        $this->showDeleteConfirmModal = false;
        $this->bookingToDeleteId = null;
    }

    /**
     * Perform the soft delete operation.
     */
    public function softDeleteBooking(): void
    {
        if ($this->bookingToDeleteId) {
            $booking = BookingRoom::find($this->bookingToDeleteId);
            $id = $this->bookingToDeleteId;

            if ($booking) {
                // Eloquent soft delete: sets the 'deleted_at' timestamp
                $booking->delete();
                Log::warning('Soft delete executed successfully for ID: ' . $id);

                $this->closeDeleteConfirmModal();

                // FIX: Manually reset pagination pages to force UI redraw
                $this->resetPage('offlinePage');
                $this->resetPage('onlinePage');

                // Send success toast
                $this->dispatch(
                    'toast',
                    type: 'success',
                    title: 'Berhasil',
                    message: 'Booking ID ' . $id . ' berhasil dihapus (soft-delete).',
                    duration: 3000
                );
            } else {
                // Send error toast if booking wasn't found (maybe already deleted)
                $this->dispatch(
                    'toast',
                    type: 'error',
                    title: 'Error',
                    message: 'Gagal menghapus: Booking ID ' . $id . ' tidak ditemukan.',
                    duration: 5000
                );
                Log::error('Attempted soft delete on non-existent booking ID: ' . $id);
            }
        }
    }

    // --- SORTING METHOD ---

    public function toggleSortDirection(): void
    {
        $this->sortDirection = $this->sortDirection === 'desc' ? 'asc' : 'desc';
        $this->resetPage(); // Reset pagination when changing sort order
    }

    // --- Existing Lifecycle and Utility Methods ---

    public function mount(): void
    {
        $user = Auth::user()->loadMissing(['company', 'department']);
        $this->company_name = optional($user->company)->company_name ?? '-';
        $this->primary_department_id = $user->department_id ?: null;

        $this->loadUserDepartments();

        if (!$this->selected_department_id) {
            $this->selected_department_id = $this->primary_department_id
                ?: ($this->deptOptions[0]['id'] ?? null);
        }
        $this->department_name = $this->resolveDeptName($this->selected_department_id);
    }

    protected function loadUserDepartments(): void
    {
        $user = Auth::user();

        $rows = DB::table('user_departments as ud')
            ->join('departments as d', 'd.department_id', '=', 'ud.department_id')
            ->where('ud.user_id', $user->user_id)
            ->orderBy('d.department_name')
            ->get(['d.department_id as id', 'd.department_name as name']);

        $this->deptOptions = $rows->map(fn($r) => ['id' => (int)$r->id, 'name' => (string)$r->name])->values()->all();

        $this->showSwitcher = true;

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
            if ($opt['id'] === (int)$deptId) return $opt['name'];
        }
        return Department::where('department_id', $deptId)->value('department_name') ?? '-';
    }

    public function updatedSelectedDepartment_id(): void
    {
        $this->resetPage();
    }

    public function updatedStatusFilter(): void
    {
        $this->resetPage();
    }

    protected function baseHistoryQuery()
    {
        $companyId = Auth::user()?->company_id;
        $deptId    = $this->selected_department_id ?: $this->primary_department_id;

        $query = BookingRoom::query()
            ->with(['room'])
            ->where('company_id', $companyId)
            ->when($deptId, fn($q) => $q->where('department_id', $deptId));

        // Handle Status Filter
        if ($this->statusFilter === 'DELETED') {
            $query->onlyTrashed();
        } else {
            $query->withoutTrashed();

            if ($this->statusFilter) {
                $query->where('status', $this->statusFilter);
            }
        }

        // Handle Search Filter
        $query->when($this->search, function ($q, $s) {
            $q->where(function ($qq) use ($s) {
                $qq->where('meeting_title', 'like', "%{$s}%")
                    ->orWhere('special_notes', 'like', "%{$s}%");
            });
        });

        $query->orderBy('end_time', $this->sortDirection);

        return $query;
    }

    public function render()
    {
        $base = $this->baseHistoryQuery();

        $offline = (clone $base)
            ->where('booking_type', 'meeting')
            ->paginate($this->perPage, pageName: 'offlinePage');

        $online = (clone $base)
            ->where('booking_type', 'online_meeting')
            ->paginate($this->perPage, pageName: 'onlinePage');

        return view('livewire.pages.admin.roommonitoring', [
            'offline' => $offline,
            'online'  => $online,
        ]);
    }
}
