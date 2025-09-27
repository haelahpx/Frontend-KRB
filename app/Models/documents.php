<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class Documents extends Model
{
    protected $table = 'documents';
    protected $primaryKey = 'document_id';
    public $incrementing = true;
    protected $keyType = 'int';
    public $timestamps = true;
    protected $fillable = [
        'company_id',
        'user_id',
        'department_id',
        'document_name',
        'nama_pengirim',
        'nama_penerima',
        'type',
        'penyimpanan',
        'pengambilan',
        'pengiriman',
        'status',
    ];
    protected $casts = [
        'pengambilan' => 'datetime',
        'pengiriman' => 'datetime',
    ];
    public function scopeForCompany(Builder $q, $companyId): Builder
    {
        return $q->where('company_id', $companyId);
    }
    public function scopeSearch(Builder $q, ?string $term): Builder
    {
        if (!$term) {
            return $q;
        }
        $like = '%' . $term . '%';
        return $q->where(function ($w) use ($like) {
            $w->where('document_name', 'like', $like)
                ->orWhere('nama_pengirim', 'like', $like)
                ->orWhere('nama_penerima', 'like', $like)
                ->orWhere('type', 'like', $like)
                ->orWhere('penyimpanan', 'like', $like)
                ->orWhere('status', 'like', $like);
        });
    }
    public function company()
    {
        return $this->belongsTo(Company::class, 'company_id');
    }
    public function department()
    {
        return $this->belongsTo(Department::class, 'department_id');
    }
    public function receptionist()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
