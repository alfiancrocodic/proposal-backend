<?php

namespace Database\Factories;

use App\Models\Client;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Project>
 */
class ProjectFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'client_id' => Client::factory(),
            'name' => $this->faker->words(3, true) . ' Project',
            'analyst' => $this->faker->name,
            'grade' => $this->faker->randomElement(['A', 'B', 'C']),
            'roles' => json_encode([
                ['name' => 'Developer'],
                ['name' => 'Designer']
            ]),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}