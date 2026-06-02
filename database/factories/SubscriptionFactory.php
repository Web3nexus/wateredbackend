<?php

namespace Database\Factories;

use App\Models\Subscription;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class SubscriptionFactory extends Factory
{
    protected $model = Subscription::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'plan_id' => 'paystack_monthly',
            'provider' => 'paystack',
            'platform' => 'android',
            'provider_subscription_id' => 'sub_' . fake()->uuid(),
            'amount' => 5000,
            'status' => 'active',
            'starts_at' => now(),
            'expires_at' => now()->addMonth(),
        ];
    }

    public function expired(): static
    {
        return $this->state(fn(array $attributes) => [
            'status' => 'expired',
            'expires_at' => now()->subDay(),
        ]);
    }

    public function pending(): static
    {
        return $this->state(fn(array $attributes) => [
            'status' => 'pending',
        ]);
    }

    public function apple(): static
    {
        return $this->state(fn(array $attributes) => [
            'provider' => 'apple',
            'platform' => 'ios',
            'plan_id' => 'apple_monthly',
        ]);
    }
}
