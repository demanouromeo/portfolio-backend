<?php

namespace App\Http\Controllers;

use App\Models\Profile;
use Firebase\JWT\ExpiredException;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    // Single admin app: the profile row itself is the account (see CLAUDE.md's Auth model).
    // Always issues role=ADMIN - there is no other role to branch on.
    private const ROLE = 'ADMIN';

    public function login(Request $request)
    {
        $data = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        $profile = Profile::where('email', $data['email'])->first();

        if (!$profile || !Hash::check($data['password'], $profile->password)) {
            return response()->json([
                'status' => false,
                'message' => 'Invalid credentials',
            ], 401);
        }

        return $this->issueTokenPair($profile);
    }

    public function refresh(Request $request)
    {
        $refreshToken = $request->cookie('refresh_token');

        if (!$refreshToken) {
            return response()->json([
                'status' => false,
                'message' => 'Refresh token missing',
            ], 401);
        }

        try {
            $decoded = JWT::decode($refreshToken, new Key(config('services.jwt_secret'), 'HS256'));
        } catch (ExpiredException $e) {
            return response()->json([
                'status' => false,
                'message' => 'Refresh token expired',
            ], 401);
        } catch (\Throwable $e) {
            return response()->json([
                'status' => false,
                'message' => 'Invalid refresh token',
            ], 401);
        }

        if (isset($decoded->jti) && MyHelper::isTokenBlacklisted($decoded->jti)) {
            return response()->json([
                'status' => false,
                'message' => 'Refresh token has been revoked',
            ], 401);
        }

        $profile = Profile::find($decoded->sub);

        if (!$profile) {
            return response()->json([
                'status' => false,
                'message' => 'Profile not found',
            ], 404);
        }

        $duration = config('services.access_token_duration');

        return response()->json([
            'status' => true,
            'message' => 'Token refreshed successfully',
            'access_token' => $this->buildAccessToken($profile, $duration),
            'token_type' => 'Bearer',
            'expires_in' => $duration,
        ], 200);
    }

    public function logout(Request $request)
    {
        $this->revokeIfPresent($request->bearerToken());
        $this->revokeIfPresent($request->cookie('refresh_token'));

        return response()->json([
            'status' => true,
            'message' => 'Logged out successfully',
        ], 200)->cookie(
            'refresh_token',
            '',
            -1,
            '/',
            null,
            app()->environment('production'),
            true,
            false,
            'Strict'
        );
    }

    public function me(Request $request)
    {
        $profile = Profile::find($request->attributes->get('auth_payload')->sub);

        if (!$profile) {
            return response()->json([
                'status' => false,
                'message' => 'Profile not found',
            ], 404);
        }

        return response()->json($profile, 200);
    }

    private function issueTokenPair(Profile $profile)
    {
        $accessDuration = config('services.access_token_duration');
        $refreshDuration = config('services.refresh_token_duration');

        return response()->json([
            'status' => true,
            'message' => 'Login successful',
            'access_token' => $this->buildAccessToken($profile, $accessDuration),
            'token_type' => 'Bearer',
            'expires_in' => $accessDuration,
            'user' => $profile,
        ], 200)->cookie(
            'refresh_token',
            $this->buildRefreshToken($profile, $refreshDuration),
            (int) ceil($refreshDuration / 60), // Cookie Max-Age is minutes; token exp is seconds.
            '/',
            null,
            app()->environment('production'), // Secure requires HTTPS - off for local http dev.
            true,
            false,
            'Strict'
        );
    }

    private function buildAccessToken(Profile $profile, int $duration): string
    {
        return JWT::encode([
            'iss' => config('app.name'),
            'sub' => $profile->id,
            'jti' => bin2hex(random_bytes(16)),
            'email' => $profile->email,
            'role' => self::ROLE,
            'name' => trim($profile->name . ' ' . $profile->surname),
            'iat' => time(),
            'exp' => time() + $duration,
        ], config('services.jwt_secret'), 'HS256');
    }

    private function buildRefreshToken(Profile $profile, int $duration): string
    {
        return JWT::encode([
            'iss' => config('app.name'),
            'sub' => $profile->id,
            'jti' => bin2hex(random_bytes(16)),
            'role' => self::ROLE,
            'iat' => time(),
            'exp' => time() + $duration,
        ], config('services.jwt_secret'), 'HS256');
    }

    private function revokeIfPresent(?string $token): void
    {
        if (!$token) {
            return;
        }

        try {
            $decoded = JWT::decode($token, new Key(config('services.jwt_secret'), 'HS256'));
            if (isset($decoded->jti, $decoded->exp)) {
                MyHelper::blacklistToken($decoded->jti, $decoded->exp - time());
            }
        } catch (\Throwable $e) {
            // Already invalid/expired - nothing left to revoke.
        }
    }
}
