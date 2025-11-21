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
        
        \DB::table('statements')->insert(array (
            0 => 
            array (
                'id' => 358,
                'type_id' => 3,
                'name' => 'Example Summary Statement',
                'created_at' => '2025-11-21 12:15:11',
                'updated_at' => '2025-11-21 12:15:11',
                'priority_action_id' => '1.1',
                'assessment_id' => 1,
            ),
        ));
        
        
    }
}