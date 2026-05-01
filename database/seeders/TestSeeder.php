<?php

namespace Database\Seeders;

use App\Models\Assessment;
use App\Models\Country;
use App\Models\Jurisdiction;
use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

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

        $role = Role::create(['name' => 'admin']);
        $user->assignRole($role);

        $country = Country::create(['id' => 'UGA', 'name' => 'Uganda', 'iso2' => 'UG', 'un_code' => '800']);
        $country2 = Country::create(['id' => 'KEN', 'name' => 'Kenya', 'iso2' => 'KE', 'un_code' => '404']);

        $jurisdiction = Jurisdiction::create([
            'name' => 'Uganda',
            'type' => 'National',
            'country_id' => $country->id,
        ]);

        $jurisdiction2 = Jurisdiction::create([
            'name' => 'Kenya',
            'type' => 'National',
            'country_id' => $country2->id,
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
            'jurisdiction_id' => $jurisdiction->id,
            'status' => 'In Progress',
            'finalised_at' => null,
            'title' => 'Test Assessment 1',
            'language_id' => 'en',
        ]);

        $assessment2 = Assessment::create([
            'jurisdiction_id' => $jurisdiction2->id,
            'status' => 'In Progress',
            'finalised_at' => null,
            'title' => 'Test Assessment 2',
            'language_id' => 'fr',
        ]);

        $this->call(StatementSeeder::class);

        $user->assessments()->sync([$assessment->id, $assessment2->id]);

    }
}
