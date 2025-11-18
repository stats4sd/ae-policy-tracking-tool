<?php

namespace App\Http\Controllers;

use App\Http\Requests\HighlightRequest;
use App\Models\Highlight;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class HighlightController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(HighlightRequest $request): JsonResponse
    {
        $validated = $request->validated();

        $highlight = Highlight::create($validated);

        $priorityActions = $request->input('priority_actions', []);
        if (! empty($priorityActions)) {
            $highlight->priorityActions()->attach($priorityActions);
        }

        return response()->json($highlight, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Highlight $highlight)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Highlight $highlight): JsonResponse
    {
        // check priority actions exist
        $validated = $request->validate([
            'priority_actions' => 'array',
            'priority_actions.*' => 'exists:priority_actions,id',
        ]);

        $priorityActions = $validated['priority_actions'];

        if (! empty($priorityActions)) {
            $highlight->priorityActions()->sync($priorityActions);
        } else {
            $highlight->priorityActions()->detach();
        }

        return response()->json($highlight, 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Highlight $highlight)
    {
        //
    }
}
