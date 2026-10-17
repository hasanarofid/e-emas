<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Employee extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'employee_code',
        'name',
        'id_card_number',
        'phone',
        'email',
        'address',
        'position',
        'status',
        'join_date',
        'base_salary',
        'allowance',
        'commission_rate',
        'bank_name',
        'bank_account_number',
        'bank_account_holder',
        'is_active',
        'notes',
    ];

    protected $casts = [
        'join_date' => 'date',
        'base_salary' => 'decimal:2',
        'allowance' => 'decimal:2',
        'commission_rate' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function payrolls(): HasMany
    {
        return $this->hasMany(Payroll::class);
    }
}
