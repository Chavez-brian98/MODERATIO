<?php

namespace Tests\Feature;

use App\Models\AuditLog;
use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Database\Seeders\PermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserPermissionTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->signIn();
    }

    private function setupUserWithRole(): array
    {
        $this->seed(PermissionSeeder::class);

        $role = Role::create(['name' => 'CAJERO', 'is_active' => true]);
        $role->permissions()->sync(
            Permission::query()->whereIn('name', ['products_view', 'sales_view'])->pluck('id')->all(),
        );

        $user = User::factory()->create();
        $user->roles()->sync($role->id);
        $user->refresh();

        return [$user, $role];
    }

    public function test_permissions_modal_renders_effective_states(): void
    {
        [$user] = $this->setupUserWithRole();

        $this->get(route('employees.permissions', $user))
            ->assertOk()
            ->assertSee('Permisos · '.$user->full_name)
            ->assertSee('Inventario')
            ->assertSee('Desmarcar un permiso que hereda de su rol');
    }

    public function test_extra_permission_is_saved_as_grant(): void
    {
        [$user] = $this->setupUserWithRole();

        $permissionId = Permission::query()->where('name', 'products_create')->value('id');

        $this->postJson(route('employees.permissions.sync', $user), [
            'permissions' => [$permissionId => 'allow'],
        ])->assertOk()->assertJson(['success' => true]);

        $this->assertDatabaseHas('user_has_permissions', [
            'user_id' => $user->id,
            'permission_id' => $permissionId,
            'type' => 'grant',
        ]);

        $this->assertTrue($user->hasEffectivePermission($permissionId));
    }

    public function test_denying_inherited_permission_saves_deny(): void
    {
        [$user] = $this->setupUserWithRole();

        $permissionId = Permission::query()->where('name', 'products_view')->value('id');
        $this->assertTrue($user->hasEffectivePermission($permissionId));

        $this->postJson(route('employees.permissions.sync', $user), [
            'permissions' => [$permissionId => 'deny'],
        ])->assertOk();

        $this->assertDatabaseHas('user_has_permissions', [
            'user_id' => $user->id,
            'permission_id' => $permissionId,
            'type' => 'deny',
        ]);

        $this->assertFalse($user->fresh()->hasEffectivePermission($permissionId));
    }

    public function test_states_matching_role_create_no_rows(): void
    {
        [$user] = $this->setupUserWithRole();

        $inherited = Permission::query()->where('name', 'sales_view')->value('id');
        $notInherited = Permission::query()->where('name', 'customers_delete')->value('id');

        $this->postJson(route('employees.permissions.sync', $user), [
            'permissions' => [
                $inherited => 'allow',
                $notInherited => 'deny',
            ],
        ])->assertOk();

        $this->assertSame(0, $user->permissions()->count());
    }

    public function test_resync_replaces_previous_overrides(): void
    {
        [$user] = $this->setupUserWithRole();

        $grant = Permission::query()->where('name', 'products_create')->value('id');
        $deny = Permission::query()->where('name', 'products_view')->value('id');
        $extra = Permission::query()->where('name', 'roles_view')->value('id');

        $this->postJson(route('employees.permissions.sync', $user), [
            'permissions' => [$grant => 'allow', $deny => 'deny'],
        ])->assertOk();

        $this->postJson(route('employees.permissions.sync', $user), [
            'permissions' => [$deny => 'deny', $extra => 'allow'],
        ])->assertOk();

        $overrides = $user->permissions()->get()->map(fn ($p) => $p->pivot->type.'='.$p->id)->sort()->values()->all();

        $this->assertSame([
            'deny='.$deny,
            'grant='.$extra,
        ], $overrides);
    }

    public function test_super_admin_role_grants_everything_regardless_of_overrides(): void
    {
        $this->seed(PermissionSeeder::class);

        $superRole = Role::create(['name' => 'GODMODE', 'is_active' => true, 'is_super_admin' => true]);

        $user = User::factory()->create();
        $user->roles()->sync($superRole->id);

        $randomPermission = Permission::query()->where('name', 'audit_log_delete')->value('id');

        $this->assertTrue($user->hasEffectivePermission($randomPermission));
        $this->assertSame(44, count($user->effectivePermissionIds()));
    }

    public function test_sync_writes_audit_entry(): void
    {
        [$user] = $this->setupUserWithRole();

        $permissionId = Permission::query()->where('name', 'products_create')->value('id');

        AuditLog::query()->delete();

        $this->postJson(route('employees.permissions.sync', $user), [
            'permissions' => [$permissionId => 'allow'],
        ])->assertOk();

        $this->assertDatabaseHas('audit_log', [
            'action' => 'PERMISSIONS_UPDATED',
            'affected_table' => 'user_has_permissions',
            'record_id' => $user->id,
        ]);
    }
}
