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
        // Trigger if status changed to published OR is_published was toggled on
        $statusChanged = $announcement->wasChanged('status') && $announcement->status === 'published';
        $publishedToggled = $announcement->wasChanged('is_published') && $announcement->is_published;

        if ($statusChanged || $publishedToggled) {
            $this->handlePushNotification($announcement);
        }
    }

    protected function handlePushNotification(Announcement $announcement): void
    {
        $isPublished = $announcement->status === 'published' || $announcement->is_published;

        if ($isPublished && $announcement->push_enabled) {
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
