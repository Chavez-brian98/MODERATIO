<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * Initial data (resources, actions, permissions, ADMINISTRATOR role and admin user)
     * is seeded by the migration: 2026_08_25_000000_seed_initial_data.
     */
    public function run(): void
    {
        $this->call(DemoDataSeeder::class);
    }
}
