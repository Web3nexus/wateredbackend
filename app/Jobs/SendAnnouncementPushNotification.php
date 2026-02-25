<?php

namespace App\Jobs;

use App\Models\Announcement;
use App\Models\User;
use App\Services\FirebaseNotificationService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SendAnnouncementPushNotification implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $announcement;

    /**
     * Create a new job instance.
     */
    public function __construct(Announcement $announcement)
    {
        $this->announcement = $announcement;
    }

    /**
     * Execute the job.
     */
    public function handle(FirebaseNotificationService $firebaseService): void
    {
        Log::info("Processing push notification for Announcement ID: " . $this->announcement->id);

        $query = User::where('push_notifications', true);

        // Filter by target audience if necessary
        if ($this->announcement->target_audience === 'premium') {
            $query->where('is_premium', true);
        }

        $title = $this->announcement->title;
        $body = strip_tags($this->announcement->content);

        // Truncate body if it's too long
        if (strlen($body) > 120) {
            $body = substr($body, 0, 117) . '...';
        }

        $data = [
            'type' => 'announcement',
            'id' => (string) $this->announcement->id,
            'link_url' => $this->announcement->link_url ?? '',
        ];

        // Chunk users to prevent memory exhaustion
        $query->chunk(500, function ($users) use ($firebaseService, $title, $body, $data) {
            $tokens = [];
            foreach ($users as $user) {
                /** @var \App\Models\User $user */
                // Collect all tokens for this chunk
                $userTokens = $user->fcmTokens()->pluck('token')->toArray();
                $tokens = array_merge($tokens, $userTokens);
            }

            if (!empty($tokens)) {
                // Firebase payload limit is 1000 tokens per request for legacy API
                foreach (array_chunk($tokens, 1000) as $tokenChunk) {
                    $firebaseService->sendToTokens($tokenChunk, $title, $body, $data);
                }
            }
        });

        Log::info("Completed push notification for Announcement ID: " . $this->announcement->id);
    }
}
