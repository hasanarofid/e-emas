<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class StockAudit extends Model
{
    use HasFactory;

    protected $fillable = [
        'audit_code',
        'tray_id',
        'auditor_id',
        'status',
        'total_expected_items',
        'total_scanned_items',
        'total_missing_items',
        'total_mismatched_items',
        'completed_at',
        'notes',
    ];

    protected $casts = [
        'total_expected_items' => 'integer',
        'total_scanned_items' => 'integer',
        'total_missing_items' => 'integer',
        'total_mismatched_items' => 'integer',
        'completed_at' => 'datetime',
    ];

    public function tray(): BelongsTo
    {
        return $this->belongsTo(Tray::class);
    }

    public function auditor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'auditor_id');
    }

    public function items(): HasMany
    {
        return $this->hasMany(StockAuditItem::class);
    }
}
