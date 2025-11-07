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

    // HANYA pending & in_use, rejected / completed ada di history
    public string $statusTab = 'pending'; // pending | in_use

    // Sort & date filter (mirip history)
    public string $sortFilter = 'recent';  // recent | oldest | nearest
    public ?string $selectedDate = null;   // YYYY-MM-DD atau null

    public int $perPage = 5;
    
    // Include deleted checkbox
    public bool $includeDeleted = false;

    protected $queryString = [
        'q'             => ['except' => ''],
        'vehicleFilter' => ['except' => null],
        'statusTab'     => ['except' => 'pending'],
        'sortFilter'    => ['except' => 'recent'],
        'selectedDate'  => ['except' => null],
        'includeDeleted'=> ['except' => false],
        'page'          => ['except' => 1],
    ];

    public function updatingQ()             { $this->resetPage(); }
    public function updatingVehicleFilter() { $this->resetPage(); }
    public function updatingStatusTab()     { $this->resetPage(); }
    public function updatingSortFilter()    { $this->resetPage(); }
    public function updatingSelectedDate()  { $this->resetPage(); }
    public function updatingIncludeDeleted(){ $this->resetPage(); }

    public function mount(): void
    {
        if (!in_array($this->statusTab, ['pending', 'in_use'], true)) {
            $this->statusTab = 'pending';
        }

        if (!in_array($this->sortFilter, ['recent', 'oldest', 'nearest'], true)) {
            $this->sortFilter = 'recent';
        }
    }

    public function approve(int $id): void
    {
        $booking = VehicleBooking::find($id);
        if (!$booking) return;

        $user = Auth::user();
        if ((int) $booking->company_id !== (int) ($user?->company_id ?? 0)) return;

        if ($booking->status !== 'pending') return;

        $booking->is_approve = 1;
        $booking->status = 'in_use'; // langsung In Use
        $booking->save();

        session()->flash('success', "Booking #{$booking->vehiclebooking_id} approved → In Use.");
    }

    public function reject(int $id): void
    {
        $booking = VehicleBooking::find($id);
        if (!$booking) return;

        $user = Auth::user();
        if ((int) $booking->company_id !== (int) ($user?->company_id ?? 0)) return;

        if ($booking->status !== 'pending') return;

        $booking->is_approve = 0;
        $booking->status = 'rejected';
        $booking->save();

        session()->flash('success', "Booking #{$booking->vehiclebooking_id} rejected.");
    }

    public function markDone(int $id): void
    {
        $booking = VehicleBooking::find($id);
        if (!$booking) return;

        $user = Auth::user();
        if ((int) $booking->company_id !== (int) ($user?->company_id ?? 0)) return;

        if (!in_array($booking->status, ['in_use', 'returned'], true)) {
            return;
        }

        $booking->status = 'completed';
        $booking->save();

        session()->flash('success', "Booking #{$booking->vehiclebooking_id} marked as completed.");
    }

    public function render()
    {
        $user = Auth::user();
        $companyId = (int) ($user?->company_id ?? 0);
        $now = Carbon::now($this->tz);

        $query = VehicleBooking::query()
            ->where('company_id', $companyId);

        // Include or exclude soft-deleted records
        if ($this->includeDeleted) {
            $query->withTrashed();
        }

        // Search
        if (strlen(trim($this->q)) > 0) {
            $q = trim($this->q);

            $query->where(function ($qq) use ($q) {
                $qq->where('purpose', 'like', "%{$q}%")
                    ->orWhere('destination', 'like', "%{$q}%")
                    ->orWhere('borrower_name', 'like', "%{$q}%")
                    ->orWhere('purpose_type', 'like', "%{$q}%");
            });
        }

        // Filter kendaraan
        if ($this->vehicleFilter) {
            $query->where('vehicle_id', $this->vehicleFilter);
        }

        // Filter status tab
        if ($this->statusTab === 'pending') {
            $query->where('status', 'pending');
        } elseif ($this->statusTab === 'in_use') {
            $query->whereIn('status', ['in_use', 'returned']);
        }

        // Filter tanggal (single date, sama kayak halaman lain)
        if (!empty($this->selectedDate)) {
            $query->whereDate('start_at', $this->selectedDate);
        }

        // Sorting
        switch ($this->sortFilter) {
            case 'oldest':
                $query->orderBy('start_at', 'asc');
                break;

            case 'nearest':
                // Urutkan yang paling dekat dengan waktu sekarang
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

        // Data kendaraan
        $vehicles = Vehicle::where('company_id', $companyId)
            ->get(['vehicle_id', 'name', 'plate_number']);

        $vehicleMap = $vehicles->mapWithKeys(function ($v) {
            $label = $v->name ?? $v->plate_number ?? ('#' . $v->vehicle_id);
            return [$v->vehicle_id => $label];
        })->toArray();

        // Hitung foto before/after
        $ids = method_exists($bookings, 'pluck') ? $bookings->pluck('vehiclebooking_id')->all() : [];
        $photoCounts = [];
        if (!empty($ids)) {
            $rows = VehicleBookingPhoto::selectRaw('vehiclebooking_id, photo_type, COUNT(*) as c')
                ->whereIn('vehiclebooking_id', $ids)
                ->groupBy('vehiclebooking_id', 'photo_type')
                ->get();

            foreach ($rows as $r) {
                $photoCounts[$r->vehiclebooking_id][$r->photo_type] = (int) $r->c;
            }
        }

        return view('livewire.pages.receptionist.vehiclestatus', [
            'bookings'    => $bookings,
            'vehicleMap'  => $vehicleMap,
            'vehicles'    => $vehicles,
            'photoCounts' => $photoCounts,
        ]);
    }
}
