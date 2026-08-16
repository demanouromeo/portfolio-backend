<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Cache;

class MyHelper extends Controller
{
    // Uses the file cache store explicitly (not the "database" store) since this API is
    // stateless JWT auth with no other use for a `cache` table - see CLAUDE.md's Conventions.
    public static function blacklistToken($jti, $ttlSeconds)
    {
        if (!$jti || $ttlSeconds <= 0) {
            return;
        }
        Cache::store('file')->put('jwt_blacklist:' . $jti, true, $ttlSeconds);
    }

    public static function isTokenBlacklisted($jti)
    {
        if (!$jti) {
            return false;
        }
        return Cache::store('file')->has('jwt_blacklist:' . $jti);
    }
}
