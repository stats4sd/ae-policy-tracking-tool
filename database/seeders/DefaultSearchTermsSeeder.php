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
                'phrase' => '{"en":"local context","es":"contexto local","fr":"contexte local","po":"local context"}',
                'created_at' => NULL,
                'updated_at' => '2026-03-17 15:19:54',
            ),
            1 =>
            array (
                'id' => 14,
                'priority_action_id' => '1.1',
                'phrase' => '{"en":"agroecology","es":"agroecología","fr":"agroécologie","po":"agroecology"}',
                'created_at' => NULL,
                'updated_at' => '2026-03-17 15:19:56',
            ),
            2 =>
            array (
                'id' => 15,
                'priority_action_id' => '1.1',
                'phrase' => '{"en":"resilience","es":"resiliencia","fr":"résilience","po":"resilience"}',
                'created_at' => NULL,
                'updated_at' => '2026-03-17 15:19:57',
            ),
            3 =>
            array (
                'id' => 16,
                'priority_action_id' => '1.2',
                'phrase' => '{"en":"export","es":"exportar","fr":"exporter","po":"export"}',
                'created_at' => NULL,
                'updated_at' => '2026-03-17 15:20:05',
            ),
            4 =>
            array (
                'id' => 17,
                'priority_action_id' => '1.2',
                'phrase' => '{"en":"import","es":"importar","fr":"importer","po":"import"}',
                'created_at' => NULL,
                'updated_at' => '2026-03-17 15:20:06',
            ),
            5 =>
            array (
                'id' => 18,
                'priority_action_id' => '1.2',
                'phrase' => '{"en":"market","es":"mercado","fr":"marché","po":"market"}',
                'created_at' => NULL,
                'updated_at' => '2026-03-17 15:20:07',
            ),
            6 =>
            array (
                'id' => 19,
                'priority_action_id' => '1.3',
                'phrase' => '{"en":"coordination","es":"coordinación","fr":"coordination","po":"coordination"}',
                'created_at' => NULL,
                'updated_at' => '2026-03-17 15:20:13',
            ),
            7 =>
            array (
                'id' => 20,
                'priority_action_id' => '1.3',
                'phrase' => '{"en":"coherence","es":"coherencia","fr":"cohérence","po":"coherence"}',
                'created_at' => NULL,
                'updated_at' => '2026-03-17 15:20:13',
            ),
            8 =>
            array (
                'id' => 21,
                'priority_action_id' => '1.3',
                'phrase' => '{"en":"intersectional","es":"interseccional","fr":"intersectionnel","po":"intersectional"}',
                'created_at' => NULL,
                'updated_at' => '2026-03-17 15:20:14',
            ),
            9 =>
            array (
                'id' => 22,
                'priority_action_id' => '1.4',
                'phrase' => '{"en":"dissemination","es":"diseminación","fr":"dissémination","po":"dissemination"}',
                'created_at' => NULL,
                'updated_at' => '2026-03-17 15:20:39',
            ),
            10 =>
            array (
                'id' => 23,
                'priority_action_id' => '1.4',
                'phrase' => '{"en":"data collection","es":"recopilación de datos","fr":"collecte de données","po":"data collection"}',
                'created_at' => NULL,
                'updated_at' => '2026-03-17 15:20:40',
            ),
            11 =>
            array (
                'id' => 24,
                'priority_action_id' => '1.4',
                'phrase' => '{"en":"data analysis","es":"análisis de datos","fr":"analyse des données","po":"data analysis"}',
                'created_at' => NULL,
                'updated_at' => '2026-03-17 15:20:40',
            ),
        ));

    }
}
