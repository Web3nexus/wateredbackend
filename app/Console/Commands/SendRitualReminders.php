<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Ritual;
use App\Models\User;
use App\Services\FirebaseNotificationService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class SendRitualReminders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'rituals:send-reminders';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send push notifications for upcoming rituals';

    protected $notificationService;

    public function __construct(FirebaseNotificationService $notificationService)
    {
        parent::__construct();
        $this->notificationService = $notificationService;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $now = Carbon::now();
        // Check for rituals happening exactly now (rounded to the minute)
        $currentMinute = $now->format('H:i');

        $rituals = Ritual::where('time_of_day', 'like', $currentMinute . '%')
            ->get();

        if ($rituals->isEmpty()) {
            return;
        }

        foreach ($rituals as $ritual) {
            $this->info("Processing ritual: {$ritual->title}");

            // Find users who have enabled ritual reminders
            User::where('ritual_reminders', true)
                ->whereHas('fcmTokens')
                ->chunk(100, function ($users) use ($ritual) {
                    foreach ($users as $user) {
                        $tokens = $user->fcmTokens->pluck('token')->toArray();
                        if (!empty($tokens)) {
                            $this->notificationService->sendToTokens(
                                $tokens,
                                "Sacred Ritual: {$ritual->title}",
                                "It is time for your scheduled ritual: {$ritual->title}.",
                                ['type' => 'ritual', 'id' => (string) $ritual->id]
                            );
                        }
                    }
                });
        }
    }
}
