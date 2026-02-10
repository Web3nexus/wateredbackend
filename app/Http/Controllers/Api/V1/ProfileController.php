<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rules\File;

class ProfileController extends Controller
{
    /**
     * Get the authenticated user's profile.
     */
    public function show(Request $request)
    {
        $user = $request->user();
        return response()->json([
            'user' => $user->fresh(),
            'is_verified' => $user->hasVerifiedEmail(),
            'verified' => $user->hasVerifiedEmail(),
            'email_verified' => $user->hasVerifiedEmail(),
        ]);
    }

    /**
     * Update the authenticated user's profile.
     */
    public function update(Request $request)
    {
        $user = $request->user();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
        ]);

        $user->update($validated);

        return response()->json([
            'message' => 'Profile updated successfully.',
            'user' => $user->fresh(),
            'is_verified' => $user->hasVerifiedEmail(),
            'verified' => $user->hasVerifiedEmail(),
            'email_verified' => $user->hasVerifiedEmail(),
        ]);
    }

    /**
     * Upload and update user's profile photo.
     */
    public function uploadPhoto(Request $request)
    {
        $request->validate([
            'photo' => [
                'required',
                File::image()
                    ->min(1)
                    ->max(2048), // 2MB Max
            ],
        ]);

        $user = $request->user();

        // Delete old photo if exists
        if ($user->profile_photo_url) {
            /** @var \Illuminate\Filesystem\FilesystemAdapter $disk */
            $disk = Storage::disk('public');
            $oldPath = str_replace($disk->url(''), '', $user->profile_photo_url);
            $disk->delete($oldPath);
        }

        // Store new photo
        /** @var \Illuminate\Filesystem\FilesystemAdapter $disk */
        $disk = Storage::disk('public');
        $path = $request->file('photo')->store('profile-photos', 'public');
        $url = $disk->url($path);

        $user->update(['profile_photo_url' => $url]);

        return response()->json([
            'message' => 'Profile photo uploaded successfully.',
            'profile_photo_url' => $url,
            'user' => $user->fresh(),
            'is_verified' => $user->hasVerifiedEmail(),
            'verified' => $user->hasVerifiedEmail(),
            'email_verified' => $user->hasVerifiedEmail(),
        ]);
    }
}
