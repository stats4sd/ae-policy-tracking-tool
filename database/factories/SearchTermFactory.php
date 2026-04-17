<?php

namespace Database\Factories;

use App\Models\Assessment;
use App\Models\PriorityAction;
use App\Models\SearchTerm;
use Illuminate\Database\Eloquent\Factories\Factory;

class SearchTermFactory extends Factory
{
    protected $model = SearchTerm::class;

    public function definition(): array
    {
        return [
            'assessment_id' => Assessment::factory(),
            'priority_action_id' => PriorityAction::factory(),
            'phrase' => ['en' => fake()->words(3, true)],
        ];
    }
}
