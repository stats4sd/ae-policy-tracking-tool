<?php

namespace Database\Factories;

use App\Models\Assessment;
use App\Models\Jurisdiction;
use App\Models\Language;
use Illuminate\Database\Eloquent\Factories\Factory;

class AssessmentFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Assessment::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'jurisdiction_id' => Jurisdiction::factory(),
            'language_id' => Language::factory(),
        ];
    }
}
