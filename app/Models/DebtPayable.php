<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class DebtPayable extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'payable_code',
        'supplier_id',
        'purchase_id',
        'total_debt',
        'paid_amount',
        'remaining_amount',
        'due_date',
        'status',
        'last_payment_date',
        'notes',
    ];

    protected $casts = [
        'due_date' => 'date',
        'last_payment_date' => 'date',
        'total_debt' => 'decimal:2',
        'paid_amount' => 'decimal:2',
        'remaining_amount' => 'decimal:2',
    ];

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    public function purchase(): BelongsTo
    {
        return $this->belongsTo(Purchase::class);
    }
}
