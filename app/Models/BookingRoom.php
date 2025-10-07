<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BookingRoom extends Model
{
    protected $table = 'booking_rooms';
    protected $primaryKey = 'bookingroom_id';
    public $incrementing = true;
    protected $keyType = 'int';
    public $timestamps = true;

    protected $fillable = [
        'room_id',
        'company_id',
        'user_id',
        'department_id',
        'meeting_title',
        'date',
        'number_of_attendees',
        'start_time',
        'end_time',
        'special_notes',
    ];

    protected $casts = [
        'date'       => 'date',
        'start_time' => 'datetime',
        'end_time'   => 'datetime',
    ];

    public function requirements()
    {
        return $this->belongsToMany(Requirement::class, 'booking_requirements', 'bookingroom_id', 'requirement_id')
            ->withTimestamps();
    }
    public function room()
    {
        return $this->belongsTo(Room::class, 'room_id', 'room_id');
    }

    public function department()
    {
        return $this->belongsTo(Department::class, 'department_id', 'department_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }
}
