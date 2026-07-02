<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::updateOrCreate(
            [
                'email' => 'admin@sanoh.com',
            ],
            [
                'name'                 => 'Administrator',
                'role'                 => 'Admin',
                'department'           => 'IT',
                'password'             => Hash::make('sanoh123'),
                'must_change_password' => false,
                'signature'            => null,
            ]
        );
    }
}