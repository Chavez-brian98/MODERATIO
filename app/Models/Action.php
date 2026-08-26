<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Action extends Model
{
    public $timestamps = false;

    protected $fillable = ['name', 'display_name', 'description'];

    protected static function booted(): void
    {
        static::creating(function (Action $action) {
            $action->created_at = now();
        });
    }

    public function permissions(): HasMany
    {
        return $this->hasMany(Permission::class);
    }
}
