<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\TextCollection;
use App\Models\BookPurchase;
use App\Models\GlobalSetting;
use App\Mail\BookPurchaseConfirmationMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;

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

        $settings = GlobalSetting::first();
        $secretKey = $settings?->paystack_secret_key ?? config('services.paystack.secret_key');

        $authorizationUrl = null;
        if ($secretKey) {
            try {
                $response = \Illuminate\Support\Facades\Http::withToken($secretKey)
                    ->post('https://api.paystack.co/transaction/initialize', [
                        'email' => $user->email,
                        'amount' => (int) ($collection->price * 100),
                        'reference' => $reference,
                        'callback_url' => route('payment.callback'),
                        'currency' => 'NGN',
                        'metadata' => [
                            'cancel_action' => 'https://cancelurl.com',
                        ],
                    ]);
                if ($response->successful()) {
                    $authorizationUrl = $response->json('data.authorization_url');
                }
            } catch (\Throwable $e) {
                \Log::error('[BookPurchase] Paystack init failed: ' . $e->getMessage());
            }
        }

        return response()->json([
            'message' => 'Purchase initiated',
            'reference' => $reference,
            'amount' => $collection->price,
            'currency' => 'NGN',
            'book' => $collection->name,
            'authorization_url' => $authorizationUrl,
            'callback_url' => route('payment.callback'),
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
        $secretKey = $settings?->paystack_secret_key ?? config('services.paystack.secret_key');

        if (!$secretKey) {
            return response()->json(['message' => 'Payment verification unavailable'], 500);
        }

        $response = Http::withToken($secretKey)
            ->get("https://api.paystack.co/transaction/verify/{$request->reference}");

        if ($response->successful()) {
            $data = $response->json('data');
            if ($data['status'] === 'success') {
                $purchase->update(['status' => 'completed']);

                // Send confirmation email
                try {
                    $purchase->loadMissing('textCollection', 'user');
                    Mail::to($purchase->user->email)->queue(new BookPurchaseConfirmationMail($purchase));
                } catch (\Throwable $e) {
                    \Log::error('[BookPurchaseController] Email failed: ' . $e->getMessage());
                }

                return response()->json([
                    'message' => 'Purchase successful',
                    'purchase' => $purchase
                ]);
            }
        }

        return response()->json(['message' => 'Payment verification failed'], 400);
    }
}
