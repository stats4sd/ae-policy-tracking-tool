<?php

namespace App\Http\Controllers;

use App\Models\PolicyDocument;
use Illuminate\Http\JsonResponse;

class PolicyDocumentController extends Controller
{
    public function getContent(PolicyDocument $document): string
    {
        return $document->content;
    }

    public function getHighlights(PolicyDocument $document): JsonResponse
    {
        $highlights = $document->highlights()
            ->with(['priorityActions.recommendation', 'searchTerms'])
            ->get()->map(function ($highlight) {
                return [
                    'id' => $highlight->id,
                    'policy_document_id' => $highlight->policy_document_id,
                    'extract' => $highlight->extract,
                    'start_offset' => $highlight->start_offset,
                    'end_offset' => $highlight->end_offset,
                    'color' => $highlight->color,
                    'automatic' => $highlight->automatic,
                    'verified' => $highlight->verified,
                    'search_terms' => $highlight->searchTerms
                        ->sortby('id')
                        ->map(function ($term) {
                            return [
                                'id' => $term->id,
                                'phrase' => $term->phrase,
                                'priority_action_id' => $term->priority_action_id,
                            ];
                        })
                        ->toArray(),
                    'search_terms_list' => $highlight->searchTerms
                        ->sortby('id')
                        ->pluck('phrase')
                        ->unique()
                        // join phrases with comma and 'and' as the last separator
                        ->join(', ', ' and '),
                    'priority_actions' => $highlight->priorityActions
                        ->sortby('id')
                        ->pluck('id')
                        ->toArray(), // return only IDs for the Vue FormKit checkboxes.
                    'type_id' => $highlight->type_id,
                ];
            });

        return response()->json($highlights);
    }
}
