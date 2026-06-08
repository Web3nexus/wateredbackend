<?php

namespace App\Console\Commands;

use App\Models\Subscription;
use App\Services\SubscriptionService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class ExpireSubscriptions extends Command
{
    protected $signature = 'subscriptions:expire';

    protected $description = 'Mark subscriptions with past expires_at as expired and downgrade users';

    public function handle(SubscriptionService $subscriptionService)
    {
        $count = 0;

        Subscription::where('status', 'active')
            ->where('expires_at', '<=', now())
            ->chunk(100, function ($subscriptions) use (&$count, $subscriptionService) {
                foreach ($subscriptions as $subscription) {
                    try {
                        if (!$subscription->user) {
                            $subscription->update(['status' => 'expired']);
                            $count++;
                            continue;
                        }
                        $subscriptionService->deactivatePremium(
                            user: $subscription->user,
                            providerTransactionId: $subscription->provider_subscription_id,
                        );
                        $count++;
                    } catch (\Exception $e) {
                        Log::error("ExpireSubscriptions: failed to expire subscription {$subscription->id}", [
                            'error' => $e->getMessage(),
                        ]);
                    }
                }
            });

        $this->info("Expired {$count} subscription(s).");
        Log::info("ExpireSubscriptions: Expired {$count} subscription(s).");
    }
}
