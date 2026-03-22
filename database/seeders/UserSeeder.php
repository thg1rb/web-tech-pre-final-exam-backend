<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Models\User;
use Hash;
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
            'username' => 'admin_account',
            'email' => 'admin@admin.com',
            'password' => Hash::make('password'),
            'role' => UserRole::ADMIN,
        ]);

        // USER
        User::create([
            'username' => 'user_account',
            'email' => 'user@user.com',
            'password' => Hash::make('password'),
            'role' => UserRole::USER,
        ]);
    }
}
