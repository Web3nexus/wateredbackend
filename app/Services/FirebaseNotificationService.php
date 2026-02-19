<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class FirebaseNotificationService
{
    /**
     * Send a push notification to a specific user.
     */
    public function sendToUser(User $user, string $title, string $body, array $data = [])
    {
        if (!$user->push_notifications) {
            return false;
        }

        $tokens = $user->fcmTokens()->pluck('token')->toArray();

        if (empty($tokens)) {
            return false;
        }

        return $this->sendToTokens($tokens, $title, $body, $data);
    }

    /**
     * Send notification to specific FCM tokens.
     */
    public function sendToTokens(array $tokens, string $title, string $body, array $data = [])
    {
        $serverKey = config('services.fcm.server_key');

        if (!$serverKey) {
            Log::warning('FCM server key is not configured.');
            return false;
        }

        // Using Legacy API for simplicity as HTTP v1 requires OAuth2 tokens which are complex to generate without a package
        // However, many older projects still use the server key method.
        $response = Http::withHeaders([
            'Authorization' => 'key=' . $serverKey,
            'Content-Type' => 'application/json',
        ])->post('https://fcm.googleapis.com/fcm/send', [
                    'registration_ids' => $tokens,
                    'notification' => [
                        'title' => $title,
                        'body' => $body,
                        'sound' => 'default',
                    ],
                    'data' => $data,
                    'priority' => 'high',
                ]);

        if ($response->failed()) {
            Log::error('FCM Notification failed: ' . $response->body());
            return false;
        }

        return $response->json();
    }
}
