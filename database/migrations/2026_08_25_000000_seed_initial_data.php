<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        if (DB::table('resources')->exists()) {
            return;
        }

        $now = now();

        // ── Resources ────────────────────────────────────────────────
        $resources = [
            ['name' => 'dashboard', 'display_name' => 'Dashboard'],
            ['name' => 'users', 'display_name' => 'Empleados'],
            ['name' => 'roles', 'display_name' => 'Roles y Permisos'],
            ['name' => 'categories', 'display_name' => 'Categorías'],
            ['name' => 'products', 'display_name' => 'Inventario'],
            ['name' => 'sales', 'display_name' => 'Ventas'],
            ['name' => 'cash_registers', 'display_name' => 'Cajas Registradoras'],
            ['name' => 'returns', 'display_name' => 'Devoluciones'],
            ['name' => 'customers', 'display_name' => 'Clientes'],
            ['name' => 'reports', 'display_name' => 'Reportes'],
            ['name' => 'settings', 'display_name' => 'Configuración'],
            ['name' => 'audit_log', 'display_name' => 'Bitácora'],
        ];

        foreach ($resources as $resource) {
            DB::table('resources')->insert(
                array_merge($resource, [
                    'is_active' => true,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]),
            );
        }

        // ── Actions ──────────────────────────────────────────────────
        $actions = [
            ['name' => 'view', 'display_name' => 'Ver'],
            ['name' => 'create', 'display_name' => 'Crear'],
            ['name' => 'edit', 'display_name' => 'Editar'],
            ['name' => 'delete', 'display_name' => 'Eliminar'],
        ];

        foreach ($actions as $action) {
            DB::table('actions')->insert(
                array_merge($action, ['created_at' => $now]),
            );
        }

        // ── Permissions (resources × actions) ────────────────────────
        $resourceRows = DB::table('resources')->select('id', 'name', 'display_name')->get();
        $actionRows = DB::table('actions')->select('id', 'name', 'display_name')->get();

        $allPermissionIds = [];

        foreach ($resourceRows as $resource) {
            foreach ($actionRows as $action) {
                $permissionId = DB::table('permissions')->insertGetId([
                    'resource_id' => $resource->id,
                    'action_id' => $action->id,
                    'name' => "{$resource->name}_{$action->name}",
                    'display_name' => "{$action->display_name} {$resource->display_name}",
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);

                $allPermissionIds[] = $permissionId;
            }
        }

        // ── Helper: permission IDs by name ────────────────────────────
        $permIds = DB::table('permissions')
            ->pluck('id', 'name')
            ->toArray();

        // ── Roles ────────────────────────────────────────────────────
        $adminRoleId = DB::table('roles')->insertGetId([
            'name' => 'ADMINISTRATOR',
            'description' => 'Acceso total al sistema',
            'is_active' => true,
            'is_super_admin' => true,
            'default_route' => 'dashboard',
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        $cashierRoleId = DB::table('roles')->insertGetId([
            'name' => 'CASHIER',
            'description' => 'Opera el punto de venta y cajas registradoras',
            'is_active' => true,
            'is_super_admin' => false,
            'default_route' => 'pos',
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        $warehouseRoleId = DB::table('roles')->insertGetId([
            'name' => 'WAREHOUSE',
            'description' => 'Gestiona inventario y categorías',
            'is_active' => true,
            'is_super_admin' => false,
            'default_route' => 'inventory.index',
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        // ── Role ↔ Permissions ───────────────────────────────────────

        // ADMINISTRATOR → all permissions
        $adminPerms = array_map(
            fn (int $id) => ['role_id' => $adminRoleId, 'permission_id' => $id, 'created_at' => $now],
            $allPermissionIds,
        );

        // CASHIER → POS-focused permissions
        $cashierPermNames = [
            'dashboard_view',
            'sales_view',
            'cash_registers_view', 'cash_registers_edit',
            'customers_view', 'customers_create',
            'returns_view', 'returns_create',
            'products_view',
        ];
        $cashierPerms = array_map(
            fn (string $name) => ['role_id' => $cashierRoleId, 'permission_id' => $permIds[$name], 'created_at' => $now],
            array_filter($cashierPermNames, fn (string $name) => isset($permIds[$name])),
        );

        // WAREHOUSE → inventory-focused permissions
        $warehousePermNames = [
            'dashboard_view',
            'products_view', 'products_create', 'products_edit', 'products_delete',
            'categories_view', 'categories_create', 'categories_edit', 'categories_delete',
            'returns_view',
        ];
        $warehousePerms = array_map(
            fn (string $name) => ['role_id' => $warehouseRoleId, 'permission_id' => $permIds[$name], 'created_at' => $now],
            array_filter($warehousePermNames, fn (string $name) => isset($permIds[$name])),
        );

        DB::table('role_has_permissions')->insert(
            array_merge($adminPerms, $cashierPerms, $warehousePerms),
        );

        // ── Admin user ───────────────────────────────────────────────
        $adminUserId = DB::table('users')->insertGetId([
            'full_name' => 'BRIAN JOSUE CHAVEZ RECINOS',
            'email' => 'brian@moderatio.com',
            'password' => bcrypt('1234'),
            'is_active' => true,
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        DB::table('user_has_roles')->insert([
            'user_id' => $adminUserId,
            'role_id' => $adminRoleId,
            'created_at' => $now,
        ]);
    }

    public function down(): void
    {
        DB::table('user_has_roles')->delete();
        DB::table('users')->where('email', 'admin@glendastore.com')->delete();
        DB::table('role_has_permissions')->delete();
        DB::table('roles')->whereIn('name', ['ADMINISTRATOR', 'CASHIER', 'WAREHOUSE'])->delete();
        DB::table('permissions')->delete();
        DB::table('actions')->delete();
        DB::table('resources')->delete();
    }
};
