<?php

namespace App\Http\Controllers;

use App\Models\Profile;
use Illuminate\Http\Request;

class ProfileController extends Controller
{
    public function show()
    {
        $profile = Profile::first();

        if (!$profile) {
            return response()->json([
                'status' => false,
                'message' => 'Profile not found',
            ], 404);
        }

        return response()->json($profile, 200);
    }

    public function update(Request $request)
    {
        $profile = Profile::first();

        if (!$profile) {
            return response()->json([
                'status' => false,
                'message' => 'Profile not found',
            ], 404);
        }

        $data = $request->validate([
            'name' => 'required|string|max:255',
            'surname' => 'required|string|max:255',
            'alias' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:profiles,email,' . $profile->id,
            'password' => 'nullable|string|min:8',
            'years_experience' => 'required|integer|min:0|max:100',
            'phone' => 'nullable|string|max:30',
            'linkedin' => 'nullable|string|max:255',
            'facebook' => 'nullable|string|max:255',
            'instagram' => 'nullable|string|max:255',
            'github' => 'nullable|string|max:255',
            'address' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:100',
            'short_intro_fr' => 'required|string',
            'short_intro_en' => 'required|string',
            'profile_picture' => 'nullable|file|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        // Optional on every save: leave the password untouched when omitted, matching
        // adminUpdateAccount's convention this auth model was adapted from (see CLAUDE.md).
        if (empty($data['password'])) {
            unset($data['password']);
        }
        unset($data['profile_picture']);

        if ($request->hasFile('profile_picture')) {
            MyHelper::deleteUpload($profile->profile_picture_path);
            $data['profile_picture_path'] = MyHelper::storeUpload($request->file('profile_picture'), 'profile');
        }

        $profile->update($data);

        return response()->json([
            'status' => true,
            'message' => 'Profile updated successfully',
            'data' => $profile->fresh(),
        ], 200);
    }
}
