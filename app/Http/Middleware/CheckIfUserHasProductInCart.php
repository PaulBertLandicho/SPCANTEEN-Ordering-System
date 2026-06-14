<?php

namespace App\Http\Middleware;

use App\Models\Cart;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckIfUserHasProductInCart
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $userId = auth()->user()->id;
        $carts = Cart::where('user_id', $userId)->whereNull('order_id')->exists();
        //dd($carts);
        if (!$carts) {
            abort(redirect()->back());
        }

        return $next($request);
    }
}
