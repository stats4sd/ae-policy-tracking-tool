<?php

namespace Database\Factories;

use App\Models\Country;
use Illuminate\Database\Eloquent\Factories\Factory;

class CountryFactory extends Factory
{
    protected $model = Country::class;

    public function definition(): array
    {
        return [
            'id' => strtoupper($this->faker->unique()->lexify('???')),
            'name' => $this->faker->country(),
            'iso2' => strtoupper($this->faker->unique()->lexify('??')),
            'un_code' => str_pad((string) $this->faker->numberBetween(1, 999), 3, '0', STR_PAD_LEFT),
        ];
    }
}
