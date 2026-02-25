<?php

namespace App\Observers;

use App\Models\Announcement;
use App\Jobs\SendAnnouncementPushNotification;
use Illuminate\Support\Facades\Log;

class AnnouncementObserver
{
    /**
     * Handle the Announcement "created" event.
     */
    public function created(Announcement $announcement): void
    {
        $this->handlePushNotification($announcement);
    }

    /**
     * Handle the Announcement "updated" event.
     */
    public function updated(Announcement $announcement): void
    {
        // Only trigger if status changed to published
        if ($announcement->wasChanged('status') && $announcement->status === 'published') {
            $this->handlePushNotification($announcement);
        }
    }

    protected function handlePushNotification(Announcement $announcement): void
    {
        if ($announcement->status === 'published' && $announcement->push_enabled) {
            // Check if it's scheduled for the future
            if ($announcement->scheduled_at && $announcement->scheduled_at->isFuture()) {
                // If there's a scheduler checking for these, it will be handled there.
                // For now, we only push instantly published ones in the observer.
                Log::info("Announcement scheduled for future. Push delayed until scheduler picks it up.");
                return;
            }

            Log::info("Dispatching push notification for Announcement ID: " . $announcement->id);
            SendAnnouncementPushNotification::dispatch($announcement);

            // Disable push_enabled so we don't spam if they edit it again
            $announcement->push_enabled = false;
            $announcement->saveQuietly();
        }
    }
}
