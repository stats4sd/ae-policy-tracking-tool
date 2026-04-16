<?php

namespace Database\Factories;

use App\Models\Assessment;
use App\Models\PriorityAction;
use App\Models\Statement;
use Illuminate\Database\Eloquent\Factories\Factory;

class StatementFactory extends Factory
{
    protected $model = Statement::class;

    public function definition(): array
    {
        return [
            'assessment_id' => Assessment::factory(),
            'priority_action_id' => PriorityAction::factory(),
            'name' => fake()->sentence(),
        ];
    }
}
