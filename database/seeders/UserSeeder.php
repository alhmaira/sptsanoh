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

            [
                'name' => 'General Manager',
                'email' => 'gm@spt.com',
                'password' => Hash::make('password123'),
                'role' => 'GM',
                'department' => 'Management',
            ],

            [
                'name' => 'Manager Purchasing',
                'email' => 'manager.purch@spt.com',
                'password' => Hash::make('password123'),
                'role' => 'Manager Purchasing',
                'department' => 'Purchasing',
            ],

            [
                'name' => 'Leader Purchasing',
                'email' => 'leader.purch@spt.com',
                'password' => Hash::make('password123'),
                'role' => 'Leader Purchasing',
                'department' => 'Purchasing',
            ],

            [
                'name' => 'Manager PPIC',
                'email' => 'manager.ppic@spt.com',
                'password' => Hash::make('password123'),
                'role' => 'Manager PPIC',
                'department' => 'PPIC',
            ],

            [
                'name' => 'SPV PPIC',
                'email' => 'spv.ppic@spt.com',
                'password' => Hash::make('password123'),
                'role' => 'SPV PPIC',
                'department' => 'PPIC',
            ],

            [
                'name' => 'Manager QC',
                'email' => 'manager.qc@spt.com',
                'password' => Hash::make('password123'),
                'role' => 'Manager QC',
                'department' => 'QC',
            ],

            [
                'name' => 'SPV QC',
                'email' => 'spv.qc@spt.com',
                'password' => Hash::make('password123'),
                'role' => 'SPV QC',
                'department' => 'QC',
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