<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $table = 'users';

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
            'password' => 'hashed',
            // 'email_verified_at' => 'datetime', // aktifkan jika kolom ini ada
        ];
    }

    /**
     * Pastikan email selalu tersimpan lowercase.
     */
    protected function email(): Attribute
    {
        return Attribute::make(
            set: fn ($value) => is_null($value) ? null : strtolower($value),
        );
    }

    /**
     * Virtual attribute "name" ⇄ "full_name"
     * Biar kompatibel kalau ada kode yang pakai $user->name.
     */
    protected function name(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->full_name,
            set: fn ($value) => ['full_name' => $value],
        );
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class, 'company_id');
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class, 'department_id');
    }

    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class, 'role_id');
    }
}
