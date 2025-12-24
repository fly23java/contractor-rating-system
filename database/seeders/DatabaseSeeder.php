<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Contractor
        User::factory()->create([
            'name' => 'Contractor',
            'email' => 'contractor@example.com',
            'role' => 'contractor',
            'password' => bcrypt('password'),
        ]);

        // Supervisor
        User::factory()->create([
            'name' => 'Supervisor',
            'email' => 'supervisor@example.com',
            'role' => 'supervisor',
            'password' => bcrypt('password'),
        ]);

        // Owner
        User::factory()->create([
            'name' => 'Owner',
            'email' => 'owner@example.com',
            'role' => 'owner',
            'password' => bcrypt('password'),
        ]);
    }
}
