<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $users = [

            [
                'name' => 'Admin',
                'email' => 'admin@spt.com',
                'password' => Hash::make('password123'),
                'role' => 'Admin',
                'department' => 'IT',
            ],


        ];

        foreach ($users as $user) {

            User::updateOrCreate(
                ['email' => $user['email']],
                [
                    'name' => $user['name'],
                    'password' => $user['password'],
                    'role' => $user['role'],
                    'department' => $user['department'],
                    'signature' => null,
                    'must_change_password' => true,
                ]
            );

        }
    }
}