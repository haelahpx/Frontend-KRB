<?php

namespace App\Livewire\Pages\User;

use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\Vehicle;
use App\Models\Department;
use App\Models\VehicleBooking;
use App\Models\VehicleBookingPhoto;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Carbon\Carbon;

#[Layout('layouts.app')]
#[Title('Book Vehicle')]
class Bookvehicle extends Component
{
    use WithFileUploads;

    // form props
    public $name;
    public $department_id;
    public $start_time;
    public $end_time;
    public $date_from;
    public $date_to;
    public $purpose;
    public $destination;
    public $odd_even_area = 'tidak';
    public $purpose_type = 'dinas';
    public $vehicle_id = null;
    public $has_sim_a = false;
    public $agree_terms = false;

    // files (Livewire temporary uploads)
    public $photo_before;
    public $photo_after;

    // helpers / ui data
    public $departments;
    public $vehicles;
    public $hasVehicles = false;
    public $availability = [];
    public $recentBookings = [];

    public $booking = null;

    public function mount()
    {
        $user = Auth::user();
        $this->name = $user->full_name ?? $user->name ?? null;
        $this->department_id = $user->department_id ?? null;
        $this_company_id = $user->company_id ?? 1;

        $this->departments = Department::where('company_id', $this_company_id)->orderBy('department_name')->get();
        $this->vehicles = Vehicle::where('company_id', $this_company_id)->where('is_active', true)->get();
        $this->hasVehicles = $this->vehicles->count() > 0;

        $this->loadAvailability();
        $this->loadRecentBookings();

        $bookingId = request()->query('id');
        if ($bookingId) {
            $this->loadBooking($bookingId, $user);
        }
    }

    public function loadBooking($id, $user)
    {
        $booking = VehicleBooking::where('vehiclebooking_id', $id)
            ->where('company_id', $user->company_id ?? 1)
            ->first();

        if (!$booking) {
            session()->flash('error', 'Booking tidak ditemukan.');
            return;
        }

        if ($booking->user_id != $user->user_id && $booking->department_id != $user->department_id) {
            session()->flash('error', 'Anda tidak memiliki akses ke booking ini.');
            return;
        }

        if (!in_array($booking->status, ['approved', 'returned'])) {
            session()->flash('error', 'Booking ini tidak sedang menunggu upload foto (Status: ' . $booking->status . ').');
            return;
        }

        $this->booking = $booking;
    }

    protected function rules()
    {
        return [
            'name' => 'required|string|max:255',
            'department_id' => 'required|integer|exists:departments,department_id',
            'date_from' => 'required|date',
            'date_to' => 'required|date|after_or_equal:date_from',
            'start_time' => 'required',
            'end_time' => 'required|after:start_time',
            'purpose' => 'required|string|max:500',
            'destination' => 'nullable|string|max:255',
            'odd_even_area' => ['required', Rule::in(['ganjil', 'genap', 'tidak'])],
            'purpose_type' => ['required', Rule::in(['dinas', 'operasional', 'antar_jemput', 'lainnya'])],
            'has_sim_a' => 'accepted',
            'agree_terms' => 'accepted',
            'vehicle_id' => 'nullable|integer|exists:vehicles,vehicle_id',
        ];
    }

    public function submitBooking()
    {
        $this->validate($this->rules());

        $user = Auth::user();
        $startAt = $this->combineDateTime($this->date_from, $this->start_time);
        $endAt = $this->combineDateTime($this->date_to, $this->end_time);

        if ($this->vehicle_id) {
            $isAvailable = $this->checkAvailabilityForVehicle($this->vehicle_id, $startAt, $endAt);
            if (!$isAvailable) {
                session()->flash('error', 'Kendaraan tidak tersedia pada jadwal yang dipilih. Silakan cek ulang.');
                return;
            }
        }

        $booking = VehicleBooking::create([
            'company_id' => $user->company_id ?? 1,
            'user_id' => $user->user_id ?? Auth::id(),
            'vehicle_id' => $this->vehicle_id ?: null,
            'borrower_name' => $this->name,
            'department_id' => $this->department_id,
            'start_at' => $startAt,
            'end_at' => $endAt,
            'purpose' => $this->purpose,
            'destination' => $this->destination,
            'odd_even_area' => $this->odd_even_area,
            'purpose_type' => $this->purpose_type,
            'terms_agreed' => $this->agree_terms,
            'has_sim_a' => $this->has_sim_a,
            'status' => 'pending',
        ]);

        session()->flash('success', 'Booking berhasil dikirim — status: PENDING.');
        return redirect()->route('vehiclestatus');
    }

    public function handlePhotoUpload()
    {
        if (!$this->booking) {
            session()->flash('error', 'Booking tidak valid.');
            return;
        }

        $user = Auth::user();
        $bookingId = $this->booking->vehiclebooking_id;
        $userId = $user->user_id ?? Auth::id();

        try {
            if ($this->booking->status == 'approved') {
                $this->validate(['photo_before' => 'required|image|max:5120']);

                $this->uploadToLocalStorageAndSave($this->photo_before, 'before', $bookingId, $userId);

                $this->booking->update(['status' => 'on_progress']);

                session()->flash('success', 'Foto "SEBELUM" berhasil diunggah. Perjalanan Anda dimulai (Status: On Progress).');

            } elseif ($this->booking->status == 'returned') {
                $this->validate(['photo_after' => 'required|image|max:5120']);

                $this->uploadToLocalStorageAndSave($this->photo_after, 'after', $bookingId, $userId);

                session()->flash('success', 'Foto "SESUDAH" berhasil diunggah.');
            }

        } catch (\Exception $e) {
            session()->flash('error', 'Upload Gagal: ' . $e->getMessage());
            return;
        }

        return redirect()->route('vehiclestatus');
    }

    private function combineDateTime($date, $time)
    {
        $t = $time ?: '00:00';
        return Carbon::parse("{$date} {$t}")->toDateTimeString();
    }

    /**
     * Upload ke local public disk dan simpan record.
     *
     * Perubahan penting:
     * - gunakan disk 'public' (storage/app/public)
     * - simpan path RELATIF yang dikembalikan oleh storeAs, mis: "vehicle_photos/xxx.png"
     * - nama file dibuat unik & disanitasi
     */
    private function uploadToLocalStorageAndSave($file, $type, $bookingId, $userId)
    {
        if (!$file) {
            throw new \Exception('Tidak ada file yang diunggah.');
        }

        // pastikan object file Livewire valid
        // Livewire temporary file supports methods like getClientOriginalName()
        $originalName = method_exists($file, 'getClientOriginalName') ? $file->getClientOriginalName() : 'upload';
        $ext = method_exists($file, 'getClientOriginalExtension') ? $file->getClientOriginalExtension() : ($file->extension() ?? 'jpg');

        // buat nama file yang aman dan unik
        $filename = "booking_{$bookingId}_{$type}_" . time() . '_' . Str::random(6) . '.' . $ext;
        $filename = preg_replace('/[^A-Za-z0-9_\-\.]/', '_', $filename);

        // simpan ke folder vehicle_photos pada disk 'public' (returns 'vehicle_photos/filename.ext')
        $storedPath = $file->storeAs('vehicle_photos', $filename, 'public');

        if (!$storedPath) {
            throw new \Exception('Gagal menyimpan file ke storage.');
        }

        // storedPath already relative (e.g. vehicle_photos/booking_...png)
        $storagePath = $storedPath;

        // create DB record (sesuaikan nama kolom di model VehicleBookingPhoto)
        VehicleBookingPhoto::create([
            'vehiclebooking_id' => $bookingId,
            'user_id' => $userId,
            'photo_type' => $type,
            'photo_path' => $storagePath,
        ]);

        return $storagePath;
    }

    private function checkAvailabilityForVehicle($vehicleId, $start, $end)
    {
        return !VehicleBooking::where('vehicle_id', $vehicleId)
            ->whereIn('status', ['pending', 'approved', 'on_progress', 'returned'])
            ->where(function ($q) use ($start, $end) {
                $q->where('start_at', '<', $end)
                    ->where('end_at', '>', $start);
            })
            ->exists();
    }

    public function loadAvailability()
    {
        $vehicles = $this->vehicles;

        if ($this->date_from && $this->start_time && $this->date_to && $this->end_time) {
            $desiredStart = $this->combineDateTime($this->date_from, $this->start_time);
            $desiredEnd = $this->combineDateTime($this->date_to, $this->end_time);
        } else {
            $this->availability = [];
            return;
        }

        $this->availability = $vehicles->map(function ($v) use ($desiredStart, $desiredEnd) {
            $isAvailable = $this->checkAvailabilityForVehicle($v->vehicle_id, $desiredStart, $desiredEnd);

            return [
                'label' => ($v->vehicle_name ?? ($v->name ?? 'Kendaraan')) . (($v->plate_number ?? $v->license_plate) ? " — " . ($v->plate_number ?? $v->license_plate) : ''),
                'status' => $isAvailable ? 'available' : 'unavailable',
                'vehicle_id' => $v->vehicle_id,
            ];
        })->toArray();
    }

    public function loadRecentBookings()
    {
        $user = Auth::user();
        if (!$user)
            return;

        $this->recentBookings = VehicleBooking::where('company_id', $user->company_id ?? 1)
            ->where('department_id', $user->department_id)
            ->orderByDesc('created_at')
            ->limit(6)
            ->get();
    }

    public function resetForm()
    {
        $this->start_time = null;
        $this->end_time = null;
        $this->date_from = null;
        $this->date_to = null;
        $this->purpose = null;
        $this->destination = null;
        $this->odd_even_area = 'tidak';
        $this->purpose_type = 'dinas';
        $this->vehicle_id = null;
        $this->photo_before = null;
        $this->photo_after = null;
        $this->has_sim_a = false;
        $this->agree_terms = false;
        $this->booking = null;
    }

    public function render()
    {
        $title = 'Book Vehicle';
        if ($this->booking) {
            if ($this->booking->status == 'approved') {
                $title = 'Upload Foto (Sebelum)';
            } elseif ($this->booking->status == 'returned') {
                $title = 'Upload Foto (Sesudah)';
            }
        }

        return view('livewire.pages.user.bookvehicle', [
            'booking' => $this->booking,
            'departments' => $this->departments,
            'vehicles' => $this->vehicles,
            'availability' => $this->availability,
            'recentBookings' => $this->recentBookings,
        ])->layout('layouts.app', ['title' => $title]);
    }
}
