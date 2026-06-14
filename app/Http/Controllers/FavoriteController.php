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

        $favorites = Favorite::where('user_id', $userId)->get();

        return view('user.favorite', compact('favorites', 'productCount'));
    }

    public function addDeleteFavorite($productId)
    {
        $product = Product::find($productId);
        $userId = auth()->user()->id;

        $checkFavorite = Favorite::where('user_id', $userId)->where('product_id', $productId)->first();
        
        if ($checkFavorite) {
            $checkFavorite->delete();
            return response()->json(false);
        } else {
            if (!$product) {
                return response()->json(['error' => 'Invalid product ID'], 400);
            } else {
    
                $favorite = new Favorite();
    
                $favorite->user_id = $userId;
                $favorite->product_id = $productId;
    
                $favorite->save();
    
                return response()->json(true);
    
            }
        }
    }

    public function showFavorite($productId) {

        $userId = auth()->user()->id;

        $checkFavorite = Favorite::where('user_id', $userId)->where('product_id', $productId)->first();
        
        if ($checkFavorite) {
            return response()->json(true);
        } else {
            return response()->json(false);
        }
    }

    public function removeFavorite($productId)
    {
        $favorite = Favorite::where('product_id', $productId)->first();

        $favorite->delete();
        return response()->json($productId);
    }
}
