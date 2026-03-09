<?php

namespace App\Http\Controllers;

use App\Models\Extract;
use App\Models\PolicyDocument;
use Illuminate\Http\JsonResponse;

class PolicyDocumentController extends Controller
{
    public function getContent(PolicyDocument $document): string
    {
        return $document->content;
    }

    public function getExtracts(PolicyDocument $document): JsonResponse
    {
        $extracts = $document->extracts()
            ->with(['priorityActions.recommendation', 'searchTerms'])
            ->get()->map(function (Extract $extract) {
                return [
                    'id' => $extract->id,
                    'policy_document_id' => $extract->policy_document_id,
                    'extract' => $extract->extract,
                    'start_offset' => $extract->start_offset,
                    'end_offset' => $extract->end_offset,
                    'color' => $extract->color,
                    'automatic' => $extract->automatic,
                    'verified' => $extract->verified,
                    'search_terms' => $extract->searchTerms
                        ->sortby('id')
                        ->map(function ($term) {
                            return [
                                'id' => $term->id,
                                'phrase' => $term->phrase,
                                'priority_action_id' => $term->priority_action_id,
                            ];
                        })
                        ->toArray(),
                    'search_terms_list' => $extract->searchTerms
                        ->sortby('id')
                        ->pluck('phrase')
                        ->unique()
                        // join phrases with comma and 'and' as the last separator
                        ->join(', ', ' and '),
                    'priority_actions' => $extract->priorityActions
                        ->sortby('id')
                        ->pluck('id')
                        ->toArray(), // return only IDs for the Vue FormKit checkboxes.
                    'type_id' => $extract->type_id,
                ];
            });

        return response()->json($extracts);
    }
}
