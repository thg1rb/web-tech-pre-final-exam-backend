<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Models\User;
use Hash;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // ADMIN
        User::create([
            'name' => 'Admin Account',
            'email' => 'admin@admin.com',
            'password' => Hash::make('password'),
            'role' => UserRole::ADMIN,
        ]);

        // USER
        User::create([
            'name' => 'User Account',
            'email' => 'user@user.com',
            'password' => Hash::make('password'),
            'role' => UserRole::USER,
        ]);
    }
}
