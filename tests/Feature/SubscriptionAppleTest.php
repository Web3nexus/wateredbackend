<?php

namespace Tests\Feature;

use App\Models\GlobalSetting;
use App\Models\Subscription;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class SubscriptionAppleTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;
    protected GlobalSetting $settings;

    protected function setUp(): void
    {
        parent::setUp();

        $this->settings = GlobalSetting::create([
            'paystack_public_key' => 'pk_test_123',
            'paystack_secret_key' => 'sk_test_123',
            'premium_monthly_amount' => 5000,
            'premium_yearly_amount' => 50000,
            'system_currency' => 'NGN',
            'currency_symbol' => '₦',
            'premium_monthly_id' => 'com.watered.premium.monthly',
            'premium_yearly_id' => 'com.watered.premium.yearly',
            'apple_shared_secret' => 'test_shared_secret',
        ]);

        $this->user = User::factory()->create();
    }

    /** @test */
    public function apple_verify_upgrades_user_to_premium()
    {
        $expiresDateMs = now()->addMonth()->timestamp * 1000;

        Http::fake([
            'buy.itunes.apple.com/verifyReceipt' => Http::response([
                'status' => 0,
                'receipt' => [
                    'in_app' => [],
                ],
                'latest_receipt_info' => [
                    [
                        'product_id' => 'com.watered.premium.monthly',
                        'transaction_id' => '1000000000000001',
                        'original_transaction_id' => '1000000000000000',
                        'expires_date_ms' => (string) $expiresDateMs,
                        'purchase_date_ms' => (string) now()->timestamp * 1000,
                    ],
                ],
                'latest_receipt' => 'base64_encoded_receipt_data',
            ], 200),
        ]);

        $response = $this->actingAs($this->user, 'sanctum')
            ->postJson('/api/v1/subscription/verify', [
                'plan_id' => 'com.watered.premium.monthly',
                'provider' => 'apple',
                'receipt_data' => 'base64_encoded_receipt_data',
            ]);

        $response->assertStatus(200)
            ->assertJson([
                'is_premium' => true,
                'subscription_status' => 'active',
                'subscription_provider' => 'apple',
            ]);

        $this->assertDatabaseHas('subscriptions', [
            'user_id' => $this->user->id,
            'provider_subscription_id' => '1000000000000001',
            'original_transaction_id' => '1000000000000000',
            'status' => 'active',
            'provider' => 'apple',
            'plan_id' => 'apple_monthly',
        ]);

        $this->user->refresh();
        $this->assertTrue($this->user->is_premium);
    }

    /** @test */
    public function apple_verify_rejects_expired_subscription()
    {
        $expiresDateMs = now()->subDay()->timestamp * 1000;

        Http::fake([
            'buy.itunes.apple.com/verifyReceipt' => Http::response([
                'status' => 0,
                'receipt' => ['in_app' => []],
                'latest_receipt_info' => [
                    [
                        'product_id' => 'com.watered.premium.monthly',
                        'transaction_id' => '1000000000000002',
                        'original_transaction_id' => '1000000000000000',
                        'expires_date_ms' => (string) $expiresDateMs,
                        'purchase_date_ms' => (string) now()->subMonth()->timestamp * 1000,
                    ],
                ],
                'latest_receipt' => 'base64_data',
            ], 200),
        ]);

        $response = $this->actingAs($this->user, 'sanctum')
            ->postJson('/api/v1/subscription/verify', [
                'plan_id' => 'com.watered.premium.monthly',
                'provider' => 'apple',
                'receipt_data' => 'base64_data',
            ]);

        $response->assertStatus(422);
        $this->user->refresh();
        $this->assertFalse($this->user->is_premium);
    }

    /** @test */
    public function apple_verify_rejects_invalid_receipt()
    {
        Http::fake([
            'buy.itunes.apple.com/verifyReceipt' => Http::response([
                'status' => 21002,
                'message' => 'Malformed receipt data',
            ], 200),
        ]);

        $response = $this->actingAs($this->user, 'sanctum')
            ->postJson('/api/v1/subscription/verify', [
                'plan_id' => 'com.watered.premium.monthly',
                'provider' => 'apple',
                'receipt_data' => 'bad_data',
            ]);

        $response->assertStatus(422);
        $this->user->refresh();
        $this->assertFalse($this->user->is_premium);
    }

    /** @test */
    public function apple_verify_rejects_wrong_product_id()
    {
        $expiresDateMs = now()->addMonth()->timestamp * 1000;

        Http::fake([
            'buy.itunes.apple.com/verifyReceipt' => Http::response([
                'status' => 0,
                'receipt' => ['in_app' => []],
                'latest_receipt_info' => [
                    [
                        'product_id' => 'com.watered.premium.yearly', // Receipt says yearly
                        'transaction_id' => '1000000000000003',
                        'original_transaction_id' => '1000000000000000',
                        'expires_date_ms' => (string) $expiresDateMs,
                        'purchase_date_ms' => (string) now()->timestamp * 1000,
                    ],
                ],
                'latest_receipt' => 'base64_data',
            ], 200),
        ]);

        // Client sends monthly but receipt shows yearly
        $response = $this->actingAs($this->user, 'sanctum')
            ->postJson('/api/v1/subscription/verify', [
                'plan_id' => 'com.watered.premium.monthly',
                'provider' => 'apple',
                'receipt_data' => 'base64_data',
            ]);

        $response->assertStatus(422);
    }

    /** @test */
    public function apple_webhook_did_renew_extends_subscription()
    {
        // Create existing subscription first
        $subscription = Subscription::factory()
            ->apple()
            ->create([
                'user_id' => $this->user->id,
                'provider_subscription_id' => '1000000000000001',
                'original_transaction_id' => '1000000000000000',
                'expires_at' => now()->addDay(),
            ]);

        $this->user->update(['is_premium' => true]);

        $newExpiresMs = now()->addMonth()->addDay()->timestamp * 1000;

        $response = $this->postJson('/api/v1/webhooks/apple', [
            'notification_type' => 'DID_RENEW',
            'latest_receipt_info' => [
                'original_transaction_id' => '1000000000000000',
                'transaction_id' => '1000000000000005',
                'product_id' => 'com.watered.premium.monthly',
                'expires_date_ms' => (string) $newExpiresMs,
            ],
        ]);

        $response->assertStatus(200);

        $this->user->refresh();
        $this->assertTrue($this->user->is_premium);
    }

    /** @test */
    public function apple_webhook_expired_disables_premium()
    {
        Subscription::factory()
            ->apple()
            ->create([
                'user_id' => $this->user->id,
                'provider_subscription_id' => '1000000000000001',
                'original_transaction_id' => '1000000000000000',
                'expires_at' => now()->addDay(),
            ]);

        $this->user->update(['is_premium' => true]);

        $response = $this->postJson('/api/v1/webhooks/apple', [
            'notification_type' => 'EXPIRED',
            'latest_receipt_info' => [
                'original_transaction_id' => '1000000000000000',
                'transaction_id' => '1000000000000001',
                'product_id' => 'com.watered.premium.monthly',
                'expires_date_ms' => (string) now()->subDay()->timestamp * 1000,
            ],
        ]);

        $response->assertStatus(200);

        $this->assertDatabaseHas('subscriptions', [
            'provider_subscription_id' => '1000000000000001',
            'status' => 'expired',
        ]);

        $this->user->refresh();
        $this->assertFalse($this->user->is_premium);
    }

    /** @test */
    public function apple_webhook_revoke_disables_premium()
    {
        Subscription::factory()
            ->apple()
            ->create([
                'user_id' => $this->user->id,
                'provider_subscription_id' => '1000000000000001',
                'original_transaction_id' => '1000000000000000',
                'expires_at' => now()->addMonth(),
            ]);

        $this->user->update(['is_premium' => true]);

        $response = $this->postJson('/api/v1/webhooks/apple', [
            'notification_type' => 'REVOKE',
            'latest_receipt_info' => [
                'original_transaction_id' => '1000000000000000',
                'transaction_id' => '1000000000000001',
                'product_id' => 'com.watered.premium.monthly',
                'expires_date_ms' => (string) now()->addMonth()->timestamp * 1000,
            ],
        ]);

        $response->assertStatus(200);

        $this->assertDatabaseHas('subscriptions', [
            'provider_subscription_id' => '1000000000000001',
            'status' => 'expired',
        ]);

        $this->user->refresh();
        $this->assertFalse($this->user->is_premium);
    }

    /** @test */
    public function apple_webhook_is_idempotent()
    {
        Subscription::factory()
            ->apple()
            ->create([
                'user_id' => $this->user->id,
                'provider_subscription_id' => '1000000000000001',
                'original_transaction_id' => '1000000000000000',
                'expires_at' => now()->addDay(),
            ]);

        $this->user->update(['is_premium' => true]);

        $newExpiresMs = now()->addMonth()->addDay()->timestamp * 1000;

        $payload = [
            'notification_type' => 'DID_RENEW',
            'latest_receipt_info' => [
                'original_transaction_id' => '1000000000000000',
                'transaction_id' => '1000000000000002',
                'product_id' => 'com.watered.premium.monthly',
                'expires_date_ms' => (string) $newExpiresMs,
            ],
        ];

        // Send twice
        $this->postJson('/api/v1/webhooks/apple', $payload);
        $response = $this->postJson('/api/v1/webhooks/apple', $payload);

        $response->assertStatus(200);

        $count = Subscription::where('original_transaction_id', '1000000000000000')
            ->where('status', 'active')
            ->count();
        $this->assertEquals(1, $count);
    }

    /** @test */
    public function user_cannot_verify_another_users_apple_transaction()
    {
        $otherUser = User::factory()->create();
        $expiresDateMs = now()->addMonth()->timestamp * 1000;

        // Create subscription for other user
        Subscription::create([
            'user_id' => $otherUser->id,
            'plan_id' => 'apple_monthly',
            'provider' => 'apple',
            'platform' => 'ios',
            'provider_subscription_id' => '1000000000000001',
            'original_transaction_id' => '1000000000000000',
            'amount' => 5000,
            'status' => 'active',
            'starts_at' => now()->subMonth(),
            'expires_at' => now()->addMonth(),
        ]);

        Http::fake([
            'buy.itunes.apple.com/verifyReceipt' => Http::response([
                'status' => 0,
                'receipt' => ['in_app' => []],
                'latest_receipt_info' => [
                    [
                        'product_id' => 'com.watered.premium.monthly',
                        'transaction_id' => '1000000000000001',
                        'original_transaction_id' => '1000000000000000',
                        'expires_date_ms' => (string) $expiresDateMs,
                        'purchase_date_ms' => (string) now()->timestamp * 1000,
                    ],
                ],
                'latest_receipt' => 'base64_data',
            ], 200),
        ]);

        $response = $this->actingAs($this->user, 'sanctum')
            ->postJson('/api/v1/subscription/verify', [
                'plan_id' => 'com.watered.premium.monthly',
                'provider' => 'apple',
                'receipt_data' => 'base64_data',
            ]);

        $response->assertStatus(403);
    }
}
