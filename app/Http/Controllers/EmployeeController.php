<?php

namespace App\Http\Controllers;

use App\Models\Action;
use App\Models\Permission;
use App\Models\Resource;
use App\Models\Role;
use App\Models\User;
use App\Services\AuditService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class EmployeeController extends Controller
{
    public function index(Request $request): View
    {
        $perPage = $request->integer('per_page', 10);

        $employees = User::query()
            ->with('roles')
            ->withCount('permissions')
            ->orderBy('full_name')
            ->paginate($perPage)
            ->withQueryString();

        return view('modules.employees.index', ['employees' => $employees]);
    }

    public function create(): View
    {
        $roles = Role::query()->orderBy('name')->get();

        return view('modules.employees.create', ['roles' => $roles]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'full_name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'string', 'max:50', 'email', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8'],
            'role_id' => ['required', 'exists:roles,id'],
            'phone' => ['nullable', 'string', 'max:20'],
            'address' => ['nullable', 'string', 'max:255'],
            'DUI' => ['nullable', 'string', 'max:10', 'unique:users,DUI'],
            'birthday' => ['nullable', 'date', 'before:today'],
            'is_active' => ['sometimes', 'boolean'],
        ]);

        $roleId = $validated['role_id'];
        unset($validated['role_id']);

        $user = User::create($validated + ['is_active' => $request->boolean('is_active')]);
        $user->roles()->sync($roleId);

        return redirect()->route('employees.index')
            ->with('success', 'Empleado creado correctamente.');
    }

    public function show(User $employee): View
    {
        $employee->load('roles');

        return view('modules.employees.show', ['employee' => $employee]);
    }

    public function edit(User $employee): View
    {
        $roles = Role::query()->orderBy('name')->get();
        $employee->load('roles');

        return view('modules.employees.edit', ['employee' => $employee, 'roles' => $roles]);
    }

    public function update(Request $request, User $employee): RedirectResponse
    {
        $validated = $request->validate([
            'full_name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'string', 'max:50', 'email', 'unique:users,email,'.$employee->id],
            'password' => ['nullable', 'string', 'min:8'],
            'role_id' => ['required', 'exists:roles,id'],
            'phone' => ['nullable', 'string', 'max:20'],
            'address' => ['nullable', 'string', 'max:255'],
            'DUI' => ['nullable', 'string', 'max:10', 'unique:users,DUI,'.$employee->id],
            'birthday' => ['nullable', 'date', 'before:today'],
            'is_active' => ['sometimes', 'boolean'],
        ]);

        $roleId = $validated['role_id'];
        unset($validated['role_id']);

        if (empty($validated['password'])) {
            unset($validated['password']);
        }

        $employee->fill($validated);
        $employee->is_active = $request->boolean('is_active');
        $employee->save();
        $employee->roles()->sync($roleId);

        return redirect()->route('employees.index')
            ->with('success', 'Empleado actualizado correctamente.');
    }

    public function toggleActive(User $employee): RedirectResponse
    {
        $employee->is_active = ! $employee->is_active;

        Model::withoutEvents(fn () => $employee->save());

        AuditService::log('TOGGLED', 'users', $employee->id, [
            'is_active' => $employee->is_active,
        ]);

        return redirect()->route('employees.index')
            ->with('success', $employee->is_active ? 'Empleado activado correctamente.' : 'Empleado deshabilitado correctamente.');
    }

    public function destroy(User $employee): RedirectResponse
    {
        $employee->delete();

        return redirect()->route('employees.index')
            ->with('success', 'Empleado eliminado correctamente.');
    }

    public function permissions(User $employee): View
    {
        $employee->load(['roles.permissions', 'permissions']);
        $role = $employee->roles->first();

        $effectiveIds = $employee->effectivePermissionIds();
        $states = [];

        foreach (Permission::query()->pluck('id') as $permissionId) {
            $states[$permissionId] = in_array($permissionId, $effectiveIds, true);
        }

        return view('modules.shared.partials.permissions-modal', [
            'modalTitle' => 'Permisos · '.$employee->full_name,
            'formAction' => route('employees.permissions.sync', $employee),
            'matrixMode' => 'states',
            'matrixStates' => $states,
            'matrixInheritedIds' => $role ? $role->permissions->pluck('id')->all() : [],
            'superToggleChecked' => null,
            'lockedMatrix' => (bool) ($role?->is_super_admin),
            'lockedHint' => $role?->is_super_admin
                ? 'Su rol '.$role->name.' es super administrador: este usuario tiene acceso total.'
                : null,
            'legend' => 'Desmarcar un permiso que hereda de su rol lo bloqueará solo para este usuario.',
            'resources' => Resource::query()->with(['permissions.action'])->orderBy('display_name')->get(),
            'actions' => Action::query()->orderBy('id')->get(),
        ]);
    }

    public function syncPermissions(Request $request, User $employee): RedirectResponse|JsonResponse
    {
        $validated = $request->validate([
            'permissions' => ['nullable', 'array'],
            'permissions.*' => ['string', 'in:allow,deny,inherit'],
        ]);

        $before = $this->overrideSnapshot($employee);

        $role = $employee->roles->first();
        $rolePermissionIds = $role ? $role->permissions()->pluck('permissions.id')->all() : [];
        $validIds = Permission::query()->pluck('id')->all();

        $rows = [];

        foreach ($validated['permissions'] ?? [] as $permissionId => $state) {
            $permissionId = (int) $permissionId;

            if (! in_array($permissionId, $validIds, true)) {
                continue;
            }

            $roleHas = in_array($permissionId, $rolePermissionIds, true);

            if ($state === 'allow' && ! $roleHas) {
                $rows[$permissionId] = ['type' => 'grant'];
            } elseif ($state === 'deny' && $roleHas) {
                $rows[$permissionId] = ['type' => 'deny'];
            }
        }

        $employee->permissions()->sync($rows);

        AuditService::log('PERMISSIONS_UPDATED', 'user_has_permissions', $employee->id, [
            'before' => $before,
            'after' => $this->overrideSnapshot($employee->fresh()),
        ]);

        if ($request->expectsJson()) {
            return response()->json(['success' => true]);
        }

        return redirect()->route('employees.index')
            ->with('success', 'Permisos actualizados correctamente.');
    }

    private function overrideSnapshot(User $employee): array
    {
        return [
            'grants' => $employee->permissions()
                ->wherePivot('type', 'grant')
                ->orderBy('name')
                ->pluck('name')
                ->all(),
            'denies' => $employee->permissions()
                ->wherePivot('type', 'deny')
                ->orderBy('name')
                ->pluck('name')
                ->all(),
        ];
    }
}
