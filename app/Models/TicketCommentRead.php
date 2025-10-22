<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TicketCommentRead extends Model
{
    protected $table = 'ticket_comment_reads';
    protected $primaryKey = 'read_id';
    public $timestamps = false;

    protected $fillable = ['comment_id', 'user_id', 'read_at'];

    public function comment(): BelongsTo
    {
        return $this->belongsTo(TicketComment::class, 'comment_id', 'comment_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }
}
