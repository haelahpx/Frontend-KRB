<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes; // <— tambahkan

class User extends Authenticatable
{
    use HasFactory, Notifiable, SoftDeletes; // <— aktifkan

    protected $table = 'users';

    protected $primaryKey = 'user_id';
    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = [
        'company_id',
        'department_id',
        'role_id',
        'full_name',
        'email',
        'phone_number',
        'password',
    ];

    protected $hidden = ['password', 'remember_token'];

    protected function casts(): array
    {
        return [
            'password' => 'hashed',      // biar otomatis hash saat di-set
            'deleted_at' => 'datetime',  // opsional (biar eksplisit)
        ];
    }

    /**
     * Simpan email lowercase.
     */
    protected function email(): Attribute
    {
        return Attribute::make(
            set: fn($value) => is_null($value) ? null : strtolower($value),
        );
    }

    /**
     * Virtual "name" -> full_name.
     */
    protected function name(): Attribute
    {
        return Attribute::make(
            get: fn() => $this->full_name,
            set: fn($value) => ['full_name' => $value],
        );
    }

    /**
     * Gunakan user_id sebagai auth identifier.
     */
    public function getAuthIdentifierName()
    {
        return 'user_id';
    }

    // ===== RELATIONS =====
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class, 'company_id', 'company_id');
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class, 'department_id', 'department_id');
    }

    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class, 'role_id', 'role_id');
    }
}
