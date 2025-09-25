<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Guestbook extends Model
{
    use HasFactory;

    protected $table = 'guestbooks';
    protected $primaryKey = 'guestbook_id';
    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = [
        'date',
        'jam_in',
        'jam_out',
        'name',
        'phone_number',
        'instansi',
        'keperluan',
        'petugas_penjaga',
    ];

    protected $casts = [
        'date' => 'date:Y-m-d',

    ];
}
