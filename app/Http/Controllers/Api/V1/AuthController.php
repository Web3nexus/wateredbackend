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

class AuthController extends Controller
{
    public function resend(Request $request)
    {
        if ($request->user()->hasVerifiedEmail()) {
            return response()->json(['message' => 'Email already verified.']);
        }

        $request->user()->sendEmailVerificationNotification();

        return response()->json(['message' => 'Verification link sent.']);
    }

    public function verify(Request $request, $id, $hash)
    {
        $user = User::findOrFail($id);

        if (!hash_equals((string) $hash, sha1($user->getEmailForVerification()))) {
            return response()->json(['message' => 'Invalid verification link.'], 403);
        }

        if ($user->hasVerifiedEmail()) {
            return response()->json(['message' => 'Email already verified.']);
        }

        if ($user->markEmailAsVerified()) {
            event(new Verified($user));
            $user->notify(new \App\Notifications\WelcomeNotification());
        }

        return response()->json(['message' => 'Email has been verified.']);
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

        event(new Registered($user));

        $token = $user->createToken($request->device_name ?? 'flutter_app')->plainTextToken;

        return response()->json([
            'user' => $user,
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

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['Invalid credentials.'],
            ]);
        }

        $token = $user->createToken($request->device_name)->plainTextToken;

        return response()->json([
            'user' => $user,
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
            // If it's a new user and we don't have an email (rare but possible for Apple), 
            // we at least need a placeholder to avoid DB errors
            $email = $request->email ?? "{$request->provider_id}@{$request->provider}.com";

            $user = User::create([
                'name' => $request->name,
                'email' => $email,
                'password' => Hash::make(str()->random(24)),
                'provider' => $request->provider,
                'provider_id' => $request->provider_id,
            ]);
        }

        $token = $user->createToken($request->device_name)->plainTextToken;

        return response()->json([
            'user' => $user,
            'token' => $token,
        ]);
    }
}
