<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\TextCollection;
use App\Models\BookPurchase;
use App\Models\GlobalSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class BookPurchaseController extends Controller
{
    public function initiate(Request $request, TextCollection $collection)
    {
        $user = $request->user();

        // Check if already purchased
        $existing = BookPurchase::where('user_id', $user->id)
            ->where('text_collection_id', $collection->id)
            ->where('status', 'completed')
            ->first();

        if ($existing) {
            return response()->json(['message' => 'You already own this book'], 400);
        }

        $reference = 'bk_' . time() . '_' . uniqid();

        $purchase = BookPurchase::create([
            'user_id' => $user->id,
            'text_collection_id' => $collection->id,
            'amount_paid' => $collection->price,
            'reference' => $reference,
            'status' => 'pending',
        ]);

        return response()->json([
            'message' => 'Purchase initiated',
            'reference' => $reference,
            'amount' => $collection->price,
            'currency' => 'NGN',
            'book' => $collection->name,
        ]);
    }

    public function verify(Request $request)
    {
        $request->validate([
            'reference' => 'required|string',
        ]);

        $purchase = BookPurchase::where('reference', $request->reference)
            ->where('user_id', $request->user()->id)
            ->firstOrFail();

        if ($purchase->status === 'completed') {
            return response()->json(['message' => 'Payment already verified', 'purchase' => $purchase]);
        }

        $settings = GlobalSetting::first();
        $secretKey = config('services.paystack.secret_key') ?? $settings?->paystack_secret_key;

        if (!$secretKey) {
            return response()->json(['message' => 'Payment verification unavailable'], 500);
        }

        $response = Http::withToken($secretKey)
            ->get("https://api.paystack.co/transaction/verify/{$request->reference}");

        if ($response->successful()) {
            $data = $response->json('data');
            if ($data['status'] === 'success') {
                $purchase->update(['status' => 'completed']);

                return response()->json([
                    'message' => 'Purchase successful',
                    'purchase' => $purchase
                ]);
            }
        }

        return response()->json(['message' => 'Payment verification failed'], 400);
    }
}
