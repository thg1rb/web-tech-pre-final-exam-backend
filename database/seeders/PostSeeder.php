<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Models\Post;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PostSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // pluck -> pick only needed column and returns as a collection (array)
        $userIds = User::query()->where('role', UserRole::USER)->pluck('id');

        if ($userIds->isEmpty()) return ;

        Post::factory(50)->create([
            // Override `user_id` in PostFactory
            'user_id' => fn() => $userIds->random(),
        ]);
    }
}
