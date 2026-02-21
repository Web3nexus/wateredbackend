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

        $formattedOrders = $orders->map(function (Order $order) {
            return $this->formatOrder($order);
        });

        return response()->json([
            'data' => $formattedOrders
        ]);
    }

    public function show(Order $order)
    {
        if (!$order->is_active) {
            return response()->json(['message' => 'Order not found'], 404);
        }

        $formattedOrder = $this->formatOrder($order);
        $formattedOrder['form_fields'] = $order->formFields()
            ->where('is_active', true)
            ->get();

        return response()->json([
            'data' => $formattedOrder
        ]);
    }

    protected function formatOrder(Order $order): array
    {
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
    }
}
