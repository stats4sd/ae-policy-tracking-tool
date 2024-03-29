<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // \App\Models\User::factory(10)->create();

        // \App\Models\User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);
        $this->call(AePrinciplesTableSeeder::class);
        $this->call(RecommendationsTableSeeder::class);
        $this->call(AePrincipleRecommendationTableSeeder::class);
        $this->call(PriorityActionsTableSeeder::class);
        $this->call(TypesTableSeeder::class);
        $this->call(TestSeeder::class);
    }
}
