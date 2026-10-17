<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Customer extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'code',
        'name',
        'birth_date',
        'identity_number',
        'phone',
        'telephone',
        'email',
        'city',
        'address',
        'level',
        'points',
        'total_items_count',
        'total_purchased_amount',
        'notes',
    ];

    protected $casts = [
        'birth_date' => 'date',
        'points' => 'integer',
        'total_items_count' => 'integer',
        'total_purchased_amount' => 'decimal:2',
    ];

    public function sales(): HasMany
    {
        return $this->hasMany(Sale::class);
    }

    public function pointLogs(): HasMany
    {
        return $this->hasMany(CustomerPointLog::class);
    }

    /**
     * Generate customer code otomatis: P + TahunBulan + Random 5 digit
     */
    public static function generateCode(): string
    {
        return 'P' . date('ym') . str_pad((string) mt_rand(10000, 99999), 5, '0', STR_PAD_LEFT);
    }
}
