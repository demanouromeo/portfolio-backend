<?php

namespace App\Http\Controllers;

use App\Models\AboutItem;
use Illuminate\Http\Request;

class AboutItemController extends Controller
{
    public function index()
    {
        return response()->json(
            AboutItem::orderBy('sort_order')->get(),
            200
        );
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title_fr' => 'required|string|max:255',
            'title_en' => 'required|string|max:255',
            'description_fr' => 'required|string',
            'description_en' => 'required|string',
            'icon' => 'required|string|max:50',
        ]);

        $data['sort_order'] = (int) AboutItem::max('sort_order') + 1;

        $aboutItem = AboutItem::create($data);

        return response()->json([
            'status' => true,
            'message' => 'About item created successfully',
            'data' => $aboutItem,
        ], 201);
    }

    public function update(Request $request, AboutItem $aboutItem)
    {
        $data = $request->validate([
            'title_fr' => 'required|string|max:255',
            'title_en' => 'required|string|max:255',
            'description_fr' => 'required|string',
            'description_en' => 'required|string',
            'icon' => 'required|string|max:50',
        ]);

        $aboutItem->update($data);

        return response()->json([
            'status' => true,
            'message' => 'About item updated successfully',
            'data' => $aboutItem->fresh(),
        ], 200);
    }

    public function destroy(AboutItem $aboutItem)
    {
        $aboutItem->delete();

        return response()->json([
            'status' => true,
            'message' => 'About item deleted successfully',
        ], 200);
    }

    public function reorder(Request $request)
    {
        $data = $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'integer|exists:about_items,id',
        ]);

        foreach ($data['ids'] as $index => $id) {
            AboutItem::where('id', $id)->update(['sort_order' => $index + 1]);
        }

        return response()->json([
            'status' => true,
            'message' => 'About items reordered successfully',
        ], 200);
    }
}
