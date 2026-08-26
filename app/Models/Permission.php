<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Permission extends Model
{
    protected static function booted(): void
    {
        static::created(fn (Permission $permission) => $permission->updateQuietly(['updated_at' => null]));
    }

    protected $fillable = ['name', 'display_name', 'description'];

    public function resource(): BelongsTo
    {
        return $this->belongsTo(Resource::class);
    }

    public function action(): BelongsTo
    {
        return $this->belongsTo(Action::class);
    }

    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class, 'role_has_permissions');
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'user_has_permissions')->withPivot('type');
    }
}
