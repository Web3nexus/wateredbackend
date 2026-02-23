<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class FirebaseService
{
    /**
     * Generate a password reset link/code for a given email.
     * Uses the Identity Toolkit API with a Service Account token.
     */
    public function generatePasswordResetCode(string $email)
    {
        try {
            $accessToken = $this->getAccessToken();
            if (!$accessToken) {
                throw new \Exception("Failed to get Firebase Access Token");
            }

            $projectId = 'watered-c14bb';
            $response = Http::withToken($accessToken)
                ->post("https://identitytoolkit.googleapis.com/v1/projects/{$projectId}/accounts:sendOobCode", [
                    'requestType' => 'PASSWORD_RESET',
                    'email' => $email,
                    'returnOobCode' => true,
                ]);

            if ($response->failed()) {
                Log::error("Firebase OOB Code generation failed with status " . $response->status() . ": " . $response->body());
                return null;
            }

            return $response->json('oobCode');
        } catch (\Exception $e) {
            Log::error("FirebaseService Error: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Generate an OAuth2 access token for the service account.
     * Implements a simple JWT-based exchange for Google APIs.
     */
    protected function getAccessToken()
    {
        $serviceAccountFile = base_path('watered-c14bb-firebase-adminsdk-fbsvc-cf02191074.json');
        if (!file_exists($serviceAccountFile)) {
            Log::error("Firebase Service Account file not found at: " . $serviceAccountFile);
            return null;
        }

        $serviceAccount = json_decode(file_get_contents($serviceAccountFile), true);

        $now = time();
        $header = json_encode(['alg' => 'RS256', 'typ' => 'JWT']);
        $payload = json_encode([
            'iss' => $serviceAccount['client_email'],
            'scope' => 'https://www.googleapis.com/auth/cloud-platform https://www.googleapis.com/auth/firebase.auth',
            'aud' => 'https://oauth2.googleapis.com/token',
            'iat' => $now,
            'exp' => $now + 3600,
        ]);

        $base64UrlHeader = $this->base64UrlEncode($header);
        $base64UrlPayload = $this->base64UrlEncode($payload);

        $signature = '';
        openssl_sign($base64UrlHeader . "." . $base64UrlPayload, $signature, $serviceAccount['private_key'], 'SHA256');
        $base64UrlSignature = $this->base64UrlEncode($signature);

        $jwt = $base64UrlHeader . "." . $base64UrlPayload . "." . $base64UrlSignature;

        $response = Http::asForm()->post('https://oauth2.googleapis.com/token', [
            'grant_type' => 'urn:ietf:params:oauth:grant-type:jwt-bearer',
            'assertion' => $jwt,
        ]);

        if ($response->failed()) {
            Log::error("Failed to exchange JWT for access token: " . $response->body());
            return null;
        }

        return $response->json('access_token');
    }

    protected function base64UrlEncode($data)
    {
        return str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($data));
    }
}
