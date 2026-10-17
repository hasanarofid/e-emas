<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class TradeIn extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'trade_in_number',
        'customer_id',
        'customer_name',
        'customer_phone',
        'old_item_name',
        'old_gold_rate_id',
        'old_weight',
        'old_estimated_price',
        'new_item_id',
        'new_item_name',
        'new_weight',
        'new_price',
        'difference_amount',
        'payment_method',
        'bank_account_id',
        'amount_paid',
        'change_due',
        'transaction_date',
        'cashier_id',
        'notes',
    ];

    protected $casts = [
        'old_weight' => 'decimal:3',
        'old_estimated_price' => 'decimal:2',
        'new_weight' => 'decimal:3',
        'new_price' => 'decimal:2',
        'difference_amount' => 'decimal:2',
        'amount_paid' => 'decimal:2',
        'change_due' => 'decimal:2',
        'transaction_date' => 'date',
    ];

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function oldGoldRate(): BelongsTo
    {
        return $this->belongsTo(GoldRate::class, 'old_gold_rate_id');
    }

    public function newItem(): BelongsTo
    {
        return $this->belongsTo(Item::class, 'new_item_id');
    }

    public function cashier(): BelongsTo
    {
        return $this->belongsTo(User::class, 'cashier_id');
    }

    public function bankAccount(): BelongsTo
    {
        return $this->belongsTo(BankAccount::class, 'bank_account_id');
    }
}
