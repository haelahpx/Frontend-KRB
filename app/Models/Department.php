<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Department extends Model
{
    use HasFactory;

    protected $table = 'departments';
    protected $primaryKey = 'department_id';   // <-- penting
    public $incrementing = true;
    protected $keyType = 'int';
    public $timestamps = false; // set true kalau tabel punya created_at/updated_at

    protected $fillable = ['company_id', 'department_name'];

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class, 'company_id', 'company_id');
    }

    public function users(): HasMany
    {
        return $this->hasMany(User::class, 'department_id');
    }
}
