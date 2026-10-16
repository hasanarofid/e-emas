<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TrayMovement extends Model
{
    use HasFactory;

    protected $fillable = [
        'item_id',
        'from_tray_id',
        'to_tray_id',
        'moved_by',
        'reason',
    ];

    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class);
    }

    public function fromTray(): BelongsTo
    {
        return $this->belongsTo(Tray::class, 'from_tray_id');
    }

    public function toTray(): BelongsTo
    {
        return $this->belongsTo(Tray::class, 'to_tray_id');
    }

    public function movedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'moved_by');
    }
}
