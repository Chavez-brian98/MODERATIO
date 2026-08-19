<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Services\AuditService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class RoleController extends Controller
{
    public function index(): View
    {
        $roles = Role::query()
            ->withCount('users')
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

        AuditService::log('CREATED', 'roles', $role->id, $validated);

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

        $before = $role->toArray();

        $role->fill($validated);
        $role->is_active = $request->boolean('is_active');
        $role->save();

        AuditService::log('UPDATED', 'roles', $role->id, [
            'before' => $before,
            'after' => $role->fresh()->toArray(),
        ]);

        return redirect()->route('roles.index');
    }

    public function toggleActive(Role $role): RedirectResponse
    {
        $role->is_active = ! $role->is_active;
        $role->save();

        AuditService::log('TOGGLED', 'roles', $role->id, [
            'is_active' => $role->is_active,
        ]);

        return redirect()->route('roles.index');
    }

    public function destroy(Role $role): RedirectResponse
    {
        $data = $role->toArray();

        $role->delete();

        AuditService::log('DELETED', 'roles', $data['id'], $data);

        return redirect()->route('roles.index');
    }
}
