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
        $projectId = config('services.fcm.project_id');
        $credentialsPath = config('services.fcm.credentials_path');

        if (!file_exists($credentialsPath)) {
            Log::error("FCM credentials file not found at: $credentialsPath. Please upload the service account JSON and set FCM_CREDENTIALS_PATH in .env");
            return false;
        }

        try {
            // 1. Get OAuth2 Access Token using google/auth
            $credentials = new \Google\Auth\Credentials\ServiceAccountCredentials(
                'https://www.googleapis.com/auth/cloud-platform',
                $credentialsPath
            );
            $token = $credentials->fetchAuthToken();
            $accessToken = $token['access_token'];

            // 2. Send to each token (HTTP v1 does not support broadcasting to multiple tokens in one request like Legacy)
            // Note: For massive scale, you'd use multicast or background jobs for each token
            foreach ($tokens as $registrationToken) {
                $response = Http::withToken($accessToken)
                    ->post("https://fcm.googleapis.com/v1/projects/$projectId/messages:send", [
                        'message' => [
                            'token' => $registrationToken,
                            'notification' => [
                                'title' => $title,
                                'body' => $body,
                            ],
                            'data' => array_merge($data, [
                                'click_action' => 'FLUTTER_NOTIFICATION_CLICK',
                            ]),
                            'android' => [
                                'priority' => 'high',
                                'notification' => [
                                    'channel_id' => 'watered_notifications',
                                    'sound' => 'default',
                                ],
                            ],
                            'apns' => [
                                'payload' => [
                                    'aps' => [
                                        'sound' => 'default',
                                        'badge' => 1,
                                    ],
                                ],
                            ],
                        ],
                    ]);

                if ($response->failed()) {
                    Log::error("FCM HTTP v1 failed for token $registrationToken: " . $response->body());
                }
            }

            return true;
        } catch (\Exception $e) {
            Log::error('FCM HTTP v1 Error: ' . $e->getMessage());
            return false;
        }
    }
}
