<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class JewelryService extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'service_number',
        'customer_id',
        'customer_name',
        'customer_phone',
        'item_name',
        'weight_before',
        'weight_after',
        'service_type',
        'damage_description',
        'estimated_cost',
        'down_payment',
        'remaining_cost',
        'received_date',
        'completion_target_date',
        'status',
        'cashier_id',
        'notes',
    ];

    protected $casts = [
        'weight_before' => 'decimal:3',
        'weight_after' => 'decimal:3',
        'estimated_cost' => 'decimal:2',
        'down_payment' => 'decimal:2',
        'remaining_cost' => 'decimal:2',
        'received_date' => 'date',
        'completion_target_date' => 'date',
    ];

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function cashier(): BelongsTo
    {
        return $this->belongsTo(User::class, 'cashier_id');
    }
}
