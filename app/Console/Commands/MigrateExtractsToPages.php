<?php

namespace App\Console\Commands;

use App\Models\Extract;
use App\Models\PolicyDocument;
use App\Models\PolicyDocumentPage;
use Illuminate\Console\Command;

class MigrateExtractsToPages extends Command
{
    protected $signature = 'app:migrate-extracts-to-pages';

    protected $description = 'Re-extract documents into pages and convert existing extract offsets from global to per-page';

    public function handle(): int
    {
        $documents = PolicyDocument::whereNotNull('content')
            ->withCount('extracts')
            ->get();

        $this->info("Found {$documents->count()} documents with content to migrate.");

        $errorDocuments = [];

        foreach ($documents as $document) {
            $this->info("Processing document #{$document->id}: {$document->name}");

            // Re-extract into pages
            $pagesJson = $this->extractPages($document);

            if (! $pagesJson) {
                $this->warn("  Could not extract pages for document #{$document->id} — skipping.");
                $errorDocuments[] = $document->id;

                continue;
            }

            $pages = json_decode($pagesJson, true);

            if (! is_array($pages) || count($pages) === 0) {
                $this->warn("  No pages extracted for document #{$document->id} — skipping.");
                $errorDocuments[] = $document->id;

                continue;
            }

            // Create page records
            $document->pages()->delete();
            foreach ($pages as $page) {
                PolicyDocumentPage::create([
                    'policy_document_id' => $document->id,
                    'page_number' => $page['page'],
                    'content' => $page['text'],
                    'page_type' => $page['page_type'] ?? 'unknown',
                ]);
            }

            $this->info("  Created {$this->countPages($pages)} page records.");

            // Convert extract offsets
            $extracts = Extract::withoutGlobalScopes()
                ->where('policy_document_id', $document->id)
                ->whereNull('page_number')
                ->withTrashed()
                ->get();

            if ($extracts->isEmpty()) {
                $this->info('  No extracts to migrate.');

                continue;
            }

            // Build cumulative offset map from pages
            $pageOffsets = [];
            $cumulative = 0;
            foreach ($pages as $page) {
                $pageOffsets[] = [
                    'page_number' => $page['page'],
                    'start' => $cumulative,
                    'end' => $cumulative + mb_strlen($page['text']),
                    'content' => $page['text'],
                ];
                $cumulative += mb_strlen($page['text']);
            }

            $migrated = 0;
            $failed = 0;

            foreach ($extracts as $extract) {
                $assigned = false;

                foreach ($pageOffsets as $po) {
                    if ($extract->start_offset >= $po['start'] && $extract->start_offset < $po['end']) {
                        $newStart = $extract->start_offset - $po['start'];
                        $newEnd = $extract->end_offset - $po['start'];

                        // Clamp end offset to page length
                        $newEnd = min($newEnd, mb_strlen($po['content']));

                        // Verify the extract text matches
                        $expectedText = mb_substr($po['content'], $newStart, $newEnd - $newStart);

                        $extract->page_number = $po['page_number'];
                        $extract->start_offset = $newStart;
                        $extract->end_offset = $newEnd;
                        $extract->save();

                        if ($expectedText !== $extract->extract) {
                            $this->warn("  Extract #{$extract->id}: text mismatch after offset conversion (may be due to re-extraction differences).");
                        }

                        $assigned = true;
                        $migrated++;

                        break;
                    }
                }

                if (! $assigned) {
                    $this->warn("  Extract #{$extract->id}: could not map global offset {$extract->start_offset} to any page.");
                    $failed++;
                }
            }

            $this->info("  Migrated {$migrated} extracts, {$failed} failed.");
        }

        if (count($errorDocuments) > 0) {
            $this->warn('Documents that could not be processed: '.implode(', ', $errorDocuments));
        }

        $this->info('Migration complete.');

        return self::SUCCESS;
    }

    private function extractPages(PolicyDocument $document): ?string
    {
        $media = $document->getFirstMedia('policy-documents');
        if (! $media) {
            return null;
        }

        $filePath = $media->getPath();
        $pythonScript = base_path('scripts/extract_with_pymupdf.py');
        $command = "venv/bin/python3 {$pythonScript} --pages {$filePath}";

        return shell_exec($command) ?: null;
    }

    private function countPages(array $pages): int
    {
        return count($pages);
    }
}
