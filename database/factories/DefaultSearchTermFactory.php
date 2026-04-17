<?php

namespace Database\Factories;

use App\Models\DefaultSearchTerm;
use App\Models\PriorityAction;
use Illuminate\Database\Eloquent\Factories\Factory;

class DefaultSearchTermFactory extends Factory
{
    protected $model = DefaultSearchTerm::class;

    public function definition(): array
    {
        return [
            'priority_action_id' => PriorityAction::factory(),
            'phrase' => ['en' => fake()->words(3, true)],
        ];
    }
}
