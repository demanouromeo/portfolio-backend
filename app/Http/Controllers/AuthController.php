<?php

namespace App\Http\Controllers;

use App\Models\Profile;
use Firebase\JWT\ExpiredException;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

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
            'remember' => 'sometimes|boolean',
        ]);

        $profile = Profile::where('email', $data['email'])->first();

        if (!$profile || !Hash::check($data['password'], $profile->password)) {
            return response()->json([
                'status' => false,
                'message' => 'Invalid credentials',
            ], 401);
        }

        return $this->issueTokenPair($profile, (bool) ($data['remember'] ?? false));
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

    public function forgotPassword(Request $request)
    {
        $data = $request->validate([
            'email' => 'required|email',
        ]);

        $profile = Profile::where('email', $data['email'])->first();

        // Always respond the same way whether or not the email matched, so this endpoint
        // can't be used to probe for the admin's account existing.
        $generic = [
            'status' => true,
            'message' => 'If that email is registered, a password reset link has been sent.',
        ];

        if (!$profile) {
            return response()->json($generic, 200);
        }

        $token = Str::random(64);
        $profile->reset_token_hash = hash('sha256', $token);
        $profile->reset_token_expires_at = now()->addMinutes(60);
        $profile->save();

        $resetUrl = config('services.frontend_url') . '/admin/reset-password?token=' . $token . '&email=' . urlencode($profile->email);

        Mail::raw(
            "A password reset was requested for your admin account.\n\n" .
            "Reset your password: {$resetUrl}\n\n" .
            "This link expires in 60 minutes. If you didn't request this, you can ignore this email.",
            function ($message) use ($profile) {
                $message->to($profile->email)->subject('Reset your admin password');
            }
        );

        return response()->json($generic, 200);
    }

    public function resetPassword(Request $request)
    {
        $data = $request->validate([
            'email' => 'required|email',
            'token' => 'required|string',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $profile = Profile::where('email', $data['email'])->first();

        $invalid = response()->json([
            'status' => false,
            'message' => 'This reset link is invalid or has expired.',
        ], 422);

        if (!$profile || !$profile->reset_token_hash || !$profile->reset_token_expires_at) {
            return $invalid;
        }

        if ($profile->reset_token_expires_at->isPast()) {
            return $invalid;
        }

        if (!hash_equals($profile->reset_token_hash, hash('sha256', $data['token']))) {
            return $invalid;
        }

        $profile->password = $data['password'];
        $profile->reset_token_hash = null;
        $profile->reset_token_expires_at = null;
        $profile->save();

        return response()->json([
            'status' => true,
            'message' => 'Password reset successfully. You can now log in.',
        ], 200);
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

    private function issueTokenPair(Profile $profile, bool $remember = true)
    {
        $accessDuration = config('services.access_token_duration');
        $refreshDuration = config('services.refresh_token_duration');

        // "Remember me" off -> omit Max-Age so the cookie is session-only (cleared when the
        // browser closes) even though the underlying JWT is still valid for the full duration
        // server-side; on -> persist the cookie for that same duration. Passing minutes=0 to
        // Laravel's cookie() is what produces a session cookie (see CookieJar::make).
        $cookieMinutes = $remember ? (int) ceil($refreshDuration / 60) : 0;

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
            $cookieMinutes,
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
