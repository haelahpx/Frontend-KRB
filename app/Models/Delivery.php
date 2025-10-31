<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Delivery extends Model
{
    use SoftDeletes;

    protected $table      = 'deliveries';
    protected $primaryKey = 'delivery_id'; // adjust if your PK is "id"

    protected $fillable = [
        'company_id',
        'department_id',
        'receptionist_id',
        'type',             // 'document' | 'package'
        'item_name',
        'nama_pengirim',
        'nama_penerima',
        'catatan',
        'status',           // 'pending' | 'stored' | 'done'
        'direction',        // 'deliver' | 'taken'   <-- NEW flow uses this
        'pengiriman',       // datetime when delivered
        'pengambilan',      // datetime when taken
    ];

    protected $casts = [
        'pengiriman'  => 'datetime',
        'pengambilan' => 'datetime',
        'created_at'  => 'datetime',
        'updated_at'  => 'datetime',
        'deleted_at'  => 'datetime',
    ];

    /* ------------ Relationships ------------- */
    public function receptionist()
    {
        return $this->belongsTo(User::class, 'receptionist_id', 'user_id');
    }

    public function department()
    {
        return $this->belongsTo(Department::class, 'department_id', 'department_id');
    }

    /* --------------- Scopes ----------------- */
    public function scopeByCompany($q, ?int $companyId)
    {
        if ($companyId) $q->where('company_id', $companyId);
        return $q;
    }

    /* --------- Computed / Accessors --------- */

    /**
     * Human label from direction ONLY when status is 'done'
     * deliver  -> delivered
     * taken    -> taken
     * other    -> the raw status (pending/stored) for clarity
     */
    public function getFinishStatusAttribute(): string
    {
        if (($this->status ?? '') !== 'done') {
            return (string) ($this->status ?? '');
        }

        return match ($this->direction) {
            'deliver' => 'delivered',
            'taken'   => 'taken',
            default   => 'done',
        };
    }

    /**
     * Finish timestamp based on direction.
     * deliver  -> pengiriman
     * taken    -> pengambilan
     * fallback -> created_at
     */
    public function getFinishAtAttribute(): ?\Illuminate\Support\Carbon
    {
        return match ($this->direction) {
            'deliver' => $this->pengiriman,
            'taken'   => $this->pengambilan,
            default   => $this->created_at,
        };
    }
}
