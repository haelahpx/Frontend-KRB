<?php

namespace App\Livewire\Pages\User;

use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\Vehicle;
use App\Models\Department;
use App\Models\VehicleBooking;
use App\Models\VehicleBookingPhoto;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Carbon\Carbon;
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;

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
    public $odd_even_area = 'tidak'; // 'ganjil','genap','tidak'
    public $jenis_keperluan = '';
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

    // edit mode
    public $isEdit = false;
    public $editingBookingId = null;
    public $editingBooking = null;

    public function mount()
    {
        $user = Auth::user();

        // prefill from user profile if available
        $this->name = $user->full_name ?? $user->name ?? null;
        $this->department_id = $user->department_id ?? null;

        $this->departments = Department::orderBy('department_name')->get();
        $this->vehicles = Vehicle::where('company_id', $user->company_id ?? 1)->get();
        $this->hasVehicles = $this->vehicles->count() > 0;

        // initial loads (lightweight)
        $this->loadAvailability();
        $this->loadRecentBookings();

        // check query param ?edit=
        $editId = request()->query('edit');
        if ($editId) {
            $this->enterEditMode($editId);
        }
    }

    protected function rules()
    {
        if ($this->isEdit) {
            return [
                'photo_before' => 'required|image|max:5120',
                'photo_after' => 'required|image|max:5120',
            ];
        }

        return [
            'name' => 'required|string|max:255',
            'department_id' => 'required|integer',
            'date_from' => 'required|date',
            'date_to' => 'required|date|after_or_equal:date_from',
            'start_time' => 'required',
            'end_time' => 'nullable',
            'purpose' => 'required|string|max:500',
            'destination' => 'nullable|string|max:255',
            'odd_even_area' => ['required', Rule::in(['ganjil', 'genap', 'tidak'])],
            'jenis_keperluan' => ['required', Rule::in(['visitasi', 'logistik barang'])],
            'has_sim_a' => 'accepted',
            'agree_terms' => 'accepted',
            'vehicle_id' => 'nullable|integer',
            'photo_before' => 'nullable|image|max:5120',
            'photo_after' => 'nullable|image|max:5120',
        ];
    }

    public function updatedPhotoBefore()
    {
        $this->validateOnly('photo_before');
    }

    public function updatedPhotoAfter()
    {
        $this->validateOnly('photo_after');
    }

    /**
     * Enter edit mode: user uploads photos after booking is approved.
     */
    public function enterEditMode($id)
    {
        $booking = VehicleBooking::where('vehiclebooking_id', $id)
            ->where('company_id', Auth::user()->company_id ?? 1)
            ->first();

        if (!$booking) {
            session()->flash('error', 'Booking tidak ditemukan.');
            return;
        }

        if (($booking->status ?? '') !== 'approved') {
            session()->flash('error', 'Booking belum disetujui sehingga tidak bisa upload foto.');
            return;
        }

        $this->isEdit = true;
        $this->editingBookingId = $booking->vehiclebooking_id;
        $this->editingBooking = $booking;

        // preview
        $this->name = $booking->borrower_name ?? $this->name;
        $this->department_id = $booking->department_id ?? $this->department_id;
        if ($booking->start_at) {
            $this->date_from = Carbon::parse($booking->start_at)->toDateString();
            $this->start_time = Carbon::parse($booking->start_at)->format('H:i');
        }
        if ($booking->end_at) {
            $this->date_to = Carbon::parse($booking->end_at)->toDateString();
            $this->end_time = Carbon::parse($booking->end_at)->format('H:i');
        }
        $this->destination = $booking->destination;
        $this->purpose = $booking->purpose;
    }

    /**
     * Submit: create booking (pending) OR upload photos (edit -> draft)
     */
    public function submit()
    {
        if ($this->isEdit) {
            $this->validate();

            if ($this->photo_before) {
                $this->uploadToCloudinaryAndSave($this->photo_before, 'before', $this->editingBookingId);
            }

            if ($this->photo_after) {
                $this->uploadToCloudinaryAndSave($this->photo_after, 'after', $this->editingBookingId);
            }

            // set booking to draft so admin can review --> then admin can set completed
            $booking = VehicleBooking::find($this->editingBookingId);
            if ($booking) {
                $booking->status = 'draft';
                $booking->save();
            }

            session()->flash('success', 'Foto berhasil diunggah. Booking diset ke DRAFT.');
            return redirect()->route('bookingstatus');
        }

        // create new booking -> status pending
        $this->validate();

        // map jenis_keperluan (UI) -> purpose_type (DB enum)
        switch ($this->jenis_keperluan) {
            case 'visitasi':
                $purposeType = 'dinas';
                break;
            case 'logistik barang':
                $purposeType = 'operasional';
                break;
            default:
                $purposeType = 'lainnya';
        }

        $startAt = $this->combineDateTime($this->date_from, $this->start_time);
        $endAt = $this->combineDateTime($this->date_to, $this->end_time ?? $this->start_time);

        $booking = VehicleBooking::create([
            'company_id' => Auth::user()->company_id ?? 1,
            // project DB uses user_id (user_id bigint) — try to use Auth user primary key name
            'user_id' => Auth::id() ?? Auth::user()->user_id ?? null,
            'vehicle_id' => $this->vehicle_id ?: null,
            'borrower_name' => $this->name,
            'department_id' => $this->department_id,
            'start_at' => $startAt,
            'end_at' => $endAt,
            'purpose' => $this->purpose,
            'destination' => $this->destination,
            'odd_even_area' => in_array($this->odd_even_area, ['ganjil', 'genap']) ? 'ya' : 'tidak',
            'purpose_type' => $purposeType,
            'terms_agreed' => $this->agree_terms ? 1 : 0,
            'is_approve' => 0,
            'status' => 'pending',
        ]);

        // optional: upload photos now if provided
        if ($this->photo_before) {
            $this->uploadToCloudinaryAndSave($this->photo_before, 'before', $booking->vehiclebooking_id);
        }
        if ($this->photo_after) {
            $this->uploadToCloudinaryAndSave($this->photo_after, 'after', $booking->vehiclebooking_id);
        }

        session()->flash('success', 'Booking dikirim — status: PENDING.');
        return redirect()->route('vehiclestatus');
    }

    private function combineDateTime($date, $time)
    {
        $t = $time ?: '00:00';
        return Carbon::parse("{$date} {$t}")->toDateTimeString();
    }

    /**
     * Upload file to Cloudinary and save record
     * returns [public_id, secure_url]
     */
    private function uploadToCloudinaryAndSave($file, $type, $bookingId)
    {
        $folder = trim(env('CLOUDINARY_BASE_FOLDER', 'krbs'), '/') . '/vehicle_photos';

        $upload = Cloudinary::upload($file->getRealPath(), [
            'folder' => $folder,
            'resource_type' => 'image',
            'upload_preset' => env('CLOUDINARY_UPLOAD_PRESET', null),
            'use_filename' => true,
            'unique_filename' => false,
        ]);

        $publicId = method_exists($upload, 'getPublicId') ? $upload->getPublicId() : ($upload['public_id'] ?? null);
        $secureUrl = method_exists($upload, 'getSecurePath') ? $upload->getSecurePath() : ($upload['secure_url'] ?? ($upload['url'] ?? null));

        VehicleBookingPhoto::create([
            'vehiclebooking_id' => $bookingId,
            'user_id' => Auth::user()->user_id ?? Auth::id(),
            'photo_type' => $type,
            'photo_url' => $secureUrl,
            'cloudinary_public_id' => $publicId,
        ]);

        return [$publicId, $secureUrl];
    }

    /**
     * PUBLIC: load availability, called by wire:poll
     */
    public function loadAvailability()
    {
        $vehicles = $this->vehicles;

        if ($this->date_from && $this->start_time) {
            $desiredStart = Carbon::parse("{$this->date_from} {$this->start_time}");
            $desiredEnd = null;
            if ($this->date_to && $this->end_time) {
                $desiredEnd = Carbon::parse("{$this->date_to} {$this->end_time}");
            } else {
                $desiredEnd = (clone $desiredStart)->addHour();
            }
        } else {
            $desiredStart = Carbon::now();
            $desiredEnd = (clone $desiredStart)->addHour();
        }

        $this->availability = $vehicles->map(function ($v) use ($desiredStart, $desiredEnd) {
            $overlaps = VehicleBooking::where('vehicle_id', $v->vehicle_id)
                ->whereIn('status', ['pending', 'approved', 'in_use'])
                ->where(function ($q) use ($desiredStart, $desiredEnd) {
                    $q->where('start_at', '<=', $desiredEnd)
                        ->where('end_at', '>=', $desiredStart);
                })
                ->exists();

            return [
                'label' => ($v->vehicle_name ?? ($v->name ?? 'Kendaraan')) . (($v->plate_number ?? $v->license_plate) ? " — " . ($v->plate_number ?? $v->license_plate) : ''),
                'status' => $overlaps ? 'unavailable' : 'available',
                'vehicle_id' => $v->vehicle_id,
            ];
        })->toArray();
    }

    /**
     * PUBLIC: load recent bookings for current user
     */
    public function loadRecentBookings()
    {
        $userId = Auth::id() ?? Auth::user()->user_id ?? null;

        $this->recentBookings = VehicleBooking::where('company_id', Auth::user()->company_id ?? 1)
            ->where('user_id', $userId)
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
        $this->jenis_keperluan = '';
        $this->vehicle_id = null;
        $this->photo_before = null;
        $this->photo_after = null;
        $this->has_sim_a = false;
        $this->agree_terms = false;
        $this->isEdit = false;
        $this->editingBookingId = null;
        $this->editingBooking = null;
    }

    public function render()
    {
        return view('livewire.pages.user.bookvehicle', [
            'departments' => $this->departments,
            'vehicles' => $this->vehicles,
            'availability' => $this->availability,
            'recentBookings' => $this->recentBookings,
        ])->layout('layouts.app', ['title' => $this->isEdit ? 'Upload Foto Booking' : 'Book Vehicle']);
    }
}
