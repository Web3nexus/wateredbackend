<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::where('is_active', true)
            ->latest()
            ->get();

        return response()->json(['data' => $products]);
    }

    public function show(Product $product)
    {
        if (!$product->is_active) {
            abort(404);
        }
        return response()->json(['data' => $product]);
    }

    public function checkout(Request $request)
    {
        // Simple dummy checkout for now
        $request->validate([
            'items' => 'required|array',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
        ]);

        // Logic to create Order would go here.
        // For Feature 7 scope, we acknowledge receipt.

        return response()->json(['message' => 'Order received successfully.']);
    }
}
