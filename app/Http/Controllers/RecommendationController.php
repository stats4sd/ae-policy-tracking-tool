<?php

namespace App\Http\Controllers;

use App\Models\Recommendation;
use Illuminate\Http\Request;

class RecommendationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return Recommendation::with('priorityActions')->get();
    }

    /**
     * Display the specified resource.
     */
    public function show(Recommendation $recommendation)
    {
        return $recommendation;
    }

}
