<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CartController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $userId = auth()->user()->id;

        $carts = Cart::where('user_id', $userId)->whereNull('order_id')->get();

        $totalQuantity = 0;
        $totalPrice = 0;

        foreach($carts as $cart) {
            $cart['sum'] = $cart->product->price * $cart->quantity;
            $totalQuantity += $cart->quantity;
            $totalPrice += $cart['sum'];
        }

        return view('user.cart', compact('carts', 'totalPrice', 'totalQuantity'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, $productId)
    {
        $quantity = $request->query('quantity');
        $userId = auth()->user()->id;

        $checkCart = Cart::where('user_id', $userId)->where('product_id', $productId)->whereNull('order_id')->first();

        if ($checkCart) {
            $checkCart->quantity += $quantity;
            $checkCart->save();

            return response()->json($productId);
        } else {
            $cart = new Cart();

            $cart->user_id = $userId;
            $cart->product_id = $productId;
            $cart->quantity = $quantity;

            $cart->save();

            return response()->json($productId);
        }

    }

    public function SingleStoreToCart($productId)
    {
        $userId = auth()->user()->id;

        $checkCart = Cart::where('user_id', $userId)->where('product_id', $productId)->whereNull('order_id')->first();

        if ($checkCart) {

            $checkCart->quantity++;
            $checkCart->save();

            $cartData = Cart::where('user_id', $userId)->whereNull('order_id')->get();

            $productCount = $cartData->count();
            return response()->json($productCount);

        } else {

            $userId = auth()->user()->id;

            $cart = new Cart();

            $cart->user_id = $userId;
            $cart->product_id = $productId;
            $cart->quantity = 1;

            $cart->save();

            $cartData = Cart::where('user_id', $userId)->whereNull('order_id')->get();

            $productCount = $cartData->count();
            return response()->json($productCount);
        }

    }


    /**
     * Display the specified resource.
     */
    public function show()
    {
        $userId = auth()->user()->id;
        $cartData = Cart::where('user_id', $userId)->whereNull('order_id')->get();

        $productCount = $cartData->count();

        return response()->json($productCount);
    }

    public function getTotalQuantity()
    {
        $userId = auth()->user()->id;

        $carts = Cart::where('user_id', $userId)->whereNull('order_id')->get();
        $totalQuantity = 0;

        foreach ($carts as $cart) {
            $totalQuantity += $cart->quantity;
        }

        return response()->json($totalQuantity);
    }

    public function getTotalPrice()
    {
        $userId = auth()->user()->id;

        $carts = Cart::where('user_id', $userId)->whereNull('order_id')->get();
        $totalPrice = 0;

        foreach ($carts as $cart) {
            $totalPrice += $cart->product->price * $cart->quantity;
        }

        return response()->json($totalPrice);
    }

    public function addQuantity(Request $request, $cartId) 
    {
        $quantity = $request->query('quantity');
        $cart = Cart::where('id', $cartId)->whereNull('order_id')->first();

        $cart->quantity = $quantity;
        $cart->save();

        $sum = $cart->product->price * $cart->quantity;

        return response()->json($sum);
    }

    public function minusQuantity(Request $request, $cartId) 
    {
        $quantity = $request->query('quantity');
        $cart = Cart::where('id', $cartId)->whereNull('order_id')->first();

        $cart->quantity = $quantity;
        $cart->save();

        $sum = $cart->product->price * $cart->quantity;

        return response()->json($sum);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($cartId)
    {   
        $userId = auth()->user()->id;
        $cart = Cart::where('id', $cartId)->whereNull('order_id')->first();

        $cart->delete();

        $carts = Cart::where('user_id', $userId)->whereNull('order_id')->get();
        $carts['deletedCartId'] = $cartId;

        return response()->json($carts);
    }
}
