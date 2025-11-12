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
use Illuminate\Validation\Rule;
use Carbon\Carbon;

// =====================================================================
// PERBAIKAN: Pastikan baris ini 'layouts.app'
// (Ini adalah path ke: resources/views/layouts/app.blade.php)
// =====================================================================
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
    public $odd_even_area = 'tidak'; // Sesuai migration baru: 'ganjil','genap','tidak'
    public $purpose_type = 'dinas'; // GANTI: dari jenis_keperluan ke purpose_type (sesuai blade & migration baru)
    public $vehicle_id = null;
    public $has_sim_a = false;
    public $agree_terms = false;

    // files
    public $photo_before;
    public $photo_after;

    // helpers / ui data
    public $departments;
    public $vehicles;
    public $hasVehicles = false;
    public $availability = [];
    public $recentBookings = [];

    // GANTI: State management tidak lagi pakai isEdit, tapi pakai model $booking
    public $booking = null;

    /**
     * Mount komponen
     * - Mengisi data user
     * - Cek query param 'id' untuk load booking yang ada (untuk upload foto)
     */
    public function mount()
    {
        $user = Auth::user();

        // prefill from user profile if available
        $this->name = $user->full_name ?? $user->name ?? null;
        $this->department_id = $user->department_id ?? null;

        $this_company_id = $user->company_id ?? 1;

        $this->departments = Department::where('company_id', $this_company_id)->orderBy('department_name')->get();
        $this->vehicles = Vehicle::where('company_id', $this_company_id)->where('is_active', true)->get();
        $this->hasVehicles = $this->vehicles->count() > 0;

        // initial loads
        $this->loadAvailability();
        $this->loadRecentBookings();

        // GANTI: check query param ?id= (bukan ?edit=)
        $bookingId = request()->query('id');
        if ($bookingId) {
            $this->loadBooking($bookingId, $user);
        }
    }

    /**
     * Load booking yang ada berdasarkan ID dari URL
     */
    public function loadBooking($id, $user)
    {
        $booking = VehicleBooking::where('vehiclebooking_id', $id)
            ->where('company_id', $user->company_id ?? 1)
            ->first();

        if (!$booking) {
            session()->flash('error', 'Booking tidak ditemukan.');
            return;
        }

        // Pastikan user ini boleh melihat booking ini (miliknya atau satu departemen)
        if ($booking->user_id != $user->user_id && $booking->department_id != $user->department_id) {
            session()->flash('error', 'Anda tidak memiliki akses ke booking ini.');
            return;
        }

        // Hanya izinkan aksi jika statusnya 'approved' (utk upload before) atau 'returned' (utk upload after)
        if (!in_array($booking->status, ['approved', 'returned'])) {
            session()->flash('error', 'Booking ini tidak sedang menunggu upload foto (Status: ' . $booking->status . ').');
            return;
        }

        $this->booking = $booking;
    }

    /**
     * Aturan validasi HANYA untuk form CREATE booking baru
     */
    protected function rules()
    {
        return [
            'name' => 'required|string|max:255',
            'department_id' => 'required|integer|exists:departments,department_id',
            'date_from' => 'required|date',
            'date_to' => 'required|date|after_or_equal:date_from',
            'start_time' => 'required',
            'end_time' => 'required|after:start_time', // Sebaiknya 'required' dan 'after'
            'purpose' => 'required|string|max:500',
            'destination' => 'nullable|string|max:255',
            'odd_even_area' => ['required', Rule::in(['ganjil', 'genap', 'tidak'])],
            'purpose_type' => ['required', Rule::in(['dinas', 'operasional', 'antar_jemput', 'lainnya'])], // GANTI: Sesuai migration baru
            'has_sim_a' => 'accepted',
            'agree_terms' => 'accepted',
            'vehicle_id' => 'nullable|integer|exists:vehicles,vehicle_id',
        ];
    }

    /**
     * GANTI: Fungsi 'submit()' lama dipecah. Ini HANYA untuk submit booking BARU.
     */
    public function submitBooking()
    {
        // Validasi hanya untuk create
        $this->validate($this->rules());

        $user = Auth::user();
        $startAt = $this->combineDateTime($this->date_from, $this->start_time);
        $endAt = $this->combineDateTime($this->date_to, $this->end_time);

        // Cek ketersediaan sekali lagi sebelum create
        if ($this->vehicle_id) {
            $isAvailable = $this->checkAvailabilityForVehicle($this->vehicle_id, $startAt, $endAt);
            if (!$isAvailable) {
                session()->flash('error', 'Kendaraan tidak tersedia pada jadwal yang dipilih. Silakan cek ulang.');
                return;
            }
        }

        $booking = VehicleBooking::create([
            'company_id' => $user->company_id ?? 1,
            'user_id' => $user->user_id ?? Auth::id(), // Pastikan user_id terisi
            'vehicle_id' => $this->vehicle_id ?: null,
            'borrower_name' => $this->name,
            'department_id' => $this->department_id,
            'start_at' => $startAt,
            'end_at' => $endAt,
            'purpose' => $this->purpose,
            'destination' => $this->destination,
            'odd_even_area' => $this->odd_even_area, // GANTI: Simpan langsung, tidak perlu mapping 'ya'/'tidak'
            'purpose_type' => $this->purpose_type, // GANTI: Dulu 'jenis_keperluan'
            'terms_agreed' => $this->agree_terms,
            'status' => 'pending', // HAPUS: 'is_approve' tidak ada, langsung 'pending'
            'has_sim_a' => $this->has_sim_a, // <-- Pastikan ini ada (dari Model)
        ]);

        // HAPUS: Foto tidak di-upload saat create

        session()->flash('success', 'Booking berhasil dikirim — status: PENDING.');
        return redirect()->route('vehiclestatus'); // Arahkan ke halaman status
    }

    /**
     * BARU: Fungsi ini menangani upload foto (sebelum/sesudah)
     * Sesuai dengan blade: wire:submit.prevent="handlePhotoUpload"
     */
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
            // ALUR 1: Upload foto SEBELUM
            if ($this->booking->status == 'approved') {
                $this->validate(['photo_before' => 'required|image|max:5120']); // Validasi foto 'before'

                // Simpan ke local storage
                $this->uploadToLocalStorageAndSave($this->photo_before, 'before', $bookingId, $userId);

                // Update status booking
                $this->booking->update(['status' => 'on_progress']);

                session()->flash('success', 'Foto "SEBELUM" berhasil diunggah. Perjalanan Anda dimulai (Status: On Progress).');

                // ALUR 2: Upload foto SESUDAH
            } elseif ($this->booking->status == 'returned') {
                $this->validate(['photo_after' => 'required|image|max:5120']); // Validasi foto 'after'

                // Simpan ke local storage
                $this->uploadToLocalStorageAndSave($this->photo_after, 'after', $bookingId, $userId);

                session()->flash('success', 'Foto "SESUDAH" berhasil diunggah.');
            }

        } catch (\Exception $e) {
            session()->flash('error', 'Upload Gagal: ' . $e->getMessage());
            return;
        }

        // Redirect ke halaman status setelah berhasil
        return redirect()->route('vehiclestatus');
    }


    private function combineDateTime($date, $time)
    {
        $t = $time ?: '00:00';
        return Carbon::parse("{$date} {$t}")->toDateTimeString();
    }

    /**
     * GANTI: Fungsi upload ke Local Storage (bukan Cloudinary)
     */
    private function uploadToLocalStorageAndSave($file, $type, $bookingId, $userId)
    {
        // 1. Buat nama file yang unik
        $filename = "booking_{$bookingId}_{$type}_" . time() . '.' . $file->extension();

        // 2. Simpan file ke 'storage/app/public/vehicle_photos'
        // 'storeAs' akan me-return path lengkap: 'public/vehicle_photos/namafile.jpg'
        $path = $file->storeAs('public/vehicle_photos', $filename);

        // 3. Kita hanya ingin menyimpan path relatif 'vehicle_photos/namafile.jpg' di DB
        $storagePath = str_replace('public/', '', $path);

        // 4. Simpan record ke database
        VehicleBookingPhoto::create([
            'vehiclebooking_id' => $bookingId,
            'user_id' => $userId,
            'photo_type' => $type,
            'photo_path' => $storagePath, // GANTI: Sesuai migration baru
            // 'photo_url' dan 'cloudinary_public_id' dihapus
        ]);

        return $storagePath;
    }

    /**
     * Cek ketersediaan untuk 1 kendaraan spesifik
     */
    private function checkAvailabilityForVehicle($vehicleId, $start, $end)
    {
        return !VehicleBooking::where('vehicle_id', $vehicleId)
            // GANTI: Status yang dihitung 'sibuk'
            ->whereIn('status', ['pending', 'approved', 'on_progress', 'returned'])
            ->where(function ($q) use ($start, $end) {
                $q->where('start_at', '<', $end)
                    ->where('end_at', '>', $start);
            })
            ->exists();
    }

    /**
     * PUBLIC: load availability, dipanggil oleh wire:poll
     */
    public function loadAvailability()
    {
        $vehicles = $this->vehicles;

        if ($this->date_from && $this->start_time && $this->date_to && $this->end_time) {
            $desiredStart = $this->combineDateTime($this->date_from, $this->start_time);
            $desiredEnd = $this->combineDateTime($this->date_to, $this->end_time);
        } else {
            // Jika form belum lengkap, jangan tampilkan apa-apa
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

    /**
     * PUBLIC: load recent bookings
     */
    public function loadRecentBookings()
    {
        $user = Auth::user();
        if (!$user)
            return;

        // GANTI: Load booking berdasarkan departemen, bukan user
        $this->recentBookings = VehicleBooking::where('company_id', $user->company_id ?? 1)
            ->where('department_id', $user->department_id) // GANTI: dari user_id ke department_id
            ->orderByDesc('created_at')
            ->limit(6)
            ->get();
    }

    public function resetForm()
    {
        // $this->name dan $this->department_id jangan di-reset karena itu data user
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
        $this->booking = null; // GANTI: reset $booking
    }

    public function render()
    {
        // GANTI: Logika judul halaman disesuaikan
        $title = 'Book Vehicle';
        if ($this->booking) {
            if ($this->booking->status == 'approved') {
                $title = 'Upload Foto (Sebelum)';
            } elseif ($this->booking->status == 'returned') {
                $title = 'Upload Foto (Sesudah)';
            }
        }

        return view('livewire.pages.user.bookvehicle', [
            'booking' => $this->booking, // BARU: Kirim data booking ke view
            'departments' => $this->departments,
            'vehicles' => $this->vehicles,
            'availability' => $this->availability,
            'recentBookings' => $this->recentBookings,
        ])->layout('layouts.app', ['title' => $title]); // <-- Ini juga harus 'layouts.app'
    }
}