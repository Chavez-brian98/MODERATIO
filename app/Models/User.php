<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Storage;

#[Fillable(['full_name', 'email', 'phone', 'password', 'address', 'DUI', 'birthday', 'photo', 'is_active'])]
#[Hidden(['password'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    protected static function booted(): void
    {
        static::created(fn (User $user) => $user->updateQuietly(['updated_at' => null]));
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'password' => 'hashed',
            'is_active' => 'boolean',
            'birthday' => 'date',
        ];
    }

    public function photoUrl(): ?string
    {
        if (empty($this->photo)) {
            return null;
        }

        return Storage::disk('public')->url($this->photo);
    }

    public function initials(): string
    {
        return collect(explode(' ', trim($this->full_name)))
            ->filter()
            ->map(fn ($word) => mb_strtoupper(mb_substr($word, 0, 1)))
            ->take(2)
            ->implode('');
    }

    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class, 'user_has_roles');
    }

    public function permissions(): BelongsToMany
    {
        return $this->belongsToMany(Permission::class, 'user_has_permissions')->withPivot('type');
    }

    public function role(): ?Role
    {
        return $this->roles->first();
    }

    public function effectivePermissionIds(): array
    {
        $role = $this->role();

        if ($role?->is_super_admin) {
            return Permission::query()->pluck('id')->all();
        }

        $inherited = $role ? $role->permissions()->pluck('permissions.id')->all() : [];

        $granted = [];
        $denied = [];

        foreach ($this->permissions()->get() as $permission) {
            if ($permission->pivot->type === 'grant') {
                $granted[] = $permission->id;
            } elseif ($permission->pivot->type === 'deny') {
                $denied[] = $permission->id;
            }
        }

        return array_values(array_unique(array_diff(array_merge($inherited, $granted), $denied)));
    }

    public function hasEffectivePermission(Permission|int|string $permission): bool
    {
        $permissionId = self::resolvePermissionId($permission);

        if ($permissionId === null) {
            return false;
        }

        return in_array((int) $permissionId, $this->effectivePermissionIds(), true);
    }

    /**
     * Filter users who can effectively use a permission: super-admin roles,
     * role inheritance and direct grants, minus direct denies.
     */
    public function scopeWithEffectivePermission(Builder $query, Permission|int|string $permission): Builder
    {
        $permissionId = self::resolvePermissionId($permission);

        if ($permissionId === null) {
            return $query->whereRaw('1 = 0');
        }

        return $query
            ->where(function (Builder $q) use ($permissionId) {
                $q->whereHas('roles', fn (Builder $r) => $r->where('is_super_admin', true))
                    ->orWhereHas('roles.permissions', fn (Builder $p) => $p->where('permissions.id', $permissionId))
                    ->orWhereHas('permissions', fn (Builder $p) => $p
                        ->where('permissions.id', $permissionId)
                        ->where('user_has_permissions.type', 'grant'));
            })
            ->whereDoesntHave('permissions', fn (Builder $p) => $p
                ->where('permissions.id', $permissionId)
                ->where('user_has_permissions.type', 'deny'));
    }

    private static function resolvePermissionId(Permission|int|string $permission): ?int
    {
        return match (true) {
            $permission instanceof Permission => $permission->id,
            is_int($permission) => $permission,
            default => Permission::query()->where('name', $permission)->value('id'),
        };
    }
}
