<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Ticket extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_id',
        'requestdept_id',   // ← pastikan ada
        'department_id',
        'user_id',
        'subject',
        'description',
        'priority',
        'status',
    ];

    // Relasi
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function attachments()
    {
        return $this->hasMany(TicketAttachment::class);
    }
}
