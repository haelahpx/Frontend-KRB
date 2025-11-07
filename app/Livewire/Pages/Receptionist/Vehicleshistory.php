<?php

namespace App\Livewire\Pages\Receptionist;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Illuminate\Support\Facades\Auth;
use App\Models\VehicleBooking;
use App\Models\Vehicle;
use App\Models\VehicleBookingPhoto;
use Carbon\Carbon;

#[Layout('layouts.receptionist')]
#[Title('Vehicle History')]
class Vehicleshistory extends Component
{
    use WithPagination;

    protected string $paginationTheme = 'tailwind';
    protected string $tz = 'Asia/Jakarta';

    // Filters
    public string $q = '';
    public ?int $vehicleFilter = null;

    /**
     * done     => status completed
     * rejected => status rejected
     */
    public string $statusTab = 'done';

    // Include deleted checkbox
    public bool $includeDeleted = false;

    // Date filter (single date seperti screenshot)
    public ?string $selectedDate = null;   // 'YYYY-MM-DD' atau null

    // Sort filter
    public string $sortFilter = 'recent';  // recent | oldest | nearest

    // Pagination
    public int $perPage = 5;

    protected $queryString = [
        'q'              => ['except' => ''],
        'vehicleFilter'  => ['except' => null],
        'statusTab'      => ['except' => 'done'],
        'includeDeleted' => ['except' => false],
        'selectedDate'   => ['except' => null],
        'sortFilter'     => ['except' => 'recent'],
        'page'           => ['except' => 1],
    ];

    // --- Reset page kalau filter berubah ---

    public function updatingQ(): void
    {
        $this->resetPage();
    }

    public function updatingVehicleFilter(): void
    {
        $this->resetPage();
    }

    public function updatingStatusTab(): void
    {
        $this->resetPage();
    }

    public function updatingIncludeDeleted(): void
    {
        $this->resetPage();
    }

    public function updatingSelectedDate(): void
    {
        $this->resetPage();
    }

    public function updatingSortFilter(): void
    {
        $this->resetPage();
    }

    public function mount(): void
    {
        if (!in_array($this->statusTab, ['done', 'rejected'], true)) {
            $this->statusTab = 'done';
        }
        if (!in_array($this->sortFilter, ['recent', 'oldest', 'nearest'], true)) {
            $this->sortFilter = 'recent';
        }
    }

    public function render()
    {
        $user = Auth::user();
        $companyId = (int) ($user?->company_id ?? 0);

        $query = VehicleBooking::where('company_id', $companyId);

        // Include / exclude soft-deleted
        if ($this->includeDeleted) {
            $query->withTrashed();
        }

        // Status filter
        if ($this->statusTab === 'rejected') {
            $query->where('status', 'rejected');
        } else {
            $query->where('status', 'completed');
        }

        // Search
        if (strlen(trim($this->q)) > 0) {
            $q = trim($this->q);

            $query->where(function ($qq) use ($q) {
                $qq->where('purpose', 'like', "%{$q}%")
                    ->orWhere('destination', 'like', "%{$q}%")
                    ->orWhere('borrower_name', 'like', "%{$q}%");
            });
        }

        // Filter kendaraan
        if ($this->vehicleFilter) {
            $query->where('vehicle_id', $this->vehicleFilter);
        }

        // Filter tanggal (single date)
        if (!empty($this->selectedDate)) {
            $query->whereDate('start_at', $this->selectedDate);
        }

        // Sorting
        $now = Carbon::now($this->tz);

        switch ($this->sortFilter) {
            case 'oldest':
                $query->orderBy('start_at', 'asc');
                break;

            case 'nearest':
                // Booking yang paling dekat dengan waktu sekarang
                $query->orderByRaw('ABS(TIMESTAMPDIFF(SECOND, start_at, ?))', [$now]);
                break;

            case 'recent':
            default:
                $query->orderBy('start_at', 'desc');
                break;
        }

        $bookings = $query->paginate($this->perPage);

        // Data kendaraan untuk label
        $vehicles = Vehicle::where('company_id', $companyId)
            ->get(['vehicle_id', 'name', 'plate_number']);

        $vehicleMap = $vehicles->mapWithKeys(function ($v) {
            $label = $v->name ?? $v->plate_number ?? ('#' . $v->vehicle_id);
            return [$v->vehicle_id => $label];
        })->toArray();

        // Hitung foto before / after
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

        return view('livewire.pages.receptionist.vehicleshistory', [
            'bookings'    => $bookings,
            'vehicleMap'  => $vehicleMap,
            'vehicles'    => $vehicles,
            'photoCounts' => $photoCounts,
        ]);
    }
}
