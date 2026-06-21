<?php

namespace App\Http\Controllers;

use App\Models\ProductReview;
use App\Models\Cart;
use Illuminate\Http\Request;

class ProductReviewController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'order_id' => 'required|exists:orders,id',
            'product_id' => 'required|exists:products,id',
            'rating' => 'required|integer|min:1|max:5',
            'feedback' => 'nullable|string'
        ]);

        $userId = auth()->id();

        // ✅ check order ownership + completed status only
        $order = \App\Models\Order::where('id', $request->order_id)
            ->where('user_id', $userId)
            ->where('status_id', 3) // completed order
            ->first();

        if (!$order) {
            return response()->json([
                'success' => false,
                'message' => 'You can only review completed orders.'
            ], 403);
        }

        // save review
        $review = ProductReview::updateOrCreate(
            [
                'user_id' => $userId,
                'product_id' => $request->product_id,
            ],
            [
                'rating' => $request->rating,
                'feedback' => $request->feedback,
            ]
        );

        return response()->json([
            'success' => true,
            'message' => 'Review saved successfully',
            'data' => $review
        ]);
    }
}
