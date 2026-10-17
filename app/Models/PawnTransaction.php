<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class PawnTransaction extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'pawn_number',
        'customer_id',
        'customer_name',
        'customer_phone',
        'item_name',
        'gold_rate_id',
        'gross_weight',
        'estimated_value',
        'loan_amount',
        'monthly_interest_rate',
        'monthly_fee',
        'start_date',
        'due_date',
        'status',
        'settled_date',
        'settled_amount',
        'cashier_id',
        'notes',
    ];

    protected $casts = [
        'gross_weight' => 'decimal:3',
        'estimated_value' => 'decimal:2',
        'loan_amount' => 'decimal:2',
        'monthly_interest_rate' => 'decimal:2',
        'monthly_fee' => 'decimal:2',
        'settled_amount' => 'decimal:2',
        'start_date' => 'date',
        'due_date' => 'date',
        'settled_date' => 'date',
    ];

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function goldRate(): BelongsTo
    {
        return $this->belongsTo(GoldRate::class);
    }

    public function cashier(): BelongsTo
    {
        return $this->belongsTo(User::class, 'cashier_id');
    }
}
