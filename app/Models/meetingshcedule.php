<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Meetingshcedule extends Model
{
    protected $table = 'booking_rooms';
    protected $primaryKey = 'bookingroom_id';
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
        'date' => 'date:Y-m-d',
        'start_time' => 'datetime:Y-m-d H:i:s',
        'end_time' => 'datetime:Y-m-d H:i:s',
    ];
}
