<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Requirement extends Model
{
    protected $table = 'requirements';
    protected $primaryKey = 'requirement_id';
    public $incrementing = true;
    protected $keyType = 'int';
    public $timestamps = true;

    protected $fillable = ['name'];

    public static function upsertByName(array $names): array
    {
        $names = array_values(array_unique(array_filter($names)));
        if (!$names)
            return [];

        $existing = self::whereIn('name', $names)->pluck('requirement_id', 'name')->toArray();
        $toInsert = array_values(array_diff($names, array_keys($existing)));

        if ($toInsert) {
            $now = now();
            DB::table('requirements')->insert(
                array_map(fn($n) => ['name' => $n, 'created_at' => $now, 'updated_at' => $now], $toInsert)
            );
        }
        return self::whereIn('name', $names)->pluck('requirement_id')->all();
    }
    
    public function bookingRooms()
    {
        return $this->belongsToMany(
            \App\Models\BookingRoom::class,
            'booking_requirements',
            'requirement_id',
            'bookingroom_id'
        )->withTimestamps();
    }

}
