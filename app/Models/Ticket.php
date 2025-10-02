<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

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

    public function department()
    {
        return $this->belongsTo(\App\Models\Department::class, 'department_id', 'department_id');
    }

    public function user()
    {
        return $this->belongsTo(\App\Models\User::class, 'user_id', 'user_id');
    }

    public function assignments()
    {
        return $this->hasMany(TicketAssignment::class, 'ticket_id', 'ticket_id');
    }

    public function latestAssignment()
    {
        return $this->hasOne(TicketAssignment::class, 'ticket_id', 'ticket_id')
            ->latestOfMany('assigned_at');
    }

    public function assignment()
    {
        return $this->hasOne(TicketAssignment::class, 'ticket_id', 'ticket_id');
    }

}
