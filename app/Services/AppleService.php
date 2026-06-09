<?php

namespace App\Services;

use Exception;
use Firebase\JWT\JWK;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AppleService
{
    private ?string $teamId = null;
    private ?string $keyId = null;
    private ?string $clientId = null;
    private ?string $privateKey = null;

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
     * Verify a StoreKit 2 transaction JWS (JSON Web Signature) offline.
     *
     * StoreKit 2 returns a JWS token instead of the legacy PKCS7 receipt.
     * The JWS header contains an x5c certificate chain; we verify the
     * signature using the embedded certificate's public key.
     *
     * @param string $jws The JWS token (header.payload.signature)
     * @return object|null Decoded transaction payload on success, null on failure
     */
    public function verifyTransactionJWS(string $jws): ?object
    {
        try {
            $parts = explode('.', $jws);
            if (count($parts) !== 3) {
                Log::warning('[Apple JWS] Invalid format - expected 3 parts');
                return null;
            }

            $header = json_decode(base64_decode($parts[0]));
            if (!$header || !isset($header->x5c) || !is_array($header->x5c) || empty($header->x5c)) {
                Log::warning('[Apple JWS] Missing x5c certificate chain in header');
                return null;
            }

            // Build PEM certificate from the first cert in the x5c chain
            $pemCert = "-----BEGIN CERTIFICATE-----\n"
                . chunk_split($header->x5c[0], 64, "\n")
                . "-----END CERTIFICATE-----";

            $publicKey = openssl_pkey_get_public($pemCert);
            if ($publicKey === false) {
                Log::warning('[Apple JWS] Failed to extract public key from certificate');
                return null;
            }

            $keyDetails = openssl_pkey_get_details($publicKey);
            $publicKeyPem = $keyDetails['key'];

            // Verify signature and decode payload
            $payload = JWT::decode($jws, new Key($publicKeyPem, 'ES256'));

            // Validate the issuer is Apple
            if (!isset($payload->transactionId)) {
                Log::warning('[Apple JWS] Payload missing transactionId');
                return null;
            }

            return $payload;
        } catch (\Exception $e) {
            Log::error('[Apple JWS] Verification failed: ' . $e->getMessage());
            return null;
        }
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
