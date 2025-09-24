<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TicketAttachment extends Model
{
    protected $fillable = [
        'ticket_id',
        'file_url',
        'file_type',
        'uploaded_by',
    ];

    public $timestamps = false; // tabelmu created_at default CURRENT_TIMESTAMP

    public function ticket() {
        return $this->belongsTo(Ticket::class);
    }
}
