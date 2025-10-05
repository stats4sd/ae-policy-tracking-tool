<?php

namespace Database\Seeders;

use App\Models\Assessment;
use App\Models\Country;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use PHPUnit\Framework\Constraint\Count;

class TestSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        $role = \Spatie\Permission\Models\Role::create(['name' => 'admin']);
        $user->assignRole($role);

        // Temp
        $country = Country::create([
            'name' => 'Uganda',
        ]);

        $country2 = Country::create([
            'name' => 'Kenya',
        ]);

        $assessment = Assessment::create([
            'country_id' => $country->id,
            'status' => 'In Progress',
            'finalised_at' => null,
        ]);

        $assessment2 = Assessment::create([
            'country_id' => $country2->id,
            'status' => 'In Progress',
            'finalised_at' => null,
        ]);

        $this->call(StatementSeeder::class);

        $user->assessments()->sync([$assessment->id, $assessment2->id]);


    }
}
