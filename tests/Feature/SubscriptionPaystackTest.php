<?php

namespace Tests\Feature;

use App\Models\GlobalSetting;
use App\Models\Subscription;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class SubscriptionPaystackTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;
    protected GlobalSetting $settings;

    protected function setUp(): void
    {
        parent::setUp();

        // Create test data
        $this->settings = GlobalSetting::create([
            'paystack_public_key' => 'pk_test_123',
            'paystack_secret_key' => 'sk_test_123',
            'paystack_monthly_plan_code' => 'PLN_test_monthly',
            'paystack_yearly_plan_code' => 'PLN_test_yearly',
            'premium_monthly_amount' => 10000,
            'premium_yearly_amount' => 100000,
            'premium_monthly_price' => '10,000',
            'premium_yearly_price' => '100,000',
            'system_currency' => 'NGN',
            'currency_symbol' => '₦',
            'premium_monthly_id' => 'com.watered.premium.monthly',
            'premium_yearly_id' => 'com.watered.premium.yearly',
            'apple_shared_secret' => 'test_shared_secret',
        ]);

        $this->user = User::factory()->create();
    }

    /** @test */
    public function paystack_initialize_creates_pending_subscription()
    {
        Http::fake([
            'api.paystack.co/transaction/initialize' => Http::response([
                'status' => true,
                'message' => 'Authorization URL created',
                'data' => [
                    'authorization_url' => 'https://paystack.com/checkout/ref_test123',
                    'reference' => 'ref_test123',
                    'access_code' => 'access_test123',
                ],
            ], 200),
        ]);

        $response = $this->actingAs($this->user, 'sanctum')
            ->postJson('/api/v1/subscription/initialize', [
                'is_yearly' => false,
            ]);

        $response->assertStatus(200)
            ->assertJson([
                'reference' => 'ref_test123',
            ]);

        $this->assertDatabaseHas('subscriptions', [
            'user_id' => $this->user->id,
            'provider_subscription_id' => 'ref_test123',
            'status' => 'pending',
            'plan_id' => 'paystack_monthly',
        ]);
    }

    /** @test */
    public function paystack_verify_upgrades_user_to_premium()
    {
        Http::fake([
            'api.paystack.co/transaction/verify/*' => Http::response([
                'status' => true,
                'message' => 'Verification successful',
                'data' => [
                    'status' => 'success',
                    'amount' => 1000000, // 10000 NGN in kobo
                    'currency' => 'NGN',
                    'reference' => 'ref_test123',
                    'channel' => 'card',
                    'paid_at' => now()->toIso8601String(),
                ],
            ], 200),
        ]);

        $response = $this->actingAs($this->user, 'sanctum')
            ->postJson('/api/v1/subscription/verify', [
                'provider' => 'paystack',
                'transaction_reference' => 'ref_test123',
                'is_yearly' => false,
            ]);

        $response->assertStatus(200)
            ->assertJson([
                'is_premium' => true,
                'subscription_status' => 'active',
                'subscription_provider' => 'paystack',
            ]);

        $this->assertDatabaseHas('subscriptions', [
            'user_id' => $this->user->id,
            'provider_subscription_id' => 'ref_test123',
            'status' => 'active',
            'provider' => 'paystack',
        ]);

        $this->user->refresh();
        $this->assertTrue($this->user->is_premium);
    }

    /** @test */
    public function paystack_verify_rejects_failed_payment()
    {
        Http::fake([
            'api.paystack.co/transaction/verify/*' => Http::response([
                'status' => true,
                'message' => 'Verification successful',
                'data' => [
                    'status' => 'failed',
                    'amount' => 1000000,
                    'currency' => 'NGN',
                    'reference' => 'ref_failed',
                    'gateway_response' => 'Declined',
                ],
            ], 200),
        ]);

        $response = $this->actingAs($this->user, 'sanctum')
            ->postJson('/api/v1/subscription/verify', [
                'provider' => 'paystack',
                'transaction_reference' => 'ref_failed',
                'is_yearly' => false,
            ]);

        $response->assertStatus(422);
        $this->user->refresh();
        $this->assertFalse($this->user->is_premium);
    }

    /** @test */
    public function paystack_verify_rejects_wrong_amount()
    {
        Http::fake([
            'api.paystack.co/transaction/verify/*' => Http::response([
                'status' => true,
                'message' => 'Verification successful',
                'data' => [
                    'status' => 'success',
                    'amount' => 10000, // 100 NGN - too low
                    'currency' => 'NGN',
                    'reference' => 'ref_wrong_amount',
                ],
            ], 200),
        ]);

        $response = $this->actingAs($this->user, 'sanctum')
            ->postJson('/api/v1/subscription/verify', [
                'provider' => 'paystack',
                'transaction_reference' => 'ref_wrong_amount',
                'is_yearly' => false,
            ]);

        $response->assertStatus(422);
        $this->user->refresh();
        $this->assertFalse($this->user->is_premium);
    }

    /** @test */
    public function user_cannot_verify_another_users_reference()
    {
        $otherUser = User::factory()->create();

        // Create a subscription for the other user
        Subscription::create([
            'user_id' => $otherUser->id,
            'plan_id' => 'paystack_monthly',
            'provider' => 'paystack',
            'platform' => 'android',
            'provider_subscription_id' => 'ref_other_user',
            'amount' => 10000,
            'status' => 'pending',
            'starts_at' => now(),
            'expires_at' => now()->addMonth(),
        ]);

        $response = $this->actingAs($this->user, 'sanctum')
            ->postJson('/api/v1/subscription/verify', [
                'provider' => 'paystack',
                'transaction_reference' => 'ref_other_user',
                'is_yearly' => false,
            ]);

        $response->assertStatus(403);
    }

    /** @test */
    public function paystack_webhook_upgrades_user_to_premium()
    {
        // Create pending subscription first (as initialize endpoint would)
        Subscription::create([
            'user_id' => $this->user->id,
            'plan_id' => 'paystack_monthly',
            'provider' => 'paystack',
            'platform' => 'android',
            'provider_subscription_id' => 'ref_webhook_test',
            'amount' => 10000,
            'status' => 'pending',
            'starts_at' => now(),
            'expires_at' => now()->addMonth(),
        ]);

        $payload = [
            'event' => 'charge.success',
            'data' => [
                'reference' => 'ref_webhook_test',
                'amount' => 1000000,
                'currency' => 'NGN',
                'channel' => 'card',
                'metadata' => [
                    'user_id' => (string) $this->user->id,
                    'plan_id' => 'paystack_monthly',
                    'platform' => 'android',
                    'type' => 'subscription',
                ],
                'customer' => [
                    'email' => $this->user->email,
                ],
            ],
        ];

        $signature = hash_hmac('sha512', json_encode($payload), 'sk_test_123');

        $response = $this->postJson('/api/v1/webhooks/paystack', $payload, [
            'x-paystack-signature' => $signature,
        ]);

        $response->assertStatus(200);

        $this->assertDatabaseHas('subscriptions', [
            'provider_subscription_id' => 'ref_webhook_test',
            'status' => 'active',
        ]);

        $this->user->refresh();
        $this->assertTrue($this->user->is_premium);
    }

    /** @test */
    public function paystack_webhook_is_idempotent()
    {
        // Create already active subscription
        Subscription::create([
            'user_id' => $this->user->id,
            'plan_id' => 'paystack_monthly',
            'provider' => 'paystack',
            'platform' => 'android',
            'provider_subscription_id' => 'ref_idempotent',
            'amount' => 10000,
            'status' => 'active',
            'starts_at' => now()->subMonth(),
            'expires_at' => now()->addMonth(),
        ]);

        $payload = [
            'event' => 'charge.success',
            'data' => [
                'reference' => 'ref_idempotent',
                'amount' => 1000000,
                'currency' => 'NGN',
                'channel' => 'card',
                'metadata' => [
                    'user_id' => (string) $this->user->id,
                    'plan_id' => 'paystack_monthly',
                ],
                'customer' => [
                    'email' => $this->user->email,
                ],
            ],
        ];

        $signature = hash_hmac('sha512', json_encode($payload), 'sk_test_123');

        // Send twice
        $this->postJson('/api/v1/webhooks/paystack', $payload, [
            'x-paystack-signature' => $signature,
        ]);
        $response = $this->postJson('/api/v1/webhooks/paystack', $payload, [
            'x-paystack-signature' => $signature,
        ]);

        $response->assertStatus(200);

        // Should only have one active subscription for this reference
        $count = Subscription::where('provider_subscription_id', 'ref_idempotent')
            ->where('status', 'active')
            ->count();
        $this->assertEquals(1, $count);
    }

    /** @test */
    public function paystack_webhook_rejects_invalid_signature()
    {
        $payload = [
            'event' => 'charge.success',
            'data' => [
                'reference' => 'ref_bad_sig',
                'amount' => 1000000,
                'currency' => 'NGN',
                'metadata' => [],
            ],
        ];

        $response = $this->postJson('/api/v1/webhooks/paystack', $payload, [
            'x-paystack-signature' => 'invalid_signature',
        ]);

        $response->assertStatus(400);
    }

    /** @test */
    public function paystack_webhook_handles_unknown_reference_safely()
    {
        $payload = [
            'event' => 'charge.success',
            'data' => [
                'reference' => 'ref_unknown',
                'amount' => 1000000,
                'currency' => 'NGN',
                'channel' => 'card',
                'metadata' => [],
                'customer' => [
                    'email' => 'nonexistent@test.com',
                ],
            ],
        ];

        $signature = hash_hmac('sha512', json_encode($payload), 'sk_test_123');

        $response = $this->postJson('/api/v1/webhooks/paystack', $payload, [
            'x-paystack-signature' => $signature,
        ]);

        $response->assertStatus(200); // Webhook should return 200 even if no action taken
    }
}
