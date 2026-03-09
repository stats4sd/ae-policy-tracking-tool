<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class ScoresTableSeeder extends Seeder
{
    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {

        \DB::table('scores')->delete();

        \DB::table('scores')->insert([
            ['id' => 1,   'name' => 'Follows the policy recommendations', 'score' => 1 ],
            ['id' => 2,   'name' => 'Measures creating perverse incentives', 'score' => -1 ],
            ['id' => 3,   'name' => 'Measures that go beyond policy recommendations', 'score' => 2],
        ]);

    }
}
