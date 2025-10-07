<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\TicketAttachment;
use App\Models\TicketAssignment;
use App\Models\TicketComment;
use App\Models\User;
use App\Models\Company;
use App\Models\Department;

class Ticket extends Model
{
    use HasFactory;

    protected $table = 'tickets';
    protected $primaryKey = 'ticket_id';
    public $timestamps = true;

    protected $fillable = [
        'company_id',
        'requestdept_id',
        'department_id',
        'user_id',
        'subject',
        'description',
        'priority',
        'status',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class, 'company_id', 'company_id');
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class, 'department_id', 'department_id');
    }

    public function requesterDepartment(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }

    /** ✅ Correct relation to ticket_attachments */
    public function attachments(): HasMany
    {
        return $this->hasMany(TicketAttachment::class, 'ticket_id', 'ticket_id');
    }

    /** If you need the most recent assignment, key by PK to avoid 'assigned_at' column issues */
    public function latestAssignment(): HasOne
    {
        return $this->hasOne(TicketAssignment::class, 'ticket_id', 'ticket_id')
            ->latestOfMany('assignment_id');
    }

    public function assignment(): HasOne
    {
        return $this->hasOne(TicketAssignment::class, 'ticket_id', 'ticket_id');
    }

    public function comments(): HasMany
    {
        return $this->hasMany(TicketComment::class, 'ticket_id', 'ticket_id');
    }
}
