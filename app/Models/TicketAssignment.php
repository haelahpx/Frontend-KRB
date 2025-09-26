<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TicketAssignment extends Model
{
    protected $fillable = ['ticket_id', 'agent_id', 'assigned_at'];
}
