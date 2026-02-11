<?php

namespace App\Jobs;

use App\Models\Highlight;
use App\Models\PolicyDocument;
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
        // foreach priority action, check if any search term matches the content
        $priorityActions = \App\Models\PriorityAction::with('searchTerms')->get();

        foreach ($priorityActions as $priorityAction) {
            foreach ($priorityAction->searchTerms as $searchTerm) {

                // search the full document for all occurrences of the search term
                $matches = [];
                $offset = 0;
                $content = $this->policyDocument->content;
                $term = $searchTerm->phrase;

                while (($pos = mb_stripos($content, $term, $offset)) !== false) {
                    $matches[] = $pos;
                    $offset = $pos + strlen($term);
                    dump('found at offset:'.$offset);
                }

                // if matches found, create highlights with start_offset and end_offset
                if (count($matches) > 0) {
                    foreach ($matches as $matchPos) {
                        $startOffset = $matchPos;
                        $endOffset = $matchPos + strlen($term);

                        // check string extract
                        $extract = mb_substr($content, $startOffset, strlen($term));

                        dump('extract: '.$extract.' (start: '.$startOffset.', end: '.$endOffset.')');

                        // expand to sentence boundaries for better context (up to 100 characters before and after)
                        $contextStart = max(0, mb_strrpos(mb_substr($content, 0, $startOffset), '.') + 1);
                        $contextEnd = min(mb_strlen($content), mb_strpos($content, '.', $endOffset) ?: mb_strlen($content));

                        // trim the start of any whitespace or newline characters before the first real character
                        while ($contextStart < $startOffset && in_array(mb_substr($content, $contextStart, 1), [' ', "\n", "\r", "\t"])) {
                            $contextStart++;
                        }

                        $extract = mb_substr($content, $contextStart, $contextEnd - $contextStart);
                        $startOffset = $contextStart;
                        $endOffset = $contextEnd;

                        dump('contextual extract: '.$extract.' (start: '.$startOffset.', end: '.$endOffset.')');

                        // check if highlight already exists (including soft deleted entries, to avoid re-creating entries that were manually deleted)
                        $highlight = Highlight::withTrashed()
                            ->updateOrCreate([
                                'policy_document_id' => $this->policyDocument->id,
                                'start_offset' => $startOffset,
                                'end_offset' => $endOffset,
                                'extract' => $extract,
                                'automatic' => true,
                                'color' => 'lightblue',
                            ]);

                        // attach highlight to priority action
                        $priorityAction->highlights()->syncWithoutDetaching([$highlight->id]);

                        // attach highlight to the search term
                        $highlight->searchTerms()->syncWithoutDetaching([$searchTerm->id]);
                    }
                }
            }
        }

        Notification::make('Auto Search Completed')
            ->body('Automatic search for priority actions completed for document: '.$this->policyDocument->name)
            ->success()
            ->send();
        $this->policyDocument->stopProcessing();
    }
}
