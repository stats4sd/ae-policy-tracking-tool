<?php

namespace Database\Factories;

use App\Models\Assessment;
use App\Models\PolicyDocument;
use Illuminate\Database\Eloquent\Factories\Factory;

class PolicyDocumentFactory extends Factory
{
    protected $model = PolicyDocument::class;

    public function definition(): array
    {
        return [
            'assessment_id' => Assessment::factory(),
            'name' => fake()->sentence(6),
            'short_title' => fake()->words(3, true),
            'comments' => fake()->optional()->paragraph(),
            'year' => fake()->year(),
            'end_year' => null,
            'language_id' => null,
        ];
    }
}
