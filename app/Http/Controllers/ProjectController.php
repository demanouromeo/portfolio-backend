<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\ProjectDemoImage;
use App\Models\ProjectFeatureGraphic;
use Illuminate\Http\Request;

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

    public function store(Request $request)
    {
        $data = $request->validate([
            'title_fr' => 'required|string|max:255',
            'title_en' => 'required|string|max:255',
            'description_fr' => 'required|string',
            'description_en' => 'required|string',
            'technologies' => 'required|array|min:1',
            'technologies.*' => 'string|max:100',
            'repo_link' => 'nullable|url|max:255',
            'video_link' => 'nullable|url|max:255',
            'website_link' => 'nullable|url|max:255',
            'play_store_link' => 'nullable|url|max:255',
            'apple_store_link' => 'nullable|url|max:255',
        ]);

        $data['sort_order'] = (int) Project::max('sort_order') + 1;

        $project = Project::create($data);

        return response()->json([
            'status' => true,
            'message' => 'Project created successfully',
            'data' => $project->load(['featureGraphics', 'demoImages']),
        ], 201);
    }

    public function update(Request $request, Project $project)
    {
        $data = $request->validate([
            'title_fr' => 'required|string|max:255',
            'title_en' => 'required|string|max:255',
            'description_fr' => 'required|string',
            'description_en' => 'required|string',
            'technologies' => 'required|array|min:1',
            'technologies.*' => 'string|max:100',
            'repo_link' => 'nullable|url|max:255',
            'video_link' => 'nullable|url|max:255',
            'website_link' => 'nullable|url|max:255',
            'play_store_link' => 'nullable|url|max:255',
            'apple_store_link' => 'nullable|url|max:255',
        ]);

        $project->update($data);

        return response()->json([
            'status' => true,
            'message' => 'Project updated successfully',
            'data' => $project->fresh()->load(['featureGraphics', 'demoImages']),
        ], 200);
    }

    public function destroy(Project $project)
    {
        foreach ($project->featureGraphics as $graphic) {
            MyHelper::deleteUpload($graphic->path);
        }
        foreach ($project->demoImages as $image) {
            MyHelper::deleteUpload($image->path);
        }

        $project->delete(); // Child image rows cascade-delete at the DB level.

        return response()->json([
            'status' => true,
            'message' => 'Project deleted successfully',
        ], 200);
    }

    public function reorder(Request $request)
    {
        $data = $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'integer|exists:projects,id',
        ]);

        foreach ($data['ids'] as $index => $id) {
            Project::where('id', $id)->update(['sort_order' => $index + 1]);
        }

        return response()->json([
            'status' => true,
            'message' => 'Projects reordered successfully',
        ], 200);
    }

    public function storeFeatureGraphic(Request $request, Project $project)
    {
        $data = $request->validate([
            'image' => 'required|file|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        $graphic = $project->featureGraphics()->create([
            'path' => MyHelper::storeUpload($request->file('image'), 'projects/feature-graphics'),
            'sort_order' => (int) $project->featureGraphics()->max('sort_order') + 1,
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Feature graphic added successfully',
            'data' => $graphic,
        ], 201);
    }

    public function destroyFeatureGraphic(Project $project, ProjectFeatureGraphic $featureGraphic)
    {
        if ($featureGraphic->project_id !== $project->id) {
            return response()->json([
                'status' => false,
                'message' => 'Feature graphic not found for this project',
            ], 404);
        }

        MyHelper::deleteUpload($featureGraphic->path);
        $featureGraphic->delete();

        return response()->json([
            'status' => true,
            'message' => 'Feature graphic deleted successfully',
        ], 200);
    }

    public function storeDemoImage(Request $request, Project $project)
    {
        $data = $request->validate([
            'image' => 'required|file|mimes:jpeg,png,jpg,webp|max:1024',
        ]);

        $image = $project->demoImages()->create([
            'path' => MyHelper::storeUpload($request->file('image'), 'projects/demo-images'),
            'original_name' => $request->file('image')->getClientOriginalName(),
            'sort_order' => (int) $project->demoImages()->max('sort_order') + 1,
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Demo image added successfully',
            'data' => $image,
        ], 201);
    }

    public function destroyDemoImage(Project $project, ProjectDemoImage $demoImage)
    {
        if ($demoImage->project_id !== $project->id) {
            return response()->json([
                'status' => false,
                'message' => 'Demo image not found for this project',
            ], 404);
        }

        MyHelper::deleteUpload($demoImage->path);
        $demoImage->delete();

        return response()->json([
            'status' => true,
            'message' => 'Demo image deleted successfully',
        ], 200);
    }
}
