<?php

namespace Database\Factories;

use App\Models\Kos;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Kos>
 */
class KosFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */

    protected $model = Kos::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->company,
            'alamat' => $this->faker->address,
            'id_pengguna' => 1,
        ];
    }
}
