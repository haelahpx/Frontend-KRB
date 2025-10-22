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

    public function reads()
    {
        return $this->hasMany(\App\Models\TicketCommentRead::class, 'comment_id', 'comment_id');
    }

    public function hasReadBy(int $userId): bool
    {
        return $this->reads()->where('user_id', $userId)->exists();
    }
    public function scopeUnreadFor($query, int $userId)
    {
        return $query->whereDoesntHave('reads', fn($q) => $q->where('user_id', $userId));
    }
}