<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TicketAttachment extends Model
{
    protected $table = 'ticket_attachments';
    protected $primaryKey = 'attachment_id';
    public $timestamps = false; // tabel hanya punya created_at (DEFAULT CURRENT_TIMESTAMP)

    protected $fillable = [
        'ticket_id',
        'file_url',
        'file_type',
        // 'created_at' biarkan diisi DB
    ];

    public function ticket()
    {
        // PK ticket: ticket_id
        return $this->belongsTo(Ticket::class, 'ticket_id', 'ticket_id');
    }
}
