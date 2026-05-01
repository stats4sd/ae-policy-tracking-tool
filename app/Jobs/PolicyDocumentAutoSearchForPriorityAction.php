<?php

namespace App\Jobs;

use App\Models\Extract;
use App\Models\PolicyDocument;
use App\Models\PolicyDocumentPage;
use App\Models\PriorityAction;
use Illuminate\Bus\Batchable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class PolicyDocumentAutoSearchForPriorityAction implements ShouldQueue
{
    use Batchable;
    use Queueable;

    public function __construct(
        public readonly PolicyDocument $policyDocument,
        public readonly PriorityAction $priorityAction,
    ) {}

    public function handle(): void
    {
        $this->priorityAction->loadMissing('searchTerms');
        $pages = $this->policyDocument->pages()->orderBy('page_number')->get();

        foreach ($pages as $page) {
            $this->searchPage($page);
        }
    }

    private function searchPage(PolicyDocumentPage $page): void
    {
        $content = $page->content;

        if (blank($content)) {
            return;
        }

        foreach ($this->priorityAction->searchTerms as $searchTerm) {
            $term = $searchTerm->getTranslation('phrase', $this->policyDocument->language_id, useFallbackLocale: $this->policyDocument->assessment->language_id);

            if (blank($term)) {
                continue;
            }

            $matches = [];
            $offset = 0;

            while (($pos = mb_stripos($content, $term, $offset)) !== false) {
                $matches[] = $pos;
                $offset = $pos + mb_strlen($term);
            }

            foreach ($matches as $matchPos) {
                $startOffset = $matchPos;
                $endOffset = $matchPos + mb_strlen($term);

                // Expand to sentence boundaries
                $lastDot = mb_strrpos(mb_substr($content, 0, $startOffset), '.');
                $contextStart = $lastDot === false ? 0 : $lastDot + 1;

                $contextEnd = min(mb_strlen($content), mb_strpos($content, '.', $endOffset) ?: mb_strlen($content));

                // Trim leading whitespace
                while ($contextStart < $startOffset && in_array(mb_substr($content, $contextStart, 1), [' ', "\n", "\r", "\t"])) {
                    $contextStart++;
                }

                $extractText = mb_substr($content, $contextStart, $contextEnd - $contextStart);
                $startOffset = $contextStart;
                $endOffset = $contextEnd;

                $extract = Extract::withTrashed()
                    ->updateOrCreate([
                        'policy_document_id' => $this->policyDocument->id,
                        'page_number' => $page->page_number,
                        'start_offset' => $startOffset,
                        'end_offset' => $endOffset,
                        'extract' => $extractText,
                        'automatic' => true,
                        'color' => 'lightblue',
                    ]);

                $this->priorityAction->extracts()->syncWithoutDetaching([$extract->id]);
                $extract->searchTerms()->syncWithoutDetaching([$searchTerm->id]);
            }
        }
    }
}
