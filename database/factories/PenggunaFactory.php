<?php

namespace Database\Factories;

use App\Models\Role;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Pengguna>
 */
class PenggunaFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            // default penghuni
            'username' => $this->faker->userName(),
            'password' => bcrypt('password'),
            'id_role' => Role::factory()->penghuni(), 
        ];
    }

    public function admin(): static
    {
        return $this->state([
            'id_role' => Role::factory()->admin(),
        ]);
    }

    public function pemilik(): static
    {
        return $this->state( [
            'id_role' => Role::factory()->pemilik(),
        ]);
    }
}
