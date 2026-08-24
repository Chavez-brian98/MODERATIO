<?php

namespace Tests\Feature;

use App\Models\Action;
use App\Models\Permission;
use App\Models\Resource;
use App\Models\Role;
use Database\Seeders\PermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RolePermissionTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->signIn();
    }

    private function seedPermissions(): Role
    {
        $role = Role::create(['name' => 'ADMINISTRATOR', 'description' => 'Full access']);

        $this->seed(PermissionSeeder::class);
        $role->refresh();

        return $role;
    }

    public function test_permission_seeder_creates_expected_counts(): void
    {
        $this->seedPermissions();

        $this->assertSame(11, Resource::count());
        $this->assertSame(4, Action::count());
        $this->assertSame(44, Permission::count());
    }

    public function test_administrator_gets_all_permissions_and_super_admin_flag(): void
    {
        $role = $this->seedPermissions();

        $this->assertTrue($role->is_super_admin);
        $this->assertSame(44, $role->permissions()->count());
    }

    public function test_permissions_can_be_synced_to_a_role(): void
    {
        $role = $this->seedPermissions();
        $role->permissions()->sync([]);
        $role->update(['is_super_admin' => false]);

        $ids = Permission::query()->whereIn('name', ['products_view', 'products_create'])->pluck('id')->all();

        $response = $this->post("/roles/{$role->id}/permisos", [
            'permissions' => $ids,
            'is_super_admin' => '0',
        ], ['HTTP_ACCEPT' => 'application/json']);

        $response->assertOk()->assertJson(['success' => true]);

        foreach ($ids as $id) {
            $this->assertDatabaseHas('role_has_permissions', [
                'role_id' => $role->id,
                'permission_id' => $id,
            ]);
        }

        $this->assertFalse($role->fresh()->is_super_admin);
    }

    public function test_super_admin_flag_can_be_toggled(): void
    {
        $role = $this->seedPermissions();

        $this->post("/roles/{$role->id}/permisos", [
            'is_super_admin' => '1',
        ])->assertRedirect('/roles');

        $this->assertTrue($role->fresh()->is_super_admin);

        $this->post("/roles/{$role->id}/permisos", [
            'is_super_admin' => '0',
        ])->assertRedirect('/roles');

        $this->assertFalse($role->fresh()->is_super_admin);
    }

    public function test_super_admin_flag_syncs_all_permissions_even_without_checks(): void
    {
        $role = $this->seedPermissions();
        $role->permissions()->sync([]);
        $role->update(['is_super_admin' => false]);

        $this->post("/roles/{$role->id}/permisos", [
            'is_super_admin' => '1',
        ], ['HTTP_ACCEPT' => 'application/json'])->assertOk();

        $this->assertSame(44, $role->fresh()->permissions()->count());
        $this->assertTrue($role->fresh()->is_super_admin);
    }

    public function test_permissions_modal_renders_with_matrix(): void
    {
        $role = $this->seedPermissions();

        $this->get("/roles/{$role->id}/permisos")
            ->assertOk()
            ->assertSee('Super administrador')
            ->assertSee('Empleados')
            ->assertSee('Inventario');
    }
}
