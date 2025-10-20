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

    /** Numeric constants (keep for writes) */
    public const ST_PENDING = 0;
    public const ST_APPROVED = 1;
    public const ST_REJECTED = 2;
    public const ST_DONE = 3;

    /** Tolerant sets (read both numeric & string rows) */
    private const PENDING_SET = [0, '0', 'pending', 'PENDING'];
    private const APPROVED_SET = [1, '1', 'approved', 'APPROVED'];
    private const REJECTED_SET = [2, '2', 'rejected', 'REJECTED'];
    private const DONE_SET = [3, '3', 'done', 'DONE'];

    protected $fillable = [
        'room_id',
        'company_id',
        'user_id',
        'department_id',
        'meeting_title',
        'date',                   // DATE
        'number_of_attendees',
        'start_time',             // TIME (HH:MM:SS)
        'end_time',               // TIME (HH:MM:SS)
        'special_notes',
        'status',
        'approved_by',
        'is_approve',
        'booking_type',           // meeting | online_meeting | hybrid | etc
        'online_provider',        // zoom | google_meet
        'online_meeting_url',
        'online_meeting_code',
        'online_meeting_password',
    ];

    protected $casts = [
        'date' => 'date',
        // Keep these as strings to avoid wrong datetime auto-casting
        'start_time' => 'string',
        'end_time' => 'string',
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
            'booking_requirements',
            'bookingroom_id',
            'requirement_id'
        )->withTimestamps();
    }

    /* ==========================
    | Scopes
     ========================== */

    /** Filter by company (no-op if null) */
    public function scopeCompany($query, $companyId)
    {
        return $companyId ? $query->where('company_id', $companyId) : $query;
    }

    public function scopePending($q)
    {
        return $q->whereIn('status', self::PENDING_SET);
    }
    public function scopeApproved($q)
    {
        return $q->whereIn('status', self::APPROVED_SET);
    }
    public function scopeRejected($q)
    {
        return $q->whereIn('status', self::REJECTED_SET);
    }
    public function scopeDone($q)
    {
        return $q->whereIn('status', self::DONE_SET);
    }

    /**
     * Approved + now inside window (DATE + TIME).
     * (Kept for reference; RoomApproval shows all approved regardless of time)
     */
    public function scopeOngoing($q, $now = null)
    {
        $now = ($now ?? now(config('app.timezone')))->format('Y-m-d H:i:s');

        return $q->approved()
            ->whereRaw("CONCAT(date, ' ', start_time) <= ?", [$now])
            ->whereRaw("CONCAT(date, ' ', end_time)   >= ?", [$now]);
    }

    /**
     * Promote finished approved meetings to DONE.
     */
    public static function autoProgressToDone(?int $companyId = null, $now = null): void
    {
        $now = ($now ?? now(config('app.timezone')))->format('Y-m-d H:i:s');

        static::query()
            ->when($companyId, fn($q) => $q->where('company_id', $companyId))
            ->approved()
            ->whereRaw("CONCAT(date, ' ', end_time) < ?", [$now])
            ->update(['status' => self::ST_DONE]);
    }

    /* ==========================
    | Helpers (optional)
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
