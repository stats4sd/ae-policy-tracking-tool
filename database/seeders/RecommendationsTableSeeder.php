<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class RecommendationsTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('recommendations')->delete();
        
        \DB::table('recommendations')->insert(array (
            0 => 
            array (
                'id' => 1,
                'name' => 'Lay or strengthen, as appropriate, the policy foundations for agroecological approaches to contribute to sustainable agriculture and food systems that enhance food security and nutrition. ',
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            1 => 
            array (
                'id' => 2,
                'name' => 'Establish, improve and apply comprehensive performance measurement and monitoring frameworks to encourage the adoption of agroecological approaches for sustainable agriculture and food systems that enhance food security and nutrition.',
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            2 => 
            array (
                'id' => 3,
                'name' => 'Foster the transition to resilient and diversified sustainable agriculture and food systems through agroecological approaches.',
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            3 => 
            array (
                'id' => 4,
                'name' => 'Strengthen research, innovation, training, and education and foster knowledge co-creation, knowledge sharing and co-learning, on agroecological approaches',
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            4 => 
            array (
                'id' => 5,
                'name' => 'Strengthen institutions or stakeholder engagement, create an enabling environment for empowering people most at risk of food insecurity and malnutrition and people in vulnerable situations and address power inequalities in agriculture and food systems.',
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
        ));
        
        
    }
}