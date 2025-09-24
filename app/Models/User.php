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

    // ✅ Tell Eloquent our PK is user_id (not id)
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
            'password' => 'hashed',
            // 'email_verified_at' => 'datetime', // enable if you have this column
        ];
    }

    /**
     * Store email in lowercase.
     */
    protected function email(): Attribute
    {
        return Attribute::make(
            set: fn ($value) => is_null($value) ? null : strtolower($value),
        );
    }

    /**
     * Virtual "name" attribute mapping to full_name for compatibility.
     */
    protected function name(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->full_name,
            set: fn ($value) => ['full_name' => $value],
        );
    }

    // (Optional safety) make sure Auth uses the right identifier name
    public function getAuthIdentifierName()
    {
        return $this->primaryKey; // 'user_id'
    }

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
