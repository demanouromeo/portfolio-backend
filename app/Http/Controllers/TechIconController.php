<?php

namespace App\Http\Controllers;

use App\Models\TechIcon;
use Illuminate\Http\Request;

class TechIconController extends Controller
{
    public function index()
    {
        return response()->json(
            TechIcon::orderBy('sort_order')->get(),
            200
        );
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'tech_name' => 'required|string|max:255',
            'tech_category' => 'required|in:framework,programming_language',
            'icon' => 'required|file|mimes:jpeg,png,jpg,webp,svg|max:200',
        ]);

        $techIcon = TechIcon::create([
            'tech_name' => $data['tech_name'],
            'tech_category' => $data['tech_category'],
            'icon_path' => MyHelper::storeUpload($request->file('icon'), 'tech-icons'),
            'sort_order' => (int) TechIcon::max('sort_order') + 1,
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Tech icon created successfully',
            'data' => $techIcon,
        ], 201);
    }

    public function update(Request $request, TechIcon $techIcon)
    {
        $data = $request->validate([
            'tech_name' => 'required|string|max:255',
            'tech_category' => 'required|in:framework,programming_language',
            'icon' => 'nullable|file|mimes:jpeg,png,jpg,webp,svg|max:200',
        ]);

        $techIcon->tech_name = $data['tech_name'];
        $techIcon->tech_category = $data['tech_category'];

        if ($request->hasFile('icon')) {
            MyHelper::deleteUpload($techIcon->icon_path);
            $techIcon->icon_path = MyHelper::storeUpload($request->file('icon'), 'tech-icons');
        }

        $techIcon->save();

        return response()->json([
            'status' => true,
            'message' => 'Tech icon updated successfully',
            'data' => $techIcon->fresh(),
        ], 200);
    }

    public function destroy(TechIcon $techIcon)
    {
        MyHelper::deleteUpload($techIcon->icon_path);
        $techIcon->delete();

        return response()->json([
            'status' => true,
            'message' => 'Tech icon deleted successfully',
        ], 200);
    }

    public function reorder(Request $request)
    {
        $data = $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'integer|exists:tech_icons,id',
        ]);

        foreach ($data['ids'] as $index => $id) {
            TechIcon::where('id', $id)->update(['sort_order' => $index + 1]);
        }

        return response()->json([
            'status' => true,
            'message' => 'Tech icons reordered successfully',
        ], 200);
    }
}
