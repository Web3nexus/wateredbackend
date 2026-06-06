<?php

namespace App\Console\Commands;

use App\Models\Subscription;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class ExpireSubscriptions extends Command
{
    protected $signature = 'subscriptions:expire';

    protected $description = 'Mark subscriptions with past expires_at as expired and downgrade users';

    public function handle()
    {
        $count = 0;

        Subscription::where('status', 'active')
            ->where('expires_at', '<=', now())
            ->chunk(100, function ($subscriptions) use (&$count) {
                foreach ($subscriptions as $subscription) {
                    $subscription->update(['status' => 'expired']);

                    $user = $subscription->user;
                    if ($user && !$user->subscriptions()->where('status', 'active')->exists()) {
                        $user->update(['is_premium' => false]);
                    }

                    $count++;
                }
            });

        $this->info("Expired {$count} subscription(s).");
        Log::info("ExpireSubscriptions: Expired {$count} subscription(s).");
    }
}
