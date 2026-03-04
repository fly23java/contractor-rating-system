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
        if (!User::where('email', 'contractor@example.com')->exists()) {
            User::factory()->create([
                'name' => 'Contractor',
                'email' => 'contractor@example.com',
                'role' => 'contractor',
                'password' => bcrypt('password'),
            ]);
        }

        // Supervisor
        if (!User::where('email', 'supervisor@example.com')->exists()) {
            User::factory()->create([
                'name' => 'Supervisor',
                'email' => 'supervisor@example.com',
                'role' => 'supervisor',
                'password' => bcrypt('password'),
            ]);
        }

        // Owner
        if (!User::where('email', 'owner@example.com')->exists()) {
            User::factory()->create([
                'name' => 'Owner',
                'email' => 'owner@example.com',
                'role' => 'owner',
                'password' => bcrypt('password'),
            ]);
        }

        // Run weights update
        $this->call(UpdateTenderWeightsSeeder::class);
    }
}
