<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderApplication;
use App\Mail\ApplicationSubmitted;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

class OrderApplicationController extends Controller
{
    public function index()
    {
        $applications = OrderApplication::with('order')
            ->where('user_id', Auth::id())
            ->latest()
            ->get();

        return response()->json([
            'data' => $applications
        ]);
    }

    public function store(Request $request, Order $order)
    {
        if (!$order->is_active) {
            return response()->json(['message' => 'Order not found'], 404);
        }

        $request->validate([
            'answers' => 'required|array',
        ]);

        $application = OrderApplication::create([
            'order_id' => $order->id,
            'user_id' => Auth::id(),
            'answers' => $request->answers,
            'status' => 'pending',
        ]);

        // Send email notification to user
        try {
            Mail::to($request->user())->send(new ApplicationSubmitted($application));
        } catch (\Exception $e) {
            // Log error but don't fail the request
            \Log::error('Failed to send application submission email: ' . $e->getMessage());
        }

        return response()->json([
            'message' => 'Application submitted successfully',
            'data' => $application
        ], 201);
    }
}
