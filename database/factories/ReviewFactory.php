<?php

namespace Database\Factories;

use App\Models\Kos;
use App\Models\Pengguna;
use App\Models\Review;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Review>
 */
class ReviewFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    protected $model = Review::class;

    public function definition(): array
    {
        return [
            'isi' => $this->faker->sentence(10),
            'tanggal_review' => $this->faker->dateTimeBetween('-1 month', 'now'), // Random tanggal sebulan terakhir
            'id_pengguna' => Pengguna::factory(),
            'id_kos' => Kos::factory(),
        ];
    }
}
