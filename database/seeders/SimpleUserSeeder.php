<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SimpleUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Add random contractors
        \App\Models\User::create([
            'name' => ' contracting co 1',
            'email' => 'contractor1@example.com',
            'password' => \Illuminate\Support\Facades\Hash::make('password'),
            'role' => 'contractor'
        ]);

        \App\Models\User::create([
            'name' => 'Elite Builders',
            'email' => 'contractor2@example.com',
            'password' => \Illuminate\Support\Facades\Hash::make('password'),
            'role' => 'contractor'
        ]);

        // Add an owner
        \App\Models\User::create([
            'name' => 'Ministry of Health',
            'email' => 'owner2@example.com',
            'password' => \Illuminate\Support\Facades\Hash::make('password'),
            'role' => 'owner'
        ]);

        // Add another supervisor
        \App\Models\User::create([
            'name' => 'Senior Supervisor',
            'email' => 'supervisor2@example.com',
            'password' => \Illuminate\Support\Facades\Hash::make('password'),
            'role' => 'supervisor'
        ]);
    }
}
