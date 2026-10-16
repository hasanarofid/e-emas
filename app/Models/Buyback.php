<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Buyback extends Model
{
    use HasFactory;

    protected $fillable = [
        'buyback_number',
        'customer_name',
        'customer_phone',
        'customer_nik',
        'original_sale_id',
        'original_invoice_number',
        'total_weight_original',
        'total_weight_actual',
        'total_deduction',
        'grand_total',
        'payment_method',
        'cashier_id',
        'notes',
    ];

    protected $casts = [
        'total_weight_original' => 'decimal:3',
        'total_weight_actual' => 'decimal:3',
        'total_deduction' => 'decimal:2',
        'grand_total' => 'decimal:2',
    ];

    public function originalSale(): BelongsTo
    {
        return $this->belongsTo(Sale::class, 'original_sale_id');
    }

    public function cashier(): BelongsTo
    {
        return $this->belongsTo(User::class, 'cashier_id');
    }

    public function items(): HasMany
    {
        return $this->hasMany(BuybackItem::class);
    }
}
