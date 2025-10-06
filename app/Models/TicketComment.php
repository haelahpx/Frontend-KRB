<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TicketComment extends Model
{
    use HasFactory;

    /**
     * @var string
     */
    protected $table = 'ticket_comments'; 

    /**
     * @var array<int, string>
     */
    protected $fillable = [
        'ticket_id',
        'user_id',
        'comment_text',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function ticket()
    {
        return $this->belongsTo(Ticket::class, 'ticket_id');
    }
}