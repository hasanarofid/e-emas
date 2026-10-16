<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class GoldRate extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'code',
        'carat',
        'percentage',
        'buy_price_per_gram',
        'sell_price_per_gram',
        'description',
        'is_active',
        'updated_by',
    ];

    protected $casts = [
        'carat' => 'decimal:2',
        'percentage' => 'decimal:2',
        'buy_price_per_gram' => 'decimal:2',
        'sell_price_per_gram' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function histories(): HasMany
    {
        return $this->hasMany(GoldRateHistory::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(Item::class);
    }
}
