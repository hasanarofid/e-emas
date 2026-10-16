<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Tray extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'name',
        'showcase_location',
        'category_id',
        'gold_rate_id',
        'capacity',
        'total_items',
        'total_weight',
        'status',
        'notes',
    ];

    protected $casts = [
        'capacity' => 'integer',
        'total_items' => 'integer',
        'total_weight' => 'decimal:3',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function goldRate(): BelongsTo
    {
        return $this->belongsTo(GoldRate::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(Item::class);
    }

    public function recalculateTotals(): void
    {
        $activeItems = $this->items()->where('status', 'available')->get();
        $this->update([
            'total_items' => $activeItems->count(),
            'total_weight' => $activeItems->sum('gross_weight'),
        ]);
    }
}
