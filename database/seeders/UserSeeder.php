<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::factory()->create([
            'name' => 'Test Manager',
            'email' => 'test_manager@example.com',
            'role' => 'manager',
        ]);

        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test_user@example.com',
            'role' => 'user',
        ]);

        User::factory()->create([
            'name' => 'Test User2',
            'email' => 'test_user2@example.com',
            'role' => 'user',
        ]);
    }
}
