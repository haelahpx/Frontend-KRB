<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TicketAttachment extends Model
{
    protected $table = 'ticket_attachments';
    protected $primaryKey = 'attachment_id';
    public $timestamps = false; // tabel hanya punya created_at default NOW()

    // Lengkapi semua kolom yg mungkin kita isi
    protected $fillable = [
        'ticket_id',
        'file_url',
        'file_type',
        'uploaded_by',           // nullable
        'cloudinary_public_id',  // nullable
        'bytes',                 // nullable
        'original_filename',     // nullable
        'created_at',            // biarkan DB isi default, tapi bolehlah diisi manual
    ];

    protected $casts = [
        'ticket_id' => 'int',
        'bytes'     => 'int',
    ];

    public function ticket()
    {
        return $this->belongsTo(Ticket::class, 'ticket_id', 'ticket_id');
    }
}
