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

        // Question: when running command "php artisan migrate:fresh --seed", 
        // error occurred as below statement tries to insert record to teams table.
        // 
        // I did below checking:
        // 1. check staging database, it does not have "teams" table
        // 2. Assessment model does not specify table name explicitly
        // 3. Assessment model extends Teams model, which has getTable() function to return a string as getModelNameLower() . 's'
        // 4. Suppose new records should be insered into "assessments" table, why it still looks for "teams" table?
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
