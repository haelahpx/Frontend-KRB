<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Guestbook extends Model {
    protected $table = 'guestbook';
    protected $fillable = ['tanggal','nama','no_hp','jam_in','jam_out','instansi','keperluan','petugas_penjaga'];
    protected $casts = ['tanggal' => 'date'];
}
