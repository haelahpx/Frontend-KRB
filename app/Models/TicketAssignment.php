<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TicketAssignment extends Model
{
    protected $table = 'ticket_assignments';
    protected $primaryKey = 'assignment_id';
    public $timestamps = true;

    // match your columns exactly
    protected $fillable = ['ticket_id', 'user_id'];

    public function ticket(): BelongsTo
    {
        return $this->belongsTo(Ticket::class, 'ticket_id', 'ticket_id');
    }

    // alias "agent" → users.user_id
    public function agent(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }
}
