<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\User;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Task>
 */
class TaskFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => fake()->words(3, true),
            'description' => fake()->paragraph(),
            'status' => fake()->randomElement(['pending', 'cancelled', 'completed']),
            'due_date' => fake()->dateTimeBetween('-1 month', 'now'),
            'assignee_id' => fake()->randomElement(User::where('role', 'user')->pluck('id')->toArray()),
            'creator_id' => fake()->randomElement(User::where('role', 'manager')->pluck('id')->toArray()),
        ];
    }
}
