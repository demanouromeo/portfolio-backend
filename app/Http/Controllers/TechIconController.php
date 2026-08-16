<?php

namespace App\Http\Controllers;

use App\Models\TechIcon;

class TechIconController extends Controller
{
    public function index()
    {
        return response()->json(
            TechIcon::orderBy('sort_order')->get(),
            200
        );
    }
}
