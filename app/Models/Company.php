<?php

// app/Models/Company.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    protected $table = 'companies';
    protected $primaryKey = 'company_id'; // <-- penting
    public $incrementing = true;          // kalau PK-nya auto-increment BIGINT
    protected $keyType = 'int';           // kalau BIGINT

    // kalau tabel companies TIDAK punya created_at/updated_at, uncomment:
    // public $timestamps = false;

    protected $fillable = ['company_name', 'company_address', 'company_email']; // sesuaikan
}
