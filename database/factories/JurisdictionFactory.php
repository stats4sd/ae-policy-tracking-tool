<?php

namespace Database\Factories;

use App\Enums\JurisdictionType;
use App\Models\Jurisdiction;
use Illuminate\Database\Eloquent\Factories\Factory;

class JurisdictionFactory extends Factory
{
    protected $model = Jurisdiction::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->country(),
            'type' => JurisdictionType::National,
            'country_id' => null,
        ];
    }
}
