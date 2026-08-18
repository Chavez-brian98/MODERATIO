<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CashRegister extends Model
{
    const CREATED_AT = null;

    const UPDATED_AT = null;

    protected $fillable = [
        'user_id', 'shift', 'opening_amount', 'theoretical_closing_amount',
        'actual_closing_amount', 'difference', 'status', 'opening_date', 'closing_date',
    ];

    protected function casts(): array
    {
        return [
            'opening_amount' => 'float',
            'theoretical_closing_amount' => 'float',
            'actual_closing_amount' => 'float',
            'difference' => 'float',
            'opening_date' => 'datetime',
            'closing_date' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function sales(): HasMany
    {
        return $this->hasMany(Sale::class);
    }
}
