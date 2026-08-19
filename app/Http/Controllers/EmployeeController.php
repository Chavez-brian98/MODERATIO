<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\User;
use App\Services\AuditService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class EmployeeController extends Controller
{
    public function index(): View
    {
        $employees = User::query()
            ->with('roles')
            ->orderBy('full_name')
            ->get();

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
            'address' => ['nullable', 'string', 'max:255'],
            'DUI' => ['nullable', 'string', 'max:10', 'unique:users,DUI'],
            'birthday' => ['nullable', 'date', 'before:today'],
            'is_active' => ['sometimes', 'boolean'],
        ]);

        $roleId = $validated['role_id'];
        unset($validated['role_id']);

        $user = User::create($validated + ['is_active' => $request->boolean('is_active')]);
        $user->roles()->sync($roleId);

        AuditService::log('CREATED', 'users', $user->id, [
            'full_name' => $user->full_name,
            'email' => $user->email,
            'role_id' => $roleId,
            'is_active' => $user->is_active,
        ]);

        return redirect()->route('employees.index');
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

        $before = $employee->toArray();

        $employee->fill($validated);
        $employee->is_active = $request->boolean('is_active');
        $employee->save();
        $employee->roles()->sync($roleId);

        AuditService::log('UPDATED', 'users', $employee->id, [
            'before' => $before,
            'after' => $employee->fresh()->toArray(),
        ]);

        return redirect()->route('employees.index');
    }

    public function toggleActive(User $employee): RedirectResponse
    {
        $employee->is_active = ! $employee->is_active;
        $employee->save();

        AuditService::log('TOGGLED', 'users', $employee->id, [
            'is_active' => $employee->is_active,
        ]);

        return redirect()->route('employees.index');
    }

    public function destroy(User $employee): RedirectResponse
    {
        $data = $employee->toArray();

        $employee->delete();

        AuditService::log('DELETED', 'users', $data['id'], $data);

        return redirect()->route('employees.index');
    }
}
