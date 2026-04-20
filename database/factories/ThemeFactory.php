<?php

namespace Database\Factories;

use App\Models\Assessment;
use App\Models\PriorityAction;
use App\Models\Theme;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class ThemeFactory extends Factory
{
    protected $model = Theme::class;

    public function definition(): array
    {
        return [
            'updated_at' => Carbon::now(),
            'created_at' => Carbon::now(),
            'name' => $this->faker->name(),

            'priority_action_id' => PriorityAction::factory(),
            'assessment_id' => Assessment::factory(),
        ];
    }
}
