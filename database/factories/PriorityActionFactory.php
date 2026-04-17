<?php

namespace Database\Factories;

use App\Models\PriorityAction;
use App\Models\Recommendation;
use Illuminate\Database\Eloquent\Factories\Factory;

class PriorityActionFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = PriorityAction::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'id' => $this->faker->unique()->bothify('PA-###'),
            'name' => $this->faker->sentence(5),
            'short_name' => $this->faker->words(3, true),
            'recommendation_id' => Recommendation::factory(),
        ];
    }
}
