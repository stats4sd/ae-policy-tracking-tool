<?php

namespace App\Console\Commands;

use App\Models\DefaultSearchTerm;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

// Written by Claude
class ImportDefaultSearchTerms extends Command
{
    protected $signature = 'app:import-default-search-terms
                            {--path= : Path to the CSV file (defaults to database/data/search_terms_long.csv)}';

    protected $description = 'Import default search terms from CSV, replacing all existing entries';

    public function handle(): int
    {
        $path = $this->option('path') ?? database_path('data/search_terms_combined.csv');

        if (! file_exists($path)) {
            $this->error("CSV file not found: {$path}");

            return self::FAILURE;
        }

        $handle = fopen($path, 'r');

        if ($handle === false) {
            $this->error("Could not open file: {$path}");

            return self::FAILURE;
        }

        // Parse header row and detect language columns
        $headers = fgetcsv($handle);

        if ($headers === false) {
            $this->error('CSV file is empty.');
            fclose($handle);

            return self::FAILURE;
        }

        $langColumns = [];
        foreach ($headers as $index => $header) {
            if (preg_match('/^search_term_([a-z]{2,})$/i', trim($header), $matches)) {
                $langColumns[strtolower($matches[1])] = $index;
            }
        }

        $codeIndex = array_search('code', array_map('trim', $headers));

        if ($codeIndex === false) {
            $this->error("CSV must have a 'code' column.");
            fclose($handle);

            return self::FAILURE;
        }

        if (empty($langColumns)) {
            $this->error("No language columns found (expected headers like 'search_term_en', 'search_term_fr').");
            fclose($handle);

            return self::FAILURE;
        }

        $this->info('Detected languages: '.implode(', ', array_keys($langColumns)));

        // Read all rows
        $rows = [];
        while (($row = fgetcsv($handle)) !== false) {
            $code = trim($row[$codeIndex] ?? '');
            if ($code === '') {
                continue;
            }

            $phrases = [];
            foreach ($langColumns as $lang => $colIndex) {
                $value = trim($row[$colIndex] ?? '');
                if ($value !== '') {
                    $phrases[$lang] = $value;
                }
            }

            if (empty($phrases)) {
                continue;
            }

            $rows[] = ['priority_action_id' => $code, 'phrase' => $phrases];
        }

        fclose($handle);

        $this->info("Read {$this->count($rows)} terms from CSV. Replacing existing entries...");

        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        DefaultSearchTerm::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1');

        DB::transaction(function () use ($rows) {
            foreach ($rows as $row) {
                $term = new DefaultSearchTerm;
                $term->priority_action_id = $row['priority_action_id'];
                $term->setTranslations('phrase', $row['phrase']);
                $term->save();
            }
        });

        $this->info("Imported {$this->count($rows)} default search terms successfully.");

        return self::SUCCESS;
    }

    /** @param array<int, mixed> $rows */
    private function count(array $rows): int
    {
        return count($rows);
    }
}
