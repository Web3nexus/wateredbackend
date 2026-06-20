<?php

namespace App\Mail;

use App\Models\Subscription;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class SubscriptionConfirmationMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Subscription $subscription)
    {
        $this->subscription->loadMissing('user');
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Premium Subscription Confirmed - Watered',
        );
    }

    public function content(): Content
    {
        $sub = $this->subscription;
        $user = $sub->user;
        $customerName = e($user->name ?? 'Valued Member');
        $planName = match (true) {
            str_contains($sub->plan_id, 'yearly') => 'Yearly Premium',
            str_contains($sub->plan_id, 'monthly') => 'Monthly Premium',
            default => 'Premium',
        };
        $expiresAt = $sub->expires_at ? $sub->expires_at->format('F j, Y') : 'Ongoing';
        $provider = ucfirst($sub->provider);

        $body = "
            <h1>Welcome to Premium!</h1>
            <p>Hi {$customerName},</p>
            <p>Your <strong>{$planName}</strong> subscription is now active. You have full access to all premium content, including sacred texts, audio, teachings, and more.</p>

            <div style='background: rgba(234, 179, 8, 0.05); padding: 20px; border-radius: 12px; margin: 20px 0;'>
                <h2 style='color: #EAB308; font-size: 18px; margin-top: 0;'>Subscription Details</h2>
                <p style='margin-bottom: 5px;'><strong>Plan:</strong> {$planName}</p>
                <p style='margin-bottom: 5px;'><strong>Provider:</strong> {$provider}</p>
                <p style='margin-bottom: 5px;'><strong>Reference:</strong> {$sub->provider_subscription_id}</p>
                <p style='margin-bottom: 0;'><strong>Valid Until:</strong> {$expiresAt}</p>
            </div>

            <div class='button-wrapper'>
                <a href='" . config('app.url') . "/library' class='button'>Explore Premium Content</a>
            </div>

            <p style='margin-top: 30px; text-align: center; color: #64748B;'>Thank you for supporting Watered. May your practice deepen and flourish.</p>
        ";

        return new Content(
            view: 'emails.premium',
            with: ['body' => $body],
        );
    }
}
