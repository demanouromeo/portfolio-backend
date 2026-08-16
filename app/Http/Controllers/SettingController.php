<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    // Every setting currently in the table happens to be public-safe, but this stays an
    // explicit allowlist (not Setting::all()) so a future admin-only setting doesn't leak
    // onto the public site just by existing in the same table.
    private const PUBLIC_KEYS = [
        'project_description_max_chars',
        'project_technologies_max_display',
    ];

    public function index()
    {
        return response()->json(
            Setting::whereIn('key', self::PUBLIC_KEYS)->pluck('value', 'key'),
            200
        );
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            'project_description_max_chars' => 'sometimes|integer|min:50|max:2000',
            'project_technologies_max_display' => 'sometimes|integer|min:1|max:20',
        ]);

        foreach ($data as $key => $value) {
            Setting::updateOrCreate(['key' => $key], ['value' => (string) $value]);
        }

        return response()->json([
            'status' => true,
            'message' => 'Settings updated successfully',
            'data' => Setting::whereIn('key', self::PUBLIC_KEYS)->pluck('value', 'key'),
        ], 200);
    }
}
