<?php

namespace App\Http\Controllers;

use App\Models\Profile;

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
}
