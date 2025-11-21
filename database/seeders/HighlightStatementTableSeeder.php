<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class HighlightStatementTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('highlight_statement')->delete();
        
        \DB::table('highlight_statement')->insert(array (
            0 => 
            array (
                'id' => 1,
                'statement_id' => 358,
                'highlight_id' => 1,
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            1 => 
            array (
                'id' => 2,
                'statement_id' => 358,
                'highlight_id' => 2,
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            2 => 
            array (
                'id' => 3,
                'statement_id' => 358,
                'highlight_id' => 3,
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            3 => 
            array (
                'id' => 4,
                'statement_id' => 358,
                'highlight_id' => 4,
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
        ));
        
        
    }
}