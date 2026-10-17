<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class BuybackDeduction extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'gold_rate_id',
        'deduction_percentage',
        'deduction_fee_per_gram',
        'crafting_fee_deducted',
        'condition_notes',
    ];

    protected $casts = [
        'deduction_percentage' => 'decimal:2',
        'deduction_fee_per_gram' => 'decimal:2',
        'crafting_fee_deducted' => 'boolean',
    ];

    public function goldRate(): BelongsTo
    {
        return $this->belongsTo(GoldRate::class);
    }
}
