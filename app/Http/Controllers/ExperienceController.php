<?php

namespace App\Http\Controllers;

use App\Models\Experience;

class ExperienceController extends Controller
{
    public function index()
    {
        return response()->json(
            Experience::orderBy('sort_order')->get(),
            200
        );
    }
}
