<?php

namespace App\Http\Controllers;

use App\Models\Type;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Psy\Util\Json;

class TypeController extends Controller
{
    public function index()
    {
        return Type::select('id', 'name', 'score')->get();
    }
}
