<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GoldRateHistory extends Model
{
    use HasFactory;

    protected $fillable = [
        'gold_rate_id',
        'buy_price_per_gram',
        'sell_price_per_gram',
        'changed_by',
        'notes',
    ];

    protected $casts = [
        'buy_price_per_gram' => 'decimal:2',
        'sell_price_per_gram' => 'decimal:2',
    ];

    public function goldRate(): BelongsTo
    {
        return $this->belongsTo(GoldRate::class);
    }

    public function changedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'changed_by');
    }
}
