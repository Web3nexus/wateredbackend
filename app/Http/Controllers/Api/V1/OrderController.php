<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function index()
    {
        $orders = Order::active()
            ->orderBy('order_level', 'asc')
            ->get();

        $formattedOrders = $orders->map(function ($order) {
            return [
                'id' => $order->id,
                'title' => $order->title,
                'description' => $order->description,
                'status' => $order->status,
                'cta_text' => $order->cta_text,
                'cta_link' => $order->cta_link,
                'action_type' => $order->action_type,
                'image_url' => $order->getFirstMediaUrl('order_images') ?: null,
                'order_level' => $order->order_level,
                'is_active' => $order->is_active,
            ];
        });

        return response()->json([
            'data' => $formattedOrders
        ]);
    }
}
