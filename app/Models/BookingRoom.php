<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class BookingRoom extends Model
{
    use SoftDeletes;

    protected $table = 'booking_rooms';
    protected $primaryKey = 'bookingroom_id';
    public $incrementing = true;
    protected $keyType = 'int';
    public $timestamps = true;

    /** Status constants (recommended for readability) */
    // Match these to your DB tinyint mapping:
    public const ST_PENDING = 0;
    public const ST_APPROVED = 1;
    public const ST_REJECTED = 2;
    public const ST_DONE = 3;

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
        'booking_type',             // meeting | online_meeting | hybrid | etc
        'online_provider',          // zoom | google_meet
        'online_meeting_url',
        'online_meeting_code',
        'online_meeting_password',
    ];

    protected $casts = [
        'date' => 'date',
        'start_time' => 'datetime',
        'end_time' => 'datetime',
        'deleted_at' => 'datetime',
        'is_approve' => 'boolean',
    ];

    /* ==========================
     | Relationships
     ========================== */

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

    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by', 'user_id');
    }

    public function requirements(): BelongsToMany
    {
        return $this->belongsToMany(
            Requirement::class,
            'booking_requirements',  // pivot table
            'bookingroom_id',        // FK to booking_rooms
            'requirement_id'         // FK to requirements
        )->withTimestamps();
    }

    /* ==========================
     | Scopes
     ========================== */

    /**
     * Filter by company_id
     */
    public function scopeCompany($query, $companyId)
    {
        return $companyId ? $query->where('company_id', $companyId) : $query;
    }

    /**
     * Common status scopes
     */
    public function scopePending($query)
    {
        return $query->where('status', self::ST_PENDING);
    }

    public function scopeApproved($query)
    {
        return $query->where('status', self::ST_APPROVED);
    }

    /* ==========================
     | Helpers
     ========================== */

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

        // google meet: xxx-xxxx-xxx pattern
        $chars = 'abcdefghijklmnopqrstuvwxyz';
        $seg = fn($n) => substr(str_shuffle(str_repeat($chars, $n)), 0, $n);
        $link = "https://meet.google.com/{$seg(3)}-{$seg(4)}-{$seg(3)}";

        return [
            'url' => $link,
            'code' => strtoupper(substr(md5($link), 0, 6)),
            'password' => strtoupper(substr(md5($link . 'pwd'), 0, 8)),
        ];
    }
}
