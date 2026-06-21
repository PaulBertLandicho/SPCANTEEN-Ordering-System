<?php

use App\Http\Controllers\CartController;
use App\Http\Controllers\FavoriteController;
use App\Http\Controllers\IntroController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\UserController;
use App\Http\Middleware\CheckIfOrderIsCompleted;
use App\Http\Middleware\CheckIfUserHasNotCompletedOrders;
use App\Http\Middleware\CheckIfUserHasProductInCart;
use App\Http\Middleware\EnsureUserHasRole;
use App\Http\Middleware\CheckUserHasRole;
use App\Http\Middleware\MakeCookieForFadeOutTitle;
use App\Http\Controllers\ProductReviewController;
use App\Http\Middleware\PreventRegister;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Order;
use Carbon\Carbon;

Route::get('/', [ProductController::class, 'index'])->middleware([EnsureUserHasRole::class, CheckIfOrderIsCompleted::class])->name('home');

Route::get('/after-intro', [IntroController::class, 'afterIntro']);

Route::post('/login', [UserController::class, 'login']);

Route::post('/logout', [UserController::class, 'logout']);

Route::get('/setup', function () {
    return view('setup');
})->middleware(CheckUserHasRole::class);

Route::post('/setup', [UserController::class, 'setup']);
Route::get('/products/{id}/reviews', [ProductController::class, 'reviews']);
Route::post('/review/store', [ProductReviewController::class, 'store']);
Route::get('/register', function () {
    return view('register');
})->middleware(PreventRegister::class);

Route::post('/register', [UserController::class, 'register']);

Route::middleware(['logged-in'])->group(function () {
    Route::middleware(['user'])->group(function () {
        Route::middleware(['noPendingOrder'])->group(function () {
            Route::get('/product/category/{categoryId}', [ProductController::class, 'getProductsByCategory']);
            Route::get('/product/category/name/{categoryId}', [ProductController::class, 'getCategoryName']);
            Route::get('/cart/store/product/{product}', [CartController::class, 'store']);
            Route::get('/cart/show/product/inside', [CartController::class, 'show']);

            Route::get('/favorite/show/{product}', [FavoriteController::class, 'showFavorite']);
            Route::get('/cart/store/single/product/{product}', [CartController::class, 'SingleStoreToCart']);
            Route::get('/product/search/{product}', [ProductController::class, 'searchProduct']);
            Route::post('/favorite/toggle/{id}', [FavoriteController::class, 'addDeleteFavorite']);
            Route::get('/favorite/check/{id}', [FavoriteController::class, 'showFavorite']);
            Route::get('/favorite', [FavoriteController::class, 'index'])->middleware(MakeCookieForFadeOutTitle::class);
            Route::get('/favorite/remove/{productId}', [FavoriteController::class, 'removeFavorite']);

            Route::get('/history', [OrderController::class, 'index3'])->middleware(MakeCookieForFadeOutTitle::class);

            Route::get('/profile', function () {
                return view('user.profile');
            })->middleware(MakeCookieForFadeOutTitle::class)->name('profile');

            //Route for edit
            Route::middleware(['editUser'])->group(function () {
                Route::get('/edit/{id}', [UserController::class, 'edit'])->name('user.edit');
                Route::post('/process.edit/{id}', [UserController::class, 'processEdit'])->name('process.edit');
            });

            Route::get('/cart', [CartController::class, 'index'])->middleware(MakeCookieForFadeOutTitle::class);
            Route::get('/cart/delete/{cartId}', [CartController::class, 'destroy']);
            Route::get('/cart/get/total/quantity', [CartController::class, 'getTotalQuantity']);
            Route::get('/cart/get/total/price', [CartController::class, 'getTotalPrice']);
            Route::get('/cart/quantity/add/{cartId}', [CartController::class, 'addQuantity']);
            Route::get('/cart/quantity/minus/{cartId}', [CartController::class, 'minusQuantity']);

            Route::get('/payment', [OrderController::class, 'paymentPage'])->middleware(CheckIfUserHasProductInCart::class);

            Route::post('/order/store', [OrderController::class, 'store']);
        });
        Route::get('/qr-code', [OrderController::class, 'getOrderId'])->middleware(CheckIfUserHasNotCompletedOrders::class);
        // User API: return order status for polling on QR page
        Route::get('/order/status/{orderId}', [OrderController::class, 'getOrderStatus']);
    });

    Route::middleware(['admin'])->group(function () {
        Route::get('/administrator', [OrderController::class, 'getStatistics']);
        Route::get('/api/chart/data', [OrderController::class, 'getChartData']);
        Route::get('/api/admin/stats', [OrderController::class, 'getAdminStats']);
        Route::get('/api/orders/latest', [OrderController::class, 'getLatestOrders']);
        // API: Orders chart data (range: today|weekly|monthly)
        Route::get('/api/chart/orders', function (Request $request) {
            $range = $request->query('range', 'weekly');

            if ($range === 'today') {
                $start = Carbon::today();
                $rows = Order::whereBetween('created_at', [$start, Carbon::now()])
                    ->select(DB::raw('HOUR(created_at) as hour'), DB::raw('COUNT(*) as count'))
                    ->groupBy('hour')
                    ->orderBy('hour')
                    ->get()
                    ->pluck('count', 'hour')
                    ->toArray();

                $labels = [];
                $values = [];
                for ($h = 0; $h < 24; $h++) {
                    $labels[] = sprintf('%02d:00', $h);
                    $values[] = isset($rows[$h]) ? (int)$rows[$h] : 0;
                }

                return response()->json(['labels' => $labels, 'values' => $values]);
            }

            if ($range === 'monthly') {
                $start = Carbon::now()->subMonths(11)->startOfMonth();
                $rows = Order::whereBetween('created_at', [$start, Carbon::now()])
                    ->select(DB::raw("DATE_FORMAT(created_at, '%Y-%m') as ym"), DB::raw('COUNT(*) as count'))
                    ->groupBy('ym')
                    ->orderBy('ym')
                    ->get()
                    ->pluck('count', 'ym')
                    ->toArray();

                $labels = [];
                $values = [];
                for ($i = 0; $i < 12; $i++) {
                    $m = $start->copy()->addMonths($i);
                    $key = $m->format('Y-m');
                    $labels[] = $m->format('M');
                    $values[] = isset($rows[$key]) ? (int)$rows[$key] : 0;
                }

                return response()->json(['labels' => $labels, 'values' => $values]);
            }

            // default: weekly (last 7 days)
            $start = Carbon::now()->subDays(6)->startOfDay();
            $rows = Order::whereBetween('created_at', [$start, Carbon::now()])
                ->select(DB::raw('DATE(created_at) as date'), DB::raw('COUNT(*) as count'))
                ->groupBy('date')
                ->orderBy('date')
                ->get()
                ->pluck('count', 'date')
                ->toArray();

            $labels = [];
            $values = [];
            for ($i = 0; $i < 7; $i++) {
                $d = $start->copy()->addDays($i);
                $key = $d->toDateString();
                $labels[] = $d->format('M d');
                $values[] = isset($rows[$key]) ? (int)$rows[$key] : 0;
            }

            return response()->json(['labels' => $labels, 'values' => $values]);
        });

        // API: Orders list for dashboard (range: today|weekly|monthly)
        Route::get('/api/orders', function (Request $request) {
            $range = $request->query('range', 'monthly');

            if ($range === 'today') {
                $start = Carbon::today();
            } elseif ($range === 'weekly') {
                $start = Carbon::now()->subDays(6)->startOfDay();
            } else {
                // monthly -> last 30 days
                $start = Carbon::now()->subDays(29)->startOfDay();
            }

            $orders = Order::with(['user', 'status'])
                ->whereBetween('created_at', [$start, Carbon::now()])
                ->orderBy('created_at', 'desc')
                ->limit(200)
                ->get()
                ->map(function ($o) {
                    return [
                        'id' => $o->id,
                        'created_at' => $o->created_at->toDateTimeString(),
                        'customer_name' => $o->user?->name ?? ($o->customer_name ?? 'Guest'),
                        'school_id' => $o->user?->school_id ?? null,
                        'location' => '',
                        'total' => (float)($o->amount ?? 0),
                        'status' => $o->status?->name ?? 'Pending'
                    ];
                });

            return response()->json(['orders' => $orders]);
        });

        Route::get('/product_list', [ProductController::class, 'adminIndex'])->middleware(MakeCookieForFadeOutTitle::class);
        // Allow admin UI to fetch products by category (same handler used by user routes)
        Route::get('/product/category/{categoryId}', [ProductController::class, 'getProductsByCategory']);
        Route::post('/addproduct', [ProductController::class, 'store']);
        Route::post('/products/destroy/{id}', [ProductController::class, 'destroy'])->name('products.destroy');
        Route::get('/products/{id}', [ProductController::class, 'show']);
        Route::post('/products/edit/{id}', [ProductController::class, 'edit']);

        Route::get('/order_list', [OrderController::class, 'index'])->middleware(MakeCookieForFadeOutTitle::class);
        Route::get('/order/get/details/{orderId}', [OrderController::class, 'getOrderDetails']);
        Route::get('/order/get/details/scan/{orderId}', [OrderController::class, 'getOrderDetailsScan']);
        Route::get('/order/get/product/{orderId}', [OrderController::class, 'getOrderProducts']);
        Route::get('/order/complete/{orderId}', [OrderController::class, 'completeOrder']);
        Route::get('/order/cancel/{orderId}', [OrderController::class, 'cancelOrder']);
        Route::get('/order/change/status/{orderId}', [OrderController::class, 'changeStatus']);

        Route::get('/transaction_history', [OrderController::class, 'index2'])->middleware(MakeCookieForFadeOutTitle::class);
        Route::get('/order/get/details2/{orderId}', [OrderController::class, 'getOrderDetails2']);

        Route::middleware(['superAdmin'])->group(function () {
            Route::get('/manage_user', [UserController::class, 'showUser'])->middleware(MakeCookieForFadeOutTitle::class);
            Route::get('/user/{id}', [UserController::class, 'show']);
            Route::post('/user/edit/{id}', [UserController::class, 'adminEdit']);
            Route::get('/user/delete/{id}', [UserController::class, 'delete']);
        });
    });
});
