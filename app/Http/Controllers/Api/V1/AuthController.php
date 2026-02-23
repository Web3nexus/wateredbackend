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
use Illuminate\Support\Facades\Password;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Support\Str;

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

            // Sync with Firebase if the user has a provider_id (UID)
            if ($user->provider_id) {
                try {
                    Log::info("[VERIFY] Syncing verification to Firebase for UID: {$user->provider_id}");
                    $firebaseService = new \App\Services\FirebaseService();
                    $firebaseService->verifyUserByUid($user->provider_id);
                } catch (\Exception $e) {
                    Log::error("[VERIFY] Firebase sync failed: " . $e->getMessage());
                }
            }

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
            'device_name' => 'required|string',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        Log::info("[REGISTER] Success for email: " . $user->email);

        event(new Registered($user));

        $token = $user->createToken($request->device_name)->plainTextToken;

        return response()->json([
            'user' => $user->fresh(),
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

        $email = strtolower(trim($request->email));
        // Case-insensitive search
        $user = User::whereRaw('LOWER(email) = ?', [$email])->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            Log::warning("[LOGIN] Failed for: {$email}");
            return response()->json([
                'message' => 'Invalid email or password.',
                'errors' => ['email' => ['Invalid email or password.']]
            ], 401);
        }

        $token = $user->createToken($request->device_name)->plainTextToken;
        $freshUser = $user->fresh();
        // Ensure is_premium is synced from subscriptions
        $freshUser->hasActivePremium();

        Log::info("[LOGIN] SUCCESS User {$user->id}.");

        // Return EXACTLY what AuthResponse.dart expects: user and token keys.
        return response()->json([
            'user' => $freshUser->fresh(),
            'token' => $token,
        ]);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json(['message' => 'Logged out.']);
    }

    public function socialLogin(Request $request)
    {
        Log::info("[SOCIAL_LOGIN] Attempting login/sync", $request->all());

        $request->validate([
            'email' => 'nullable|email',
            'name' => 'required|string',
            'provider' => 'required|string|in:google,apple,firebase_email',
            'provider_id' => 'required|string',
            'device_name' => 'required|string',
        ]);

        $user = User::where('provider', $request->provider)
            ->where('provider_id', $request->provider_id)
            ->first();

        if (!$user && $request->email) {
            Log::info("[SOCIAL_LOGIN] User not found by provider, checking email: {$request->email}");
            $user = User::where('email', $request->email)->first();
            if ($user) {
                Log::info("[SOCIAL_LOGIN] Matching email found, linking provider: {$request->provider}");
                $user->update([
                    'provider' => $request->provider,
                    'provider_id' => $request->provider_id,
                ]);
            }
        }

        if (!$user) {
            Log::info("[SOCIAL_LOGIN] Creating new user for: {$request->email}");
            $email = $request->email ?? "{$request->provider_id}@{$request->provider}.com";

            try {
                $user = User::create([
                    'name' => $request->name,
                    'email' => $email,
                    'password' => Hash::make(str()->random(24)),
                    'provider' => $request->provider,
                    'provider_id' => $request->provider_id,
                ]);
                Log::info("[SOCIAL_LOGIN] New user created: ID {$user->id}");
            } catch (\Exception $e) {
                Log::error("[SOCIAL_LOGIN] FAILED TO CREATE USER: " . $e->getMessage());
                return response()->json(['message' => 'Failed to create account in backend.'], 500);
            }
        }

        $token = $user->createToken($request->device_name)->plainTextToken;
        $freshUser = $user->fresh();

        try {
            // Ensure is_premium is synced from subscriptions
            $freshUser->hasActivePremium();
        } catch (\Exception $e) {
            Log::warning("[SOCIAL_LOGIN] Premium sync warning: " . $e->getMessage());
        }

        Log::info("[SOCIAL_LOGIN] SUCCESS User {$user->id}");

        return response()->json([
            'user' => $freshUser->fresh(),
            'token' => $token,
        ]);
    }

    /**
     * Send password reset link to user's email.
     */
    public function forgotPassword(Request $request)
    {
        Log::info('===== FORGOT PASSWORD REQUEST RECEIVED =====', [
            'email' => $request->email,
            'ip' => $request->ip(),
            'method' => $request->method(),
        ]);

        $request->validate(['email' => 'required|email']);

        $email = $request->email;
        $user = User::where('email', $email)->first();

        if (!$user) {
            Log::warning('Forgot Password: User not found', ['email' => $email]);
            return response()->json([
                'message' => 'Unable to send reset link. Please check your email address.',
                'errors' => ['email' => ['We could not find a user with that email address.']]
            ], 422);
        }

        try {
            $firebaseService = new \App\Services\FirebaseService();
            $oobLink = $firebaseService->generatePasswordResetLink($email);

            if (!$oobLink) {
                throw new \Exception("Could not generate reset link from Firebase.");
            }

            // Send branded email using the exact link generated by Firebase
            \Illuminate\Support\Facades\Mail::to($email)->send(
                new \App\Mail\ResetPasswordMail($user->name, $oobLink)
            );

            return response()->json([
                'message' => 'Branded password reset link sent to your email.',
            ]);
        } catch (\Exception $e) {
            Log::error("Custom Password Reset Failure: " . $e->getMessage());

            // Fallback to standard Laravel/Firebase flow if custom fails
            // (Optional, but good for robustness)
            return response()->json([
                'message' => 'Failed to send branded reset link. Please try again later.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Reset user's password with token.
     */
    public function resetPassword(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function (User $user, string $password) {
                $user->forceFill([
                    'password' => Hash::make($password)
                ])->setRememberToken(Str::random(60));

                $user->save();

                event(new PasswordReset($user));
            }
        );

        if ($status === Password::PASSWORD_RESET) {
            return response()->json([
                'message' => 'Password has been reset successfully.',
            ]);
        }

        return response()->json([
            'message' => 'Failed to reset password.',
            'errors' => ['email' => ['Invalid token or email address.']]
        ], 422);
    }
}
