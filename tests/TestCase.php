<?php

namespace Tests;

use App\Models\Role;
use App\Models\User;
use Database\Seeders\PermissionSeeder;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(PermissionSeeder::class);
    }

    protected function signIn(array $attributes = [], bool $superAdmin = true): User
    {
        $user = User::factory()->create($attributes);

        if ($superAdmin) {
            $role = Role::firstOrCreate(
                ['name' => 'SUPER_ADMIN'],
                ['is_super_admin' => true, 'is_active' => true]
            );
            $user->roles()->sync([$role->id]);
        }

        $this->actingAs($user);

        return $user;
    }
}
