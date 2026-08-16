<?php

namespace App\Http\Controllers;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

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

    // Moves an uploaded file into backend/public/uploads/{$folder}/ under a fresh generated
    // name (never the client-supplied filename) and returns the DB-storable relative path -
    // see CLAUDE.md's Conventions for why uploads live under public/uploads rather than
    // storage/app/public (this backend's index.php sits at the project root, not public/).
    public static function storeUpload(UploadedFile $file, string $folder): string
    {
        $filename = Str::uuid() . '.' . $file->extension();
        $file->move(public_path("uploads/{$folder}"), $filename);

        return "uploads/{$folder}/{$filename}";
    }

    // Deletes a previously stored upload given the relative path saved by storeUpload() -
    // silently no-ops for null/already-missing files, since callers use this both when
    // replacing an upload and when deleting the owning record.
    public static function deleteUpload(?string $relativePath): void
    {
        if (!$relativePath) {
            return;
        }

        $fullPath = public_path($relativePath);
        if (is_file($fullPath)) {
            @unlink($fullPath);
        }
    }
}
