<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Account>
 */
class AccountFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => \App\Models\User::factory(),
            'name' => $this->faker->words(3, true),
            'type' => $this->faker->randomElement(['checking', 'savings', 'credit_card', 'investment', 'other']),
            'balance' => $this->faker->randomFloat(2, -5000, 50000),
            'currency' => 'BRL',
            'description' => $this->faker->sentence(),
            'is_active' => $this->faker->boolean(90), // 90% chance of being active
        ];
    }
}
