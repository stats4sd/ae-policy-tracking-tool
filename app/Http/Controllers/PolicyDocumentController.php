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
            ->with('priorityActions.recommendation')
            ->get()->map(function ($highlight) {
                return [
                    'id' => $highlight->id,
                    'policy_document_id' => $highlight->policy_document_id,
                    'extract' => $highlight->extract,
                    'start_offset' => $highlight->start_offset,
                    'end_offset' => $highlight->end_offset,
                    'color' => $highlight->color,
                    'priority_actions' => $highlight->priorityActions
                        ->sortby('id')
                        ->pluck('id')
                        ->toArray(), // return only IDs for the Vue FormKit checkboxes.
                ];
            });

        return response()->json($highlights);
    }
}
