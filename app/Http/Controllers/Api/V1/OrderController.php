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

        return response()->json([
            'data' => $orders
        ]);
    }
}
