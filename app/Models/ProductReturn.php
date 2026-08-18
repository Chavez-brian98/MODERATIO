<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ProductReturn extends Model
{
    public const CREATED_AT = 'created_at';

    public const UPDATED_AT = null;

    protected $table = 'returns';

    protected $fillable = [
        'sale_id', 'user_id', 'cash_register_id', 'reason', 'total_returned', 'created_at',
    ];

    protected function casts(): array
    {
        return [
            'total_returned' => 'float',
            'created_at' => 'datetime',
        ];
    }

    public function sale(): BelongsTo
    {
        return $this->belongsTo(Sale::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function cashRegister(): BelongsTo
    {
        return $this->belongsTo(CashRegister::class);
    }

    public function details(): HasMany
    {
        return $this->hasMany(ReturnDetail::class);
    }
}
