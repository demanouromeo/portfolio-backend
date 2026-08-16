<?php

namespace App\Http\Controllers;

use App\Models\Experience;
use Illuminate\Http\Request;

class ExperienceController extends Controller
{
    public function index()
    {
        return response()->json(
            Experience::orderBy('sort_order')->get(),
            200
        );
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'role_fr' => 'required|string|max:255',
            'role_en' => 'required|string|max:255',
            'company' => 'required|string|max:255',
            'period_fr' => 'required|string|max:100',
            'period_en' => 'required|string|max:100',
            'description_fr' => 'required|array|min:1',
            'description_fr.*' => 'string',
            'description_en' => 'required|array|min:1',
            'description_en.*' => 'string',
            'image' => 'nullable|file|mimes:jpeg,png,jpg,webp|max:500',
        ]);

        unset($data['image']);
        $data['sort_order'] = (int) Experience::max('sort_order') + 1;

        if ($request->hasFile('image')) {
            $data['image_path'] = MyHelper::storeUpload($request->file('image'), 'experiences');
        }

        $experience = Experience::create($data);

        return response()->json([
            'status' => true,
            'message' => 'Experience created successfully',
            'data' => $experience,
        ], 201);
    }

    public function update(Request $request, Experience $experience)
    {
        $data = $request->validate([
            'role_fr' => 'required|string|max:255',
            'role_en' => 'required|string|max:255',
            'company' => 'required|string|max:255',
            'period_fr' => 'required|string|max:100',
            'period_en' => 'required|string|max:100',
            'description_fr' => 'required|array|min:1',
            'description_fr.*' => 'string',
            'description_en' => 'required|array|min:1',
            'description_en.*' => 'string',
            'image' => 'nullable|file|mimes:jpeg,png,jpg,webp|max:500',
        ]);

        unset($data['image']);

        if ($request->hasFile('image')) {
            MyHelper::deleteUpload($experience->image_path);
            $data['image_path'] = MyHelper::storeUpload($request->file('image'), 'experiences');
        }

        $experience->update($data);

        return response()->json([
            'status' => true,
            'message' => 'Experience updated successfully',
            'data' => $experience->fresh(),
        ], 200);
    }

    public function destroy(Experience $experience)
    {
        MyHelper::deleteUpload($experience->image_path);
        $experience->delete();

        return response()->json([
            'status' => true,
            'message' => 'Experience deleted successfully',
        ], 200);
    }

    public function reorder(Request $request)
    {
        $data = $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'integer|exists:experiences,id',
        ]);

        foreach ($data['ids'] as $index => $id) {
            Experience::where('id', $id)->update(['sort_order' => $index + 1]);
        }

        return response()->json([
            'status' => true,
            'message' => 'Experiences reordered successfully',
        ], 200);
    }
}
