<?php

namespace App\Http\Controllers;

use App\Http\Requests\HighlightRequest;
use App\Models\Highlight;
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
    public function store(HighlightRequest $request)
    {
        $validated = $request->validated();

        $highlight = Highlight::create($validated);
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
    public function update(Request $request, Highlight $highlight)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Highlight $highlight)
    {
        //
    }
}
