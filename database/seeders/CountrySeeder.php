<?php

namespace Database\Seeders;

use App\Models\Country;
use Illuminate\Database\Seeder;

class CountrySeeder extends Seeder
{
    public function run(): void
    {
        $path = database_path('data/UNSD — Methodology - httpsunstats.un.orgunsdmethodologym49overview.csv');
        $handle = fopen($path, 'r');

        // Strip UTF-8 BOM if present and skip header row
        $header = fgets($handle);
        $header = ltrim($header, "\xEF\xBB\xBF");

        while (($line = fgets($handle)) !== false) {
            $row = str_getcsv(trim($line), ';');

            $iso3 = $row[11] ?? '';
            if ($iso3 === '') {
                continue;
            }

            Country::updateOrCreate(
                ['id' => $iso3],
                [
                    'name' => $row[8],
                    'iso2' => $row[10] !== '' ? $row[10] : null,
                    'un_code' => $row[9] !== '' ? $row[9] : null,
                ]
            );
        }

        fclose($handle);
    }
}
