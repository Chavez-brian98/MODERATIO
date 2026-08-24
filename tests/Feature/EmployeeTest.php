<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EmployeeTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->signIn();
    }

    private function role(): Role
    {
        return Role::create(['name' => 'CASHIER', 'description' => 'Can operate the POS']);
    }

    public function test_employee_can_be_created(): void
    {
        $role = $this->role();

        $this->post('/empleados', [
            'full_name' => 'Ana María López',
            'email' => 'ana.lopez@test.com',
            'password' => 'password123',
            'role_id' => $role->id,
            'is_active' => '1',
        ])->assertRedirect('/empleados');

        $userId = User::where('email', 'ana.lopez@test.com')->first()->id;

        $this->assertDatabaseHas('users', [
            'full_name' => 'Ana María López',
            'email' => 'ana.lopez@test.com',
            'is_active' => true,
        ]);

        $this->assertDatabaseHas('user_has_roles', [
            'user_id' => $userId,
            'role_id' => $role->id,
        ]);

        $this->assertNotEquals('password123', User::where('email', 'ana.lopez@test.com')->first()->password);
    }

    public function test_employee_requires_unique_email(): void
    {
        $role = $this->role();
        $user = User::create([
            'full_name' => 'Existing User',
            'email' => 'ana.lopez@test.com',
            'password' => 'password123',
        ]);
        $user->roles()->sync($role->id);

        $this->post('/empleados', [
            'full_name' => 'Another User',
            'email' => 'ana.lopez@test.com',
            'password' => 'password123',
            'role_id' => $role->id,
        ])->assertSessionHasErrors('email');
    }

    public function test_employee_can_be_updated(): void
    {
        $role = $this->role();
        $employee = User::create([
            'full_name' => 'Ana López',
            'email' => 'ana.lopez@test.com',
            'password' => 'password123',
            'is_active' => true,
        ]);
        $employee->roles()->sync($role->id);

        $this->put("/empleados/{$employee->id}", [
            'full_name' => 'Ana María López',
            'email' => 'ana.lopez@test.com',
            'password' => '',
            'role_id' => $role->id,
            'is_active' => '1',
        ])->assertRedirect('/empleados');

        $this->assertDatabaseHas('users', [
            'id' => $employee->id,
            'full_name' => 'Ana María López',
        ]);
    }

    public function test_employee_active_state_can_be_toggled(): void
    {
        $role = $this->role();
        $employee = User::create([
            'full_name' => 'Ana López',
            'email' => 'ana.lopez@test.com',
            'password' => 'password123',
            'is_active' => true,
        ]);
        $employee->roles()->sync($role->id);

        $this->patch("/empleados/{$employee->id}/estado")
            ->assertRedirect('/empleados');

        $this->assertDatabaseHas('users', ['id' => $employee->id, 'is_active' => false]);
    }

    public function test_employee_can_be_deleted(): void
    {
        $role = $this->role();
        $employee = User::create([
            'full_name' => 'Ana López',
            'email' => 'ana.lopez@test.com',
            'password' => 'password123',
        ]);
        $employee->roles()->sync($role->id);

        $this->delete("/empleados/{$employee->id}")
            ->assertRedirect('/empleados');

        $this->assertDatabaseMissing('users', ['id' => $employee->id]);
    }

    public function test_employee_pages_render(): void
    {
        $role = $this->role();
        $employee = User::create([
            'full_name' => 'Ana López',
            'email' => 'ana.lopez@test.com',
            'password' => 'password123',
        ]);
        $employee->roles()->sync($role->id);

        $this->get('/empleados')->assertOk()->assertSee('Empleados');
        $this->get('/empleados/crear')->assertOk()->assertSee('Nuevo empleado');
        $this->get("/empleados/{$employee->id}/editar")->assertOk()->assertSee('Editar empleado');
        $this->get("/empleados/{$employee->id}")->assertOk()->assertSee($employee->full_name);
    }
}
