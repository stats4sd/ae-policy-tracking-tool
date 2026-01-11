<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DefaultSearchTermsSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {


        \DB::table('default_search_terms')->delete();

        \DB::table('default_search_terms')->insert(array (
            0 =>
            array (
                'id' => 13,
                'priority_action_id' => '1.1',
                'phrase' => 'local context',
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            1 =>
            array (
                'id' => 14,
                'priority_action_id' => '1.1',
                'phrase' => 'agroecology',
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            2 =>
            array (
                'id' => 15,
                'priority_action_id' => '1.1',
                'phrase' => 'resilience',
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            3 =>
            array (
                'id' => 16,
                'priority_action_id' => '1.2',
                'phrase' => 'export',
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            4 =>
            array (
                'id' => 17,
                'priority_action_id' => '1.2',
                'phrase' => 'import',
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            5 =>
            array (
                'id' => 18,
                'priority_action_id' => '1.2',
                'phrase' => 'market',
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            6 =>
            array (
                'id' => 19,
                'priority_action_id' => '1.3',
                'phrase' => 'coordination',
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            7 =>
            array (
                'id' => 20,
                'priority_action_id' => '1.3',
                'phrase' => 'coherence',
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            8 =>
            array (
                'id' => 21,
                'priority_action_id' => '1.3',
                'phrase' => 'intersectional',
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            9 =>
            array (
                'id' => 22,
                'priority_action_id' => '1.4',
                'phrase' => 'dissemination',
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            10 =>
            array (
                'id' => 23,
                'priority_action_id' => '1.4',
                'phrase' => 'data collection',
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            11 =>
            array (
                'id' => 24,
                'priority_action_id' => '1.4',
                'phrase' => 'data analysis',
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
        ));


    }
}
