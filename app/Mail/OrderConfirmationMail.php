<?php

namespace App\Mail;

use App\Models\ShopOrder;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class OrderConfirmationMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public ShopOrder $order)
    {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Order Confirmation - #' . $this->order->reference,
        );
    }

    public function content(): Content
    {
        $order = $this->order->loadMissing('items.product', 'user');
        $items = $order->items;
        $totalNgn = $order->amount_kobo / 100;

        $itemsHtml = '';
        foreach ($items as $item) {
            $productName = e($item->product?->name ?? 'Product');
            $unitPrice = $item->unit_price_kobo / 100;
            $lineTotal = $unitPrice * $item->quantity;
            $itemsHtml .= "
                <tr>
                    <td style='padding: 12px; border-bottom: 1px solid rgba(234, 179, 8, 0.1);'>{$productName}</td>
                    <td style='padding: 12px; border-bottom: 1px solid rgba(234, 179, 8, 0.1); text-align: center;'>{$item->quantity}</td>
                    <td style='padding: 12px; border-bottom: 1px solid rgba(234, 179, 8, 0.1); text-align: right;'>₦" . number_format($unitPrice, 2) . "</td>
                    <td style='padding: 12px; border-bottom: 1px solid rgba(234, 179, 8, 0.1); text-align: right;'>₦" . number_format($lineTotal, 2) . "</td>
                </tr>
            ";
        }

        $customerName = e($order->user?->name ?? 'Customer');

        $body = "
            <h1>Order Confirmed!</h1>
            <p>Hi {$customerName},</p>
            <p>Thank you for your purchase! Your order has been received and is being processed.</p>

            <div style='background: rgba(234, 179, 8, 0.05); padding: 20px; border-radius: 12px; margin: 20px 0;'>
                <h2 style='color: #EAB308; font-size: 18px; margin-top: 0;'>Order Summary</h2>
                <p style='margin-bottom: 5px;'><strong>Order ID:</strong> {$order->reference}</p>
                <p style='margin-bottom: 0;'><strong>Date:</strong> {$order->created_at->format('F j, Y g:i A')}</p>
            </div>

            <table style='width: 100%; border-collapse: collapse; margin: 20px 0;'>
                <thead>
                    <tr style='background: rgba(234, 179, 8, 0.1);'>
                        <th style='padding: 12px; text-align: left; color: #EAB308;'>Item</th>
                        <th style='padding: 12px; text-align: center; color: #EAB308;'>Qty</th>
                        <th style='padding: 12px; text-align: right; color: #EAB308;'>Price</th>
                        <th style='padding: 12px; text-align: right; color: #EAB308;'>Total</th>
                    </tr>
                </thead>
                <tbody>
                    {$itemsHtml}
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan='3' style='padding: 12px; text-align: right; font-weight: bold;'>Total:</td>
                        <td style='padding: 12px; text-align: right; font-weight: bold; color: #EAB308;'>₦" . number_format($totalNgn, 2) . "</td>
                    </tr>
                </tfoot>
            </table>

            <div style='background: rgba(234, 179, 8, 0.05); padding: 20px; border-radius: 12px; margin: 20px 0;'>
                <h2 style='color: #EAB308; font-size: 18px; margin-top: 0;'>Delivery Information</h2>
                <p style='margin-bottom: 0;'>An admin will reach out to you shortly regarding delivery arrangements. If you have any questions, please contact our support team.</p>
            </div>

            <p style='margin-top: 30px; text-align: center; color: #64748B;'>We appreciate your patronage!</p>
        ";

        return new Content(
            view: 'emails.premium',
            with: ['body' => $body],
        );
    }
}
