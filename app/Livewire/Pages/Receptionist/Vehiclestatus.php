<?php

namespace App\Livewire\Pages\Receptionist;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Models\VehicleBooking;
use App\Models\Vehicle;
use App\Models\VehicleBookingPhoto;

#[Layout('layouts.receptionist')]
#[Title('Vehicle Status')]
class Vehiclestatus extends Component
{
    use WithPagination;

    protected string $paginationTheme = 'tailwind';
    protected string $tz = 'Asia/Jakarta';

    public string $q = '';
    public ?int $vehicleFilter = null;

    // PERBAIKAN: Mengganti 'in_use' menjadi 'on_progress' agar sesuai alur baru
    public string $statusTab = 'pending'; // pending | on_progress

    // Sort & date filter
    public string $sortFilter = 'recent';  // recent | oldest | nearest
    public ?string $selectedDate = null;   // YYYY-MM-DD atau null

    public int $perPage = 5;

    public bool $includeDeleted = false;

    protected $queryString = [
        'q' => ['except' => ''],
        'vehicleFilter' => ['except' => null],
        'statusTab' => ['except' => 'pending'], // Default tetap 'pending'
        'sortFilter' => ['except' => 'recent'],
        'selectedDate' => ['except' => null],
        'includeDeleted' => ['except' => false],
        'page' => ['except' => 1],
    ];

    public function updatingQ()
    {
        $this->resetPage();
    }
    public function updatingVehicleFilter()
    {
        $this->resetPage();
    }
    public function updatingStatusTab()
    {
        $this->resetPage();
    }
    public function updatingSortFilter()
    {
        $this->resetPage();
    }
    public function updatingSelectedDate()
    {
        $this->resetPage();
    }
    public function updatingIncludeDeleted()
    {
        $this->resetPage();
    }

    public function mount(): void
    {
        // PERBAIKAN: Cek status tab baru ('on_progress')
        if (!in_array($this->statusTab, ['pending', 'on_progress'], true)) {
            $this->statusTab = 'pending';
        }

        if (!in_array($this->sortFilter, ['recent', 'oldest', 'nearest'], true)) {
            $this->sortFilter = 'recent';
        }
    }

    /**
     * PERBAIKAN: Logika Approve disesuaikan dengan Alur Baru.
     * Hanya mengubah status ke 'approved' untuk menunggu foto 'before' dari user.
     */
    public function approve(int $id): void
    {
        $booking = VehicleBooking::find($id);
        if (!$booking)
            return;

        $user = Auth::user();
        if ((int) $booking->company_id !== (int) ($user?->company_id ?? 0))
            return;

        if ($booking->status !== 'pending')
            return;

        // PERBAIKAN: Hapus kolom 'is_approve' dan set status ke 'approved'
        // $booking->is_approve = 1; // <-- DIHAPUS (Kolom tidak ada)
        $booking->status = 'approved'; // <-- Ganti dari 'in_use' ke 'approved'
        $booking->save();

        session()->flash('success', "Booking #{$booking->vehiclebooking_id} approved. Menunggu foto 'Before' dari user.");
    }

    /**
     * PERBAIKAN: Logika Reject disesuaikan.
     * Hanya mengubah status, tanpa 'is_approve'.
     */
    public function reject(int $id): void
    {
        $booking = VehicleBooking::find($id);
        if (!$booking)
            return;

        $user = Auth::user();
        if ((int) $booking->company_id !== (int) ($user?->company_id ?? 0))
            return;

        if ($booking->status !== 'pending')
            return;

        // $booking->is_approve = 0; // <-- DIHAPUS (Kolom tidak ada)
        $booking->status = 'rejected';
        $booking->save();

        session()->flash('success', "Booking #{$booking->vehiclebooking_id} rejected.");
    }

    /**
     * PERBAIKAN: Mengganti nama 'markDone' menjadi 'markReturned'
     * Fungsi ini untuk menandakan mobil SUDAH KEMBALI,
     * dan mengubah status ke 'returned' agar user bisa upload foto 'after'.
     */
    public function markReturned(int $id): void
    {
        $booking = VehicleBooking::find($id);
        if (!$booking)
            return;

        $user = Auth::user();
        if ((int) $booking->company_id !== (int) ($user?->company_id ?? 0))
            return;

        // Hanya mobil yang 'on_progress' (sedang dipakai) yang bisa dikembalikan
        if ($booking->status !== 'on_progress') {
            session()->flash('error', 'Booking ini tidak sedang dalam status "On Progress".');
            return;
        }

        // Set status ke 'returned'
        $booking->status = 'returned';
        $booking->save();

        session()->flash('success', "Booking #{$booking->vehiclebooking_id} ditandai sudah kembali. Menunggu foto 'After' dari user.");
    }

    // CATATAN: Fungsi 'markDone' (yang lama) dihapus. 
    // Booking akan otomatis 'completed' saat user upload foto 'after'.

    public function render()
    {
        $user = Auth::user();
        $companyId = (int) ($user?->company_id ?? 0);
        $now = Carbon::now($this->tz);

        $query = VehicleBooking::query()
            ->where('company_id', $companyId);

        if ($this->includeDeleted) {
            $query->withTrashed();
        }

        if (strlen(trim($this->q)) > 0) {
            $q = trim($this->q);

            $query->where(function ($qq) use ($q) {
                $qq->where('purpose', 'like', "%{$q}%")
                    ->orWhere('destination', 'like', "%{$q}%")
                    ->orWhere('borrower_name', 'like', "%{$q}%")
                    ->orWhere('purpose_type', 'like', "%{$q}%");
            });
        }

        if ($this->vehicleFilter) {
            $query->where('vehicle_id', $this->vehicleFilter);
        }

        // PERBAIKAN: Logika filter tab disesuaikan
        if ($this->statusTab === 'pending') {
            $query->where('status', 'pending');
        } elseif ($this->statusTab === 'on_progress') {
            // Tab 'On Progress' (sebelumnya 'In Use') sekarang memonitor:
            // 'approved' (menunggu foto before)
            // 'on_progress' (sedang dipakai)
            // 'returned' (menunggu foto after)
            $query->whereIn('status', ['approved', 'on_progress', 'returned']);
        }

        if (!empty($this->selectedDate)) {
            $query->whereDate('start_at', $this->selectedDate);
        }

        switch ($this->sortFilter) {
            case 'oldest':
                $query->orderBy('start_at', 'asc');
                break;
            case 'nearest':
                $query->orderByRaw(
                    'CASE WHEN start_at >= ? THEN 0 ELSE 1 END',
                    [$now->toDateTimeString()]
                )->orderBy('start_at', 'asc');
                break;
            case 'recent':
            default:
                $query->orderBy('start_at', 'desc');
                break;
        }

        $bookings = $query->paginate($this->perPage);

        $vehicles = Vehicle::where('company_id', $companyId)
            ->get(['vehicle_id', 'name', 'plate_number']);

        $vehicleMap = $vehicles->mapWithKeys(function ($v) {
            $label = $v->name ?? $v->plate_number ?? ('#' . $v->vehicle_id);
            return [$v->vehicle_id => $label];
        })->toArray();

        $ids = method_exists($bookings, 'pluck') ? $bookings->pluck('vehiclebooking_id')->all() : [];
        $photoCounts = [];
        if (!empty($ids)) {
            // PERBAIKAN: Query ke 'photo_path' (meskipun di sini tidak error,
            // hanya memastikan query tetap efisien tanpa kolom 'photo_url')
            $rows = VehicleBookingPhoto::selectRaw('vehiclebooking_id, photo_type, COUNT(id) as c')
                ->whereIn('vehiclebooking_id', $ids)
                ->groupBy('vehiclebooking_id', 'photo_type')
                ->get();

            foreach ($rows as $r) {
                $photoCounts[$r->vehiclebooking_id][$r->photo_type] = (int) $r->c;
            }
        }

        return view('livewire.pages.receptionist.vehiclestatus', [
            'bookings' => $bookings,
            'vehicleMap' => $vehicleMap,
            'vehicles' => $vehicles,
            'photoCounts' => $photoCounts,
        ]);
    }
}