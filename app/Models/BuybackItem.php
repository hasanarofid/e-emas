<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BuybackItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'buyback_id',
        'item_name',
        'gold_rate_id',
        'weight_actual',
        'buy_price_per_gram',
        'deduction_amount',
        'total_amount',
        'condition_notes',
        'destination',
    ];

    protected $casts = [
        'weight_actual' => 'decimal:3',
        'buy_price_per_gram' => 'decimal:2',
        'deduction_amount' => 'decimal:2',
        'total_amount' => 'decimal:2',
    ];

    public function buyback(): BelongsTo
    {
        return $this->belongsTo(Buyback::class);
    }

    public function goldRate(): BelongsTo
    {
        return $this->belongsTo(GoldRate::class);
    }
}
