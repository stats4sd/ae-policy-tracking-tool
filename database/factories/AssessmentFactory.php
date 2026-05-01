<?php

namespace Database\Factories;

use App\Models\Language;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use App\Models\Assessment;
use App\Models\Country;

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
            'country_id' => Country::factory(),
            'language_id' => Language::factory(),
        ];
    }
}
