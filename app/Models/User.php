<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Storage;

#[Fillable(['full_name', 'email', 'password', 'address', 'DUI', 'birthday', 'photo', 'is_active'])]
#[Hidden(['password'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

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
        $permissionId = match (true) {
            $permission instanceof Permission => $permission->id,
            is_int($permission) => $permission,
            default => Permission::query()->where('name', $permission)->value('id'),
        };

        if ($permissionId === null) {
            return false;
        }

        return in_array((int) $permissionId, $this->effectivePermissionIds(), true);
    }
}
