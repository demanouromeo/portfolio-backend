<?php

namespace App\Http\Controllers;

use App\Models\AboutItem;

class AboutItemController extends Controller
{
    public function index()
    {
        return response()->json(
            AboutItem::orderBy('sort_order')->get(),
            200
        );
    }
}
