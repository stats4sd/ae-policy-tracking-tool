<?php

namespace App\Http\Controllers;

use App\Models\PolicyDocument;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\HtmlString;

class PolicyDocumentController extends Controller
{
    public function getContent(PolicyDocument $document): string
    {
        return $document->content;
    }

    public function getHighlights(PolicyDocument $document): JsonResponse
    {
        $highlights = $document->highlights()->get()->map(function ($highlight) {
            return [
                'id' => $highlight->id,
                'policy_document_id' => $highlight->policy_document_id,
                'extract' => $highlight->extract,
                'start_offset' => $highlight->start_offset,
                'end_offset' => $highlight->end_offset,
                'color' => $highlight->color,
                'automatic' => $highlight->automatic,
                'verified' => $highlight->verified,
            ];
        });

        return response()->json($highlights);
    }
}
