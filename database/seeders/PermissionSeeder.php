<?php

namespace Database\Seeders;

use App\Models\Action;
use App\Models\Permission;
use App\Models\Resource;
use App\Models\Role;
use Illuminate\Database\Seeder;

class PermissionSeeder extends Seeder
{
    private array $resources = [
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

    private array $actions = [
        ['name' => 'view', 'display_name' => 'Ver'],
        ['name' => 'create', 'display_name' => 'Crear'],
        ['name' => 'edit', 'display_name' => 'Editar'],
        ['name' => 'delete', 'display_name' => 'Eliminar'],
    ];

    public function run(): void
    {
        foreach ($this->resources as $resource) {
            Resource::firstOrCreate(['name' => $resource['name']], $resource);
        }

        foreach ($this->actions as $action) {
            Action::firstOrCreate(['name' => $action['name']], $action);
        }

        $allPermissionIds = [];

        foreach (Resource::all() as $resource) {
            foreach (Action::all() as $action) {
                $permission = Permission::firstOrCreate(
                    [
                        'resource_id' => $resource->id,
                        'action_id' => $action->id,
                    ],
                    [
                        'name' => "{$resource->name}_{$action->name}",
                        'display_name' => "{$action->display_name} {$resource->display_name}",
                    ],
                );

                $allPermissionIds[] = $permission->id;
            }
        }

        $administrator = Role::where('name', 'ADMINISTRATOR')->first();

        if ($administrator) {
            $administrator->permissions()->sync($allPermissionIds);
            $administrator->update(['is_super_admin' => true, 'default_route' => 'dashboard']);
        }

        $cashier = Role::where('name', 'CASHIER')->first();
        if ($cashier) {
            $cashier->update(['default_route' => 'pos']);
        }

        $warehouse = Role::where('name', 'WAREHOUSE')->first();
        if ($warehouse) {
            $warehouse->update(['default_route' => 'inventory.index']);
        }
    }
}
