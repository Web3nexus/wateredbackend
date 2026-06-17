<?php

namespace App\Mail;

use App\Models\ShopOrder;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use App\Models\AdminUser;
use Illuminate\Support\Facades\Mail;

class AdminOrderNotificationMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public ShopOrder $order)
    {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'New Order - #' . $this->order->reference,
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

        $customerName = e($order->user?->name ?? 'N/A');
        $customerEmail = e($order->user?->email ?? 'N/A');

        $body = "
            <h1>New Order Received</h1>
            <p>A new order has been placed on Watered.</p>

            <div style='background: rgba(234, 179, 8, 0.05); padding: 20px; border-radius: 12px; margin: 20px 0;'>
                <h2 style='color: #EAB308; font-size: 18px; margin-top: 0;'>Customer Information</h2>
                <p style='margin-bottom: 5px;'><strong>Name:</strong> {$customerName}</p>
                <p style='margin-bottom: 5px;'><strong>Email:</strong> {$customerEmail}</p>
                <p style='margin-bottom: 0;'><strong>Order ID:</strong> {$order->reference}</p>
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

            <p>Please reach out to the customer to arrange delivery.</p>
        ";

        return new Content(
            view: 'emails.premium',
            with: ['body' => $body],
        );
    }

    public static function sendToAdmins(ShopOrder $order): void
    {
        $admins = AdminUser::all();
        foreach ($admins as $admin) {
            if ($admin->email) {
                Mail::to($admin->email)->queue(new self($order));
            }
        }
    }
}
