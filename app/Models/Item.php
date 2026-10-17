<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Item extends Model
{
    use HasFactory;

    protected $fillable = [
        'sku',
        'name',
        'category_id',
        'gold_rate_id',
        'brand_id',
        'unit_id',
        'warehouse_id',
        'tray_id',
        'gross_weight',
        'stone_weight',
        'net_weight',
        'crafting_fee',
        'total_price',
        'image_url',
        'status',
        'notes',
    ];

    protected $casts = [
        'gross_weight' => 'decimal:3',
        'stone_weight' => 'decimal:3',
        'net_weight' => 'decimal:3',
        'crafting_fee' => 'decimal:2',
        'total_price' => 'decimal:2',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function goldRate(): BelongsTo
    {
        return $this->belongsTo(GoldRate::class);
    }

    public function tray(): BelongsTo
    {
        return $this->belongsTo(Tray::class);
    }

    public function brand(): BelongsTo
    {
        return $this->belongsTo(Brand::class);
    }

    public function unit(): BelongsTo
    {
        return $this->belongsTo(Unit::class);
    }

    public function warehouse(): BelongsTo
    {
        return $this->belongsTo(Warehouse::class);
    }

    public function movements(): HasMany
    {
        return $this->hasMany(TrayMovement::class);
    }

    public function stockMovements(): HasMany
    {
        return $this->hasMany(StockMovement::class);
    }

    /**
     * Hitung perkiraan harga jual saat ini berdasarkan live gold rate + ongkos
     */
    public function getCurrentCalculatedPrice(): float
    {
        $rate = $this->goldRate ? (float) $this->goldRate->sell_price_per_gram : 0;
        return round(($rate * (float) $this->net_weight) + (float) $this->crafting_fee, 2);
    }
}
