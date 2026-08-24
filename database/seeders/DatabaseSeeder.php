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

        $user = User::firstOrCreate(
            ['email' => 'brian@modeartio.com'],
            [
                'full_name' => 'BRIAN JOSUE CHAVEZ RECINOS',
                'password' => bcrypt('1234'),
                'is_active' => true,
            ],
        );

        $user->roles()->syncWithoutDetaching([$administrator->id]);

        $this->call(PermissionSeeder::class);
        $this->call(DemoDataSeeder::class);
    }
}
