<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $administrator = Role::firstOrCreate(
            ['name' => 'ADMINISTRATOR'],
            ['description' => 'Full system access'],
        );

        Role::firstOrCreate(
            ['name' => 'CASHIER'],
            ['description' => 'Can operate the POS and cash registers'],
        );

        Role::firstOrCreate(
            ['name' => 'WAREHOUSE'],
            ['description' => 'Manages inventory'],
        );

        User::firstOrCreate(
            ['email' => 'testuser@test.com'],
            [
                'role_id' => $administrator->id,
                'full_name' => 'Test User',
                'password' => bcrypt('password'),
                'is_active' => true,
            ],
        );

        $this->call(DemoDataSeeder::class);
    }
}
