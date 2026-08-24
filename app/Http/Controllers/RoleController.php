<?php

namespace App\Http\Controllers;

use App\Models\Action;
use App\Models\Permission;
use App\Models\Resource;
use App\Models\Role;
use App\Services\AuditService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class RoleController extends Controller
{
    public function index(): View
    {
        $roles = Role::query()
            ->withCount('users')
            ->withCount('permissions')
            ->orderBy('name')
            ->get();

        return view('modules.roles.index', ['roles' => $roles]);
    }

    public function create(): View
    {
        return view('modules.roles.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100', 'unique:roles,name'],
            'description' => ['nullable', 'string', 'max:255'],
            'is_active' => ['sometimes', 'boolean'],
        ]);

        $validated['is_active'] = $request->boolean('is_active');

        $role = Role::create($validated);

        return redirect()->route('roles.index');
    }

    public function show(Role $role): View
    {
        $role->loadCount('users');

        return view('modules.roles.show', ['role' => $role]);
    }

    public function edit(Role $role): View
    {
        return view('modules.roles.edit', ['role' => $role]);
    }

    public function update(Request $request, Role $role): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100', 'unique:roles,name,'.$role->id],
            'description' => ['nullable', 'string', 'max:255'],
            'is_active' => ['sometimes', 'boolean'],
        ]);

        $role->fill($validated);
        $role->is_active = $request->boolean('is_active');
        $role->save();

        return redirect()->route('roles.index');
    }

    public function toggleActive(Role $role): RedirectResponse
    {
        $role->is_active = ! $role->is_active;

        Model::withoutEvents(fn () => $role->save());

        AuditService::log('TOGGLED', 'roles', $role->id, [
            'is_active' => $role->is_active,
        ]);

        return redirect()->route('roles.index');
    }

    public function destroy(Role $role): RedirectResponse
    {
        $role->delete();

        return redirect()->route('roles.index');
    }

    public function permissions(Role $role): View
    {
        $role->load('permissions');

        $states = [];

        foreach (Permission::query()->pluck('id') as $permissionId) {
            $states[$permissionId] = $role->is_super_admin || $role->permissions->contains('id', $permissionId);
        }

        return view('modules.shared.partials.permissions-modal', [
            'modalTitle' => 'Permisos · '.$role->name,
            'formAction' => route('roles.permissions.sync', $role),
            'matrixMode' => 'ids',
            'matrixStates' => $states,
            'matrixInheritedIds' => [],
            'superToggleChecked' => $role->is_super_admin,
            'lockedMatrix' => $role->is_super_admin,
            'lockedHint' => null,
            'legend' => null,
            'resources' => Resource::query()->with(['permissions.action'])->orderBy('display_name')->get(),
            'actions' => Action::query()->orderBy('id')->get(),
        ]);
    }

    public function syncPermissions(Request $request, Role $role): RedirectResponse|JsonResponse
    {
        $validated = $request->validate([
            'is_super_admin' => ['sometimes', 'boolean'],
            'permissions' => ['nullable', 'array'],
            'permissions.*' => ['integer', 'exists:permissions,id'],
        ]);

        $before = [
            'is_super_admin' => $role->is_super_admin,
            'permissions' => $role->permissions()->orderBy('name')->pluck('name')->all(),
        ];

        $role->is_super_admin = $request->boolean('is_super_admin');
        $role->save();

        $role->permissions()->sync(
            $role->is_super_admin
                ? Permission::query()->pluck('id')->all()
                : ($validated['permissions'] ?? []),
        );

        AuditService::log('PERMISSIONS_UPDATED', 'role_has_permissions', $role->id, [
            'before' => $before,
            'after' => [
                'is_super_admin' => $role->is_super_admin,
                'permissions' => $role->permissions()->orderBy('name')->pluck('name')->all(),
            ],
        ]);

        if ($request->expectsJson()) {
            return response()->json(['success' => true]);
        }

        return redirect()->route('roles.index')
            ->with('success', 'Permisos actualizados correctamente.');
    }
}
