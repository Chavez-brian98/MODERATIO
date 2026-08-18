<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Sale extends Model
{
    const UPDATED_AT = null;

    protected $fillable = [
        'cash_register_id', 'user_id', 'customer_id', 'ticket_number', 'total',
        'amount_received', 'change_due', 'payment_method', 'status', 'observations', 'created_at',
    ];

    protected function casts(): array
    {
        return [
            'total' => 'float',
            'amount_received' => 'float',
            'change_due' => 'float',
            'created_at' => 'datetime',
        ];
    }

    public function scopeNotCancelled(Builder $query): Builder
    {
        return $query->where('status', '!=', 'CANCELLED');
    }

    public function cashRegister(): BelongsTo
    {
        return $this->belongsTo(CashRegister::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function details(): HasMany
    {
        return $this->hasMany(SaleDetail::class);
    }
}
