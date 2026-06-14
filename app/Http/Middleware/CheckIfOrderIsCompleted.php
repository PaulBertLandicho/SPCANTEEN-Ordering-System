<?php

namespace App\Http\Middleware;

use App\Models\Order;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckIfOrderIsCompleted
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {   
        if (Auth::check()) {
            $userId = auth()->user()->id;
            $order = Order::where('user_id', $userId)->whereIn('status_id', [1, 2])->exists();

            if ($order) {
                return redirect('/qr-code');
            }

            return $next($request);
        } else {
            return $next($request);
        }
    }
}
