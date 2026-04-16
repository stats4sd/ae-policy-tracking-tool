<?php

namespace Database\Factories;

use App\Models\Extract;
use App\Models\PolicyDocument;
use Illuminate\Database\Eloquent\Factories\Factory;

class ExtractFactory extends Factory
{
    protected $model = Extract::class;

    public function definition(): array
    {
        $start = fake()->numberBetween(0, 500);

        return [
            'policy_document_id' => PolicyDocument::factory(),
            'page_number' => fake()->numberBetween(1, 100),
            'extract' => fake()->paragraph(),
            'start_offset' => $start,
            'end_offset' => $start + fake()->numberBetween(50, 300),
            'color' => 'lightblue',
            'automatic' => false,
            'verified' => false,
        ];
    }

    public function automatic(): static
    {
        return $this->state(['automatic' => true, 'verified' => false]);
    }

    public function verified(): static
    {
        return $this->state(['verified' => true]);
    }
}
