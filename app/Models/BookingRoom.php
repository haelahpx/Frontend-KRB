<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class BookingRoom extends Model
{

    use SoftDeletes;
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
        'status',
        'approved_by',
        'is_approve',
        'booking_type',          // meeting | online_meeting | hybrid
        'online_provider',       // zoom | google_meet
        'online_meeting_url',
        'online_meeting_code',
        'online_meeting_password',
    ];

    protected $casts = [
        'date' => 'date',
        'start_time' => 'datetime',
        'end_time' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    public function room(): BelongsTo
    {
        return $this->belongsTo(Room::class, 'room_id', 'room_id');
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class, 'department_id', 'department_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }

    // Helper to generate online meeting link
    public static function generateMeetingUrl(string $provider): array
    {
        if ($provider === 'zoom') {
            $code = rand(1000000000, 9999999999);
            return [
                'url' => "https://zoom.us/j/{$code}",
                'code' => (string) $code,
                'password' => strtoupper(substr(md5($code), 0, 6)),
            ];
        }

        // google meet pattern: xxx-xxxx-xxx
        $chars = 'abcdefghijklmnopqrstuvwxyz';
        $seg = fn($n) => substr(str_shuffle(str_repeat($chars, $n)), 0, $n);
        $link = "https://meet.google.com/{$seg(3)}-{$seg(4)}-{$seg(3)}";

        return [
            'url' => $link,
            'code' => strtoupper(substr(md5($link), 0, 6)),
            'password' => strtoupper(substr(md5($link . 'pwd'), 0, 8)),
        ];
    }

    public function requirements()
    {
        return $this->belongsToMany(
            \App\Models\Requirement::class,
            'booking_requirements',     // nama tabel pivot
            'bookingroom_id',           // FK ke booking_rooms
            'requirement_id'            // FK ke requirements
        )->withTimestamps();
    }
}
