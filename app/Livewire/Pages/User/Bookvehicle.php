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

class Bookvehicle extends Component
{
    use WithFileUploads;

    /* -------------------------
       Form properties
       ------------------------- */
    public $name;
    public $department_id;
    public $start_time;
    public $end_time;
    public $date_from;
    public $date_to;
    public $purpose;
    public $destination;
    public $odd_even_area = 'tidak';
    public $jenis_keperluan = '';
    public $vehicle_id = null;
    public $has_sim_a = false;
    public $agree_terms = false;

    /* -------------------------
       File uploads
       ------------------------- */
    public $photo_before;
    public $photo_after;

    /* -------------------------
       Helpers / UI data
       ------------------------- */
    public $departments;
    public $vehicles;
    public $hasVehicles = false;
    public $availability = [];
    public $recentBookings = [];

    /* -------------------------
       Edit / upload mode (after approved)
       ------------------------- */
    public $isEdit = false;
    public $editingBookingId = null;
    public $editingBooking = null;

    /* -------------------------
       Mount - load masters + initial data
       ------------------------- */
    public function mount()
    {
        $user = Auth::user();

        // prefill name & department if available in user profile
        $this->name = $user->full_name ?? $user->name ?? null;
        $this->department_id = $user->department_id ?? null;

        // load static/master data
        $this->departments = Department::orderBy('department_name')->get();
        $this->vehicles = Vehicle::where('company_id', $user->company_id ?? 1)->get();
        $this->hasVehicles = $this->vehicles->count() > 0;

        // initial load (safe lightweight)
        $this->loadAvailability();
        $this->loadRecentBookings();

        // if ?edit=id present, enter edit mode
        $editId = request()->query('edit');
        if ($editId) {
            $this->enterEditMode($editId);
        }
    }

    /* -------------------------
       Validation rules
       ------------------------- */
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

    /* -------------------------
       Livewire file validation hooks
       ------------------------- */
    public function updatedPhotoBefore()
    {
        $this->validateOnly('photo_before');
    }

    public function updatedPhotoAfter()
    {
        $this->validateOnly('photo_after');
    }

    /* -------------------------
       Enter edit mode (public because Blade may call via ?edit or user flow)
       - only allowed when booking status is 'approved'
       ------------------------- */
    public function enterEditMode($id)
    {
        $booking = VehicleBooking::where('vehicle_booking_id', $id)
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
        $this->editingBookingId = $booking->vehicle_booking_id;
        $this->editingBooking = $booking;

        // fill small preview info
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

    /* -------------------------
       Submit handler (create OR upload mode)
       ------------------------- */
    public function submit()
    {
        if ($this->isEdit) {
            // upload photos flow (after approved)
            $this->validate();

            $beforePath = $this->photo_before->store('vehicle_bookings', 'public');
            $afterPath = $this->photo_after->store('vehicle_bookings', 'public');

            VehicleBookingPhoto::create([
                'vehicle_booking_id' => $this->editingBookingId,
                'uploaded_by' => Auth::id(),
                'type' => 'before',
                'path' => $beforePath,
                'mime' => $this->photo_before->getClientMimeType(),
                'size' => $this->photo_before->getSize(),
            ]);

            VehicleBookingPhoto::create([
                'vehicle_booking_id' => $this->editingBookingId,
                'uploaded_by' => Auth::id(),
                'type' => 'after',
                'path' => $afterPath,
                'mime' => $this->photo_after->getClientMimeType(),
                'size' => $this->photo_after->getSize(),
            ]);

            // set to draft per permintaan
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

        $startAt = $this->combineDateTime($this->date_from, $this->start_time);
        $endAt = $this->combineDateTime($this->date_to, $this->end_time ?? $this->start_time);

        $purposeType = $this->jenis_keperluan === 'visitasi' ? 'visitasi' : 'logistik barang';

        $booking = VehicleBooking::create([
            'company_id' => Auth::user()->company_id ?? 1,
            'user_id' => Auth::id(),
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

        // optional: store photos at creation time if provided
        if ($this->photo_before) {
            $path = $this->photo_before->store('vehicle_bookings', 'public');
            VehicleBookingPhoto::create([
                'vehicle_booking_id' => $booking->vehicle_booking_id,
                'uploaded_by' => Auth::id(),
                'type' => 'before',
                'path' => $path,
                'mime' => $this->photo_before->getClientMimeType(),
                'size' => $this->photo_before->getSize(),
            ]);
        }

        if ($this->photo_after) {
            $path = $this->photo_after->store('vehicle_bookings', 'public');
            VehicleBookingPhoto::create([
                'vehicle_booking_id' => $booking->vehicle_booking_id,
                'uploaded_by' => Auth::id(),
                'type' => 'after',
                'path' => $path,
                'mime' => $this->photo_after->getClientMimeType(),
                'size' => $this->photo_after->getSize(),
            ]);
        }

        session()->flash('success', 'Booking dikirim — status: PENDING.');
        return redirect()->route('bookingstatus');
    }

    /* -------------------------
       Helper: combine date + time
       ------------------------- */
    private function combineDateTime($date, $time)
    {
        $t = $time ?: '00:00';
        return Carbon::parse("{$date} {$t}")->toDateTimeString();
    }

    /* -------------------------
       PUBLIC: loadAvailability
       - Must be public because Blade calls via wire:poll
       - Checks each vehicle for overlapping bookings in selected window
       ------------------------- */
    public function loadAvailability()
    {
        $vehicles = $this->vehicles;

        if ($this->date_from && $this->start_time) {
            $desiredStart = Carbon::parse("{$this->date_from} {$this->start_time}");
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

    /* -------------------------
       PUBLIC: loadRecentBookings
       - Must be public if you want to poll it; otherwise Blade does not call it directly.
       - Filters to only current logged-in user's bookings.
       ------------------------- */
    public function loadRecentBookings()
    {
        $this->recentBookings = VehicleBooking::where('company_id', Auth::user()->company_id ?? 1)
            ->where('user_id', Auth::id())
            ->orderByDesc('created_at')
            ->limit(6)
            ->get();
    }

    /* -------------------------
       Reset form convenience
       ------------------------- */
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
    }

    /* -------------------------
       Render
       - We no longer call loadAvailability/loadRecentBookings repeatedly here,
         Blade will poll loadAvailability; we only ensure initial data in mount.
       ------------------------- */
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
