<?php

namespace App\Services;

use Exception;
use Firebase\JWT\JWK;
use Firebase\JWT\JWT;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AppleService
{
    private string $teamId;
    private string $keyId;
    private string $clientId;
    private string $privateKey;

    public function __construct()
    {
        $this->teamId = config('services.apple.team_id');
        $this->keyId = config('services.apple.key_id');
        $this->clientId = config('services.apple.client_id');
        $this->privateKey = config('services.apple.private_key');
    }

    public function getClientId(): string
    {
        return $this->clientId;
    }

    /**
     * Verify an Apple identity token (JWT) server-side.
     *
     * Steps:
     * 1. Fetch Apple's public keys from https://appleid.apple.com/auth/keys
     * 2. Find the key matching the `kid` in the JWT header
     * 3. Verify the signature using ES256
     * 4. Validate standard claims (iss, aud, exp)
     *
     * @param string $identityToken The raw JWT from Apple
     * @return array|null Decoded payload on success, null on failure
     */
    public function verifyIdentityToken(string $identityToken): ?array
    {
        try {
            $keys = $this->fetchPublicKeys();
            $payload = JWT::decode($identityToken, $keys);

            if ($payload->iss !== 'https://appleid.apple.com') {
                Log::warning('Apple token validation failed: invalid issuer', ['iss' => $payload->iss ?? null]);
                return null;
            }

            if ($payload->aud !== $this->clientId) {
                Log::warning('Apple token validation failed: invalid audience', ['aud' => $payload->aud ?? null]);
                return null;
            }

            if (!isset($payload->sub)) {
                Log::warning('Apple token validation failed: missing subject');
                return null;
            }

            return (array) $payload;
        } catch (Exception $e) {
            Log::error('Apple identity token verification failed: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Fetch Apple's public keys from their JWKS endpoint.
     */
    private function fetchPublicKeys(): array
    {
        $cacheKey = 'apple_public_keys';

        $keys = Cache::remember($cacheKey, 86400, function () {
            $response = Http::get('https://appleid.apple.com/auth/keys');

            if ($response->failed()) {
                throw new Exception('Failed to fetch Apple public keys: ' . $response->body());
            }

            return $response->json();
        });

        return JWK::parseKeySet($keys);
    }

    /**
     * Generate a client secret JWT for Apple's REST API
     * (used for subscription notifications and token validation)
     */
    public function generateClientSecret(): string
    {
        $now = time();

        $payload = [
            'iss' => $this->teamId,
            'iat' => $now,
            'exp' => $now + 86400 * 180,
            'aud' => 'https://appleid.apple.com',
            'sub' => $this->clientId,
        ];

        return JWT::encode($payload, $this->privateKey, 'ES256', $this->keyId);
    }
}
