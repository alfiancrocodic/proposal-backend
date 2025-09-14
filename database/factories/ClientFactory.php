<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Client>
 */
class ClientFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'company' => $this->faker->company,
            'location' => $this->faker->city,
            'badanUsaha' => $this->faker->randomElement(['CV', 'PT', 'Lainnya']),
            'picName' => $this->faker->name,
            'position' => $this->faker->jobTitle,
        ];
    }
}
