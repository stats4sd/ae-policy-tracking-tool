<?php

namespace App\Jobs;

use App\Models\Extract;
use App\Models\PolicyDocument;
use App\Models\PolicyDocumentPage;
use Filament\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class PolicyDocumentAutoSearch implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(public readonly PolicyDocument $policyDocument)
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $priorityActions = \App\Models\PriorityAction::with('searchTerms')->get();
        $pages = $this->policyDocument->pages()->orderBy('page_number')->get();

        foreach ($pages as $page) {
            $this->searchPage($page, $priorityActions);
        }

        Notification::make('Auto Search Completed')
            ->body('Automatic search for priority actions completed for document: '.$this->policyDocument->name)
            ->success()
            ->send();
        $this->policyDocument->stopProcessing();
    }

    private function searchPage(PolicyDocumentPage $page, $priorityActions): void
    {
        $content = $page->content;

        if (blank($content)) {
            return;
        }

        foreach ($priorityActions as $priorityAction) {
            foreach ($priorityAction->searchTerms as $searchTerm) {
                $term = $searchTerm->getTranslation('phrase', $this->policyDocument->assessment->language_id, useFallbackLocale: false);

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
                    $contextStart = max(0, mb_strrpos(mb_substr($content, 0, $startOffset), '.') + 1);
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

                    $priorityAction->extracts()->syncWithoutDetaching([$extract->id]);
                    $extract->searchTerms()->syncWithoutDetaching([$searchTerm->id]);
                }
            }
        }
    }
}
