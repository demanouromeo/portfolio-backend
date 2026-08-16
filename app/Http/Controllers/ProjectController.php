<?php

namespace App\Http\Controllers;

use App\Models\Project;

class ProjectController extends Controller
{
    public function index()
    {
        return response()->json(
            Project::with(['featureGraphics', 'demoImages'])
                ->orderBy('sort_order')
                ->get(),
            200
        );
    }
}
