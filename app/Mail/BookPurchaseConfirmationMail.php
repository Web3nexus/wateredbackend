<?php

namespace App\Mail;

use App\Models\BookPurchase;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class BookPurchaseConfirmationMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public BookPurchase $purchase)
    {
        $this->purchase->loadMissing('textCollection', 'user');
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Book Purchase Confirmation - ' . ($this->purchase->textCollection?->name ?? 'Sacred Book'),
        );
    }

    public function content(): Content
    {
        $purchase = $this->purchase;
        $book = $purchase->textCollection;
        $bookName = e($book->name ?? 'Sacred Book');
        $customerName = e($purchase->user?->name ?? 'Customer');

        $body = "
            <h1>Purchase Confirmed!</h1>
            <p>Hi {$customerName},</p>
            <p>Thank you for purchasing <strong>{$bookName}</strong>! Your payment has been confirmed and the book is now unlocked in your library.</p>

            <div style='background: rgba(234, 179, 8, 0.05); padding: 20px; border-radius: 12px; margin: 20px 0;'>
                <h2 style='color: #EAB308; font-size: 18px; margin-top: 0;'>Purchase Details</h2>
                <p style='margin-bottom: 5px;'><strong>Book:</strong> {$bookName}</p>
                <p style='margin-bottom: 5px;'><strong>Reference:</strong> {$purchase->reference}</p>
                <p style='margin-bottom: 5px;'><strong>Amount Paid:</strong> ₦" . number_format($purchase->amount_paid, 2) . "</p>
                <p style='margin-bottom: 0;'><strong>Date:</strong> {$purchase->created_at->format('F j, Y g:i A')}</p>
            </div>

            <div class='button-wrapper'>
                <a href='" . config('app.url') . "/library' class='button'>Read Now</a>
            </div>

            <p style='margin-top: 30px; text-align: center; color: #64748B;'>May this sacred knowledge enrich your journey.</p>
        ";

        return new Content(
            view: 'emails.premium',
            with: ['body' => $body],
        );
    }
}
