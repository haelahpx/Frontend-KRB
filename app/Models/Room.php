<?php

namespace App\Models;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class Room extends Model
{

    use SoftDeletes;
    protected $table = 'rooms';
    protected $primaryKey = 'room_id';
    public $incrementing = true;
    protected $keyType = 'int';
    public $timestamps = true;

    protected $dates = ['deleted_at'];

    protected $fillable = ['company_id', 'room_number'];

    public function getNameAttribute()
    {
        return $this->attributes['name']
            ?? $this->attributes['room_name']
            ?? $this->attributes['room_number']
            ?? null;
    }
    public function bookings()
    {
        return $this->hasMany(BookingRoom::class, 'room_id', 'room_id');
    }

    public function company()
    {
        return $this->belongsTo(Company::class, 'company_id', 'company_id');
    }
}
