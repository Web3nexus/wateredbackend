<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use App\Models\ShopOrder;
use App\Models\ShopOrderItem;
use App\Models\GlobalSetting;
use App\Models\UserStat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::where('is_active', true)
            ->latest()
            ->get();

        return response()->json([
            'data' => ProductResource::collection($products)
        ]);
    }

    public function show(Product $product)
    {
        if (!$product->is_active) {
            abort(404);
        }
        return response()->json([
            'data' => new ProductResource($product)
        ]);
    }

    public function checkout(Request $request)
    {
        $request->validate([
            'items' => 'required|array',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
        ]);

        $user = $request->user();
        $totalKobo = 0;
        
        $reference = 'sho_' . time() . '_' . uniqid();

        $order = ShopOrder::create([
            'user_id' => $user->id,
            'reference' => $reference,
            'amount_kobo' => 0, // Will calculate below
        ]);

        foreach ($request->items as $itemReq) {
            $product = Product::find($itemReq['product_id']);
            $quantity = $itemReq['quantity'];
            
            // Prioritize price_ngn (stored in Naira decimal) over legacy price (stored in cents integer)
            $priceNgn = $product->price_ngn ?: ($product->price / 100);
            $priceKobo = (int) round($priceNgn * 100);
            
            ShopOrderItem::create([
                'shop_order_id' => $order->id,
                'product_id' => $product->id,
                'quantity' => $quantity,
                'unit_price_kobo' => $priceKobo,
            ]);
            
            $totalKobo += ($priceKobo * $quantity);
        }

        $order->update(['amount_kobo' => $totalKobo]);

        return response()->json([
            'message' => 'Order initiated successfully.',
            'reference' => $reference,
            'amount_kobo' => $totalKobo,
            'currency' => 'NGN',
        ]);
    }

    public function verifyCheckout(Request $request)
    {
        $request->validate([
            'reference' => 'required|string',
        ]);

        $order = ShopOrder::where('reference', $request->reference)
            ->where('user_id', $request->user()->id)
            ->firstOrFail();

        if ($order->status === 'paid') {
            return response()->json(['message' => 'Payment already verified', 'order' => $order]);
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
                $order->update(['status' => 'paid']);

                // Immediately sync the user's spend stat so the dashboard
                // and leaderboard reflect the payment without delay.
                try {
                    $stat = UserStat::firstOrCreate(
                        ['user_id' => $request->user()->id],
                        ['daily_streak' => 0, 'time_spent_minutes' => 0,
                         'nima_sedani_time_minutes' => 0, 'amount_spent_kobo' => 0]
                    );
                    $stat->syncAmountSpent();
                } catch (\Throwable $e) {
                    \Log::warning('[ProductController] UserStat sync failed: ' . $e->getMessage());
                }

                return response()->json(['message' => 'Payment successful', 'order' => $order]);
            }
        }

        return response()->json(['message' => 'Payment verification failed'], 400);
    }
}
