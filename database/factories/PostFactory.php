<?php

namespace Database\Factories;

use App\Enums\UserRole;
use App\Models\Post;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Post>
 */
class PostFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => function () {
                $user = User::query()->where('role', UserRole::USER)->inRandomOrder()->first();
                return $user ? $user->id : User::create([ // Exist -> use ID of exist user, otherwise create new user and return ID of new user
                    'name' => 'Test User',
                    'email' => fake()->unique()->safeEmail(),
                    'password' => bcrypt('password'),
                    'role' => UserRole::USER,
                ])->id;
            },

            'title' => fake()->sentence(6),
            'content' => fake()->paragraphs(3, true),
            'image_path' => 'posts/' . fake()->uuid() . '.jpg',
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
