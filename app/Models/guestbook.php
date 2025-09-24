<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Guestbook extends Model
{
    protected $table = 'guestbooks';
    protected $primaryKey = 'guestbook_id';
    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = [
        'date',
        'name',
        'phone_number',
        'jam_in',
        'jam_out',
        'instansi',
        'keperluan',
        'petugas_penjaga',
    ];

    protected $casts = [
        'date' => 'date', 
    ];
}
