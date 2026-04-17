<?php

namespace Database\Factories;

use App\Models\Recommendation;
use Illuminate\Database\Eloquent\Factories\Factory;

class RecommendationFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Recommendation::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'code' => $this->faker->unique()->bothify('R##'),
            'name' => $this->faker->sentence(4),
            'short_title' => $this->faker->words(3, true),
        ];
    }
}
