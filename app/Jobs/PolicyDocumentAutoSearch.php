<?php

namespace App\Jobs;

use App\Models\PolicyDocument;
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

                dump('working on term:' . $term);

                while (($pos = mb_stripos($content, $term, $offset)) !== false) {
                    $matches[] = $pos;
                    $offset = $pos + strlen($term);
                    dump('found at offset:' . $offset);
                }

                dump('done with term:' . $term . ', total matches: ' . count($matches));

                dump($matches);

                // if matches found, create highlights with start_offset and end_offset
                if (count($matches) > 0) {
                    foreach ($matches as $matchPos) {
                        $startOffset = $matchPos;
                        $endOffset = $matchPos + strlen($term);

                        // check string extract
                        $extract = mb_substr($content, $startOffset, strlen($term));

                        dump('extract: ' . $extract . ' (start: ' . $startOffset . ', end: ' . $endOffset . ')');



                        // create highlight
                        $highlight = \App\Models\Highlight::updateOrCreate([
                            'policy_document_id' => $this->policyDocument->id,
                            'start_offset' => $startOffset,
                            'end_offset' => $endOffset,
                            'extract' => $extract,
                            'automatic' => true,
                            'color' => 'lightblue',
                        ]);

                        // attach highlight to priority action
                        $priorityAction->highlights()->syncWithoutDetaching([$highlight->id]);
                    }
                }
            }
        }
    }
}
