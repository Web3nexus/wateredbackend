<?php

namespace App\Jobs;

use App\Models\Newsletter;
use App\Models\User;
use App\Notifications\NewsletterNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Notification;

class SendNewsletterJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $newsletter;

    public function __construct(Newsletter $newsletter)
    {
        $this->newsletter = $newsletter;
    }

    public function handle(): void
    {
        $users = User::query();

        if ($this->newsletter->recipients_type === 'subscribers') {
            // Default to all users since no specific subscriber column exists yet
            // but we could filter by notification preferences if needed.
            // $users->where('is_premium', true);
        }

        $users->chunk(100, function ($chunk) {
            Notification::send($chunk, new NewsletterNotification($this->newsletter->subject, $this->newsletter->content));
        });

        $this->newsletter->update([
            'status' => 'sent',
            'sent_at' => now(),
        ]);
    }
}
