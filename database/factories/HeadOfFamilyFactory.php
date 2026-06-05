<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\HeadOfFamily>
 */
class HeadOfFamilyFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(), // Auto-create user jika belum ada
            'full_name' => fake()->name(),
            'identity_number' => fake()->numerify('################'),
            'gender' => fake()->randomElement(['male', 'female']),
            'date_of_birth' => fake()->date('Y-m-d', '2000-01-01'),
            'occupation' => fake()->jobTitle(),
            'marital_status' => fake()->randomElement(['single', 'married', 'widow', 'widower']),
            'phone_number' => fake()->numerify('08##########'),
            'file_id' => null, // Optional
        ];
    }
}
