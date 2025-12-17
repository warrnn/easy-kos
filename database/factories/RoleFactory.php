<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Role>
 */
class RoleFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'nama' => $this->faker->word,
        ];
    }

    public function admin(): static
    {
        return $this->state([
            'id' => 1,
            'nama' => 'admin',
        ]);
    }

    public function pemilik(): static
    {
        return $this->state([
            'id' => 2,
            'nama' => 'pemilik',
        ]);
    }

    public function penghuni(): static
    {
        return $this->state([
            'id' => 3,
            'nama' => 'penghuni',
        ]);
    }
}
