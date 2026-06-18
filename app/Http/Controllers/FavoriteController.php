<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\Favorite;
use App\Models\Product;
use Illuminate\Http\Request;

class FavoriteController extends Controller
{
    public function index()
    {
        $userId = auth()->user()->id;

        $cartData = Cart::where('user_id', $userId)->whereNull('order_id')->get();
        $productCount = $cartData->count();

        $favorites = Favorite::where('user_id', $userId)
            ->with('product')
            ->whereHas('product', function ($query) {
                $query->where('availability', 1);
            })
            ->get();
        foreach ($favorites as $favorite) {
            $favorite['product_name'] = $favorite->product->name;
        }

        return view('user.favorite', compact('favorites', 'productCount'));
    }

    public function addDeleteFavorite($variantId)
    {
        $userId = auth()->id();

        $variant = Product::find($variantId);

        if (!$variant) {
            return response()->json(false);
        }

        $favorite = Favorite::where('user_id', $userId)
            ->where('product_variant_id', $variantId)
            ->first();

        if ($favorite) {
            $favorite->delete();
            return response()->json(false);
        }

        Favorite::create([
            'user_id' => $userId,
            'product_id' => $variant->id,
            'product_variant_id' => $variantId,
        ]);

        return response()->json(true);
    }
    public function showFavorite($variantId)
    {
        $userId = auth()->user()->id;

        $exists = Favorite::where('user_id', $userId)
            ->where('product_variant_id', $variantId)
            ->exists();

        return response()->json($exists);
    }

    public function removeFavorite($productId)
    {
        $favorite = Favorite::where('product_id', $productId)->first();

        $favorite->delete();
        return response()->json($productId);
    }
}
