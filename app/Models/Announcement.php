<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Announcement extends Model
{
    use HasFactory;

    protected $table = 'announcements';
    protected $primaryKey = 'announcements_id';
    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = [
        'company_id',
        'description',
        'event_at',
    ];

    protected $casts = [
        'event_at'   => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function getFormattedCreatedDateAttribute(): ?string
    {
        return $this->created_at ? $this->created_at->format('M d, Y') : null;
    }

    public function getFormattedEventDateAttribute(): ?string
    {
        return $this->event_at ? $this->event_at->format('M d, Y H:i') : null;
    }

    public function scopeForCompany($query, int $companyId)
    {
        return $query->where('company_id', $companyId);
    }
}
