<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class TrayScaleLog extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'tray_id',
        'scale_date',
        'session',
        'item_count_system',
        'weight_system',
        'item_count_real',
        'weight_real',
        'difference_weight',
        'status',
        'user_id',
        'notes',
    ];

    protected $casts = [
        'weight_system' => 'decimal:3',
        'weight_real' => 'decimal:3',
        'difference_weight' => 'decimal:3',
        'scale_date' => 'date',
    ];

    public function tray(): BelongsTo
    {
        return $this->belongsTo(Tray::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
