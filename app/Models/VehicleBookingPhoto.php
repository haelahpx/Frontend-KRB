<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VehicleBookingPhoto extends Model
{
    use HasFactory;

    protected $table = 'vehicle_booking_photos';

    protected $fillable = [
        'vehiclebooking_id',
        'user_id',
        'photo_type',
        'photo_url',
        'cloudinary_public_id',
    ];

    public function booking()
    {
        return $this->belongsTo(\App\Models\VehicleBooking::class, 'vehiclebooking_id', 'vehiclebooking_id');
    }

    public function user()
    {
        return $this->belongsTo(\App\Models\User::class, 'user_id', 'user_id');
    }
}
