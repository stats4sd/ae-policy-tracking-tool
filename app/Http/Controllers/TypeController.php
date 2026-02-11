<?php

namespace App\Http\Controllers;

use App\Models\Score;

class TypeController extends Controller
{
    public function index()
    {
        return Score::select('id', 'name', 'score')->get();
    }
}
