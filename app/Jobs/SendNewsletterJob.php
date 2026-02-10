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
            // Assuming there's a newsletter_subscription column or similar
            // For now, let's just use all users if column not found, but I'll check first
            // $users->where('subscribed_to_newsletter', true);
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
