<?php

namespace App\Http\Controllers;

use App\Models\Role;
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

        Role::create($validated);

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
        $role->save();

        return redirect()->route('roles.index');
    }

    public function destroy(Role $role): RedirectResponse
    {
        $role->delete();

        return redirect()->route('roles.index');
    }
}
