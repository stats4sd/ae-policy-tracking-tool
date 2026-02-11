<?php

namespace App\Http\Controllers;

use App\Http\Requests\ExtractRequest;
use App\Models\Extract;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ExtractController extends Controller
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
    public function store(ExtractRequest $request): JsonResponse
    {
        $validated = $request->validated();

        $extract = Extract::create($validated);

        $priorityActions = $request->input('priority_actions', []);
        if (! empty($priorityActions)) {
            $extract->priorityActions()->attach($priorityActions);
        }

        return response()->json($extract, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Extract $extract)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Extract $extract): JsonResponse
    {
        // check priority actions exist
        $validated = $request->validate([
            'verified' => 'boolean',
            'priority_actions' => 'array',
            'priority_actions.*' => 'exists:priority_actions,id',
            'type_id' => 'nullable|exists:types,id',
        ]);

        if (isset($validated['verified'])) {
            $extract->verified = $validated['verified'];
        }

        if (isset($validated['type_id'])) {
            $extract->type_id = $validated['type_id'];
        }

        $extract->save();

        $priorityActions = $validated['priority_actions'];

        if (! empty($priorityActions)) {
            $extract->priorityActions()->sync($priorityActions);
        } else {
            $extract->priorityActions()->detach();
        }

        return response()->json($extract, 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Extract $extract)
    {
        $extract->delete();

        return response()->json(null, 204);
    }
}
