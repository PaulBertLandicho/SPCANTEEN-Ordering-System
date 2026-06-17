<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\Order;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $orders = Order::whereIn('status_id', [1, 2])->orderBy('status_id', 'asc')->get();
        $date = Carbon::now();

        foreach ($orders as $order) {
            $order['username'] = $order->user->name;
            $order['school_id'] = $order->user->school_id;
            $order['status_name'] = $order->status->name;
        }

        $formattedDate = $date->format('F d Y');

        return view('admin.order_list', compact('orders', 'formattedDate'));
    }

    public function index2()
    {
        $orders = Order::whereIn('status_id', [3, 4])->orderBy('status_id', 'desc')->get();
        $date = Carbon::now();

        foreach ($orders as $order) {
            $order['username'] = $order->user->name;
            $order['school_id'] = $order->user->school_id;
            $order['status_name'] = $order->status->name;
        }

        $formattedDate = $date->format('F d Y');

        return view('admin.transaction_history', compact('orders', 'formattedDate'));
    }

    public function index3()
    {
        $userId = auth()->user()->id;
        $orders = Order::where('status_id', 3)->where('user_id', $userId)->orderBy('id', 'desc')->get();

        $orderIds = $orders->pluck('id');
        $carts = Cart::where('id', $userId)->whereNotNull('order_id')->whereIn('order_id', $orderIds)->get();

        $formattedCarts = [];
        foreach ($carts as $cart) {
            if ($cart->product) {
                $formattedCarts[] = [
                    'id' => $cart->id,
                    'product_name' => $cart->product->name,
                    'product_quantity' => $cart->quantity,
                ];
            }
        }

        foreach ($orders as $order) {
            $cart = Cart::where('order_id', $order->id)->count();
            $order['totalCarts'] = $cart;
        }

        //dd($cartIds);
        return view('user.history', compact('orders', 'carts'));
    }

    public function getStatistics()
    {
        $totalOrders = Order::count();

        $completedOrders = Order::where('status_id', 3)->count();

        $cancelledOrders = Order::where('status_id', 4)->count();

        $totalRevenue = Order::where('status_id', 3)->sum('amount');

        /*
    |--------------------------------------------------------------------------
    | Last 30 Days vs Previous 30 Days
    |--------------------------------------------------------------------------
    */

        $currentStart = Carbon::now()->subDays(30);
        $previousStart = Carbon::now()->subDays(60);
        $previousEnd = Carbon::now()->subDays(30);

        // Total Orders
        $currentOrders = Order::where('created_at', '>=', $currentStart)->count();

        $previousOrders = Order::whereBetween('created_at', [
            $previousStart,
            $previousEnd
        ])->count();

        // Completed
        $currentCompleted = Order::where('status_id', 3)
            ->where('created_at', '>=', $currentStart)
            ->count();

        $previousCompleted = Order::where('status_id', 3)
            ->whereBetween('created_at', [
                $previousStart,
                $previousEnd
            ])
            ->count();

        // Cancelled
        $currentCancelled = Order::where('status_id', 4)
            ->where('created_at', '>=', $currentStart)
            ->count();

        $previousCancelled = Order::where('status_id', 4)
            ->whereBetween('created_at', [
                $previousStart,
                $previousEnd
            ])
            ->count();

        // Revenue
        $currentRevenue = Order::where('status_id', 3)
            ->where('created_at', '>=', $currentStart)
            ->sum('amount');

        $previousRevenue = Order::where('status_id', 3)
            ->whereBetween('created_at', [
                $previousStart,
                $previousEnd
            ])
            ->sum('amount');

        /*
    |--------------------------------------------------------------------------
    | Percentage Calculation
    |--------------------------------------------------------------------------
    */

        $totalOrdersPercent = $previousOrders > 0
            ? round((($currentOrders - $previousOrders) / $previousOrders) * 100, 1)
            : ($currentOrders > 0 ? 100 : 0);

        $completedPercent = $previousCompleted > 0
            ? round((($currentCompleted - $previousCompleted) / $previousCompleted) * 100, 1)
            : ($currentCompleted > 0 ? 100 : 0);

        $cancelledPercent = $previousCancelled > 0
            ? round((($currentCancelled - $previousCancelled) / $previousCancelled) * 100, 1)
            : ($currentCancelled > 0 ? 100 : 0);

        $revenuePercent = $previousRevenue > 0
            ? round((($currentRevenue - $previousRevenue) / $previousRevenue) * 100, 1)
            : ($currentRevenue > 0 ? 100 : 0);

        return view('admin.admin', compact(
            'totalOrders',
            'completedOrders',
            'cancelledOrders',
            'totalRevenue',
            'totalOrdersPercent',
            'completedPercent',
            'cancelledPercent',
            'revenuePercent'
        ));
    }

    public function getChartData(Request $request)
    {
        $range = $request->query('range', 'monthly');

        if ($range === 'today') {
            // hourly totals for today
            $start = Carbon::today();
            $rows = Order::where('status_id', 3)
                ->whereBetween('created_at', [$start, Carbon::now()])
                ->select(DB::raw('HOUR(created_at) as hour'), DB::raw('SUM(amount) as total'))
                ->groupBy('hour')
                ->orderBy('hour')
                ->get()
                ->pluck('total', 'hour')
                ->toArray();

            $labels = [];
            $values = [];
            for ($h = 0; $h < 24; $h++) {
                $labels[] = sprintf('%02d:00', $h);
                $values[] = isset($rows[$h]) ? (float)$rows[$h] : 0;
            }

            return response()->json(['labels' => $labels, 'values' => $values]);
        }

        if ($range === 'weekly') {
            // last 7 days totals
            $start = Carbon::now()->subDays(6)->startOfDay();
            $rows = Order::where('status_id', 3)
                ->whereBetween('created_at', [$start, Carbon::now()])
                ->select(DB::raw('DATE(created_at) as date'), DB::raw('SUM(amount) as total'))
                ->groupBy('date')
                ->orderBy('date')
                ->get()
                ->pluck('total', 'date')
                ->toArray();

            $labels = [];
            $values = [];
            for ($i = 0; $i < 7; $i++) {
                $d = $start->copy()->addDays($i);
                $key = $d->toDateString();
                $labels[] = $d->format('M d');
                $values[] = isset($rows[$key]) ? (float)$rows[$key] : 0;
            }

            return response()->json(['labels' => $labels, 'values' => $values]);
        }

        // default: monthly (last 12 months)
        $start = Carbon::now()->subMonths(11)->startOfMonth();
        $orders = Order::where('status_id', 3)->whereBetween('created_at', [$start, Carbon::now()])->select(
            DB::raw('MONTH(created_at) as month'),
            DB::raw('SUM(amount) as total_amount')
        )
            ->groupBy('month')
            ->get();

        $total_amount_per_month = [];
        $monthNames = [
            1 => 'Jan',
            2 => 'Feb',
            3 => 'Mar',
            4 => 'Apr',
            5 => 'May',
            6 => 'Jun',
            7 => 'Jul',
            8 => 'Aug',
            9 => 'Sep',
            10 => 'Oct',
            11 => 'Nov',
            12 => 'Dec',
        ];

        foreach ($orders as $order) {
            $month = $order->month;
            $total_amount = $order->total_amount;

            $monthName = isset($monthNames[$month]) ? $monthNames[$month] : $month;
            $total_amount_per_month[$monthName] = (float)$total_amount;
        }

        // ensure all months present (last 12 months)
        $labels = [];
        $values = [];
        for ($i = 0; $i < 12; $i++) {
            $m = $start->copy()->addMonths($i);
            $key = $m->format('M');
            $labels[] = $key;
            $values[] = isset($total_amount_per_month[$key]) ? $total_amount_per_month[$key] : 0;
        }

        return response()->json(['labels' => $labels, 'values' => $values]);
    }

    /**
     * Return admin stats as JSON for dashboard polling.
     */
    public function getAdminStats()
    {
        $totalOrders = Order::whereIn('status_id', [1, 2, 3, 4])->count();
        $completedOrders = Order::where('status_id', 3)->count();
        $cancelledOrders = Order::where('status_id', 4)->count();

        $orders = Order::where('status_id', 3)->get();
        $totalRevenue = 0;
        foreach ($orders as $order) {
            $totalRevenue += $order->amount;
        }

        return response()->json([
            'totalOrders' => $totalOrders,
            'completedOrders' => $completedOrders,
            'cancelledOrders' => $cancelledOrders,
            'totalRevenue' => $totalRevenue,
        ]);
    }

    public function getOrderId()
    {
        $userId = auth()->user()->id;

        $order = Order::where('user_id', $userId)->whereIn('status_id', [1, 2])->first();

        if ($order) {
            return view('user.qr-code', compact('order'));
        }

        return redirect('/');
    }

    public function getOrderDetails($orderId)
    {
        $order = Order::where('id', $orderId)->whereIn('status_id', [1, 2])->first();

        if (!$order) {
            throw new \Exception('Order not found.');
            return response()->json();
        }

        $order['status_name'] = $order->status->name;
        $order['user_name'] = $order->user->name;
        $order['status_name'] = $order->status->name;
        $order['payment_type'] = $order->payment->name;
        $order['user_role'] = $order->user->role->name;
        $order['date'] = $order->created_at->format('m/d/y');

        //dd($order->cart->get()->product->name);
        return response()->json($order);
    }

    /**
     * Return minimal order status for polling (user access).
     */
    public function getOrderStatus($orderId)
    {
        $userId = auth()->user()->id;

        $order = Order::where('id', $orderId)->where('user_id', $userId)->first();

        if (!$order) {
            return response()->json(['error' => 'Order not found'], 404);
        }

        return response()->json([
            'status_id' => $order->status_id,
            'status_name' => $order->status?->name ?? null
        ]);
    }

    public function getOrderDetailsScan($orderId)
    {
        $order = Order::where('id', $orderId)->where('status_id', 2)->first();

        if (!$order) {
            throw new \Exception('Order not found.');
            return response()->json();
        }

        $order['status_name'] = $order->status->name;
        $order['user_name'] = $order->user->name;
        $order['status_name'] = $order->status->name;
        $order['payment_type'] = $order->payment->name;
        $order['user_role'] = $order->user->role->name;
        $order['date'] = $order->created_at->format('m/d/y');

        //dd($order->cart->get()->product->name);
        return response()->json($order);
    }

    public function getOrderProducts($orderId)
    {
        $carts = Cart::where('order_id', $orderId)->whereNotNull('order_id')->get();

        $formattedCarts = [];
        foreach ($carts as $cart) {
            if ($cart->product) {
                $formattedCarts[] = [
                    'id' => $cart->id,
                    'product_name' => $cart->product->name,
                    'product_quantity' => $cart->quantity,
                ];
            }
        }

        return response()->json($formattedCarts);
    }

    public function getOrderDetails2($orderId)
    {
        $order = Order::where('id', $orderId)->whereIn('status_id', [3, 4])->first();

        if (!$order) {
            throw new \Exception('Order not found.');
            return response()->json();
        }

        $order['status_name'] = $order->status->name;
        $order['user_name'] = $order->user->name;
        $order['status_name'] = $order->status->name;
        $order['payment_type'] = $order->payment->name;
        $order['user_role'] = $order->user->role->name;
        $order['date'] = $order->created_at->format('m/d/y');

        //dd($order->cart->get()->product->name);
        return response()->json($order);
    }

    public function paymentPage()
    {
        $userId = auth()->user()->id;

        $carts = Cart::where('user_id', $userId)->whereNull('order_id')->get();

        $productSelected = $carts->count();
        $totalPrice = 0;

        foreach ($carts as $cart) {
            $totalPrice += $cart->product->price * $cart->quantity;
        }

        return view('user.payment', compact('totalPrice', 'productSelected'));
    }

    public function completeOrder($orderId)
    {
        $order = Order::where('id', $orderId)->where('status_id', 2)->first();

        $order->status_id = 3;
        $order->save();

        return response()->json($order->id);
    }

    public function cancelOrder($orderId)
    {
        $order = Order::where('id', $orderId)->whereIn('status_id', [1, 2])->first();

        $order->status_id = 4;
        $order->save();

        return response()->json($order->id);
    }

    public function changeStatus($orderId)
    {
        $order = Order::where('id', $orderId)->whereIn('status_id', [1, 2])->first();

        if ($order->status_id == 1) {
            $order->status_id = 2;
            $order->save();
        } else {
            $order->status_id = 1;
            $order->save();
        }

        return response()->json($order->status_id);
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
    public function store(Request $request)
    {
        $paymentOption = $request->input('payment_option');
        $totalPrice = $request->input('totalPrice');

        $userId = auth()->user()->id;

        $order = new Order();

        $order->user_id = $userId;
        $order->payment_id = $paymentOption;
        $order->status_id  = 1;
        $order->amount = $totalPrice;

        $order->save();

        $carts = Cart::where('user_id', $userId)->whereNull('order_id')->get();
        $order = Order::latest()->where('user_id', $userId)->first();
        foreach ($carts as $cart) {
            $cart->order_id = $order->id;
            $cart->save();
        }

        return redirect('/qr-code');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
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
    public function destroy(string $id)
    {
        //
    }
}
