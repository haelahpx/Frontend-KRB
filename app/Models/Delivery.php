<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Delivery extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'deliveries';

    protected $primaryKey = 'delivery_id';
    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = [
        'company_id',
        'receptionist_id',
        'item_name',
        'type',            // 'document' | 'package'
        'nama_pengirim',
        'nama_penerima',
        'storage_id',
        'pengambilan',     // datetime (pickup)
        'pengiriman',      // datetime (courier drop time / sent time)
        'status',          // 'taken' | 'delivered' | 'pending' | 'stored'
    ];

    protected $casts = [
        'pengambilan' => 'datetime',
        'pengiriman' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    // --- Canonical constants (optional but handy)
    public const TYPE_DOCUMENT = 'document';
    public const TYPE_PACKAGE = 'package';

    public const STATUS_TAKEN = 'taken';
    public const STATUS_DELIVERED = 'delivered';
    public const STATUS_PENDING = 'pending';
    public const STATUS_STORED = 'stored';

    // --- Relations
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class, 'company_id', 'company_id');
    }

    public function receptionist(): BelongsTo
    {
        return $this->belongsTo(User::class, 'receptionist_id', 'user_id');
    }

    public function storage(): BelongsTo
    {
        return $this->belongsTo(Storage::class, 'storage_id', 'storage_id');
    }

    // --- Query Scopes
    public function scopeByCompany($query, ?int $companyId)
    {
        if ($companyId)
            $query->where('company_id', $companyId);
        return $query;
    }

    public function scopeStatus($query, ?string $status)
    {
        if ($status)
            $query->where('status', $status);
        return $query;
    }

    public function scopeSearch($query, ?string $term)
    {
        if (!$term)
            return $query;

        return $query->where(function ($q) use ($term) {
            $q->where('item_name', 'like', "%{$term}%")
                ->orWhere('nama_pengirim', 'like', "%{$term}%")
                ->orWhere('nama_penerima', 'like', "%{$term}%");
        });
    }
}
