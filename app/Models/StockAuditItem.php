<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StockAuditItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'stock_audit_id',
        'item_id',
        'sku',
        'scan_status',
        'scanned_at',
    ];

    protected $casts = [
        'scanned_at' => 'datetime',
    ];

    public function stockAudit(): BelongsTo
    {
        return $this->belongsTo(StockAudit::class);
    }

    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class);
    }
}
