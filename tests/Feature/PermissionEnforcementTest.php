<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Database\Seeders\PermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PermissionEnforcementTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(PermissionSeeder::class);
    }

    public function test_unauthenticated_user_redirected_to_login(): void
    {
        $this->get('/dashboard')->assertRedirect('/');
    }

    public function test_user_without_permissions_gets_403_on_protected_routes(): void
    {
        $user = $this->signIn([], false);
        $role = Role::create([
            'name' => 'CAJERO_LIMITADO',
            'is_active' => true,
            'is_super_admin' => false,
        ]);
        $user->roles()->sync([$role->id]);

        $this->get('/pos')->assertStatus(403);
        $this->get('/inventario')->assertStatus(403);
        $this->get('/categorias')->assertStatus(403);
        $this->get('/empleados')->assertStatus(403);
        $this->get('/roles')->assertStatus(403);
        $this->get('/caja')->assertStatus(403);
        $this->get('/devoluciones')->assertStatus(403);
        $this->get('/reportes')->assertStatus(403);
        $this->get('/bitacora')->assertStatus(403);
        $this->get('/configuracion')->assertStatus(403);
    }

    public function test_user_with_products_view_can_access_inventory_index(): void
    {
        $user = $this->signIn([], false);
        $role = Role::create([
            'name' => 'ALMACEN_VIEW_ONLY',
            'is_active' => true,
            'is_super_admin' => false,
        ]);
        $user->roles()->sync([$role->id]);
        $permission = Permission::where('name', 'products_view')->first();
        $role->permissions()->sync([$permission->id]);

        $this->get('/inventario')->assertStatus(200);
    }

    public function test_user_without_products_create_cannot_access_create_form(): void
    {
        $user = $this->signIn([], false);
        $role = Role::create([
            'name' => 'ALMACEN_VIEW_ONLY',
            'is_active' => true,
            'is_super_admin' => false,
        ]);
        $user->roles()->sync([$role->id]);
        $permission = Permission::where('name', 'products_view')->first();
        $role->permissions()->sync([$permission->id]);

        $this->get('/inventario/crear')->assertStatus(403);
        $this->post('/inventario', [])->assertStatus(403);
    }

    public function test_user_with_products_create_can_access_create_form(): void
    {
        $user = $this->signIn([], false);
        $role = Role::create([
            'name' => 'ALMACEN_FULL',
            'is_active' => true,
            'is_super_admin' => false,
        ]);
        $user->roles()->sync([$role->id]);
        $permissions = Permission::whereIn('name', ['products_view', 'products_create'])->get();
        $role->permissions()->sync($permissions->pluck('id')->toArray());

        $this->get('/inventario/crear')->assertStatus(200);
    }

    public function test_user_without_categories_edit_cannot_access_edit_form(): void
    {
        $user = $this->signIn([], false);
        $role = Role::create([
            'name' => 'ALMACEN_VIEW_ONLY',
            'is_active' => true,
            'is_super_admin' => false,
        ]);
        $user->roles()->sync([$role->id]);
        $permission = Permission::where('name', 'categories_view')->first();
        $role->permissions()->sync([$permission->id]);

        $category = Category::create([
            'name' => 'Test Category',
            'is_active' => true,
        ]);
        $this->get("/categorias/{$category->id}/editar")->assertStatus(403);
    }

    public function test_user_with_users_edit_can_access_employee_permissions(): void
    {
        $user = $this->signIn([], false);
        $role = Role::create([
            'name' => 'RRHH',
            'is_active' => true,
            'is_super_admin' => false,
        ]);
        $user->roles()->sync([$role->id]);
        $permissions = Permission::whereIn('name', ['users_view', 'users_edit'])->get();
        $role->permissions()->sync($permissions->pluck('id')->toArray());

        $employee = User::factory()->create();
        $this->get("/empleados/{$employee->id}/permisos")->assertStatus(200);
    }

    public function test_super_admin_has_access_to_all_routes(): void
    {
        $this->signIn();

        $response = $this->get('/pos');
        $this->assertEquals(200, $response->getStatusCode(), 'POS route failed: '.$response->getStatusCode());

        $response = $this->get('/inventario');
        $this->assertEquals(200, $response->getStatusCode(), 'Inventario route failed: '.$response->getStatusCode());

        $response = $this->get('/categorias');
        $this->assertEquals(200, $response->getStatusCode(), 'Categorias route failed: '.$response->getStatusCode());

        $response = $this->get('/empleados');
        $this->assertEquals(200, $response->getStatusCode(), 'Empleados route failed: '.$response->getStatusCode());

        $response = $this->get('/roles');
        $this->assertEquals(200, $response->getStatusCode(), 'Roles route failed: '.$response->getStatusCode());

        $response = $this->get('/caja');
        $this->assertEquals(200, $response->getStatusCode(), 'Caja route failed: '.$response->getStatusCode());

        $response = $this->get('/devoluciones');
        $this->assertEquals(200, $response->getStatusCode(), 'Devoluciones route failed: '.$response->getStatusCode());

        $response = $this->get('/reportes');
        $this->assertEquals(200, $response->getStatusCode(), 'Reportes route failed: '.$response->getStatusCode());

        $response = $this->get('/bitacora');
        $this->assertEquals(200, $response->getStatusCode(), 'Bitacora route failed: '.$response->getStatusCode());

        $response = $this->get('/configuracion');
        $this->assertEquals(200, $response->getStatusCode(), 'Configuracion route failed: '.$response->getStatusCode());
    }

    public function test_sidebar_items_filtered_by_permissions(): void
    {
        $user = $this->signIn([], false);
        $role = Role::create([
            'name' => 'CAJERO_SOLO_POS',
            'is_active' => true,
            'is_super_admin' => false,
        ]);
        $user->roles()->sync([$role->id]);
        $permission = Permission::where('name', 'sales_view')->first();
        $role->permissions()->sync([$permission->id]);

        $response = $this->get('/pos');
        $response->assertStatus(200);

        $content = $response->getContent();
        $this->assertStringContainsString('POS', $content);
        $this->assertStringNotContainsString('Inventario', $content);
        $this->assertStringNotContainsString('Empleados', $content);
        $this->assertStringNotContainsString('Roles y Permisos', $content);
    }

    public function test_directive_can_hides_buttons_in_views(): void
    {
        $user = $this->signIn([], false);
        $role = Role::create([
            'name' => 'ALMACEN_VIEW_ONLY',
            'is_active' => true,
            'is_super_admin' => false,
        ]);
        $user->roles()->sync([$role->id]);
        $permission = Permission::where('name', 'products_view')->first();
        $role->permissions()->sync([$permission->id]);

        $response = $this->get('/inventario');
        $response->assertStatus(200);

        $content = $response->getContent();
        $this->assertStringContainsString('Inventario', $content);
        $this->assertStringNotContainsString('Nuevo Producto', $content);
    }

    public function test_user_with_deny_permission_cannot_access_even_if_role_allows(): void
    {
        $user = $this->signIn([], false);
        $role = Role::create([
            'name' => 'ALMACEN_FULL',
            'is_active' => true,
            'is_super_admin' => false,
        ]);
        $user->roles()->sync([$role->id]);
        $permissions = Permission::whereIn('name', ['products_view', 'products_create', 'products_edit', 'products_delete'])->get();
        $role->permissions()->sync($permissions->pluck('id')->toArray());

        $denyPermission = Permission::where('name', 'products_create')->first();
        $user->permissions()->sync([
            $denyPermission->id => ['type' => 'deny'],
        ]);

        $this->get('/inventario/crear')->assertStatus(403);
        $this->post('/inventario', [])->assertStatus(403);
    }
}
