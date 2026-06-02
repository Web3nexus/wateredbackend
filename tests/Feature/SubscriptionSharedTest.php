<?php

namespace Tests\Feature;

use App\Models\GlobalSetting;
use App\Models\Subscription;
use App\Models\User;
use App\Services\SubscriptionService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SubscriptionSharedTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;
    protected SubscriptionService $service;
    protected GlobalSetting $settings;

    protected function setUp(): void
    {
        parent::setUp();

        $this->settings = GlobalSetting::create([
            'premium_monthly_amount' => 10000,
            'premium_yearly_amount' => 100000,
            'system_currency' => 'NGN',
            'currency_symbol' => '₦',
            'premium_monthly_id' => 'com.watered.premium.monthly',
            'premium_yearly_id' => 'com.watered.premium.yearly',
            'apple_shared_secret' => 'test_shared_secret',
            'paystack_secret_key' => 'sk_test_123',
        ]);

        $this->user = User::factory()->create();
        $this->service = $this->app->make(SubscriptionService::class);
    }

    /** @test */
    public function activate_premium_sets_is_premium_true()
    {
        $expiresAt = now()->addMonth();

        $this->service->activatePremium(
            user: $this->user,
            provider: 'paystack',
            providerTransactionId: 'ref_test',
            originalTransactionId: null,
            planId: 'paystack_monthly',
            expiresAt: $expiresAt,
            amount: 10000,
            platform: 'android',
        );

        $this->user->refresh();
        $this->assertTrue($this->user->is_premium);

        $this->assertDatabaseHas('subscriptions', [
            'user_id' => $this->user->id,
            'provider_subscription_id' => 'ref_test',
            'status' => 'active',
            'provider' => 'paystack',
        ]);
    }

    /** @test */
    public function activate_premium_extends_existing_active_subscription()
    {
        $fiveDaysFromNow = now()->addDays(5);

        Subscription::create([
            'user_id' => $this->user->id,
            'plan_id' => 'paystack_monthly',
            'provider' => 'paystack',
            'platform' => 'android',
            'provider_subscription_id' => 'ref_original',
            'amount' => 10000,
            'status' => 'active',
            'starts_at' => now()->subMonth(),
            'expires_at' => $fiveDaysFromNow,
        ]);

        $this->user->update(['is_premium' => true]);

        // New purchase should extend by 30 days from the old expiry
        $newExpiresAt = now()->addMonth();
        $this->service->activatePremium(
            user: $this->user,
            provider: 'paystack',
            providerTransactionId: 'ref_new',
            originalTransactionId: null,
            planId: 'paystack_monthly',
            expiresAt: $newExpiresAt,
            amount: 10000,
            platform: 'android',
        );

        $this->user->refresh();

        $latestSub = $this->user->subscription;
        $this->assertNotNull($latestSub);
        // The new expiry should be > 30 days from now (extended from old expiry)
        $this->assertTrue(
            $latestSub->expires_at->greaterThan(now()->addDays(30))
        );
    }

    /** @test */
    public function activate_premium_is_idempotent()
    {
        $expiresAt = now()->addMonth();

        $this->service->activatePremium(
            user: $this->user,
            provider: 'paystack',
            providerTransactionId: 'ref_dup',
            originalTransactionId: null,
            planId: 'paystack_monthly',
            expiresAt: $expiresAt,
        );

        $this->service->activatePremium(
            user: $this->user,
            provider: 'paystack',
            providerTransactionId: 'ref_dup',
            originalTransactionId: null,
            planId: 'paystack_monthly',
            expiresAt: $expiresAt,
        );

        $count = Subscription::where('provider_subscription_id', 'ref_dup')->count();
        $this->assertEquals(1, $count, 'Duplicate activation should not create duplicate records');
    }

    /** @test */
    public function deactivate_premium_removes_is_premium()
    {
        Subscription::create([
            'user_id' => $this->user->id,
            'plan_id' => 'paystack_monthly',
            'provider' => 'paystack',
            'platform' => 'android',
            'provider_subscription_id' => 'ref_deactivate',
            'amount' => 10000,
            'status' => 'active',
            'starts_at' => now()->subMonth(),
            'expires_at' => now()->addMonth(),
        ]);

        $this->user->update(['is_premium' => true]);

        $this->service->deactivatePremium($this->user, 'ref_deactivate');

        $this->user->refresh();
        $this->assertFalse($this->user->is_premium);

        $this->assertDatabaseHas('subscriptions', [
            'provider_subscription_id' => 'ref_deactivate',
            'status' => 'expired',
        ]);
    }

    /** @test */
    public function expired_subscription_does_not_return_is_premium()
    {
        Subscription::create([
            'user_id' => $this->user->id,
            'plan_id' => 'paystack_monthly',
            'provider' => 'paystack',
            'platform' => 'android',
            'provider_subscription_id' => 'ref_expired',
            'amount' => 10000,
            'status' => 'expired',
            'starts_at' => now()->subMonths(2),
            'expires_at' => now()->subMonth(),
        ]);

        // is_premium is false by default from factory
        $this->assertFalse($this->user->hasActivePremium());
    }

    /** @test */
    public function subscription_status_reflects_in_user_profile()
    {
        Subscription::create([
            'user_id' => $this->user->id,
            'plan_id' => 'paystack_monthly',
            'provider' => 'paystack',
            'platform' => 'android',
            'provider_subscription_id' => 'ref_profile',
            'amount' => 10000,
            'status' => 'active',
            'starts_at' => now()->subMonth(),
            'expires_at' => now()->addMonth(),
        ]);

        $this->user->update(['is_premium' => true]);

        $response = $this->actingAs($this->user, 'sanctum')
            ->getJson('/api/v1/profile');

        $response->assertStatus(200)
            ->assertJson([
                'is_premium' => true,
                'subscription_status' => 'active',
                'subscription_provider' => 'paystack',
                'subscription_plan' => 'paystack_monthly',
            ]);
    }

    /** @test */
    public function subscription_endpoint_returns_enhanced_status()
    {
        Subscription::create([
            'user_id' => $this->user->id,
            'plan_id' => 'paystack_yearly',
            'provider' => 'paystack',
            'platform' => 'android',
            'provider_subscription_id' => 'ref_status',
            'amount' => 100000,
            'status' => 'active',
            'starts_at' => now(),
            'expires_at' => now()->addYear(),
        ]);

        $this->user->update(['is_premium' => true]);

        $response = $this->actingAs($this->user, 'sanctum')
            ->getJson('/api/v1/subscription');

        $response->assertStatus(200)
            ->assertJson([
                'is_premium' => true,
                'subscription_status' => 'active',
                'subscription_provider' => 'paystack',
                'subscription_plan' => 'paystack_yearly',
            ]);
    }
}
