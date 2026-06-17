<?php

namespace App\Filament\Resources\ShopOrders\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Section;
use Filament\Schemas\Schema;

class ShopOrderForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Order Information')
                    ->columns(2)
                    ->components([
                        Placeholder::make('reference')
                            ->label('Order ID')
                            ->content(fn ($record) => $record?->reference),
                        Placeholder::make('created_at')
                            ->label('Date')
                            ->content(fn ($record) => $record?->created_at?->format('M d, Y g:i A')),
                        Placeholder::make('amount_kobo')
                            ->label('Total')
                            ->content(fn ($record) => $record ? '₦' . number_format($record->amount_kobo / 100, 2) : ''),
                        Select::make('status')
                            ->label('Status')
                            ->options([
                                'pending' => 'Pending',
                                'paid' => 'Paid',
                                'processing' => 'Processing',
                                'shipped' => 'Shipped',
                                'delivered' => 'Delivered',
                                'cancelled' => 'Cancelled',
                            ])
                            ->required(),
                    ]),

                Section::make('Customer')
                    ->columns(2)
                    ->components([
                        Placeholder::make('user.name')
                            ->label('Name')
                            ->content(fn ($record) => $record?->user?->name),
                        Placeholder::make('user.email')
                            ->label('Email')
                            ->content(fn ($record) => $record?->user?->email),
                    ]),

                Section::make('Delivery Details')
                    ->columns(2)
                    ->components([
                        TextInput::make('shipping_name')
                            ->label('Recipient Name'),
                        TextInput::make('shipping_phone')
                            ->label('Phone Number'),
                        Textarea::make('shipping_address')
                            ->label('Delivery Address')
                            ->columnSpanFull(),
                        Placeholder::make('shipped_at')
                            ->label('Shipped Date')
                            ->content(fn ($record) => $record?->shipped_at?->format('M d, Y g:i A')),
                        Placeholder::make('delivered_at')
                            ->label('Delivered Date')
                            ->content(fn ($record) => $record?->delivered_at?->format('M d, Y g:i A')),
                    ]),

                Section::make('Order Items')
                    ->components([
                        Placeholder::make('items')
                            ->label('')
                            ->content(function ($record) {
                                if (!$record || !$record->relationLoaded('items')) {
                                    $record?->loadMissing('items.product');
                                }
                                $html = '<table style="width:100%; border-collapse:collapse;">';
                                $html .= '<thead><tr style="border-bottom:1px solid #e5e7eb;">
                                    <th style="text-align:left; padding:8px; font-weight:600;">Product</th>
                                    <th style="text-align:center; padding:8px; font-weight:600;">Qty</th>
                                    <th style="text-align:right; padding:8px; font-weight:600;">Price</th>
                                    <th style="text-align:right; padding:8px; font-weight:600;">Total</th>
                                </tr></thead><tbody>';
                                foreach ($record?->items ?? [] as $item) {
                                    $name = e($item->product?->name ?? 'Product #' . $item->product_id);
                                    $price = '₦' . number_format($item->unit_price_kobo / 100, 2);
                                    $total = '₦' . number_format(($item->unit_price_kobo * $item->quantity) / 100, 2);
                                    $html .= "<tr style='border-bottom:1px solid #f3f4f6;'>
                                        <td style='padding:8px;'>{$name}</td>
                                        <td style='padding:8px; text-align:center;'>{$item->quantity}</td>
                                        <td style='padding:8px; text-align:right;'>{$price}</td>
                                        <td style='padding:8px; text-align:right;'>{$total}</td>
                                    </tr>";
                                }
                                $html .= '</tbody></table>';
                                return new \Illuminate\Support\HtmlString($html);
                            }),
                    ]),

                Section::make('Admin Notes')
                    ->components([
                        Textarea::make('admin_notes')
                            ->label('Notes')
                            ->placeholder('Add internal notes about this order...')
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}
