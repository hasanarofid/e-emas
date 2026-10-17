<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PurchaseItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'purchase_id',
        'gold_rate_id',
        'category_id',
        'item_name',
        'weight',
        'price_per_gram',
        'crafting_fee',
        'subtotal',
        'created_item_id',
    ];

    protected $casts = [
        'weight' => 'decimal:3',
        'price_per_gram' => 'decimal:2',
        'crafting_fee' => 'decimal:2',
        'subtotal' => 'decimal:2',
    ];

    public function purchase(): BelongsTo
    {
        return $this->belongsTo(Purchase::class);
    }

    public function goldRate(): BelongsTo
    {
        return $this->belongsTo(GoldRate::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function createdItem(): BelongsTo
    {
        return $this->belongsTo(Item::class, 'created_item_id');
    }
}
