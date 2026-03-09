<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class LanguagesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        \DB::table('languages')->delete();

        \DB::table('languages')->insert(values: [
            ['id' => 'en',   'name' => collect(['en' => 'English', 'fr' => 'Anglais', 'es' => 'Inglés', 'po' => 'Inglês'])->toJson()],
            ['id' => 'fr',   'name' => collect(['en' => 'French', 'fr' => 'français', 'es' => 'Francés', 'po' => 'Francês'])->toJson()],
            ['id' => 'es',   'name' => collect(['en' => 'Spanish', 'fr' => 'Espagnol', 'es' => 'Español', 'po' => 'Espanhol'])->toJson()],
            ['id' => 'po',   'name' => collect(['en' => 'Portuguese', 'fr' => 'Portugais', 'es' => 'Portugués', 'po' => 'Português'])->toJson()],
        ]);

    }
}
