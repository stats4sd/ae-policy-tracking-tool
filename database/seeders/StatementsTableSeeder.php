<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class StatementsTableSeeder extends Seeder
{
    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {

        \DB::table('statements')->delete();

        \DB::table('statements')->insert([
            0 => [
                'id' => 358,
                'name' => 'Example Summary Statement',
                'created_at' => '2025-11-21 12:15:11',
                'updated_at' => '2025-11-21 12:15:11',
                'priority_action_id' => '1.1',
                'assessment_id' => 1,
            ],
        ]);

    }
}
