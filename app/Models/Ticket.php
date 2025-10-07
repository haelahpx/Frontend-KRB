<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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
        return $this->belongsTo(\App\Models\Department::class, 'department_id', 'department_id');
    }

    // requesterDepartment seharusnya merujuk ke kolom requestdept_id
    public function requesterDepartment(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Department::class, 'requestdept_id', 'department_id');
    }

    // attachments must reference TicketAttachment (not TicketAssignment)
    public function attachments(): HasMany
    {
        return $this->hasMany(\App\Models\TicketAttachment::class, 'ticket_id', 'ticket_id');
    }

    public function latestAssignment()
    {
        return $this->hasOne(TicketAssignment::class, 'ticket_id', 'ticket_id')
            ->latestOfMany('assigned_at');
    }

    public function assignment(): HasOne
    {
        return $this->hasOne(TicketAssignment::class, 'ticket_id', 'ticket_id');
    }

    public function comments(): HasMany
    {
        return $this->hasMany(TicketComment::class, 'ticket_id', 'ticket_id');
    }

    // Optional: compatibility accessors (kalau blade masih pakai title/notes/id)
    public function getTitleAttribute()
    {
        return $this->subject;
    }

    public function getNotesAttribute()
    {
        return $this->description;
    }

    public function getIdAttribute()
    {
        return $this->ticket_id;
    }
}
