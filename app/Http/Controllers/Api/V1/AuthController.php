<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Events\Verified;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Log;

class AuthController extends Controller
{
    public function resend(Request $request)
    {
        $userId = $request->user()->id;
        Log::info("[RESEND] Request started for User ID: {$userId}");

        if ($request->user()->hasVerifiedEmail()) {
            Log::info("[RESEND] User {$userId} already verified.");
            return response()->json(['message' => 'Email already verified.']);
        }

        try {
            Log::info("[RESEND] Calling sendEmailVerificationNotification for User {$userId}");
            $request->user()->sendEmailVerificationNotification();
            Log::info("[RESEND] sendEmailVerificationNotification finished for User {$userId}");
        } catch (\Exception $e) {
            Log::error("[RESEND] CRITICAL FAILURE for User {$userId}: " . $e->getMessage());
            Log::error($e->getTraceAsString());
            return response()->json(['message' => 'Failed to send verification link. Error: ' . $e->getMessage()], 500);
        }

        return response()->json(['message' => 'Verification link sent.']);
    }

    public function verify(Request $request, $id, $hash)
    {
        Log::info("[VERIFY] --- START --- Request for User ID: {$id}");

        $user = User::find($id);

        if (!$user) {
            Log::error("[VERIFY] User NOT FOUND in database for ID: {$id}");
            return response()->json(['message' => 'User not found.'], 404);
        }

        Log::info("[VERIFY] User {$id} found: {$user->email}. Current verified_at: " . ($user->email_verified_at ?: 'NULL'));

        $expectedHash = sha1($user->getEmailForVerification());
        if (!hash_equals((string) $hash, $expectedHash)) {
            Log::warning("[VERIFY] HASH MISMATCH. Received: {$hash}, Expected: {$expectedHash}");
            return response()->json(['message' => 'Invalid verification link.'], 403);
        }

        if ($user->hasVerifiedEmail()) {
            Log::info("[VERIFY] User {$id} already verified in DB. Returning success view.");
            return view('auth.verify-success');
        }

        Log::info("[VERIFY] Attempting markEmailAsVerified() for User {$id}");
        if ($user->markEmailAsVerified()) {
            Log::info("[VERIFY] markEmailAsVerified() returned TRUE for User {$id}");
            $user->email_verified_at = now(); // Double force it
            $saved = $user->save();
            Log::info("[VERIFY] Database save() result for User {$id}: " . ($saved ? 'SUCCESS' : 'FAILURE'));

            Log::info("[VERIFY] Triggering Verified event and Success Email for User {$id}");
            event(new Verified($user));
            $user->notify(new \App\Notifications\WelcomeNotification());
        } else {
            Log::error("[VERIFY] markEmailAsVerified() returned FALSE for User {$id}");
        }

        Log::info("[VERIFY] --- END --- Request for User ID: {$id}");
        return view('auth.verify-success', ['user' => $user]);
    }

    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        Log::info("[REGISTER] Success for email: " . $user->email);

        $token = $user->createToken($request->device_name ?? 'flutter_app')->plainTextToken;
        $freshUser = $user->fresh();

        return response()->json([
            'user' => $freshUser,
            'is_verified' => $freshUser->hasVerifiedEmail(),
            'token' => $token,
        ]);
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
            'device_name' => 'required',
        ]);

        Log::info("[LOGIN] Attempt for email: " . $request->email);

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            Log::warning("[LOGIN] Failed for email: " . $request->email);
            return response()->json([
                'message' => 'Invalid credentials.',
                'errors' => ['email' => ['Invalid credentials.']]
            ], 422);
        }

        $token = $user->createToken($request->device_name)->plainTextToken;
        $freshUser = $user->fresh();
        $isVerified = $user->hasVerifiedEmail();

        $responseData = [
            'user' => $freshUser,
            'is_verified' => $isVerified,
            'token' => $token,
        ];

        Log::info("[LOGIN] SUCCESS for User {$user->id}. Response: " . json_encode([
            'user_id' => $user->id,
            'email' => $user->email,
            'is_verified_root' => $isVerified,
            'email_verified_at' => $freshUser->email_verified_at,
        ]));

        return response()->json($responseData);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json(['message' => 'Logged out.']);
    }

    public function socialLogin(Request $request)
    {
        $request->validate([
            'email' => 'nullable|email',
            'name' => 'required|string',
            'provider' => 'required|string|in:google,apple',
            'provider_id' => 'required|string',
            'device_name' => 'required|string',
        ]);

        // 1. Try to find by provider info first (Best for Apple)
        $user = User::where('provider', $request->provider)
            ->where('provider_id', $request->provider_id)
            ->first();

        // 2. If not found, try to find by email
        if (!$user && $request->email) {
            $user = User::where('email', $request->email)->first();

            if ($user) {
                // Link the account
                $user->update([
                    'provider' => $request->provider,
                    'provider_id' => $request->provider_id,
                ]);
            }
        }

        // 3. If still not found, create new user
        if (!$user) {
            $email = $request->email ?? "{$request->provider_id}@{$request->provider}.com";

            $user = User::create([
                'name' => $request->name,
                'email' => $email,
                'password' => Hash::make(str()->random(24)),
                'provider' => $request->provider,
                'provider_id' => $request->provider_id,
            ]);
        }

        Log::info("[SOCIAL_LOGIN] Success for email: " . $user->email . " (Provider: {$request->provider})");

        $token = $user->createToken($request->device_name)->plainTextToken;
        $freshUser = $user->fresh();

        return response()->json([
            'user' => $freshUser,
            'is_verified' => $freshUser->fresh()->hasVerifiedEmail(),
            'token' => $token,
        ]);
    }
}
