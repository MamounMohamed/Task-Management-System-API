<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{

    public function run(): void
    {
        $users = [
            [
                'name' => 'Test Manager',
                'email' => 'test_manager@example.com',
                'role' => 'manager',
            ],
            [
                'name' => 'Test User',
                'email' => 'test_user@example.com',
                'role' => 'user',
            ],
            [
                'name' => 'Test User2',
                'email' => 'test_user2@example.com',
                'role' => 'user',
            ],
        ];

        foreach ($users as $user) {
            User::updateOrCreate(
                ['email' => $user['email']], // match by email
                [
                    'name' => $user['name'],
                    'role' => $user['role'],
                    'password' => Hash::make('password'),
                ]
            );
        }
    }
}
