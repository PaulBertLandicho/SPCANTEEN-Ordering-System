@extends('layouts.admin')

<link rel="stylesheet" href="{{ asset('css/admin.css') }}">
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
@section('content1')
@if (!Cookie::has('seenFirstFadeOut'))
<div id="splash-screen">
    <div class="logo1">
        <img class="futuristic-heading" src="/images/SPCanteen.png" alt="SPCanteen.png">
    </div>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        setTimeout(function() {
            document.getElementById("splash-screen").style.opacity = 0;

            setTimeout(function() {
                document.getElementById("content").style.display = "block";
            }, 1000);

            setTimeout(function() {
                document.getElementById("splash-screen").remove();
            }, 1500);
        }, 1000);
    });

    function switchPage() {

        document.querySelector('.content').classList.add('fade-out');

        setTimeout(function() {
            window.location.href = 'login';
        }, 100);
    }
</script>
@endif
<div class="content1">
    @if(auth()->user()->role_id == 3)
    <h1>Hello Admin!</h1>
    @elseif(auth()->user()->role_id == 4)
    <h1>Hello Super Admin!</h1>
    @endif
    <div class="dashboard-metrics">
        <div class="metric card box1">
            <div class="box-content">
                <div class="sales-icon">
                    <img id="box-icon" src="images/icon1.png">
                </div>
                <div class="sales-txt">
                    <div class="header">
                        <h3 id="total-orders">{{$totalOrders}}</h3>
                    </div>
                    <div class="total-completed">
                        <p>Total Orders</p>
                    </div>
                    <div class="percentage-days">
                        <img id="arrow-icon" src="images/arrow-icon.png">
                        <p id="days">
                            {{ $totalOrdersPercent >= 0 ? '+' : '' }}{{ $totalOrdersPercent }}% (30 days)
                        </p>
                    </div>
                </div>
            </div>
        </div>
        <div class="metric card box2">
            <div class="box-content">
                <div class="sales-icon">
                    <img id="box-icon" src="images/icon2.png">
                </div>
                <div class="sales-txt">
                    <div class="header">
                        <h3 id="completed-orders">{{$completedOrders}}</h3>
                    </div>
                    <div class="total-completed">
                        <p>Completed</p>
                    </div>
                    <div class="percentage-days">
                        <img id="arrow-icon" src="images/arrow-icon.png">
                        <p id="days">
                            {{ $completedPercent >= 0 ? '+' : '' }}{{ $completedPercent }}% (30 days)
                        </p>
                    </div>
                </div>
            </div>
        </div>
        <div class="metric card box3">
            <div class="box-content">
                <div class="sales-icon">
                    <img id="box-icon" src="images/icon3.png">
                </div>
                <div class="sales-txt">
                    <div class="header">
                        <h3 id="cancelled-orders">{{$cancelledOrders}}</h3>
                    </div>
                    <div class="total-completed">
                        <p>Cancelled</p>
                    </div>
                    <div class="percentage-days">
                        <img id="arrow-icon" src="images/arrow-icon.png">
                        <p id="days">
                            {{ $cancelledPercent >= 0 ? '+' : '' }}{{ $cancelledPercent }}% (30 days)
                        </p>
                    </div>
                </div>
            </div>
        </div>
        <div class="metric card box4">
            <div class="box-content">
                <div class="sales-icon">
                    <img id="box-icon" src="images/icon1.png">
                </div>
                <div class="sales-txt">
                    <div class="header">
                        <h3 id="total-revenue">₱{{ number_format($totalRevenue, 2) }}</h3>
                    </div>
                    <div class="total-completed">
                        <p>Total Revenue</p>
                    </div>
                    <div class="percentage-days">
                        <img id="arrow-icon" src="images/arrow-icon.png">
                        <p id="days">
                            {{ $revenuePercent >= 0 ? '+' : '' }}{{ $revenuePercent }}% (30 days)
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="charts-grid">
        <div class="chart-card card">
            <div class="chart-header">
                <h3>Revenue</h3>
                <div class="chart-controls">
                    <button class="btn small active" data-range="monthly">Monthly</button>
                    <button class="btn small" data-range="weekly">Weekly</button>
                    <button class="btn small" data-range="today">Today</button>
                </div>
            </div>
            <div class="chart-body">
                <canvas id="revenueChart"></canvas>
            </div>
        </div>

        <div class="chart-card card">
            <div class="chart-header">
                <h3>Orders Summary</h3>
                <div class="chart-controls">
                    <button class="btn small active" data-range="weekly">Weekly</button>
                    <button class="btn small" data-range="monthly">Monthly</button>
                    <button class="btn small" data-range="today">Today</button>
                </div>
            </div>
            <div class="chart-body">
                <canvas id="ordersChart"></canvas>
            </div>
        </div>
    </div>

    <div class="orders-section">
        <div class="orders-card card">
            <div class="orders-header">
                <h3>Order List</h3>
                <div class="orders-controls chart-controls">
                    <button class="btn small" data-range="monthly">Monthly</button>
                    <button class="btn small" data-range="weekly">Weekly</button>
                    <button class="btn small active" data-range="today">Today</button>
                </div>
            </div>
            <div class="orders-body">
                <table class="orders-table">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>ID</th>
                            <th>Date</th>
                            <th>Customer Name</th>
                            <th>School ID</th>
                            <th>Amount</th>
                            <th>Status Order</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @isset($orders)
                        @foreach($orders as $i => $order)
                        <tr>
                            <td>{{ $i + 1 }}</td>
                            <td>#{{ $order->id ?? ($order->order_id ?? '-') }}</td>
                            <td>{{ optional($order->created_at)->format('M d, Y') ?? ($order->date ?? '-') }}</td>
                            <td>{{ $order->user->name ?? $order->customer_name ?? 'Guest' }}</td>
                            <td>{{ $order->user->school_id ?? '-' }}</td>
                            <td>{{ isset($order->amount) ? '₱'.number_format($order->amount,2) : '-' }}</td>
                            @php
                            $statusName = isset($order->status) && is_string($order->status) ? $order->status : ($order->status->name ?? ($order->status ?? 'Pending'));
                            $statusClass = strtolower(str_replace(' ', '-', $statusName));
                            @endphp
                            <td><span class="order-badge status-{{ $statusClass }}">{{ ucfirst($statusName) }}</span></td>
                            <td><a class="action-btn details-btn" href="#" data-order-id="{{ $order->id ?? '' }}"><span class="details-badge status-{{ $statusClass }}"></span> Details</a></td>
                        </tr>
                        @endforeach
                        @else

                        @endisset
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<script>
    // Build Revenue & Orders charts with range controls (monthly/weekly/today)
    (function() {
        const months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];

        let revenueChart = null;
        let ordersChart = null;

        function safeFetch(url) {
            return fetch(url).then(r => {
                if (!r.ok) throw new Error('Network response was not ok');
                return r.json();
            });
        }

        function buildRevenueChart(data, range) {
            let labels = months;
            let revenueSeries = [];
            let incomeSeries = null;
            let expenseSeries = null;

            // If API returned explicit labels/values (today/weekly/monthly), use them
            if (data && Array.isArray(data.labels) && Array.isArray(data.values)) {
                labels = data.labels;
                revenueSeries = data.values;
            } else {
                // legacy: object keyed by month names
                labels = months;
                revenueSeries = labels.map(m => (data && data[m]) ? data[m] : 0);
                if (data && data.income && data.expenses) {
                    incomeSeries = labels.map(m => data.income[m] ?? 0);
                    expenseSeries = labels.map(m => data.expenses[m] ?? 0);
                }
            }

            const ctx = document.getElementById('revenueChart').getContext('2d');
            const gradient = ctx.createLinearGradient(0, 0, 0, 300);
            gradient.addColorStop(0, 'rgba(99, 102, 241, 0.15)');
            gradient.addColorStop(1, 'rgba(99, 102, 241, 0)');

            const datasets = [];
            if (incomeSeries) {
                datasets.push({
                    label: 'Income',
                    data: incomeSeries,
                    borderColor: '#111827',
                    backgroundColor: gradient,
                    tension: 0.3,
                    fill: true
                });
                datasets.push({
                    label: 'Expenses',
                    data: expenseSeries,
                    borderColor: '#ef4444',
                    backgroundColor: 'rgba(239,68,68,0.08)',
                    tension: 0.3,
                    fill: true
                });
            } else {
                datasets.push({
                    label: 'Total Revenue',
                    data: revenueSeries,
                    borderColor: '#111827',
                    backgroundColor: gradient,
                    tension: 0.3,
                    fill: true
                });
            }

            const config = {
                type: 'line',
                data: {
                    labels,
                    datasets
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: true
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: {
                                color: '#f3f4f6'
                            }
                        },
                        x: {
                            grid: {
                                display: false
                            }
                        }
                    }
                }
            };

            if (revenueChart) {
                revenueChart.data = config.data;
                revenueChart.options = config.options;
                revenueChart.update();
            } else {
                revenueChart = new Chart(ctx, config);
            }
        }

        function buildOrdersChart(data, range) {
            const labels = data.labels || data.days || ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'];
            const values = data.values || data.counts || data.orders || [12, 19, 7, 15, 10, 8, 13];

            const ctx2 = document.getElementById('ordersChart').getContext('2d');
            const config = {
                type: 'bar',
                data: {
                    labels,
                    datasets: [{
                        label: 'Orders',
                        data: values,
                        backgroundColor: ['#111827', '#6b7280', '#c7d2fe', '#a78bfa', '#60a5fa']
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: {
                                color: '#f3f4f6'
                            }
                        },
                        x: {
                            grid: {
                                display: false
                            }
                        }
                    }
                }
            };

            if (ordersChart) {
                ordersChart.data = config.data;
                ordersChart.options = config.options;
                ordersChart.update();
            } else {
                ordersChart = new Chart(ctx2, config);
            }
        }

        function loadRevenue(range = 'monthly') {
            safeFetch('/api/chart/data?range=' + encodeURIComponent(range))
                .then(data => buildRevenueChart(data, range))
                .catch(err => {
                    console.warn('Revenue data fetch failed, using demo data', err);
                    // fallback demo linear growth
                    const demo = {};
                    ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'].forEach((m, i) => demo[m] = (i + 1) * 3000 + Math.round(Math.random() * 1000));
                    buildRevenueChart(demo, range);
                });
        }

        function loadOrders(range = 'weekly') {
            safeFetch('/api/chart/orders?range=' + encodeURIComponent(range))
                .then(data => buildOrdersChart(data, range))
                .catch(err => {
                    console.warn('Orders data fetch failed, using demo data', err);
                    const demoLabels = ['Jun 24', 'Jun 25', 'Jun 26', 'Jun 27'];
                    const demoValues = [12000, 15000, 11000, 17000];
                    buildOrdersChart({
                        labels: demoLabels,
                        values: demoValues
                    }, range);
                });
        }

        // wire up controls
        document.addEventListener('DOMContentLoaded', function() {
            // initial loads
            loadRevenue('monthly');
            loadOrders('weekly');

            // attach click handlers to range buttons inside each chart-card
            document.querySelectorAll('.chart-card').forEach(card => {
                const buttons = card.querySelectorAll('.chart-controls .btn');
                buttons.forEach(btn => {
                    btn.addEventListener('click', function() {
                        // toggle active class for this card only
                        buttons.forEach(b => b.classList.remove('active'));
                        this.classList.add('active');
                        const range = this.dataset.range;
                        if (card.querySelector('#revenueChart')) {
                            loadRevenue(range);
                        } else if (card.querySelector('#ordersChart')) {
                            loadOrders(range);
                        }
                    });
                });
            });

            // Orders list controls (populate Order List table based on range)
            function renderOrdersTable(orders) {
                const tbody = document.querySelector('.orders-table tbody');
                if (!tbody) return;
                if (!orders || orders.length === 0) {
                    tbody.innerHTML = '<tr><td colspan="8" style="text-align:center;padding:20px;color:#6b7280">No orders found for this range.</td></tr>';
                    return;
                }
                tbody.innerHTML = orders.map((o, i) => {
                    const date = new Date(o.created_at);
                    const dateStr = date.toLocaleString(undefined, {
                        month: 'short',
                        day: 'numeric',
                        year: 'numeric'
                    });
                    const total = o.total ? '₱' + Number(o.total).toFixed(2) : '-';
                    const status = (o.status || 'pending').toLowerCase();
                    const statusText = (o.status || 'Pending');
                    return `
                        <tr>
                            <td>${i+1}</td>
                            <td>#${o.id}</td>
                            <td>${dateStr}</td>
                            <td>${o.customer_name}</td>
                            <td>${o.school_id || '-'}</td>
                            <td>${total}</td>
                            <td><span class="order-badge status-${status}">${statusText}</span></td>
                            <td><a class="action-btn details-btn" href="#" data-order-id="${o.id}"><span class="details-badge status-${status}"></span> Details</a></td>
                        </tr>`;
                }).join('');
            }

            function loadOrdersList(range = 'monthly') {
                fetch('/api/orders?range=' + encodeURIComponent(range))
                    .then(r => r.ok ? r.json() : Promise.reject(r))
                    .then(json => {
                        renderOrdersTable(json.orders || []);
                    })
                    .catch(err => {
                        console.warn('Failed to load orders list', err);
                        // leave demo rows as-is or show empty
                        renderOrdersTable([]);
                    });
            }

            // wire orders controls
            document.querySelectorAll('.orders-controls .btn').forEach(btn => {
                btn.addEventListener('click', function() {
                    document.querySelectorAll('.orders-controls .btn').forEach(b => b.classList.remove('active'));
                    this.classList.add('active');
                    const range = this.dataset.range;
                    loadOrdersList(range);
                });
            });

            // initial orders list load (match default active button)
            const activeOrdersBtn = document.querySelector('.orders-controls .btn.active');
            loadOrdersList(activeOrdersBtn ? activeOrdersBtn.dataset.range : 'monthly');
            // expose loader functions for external polling
            window.loadRevenue = loadRevenue;
            window.loadOrders = loadOrders;
            window.loadOrdersList = loadOrdersList;
            // expose helper to get current ranges
            window.getActiveChartRange = function(chartId) {
                if (chartId === 'revenue') {
                    const btn = document.querySelector('.chart-card:has(#revenueChart) .chart-controls .btn.active');
                    return btn ? btn.dataset.range : 'monthly';
                }
                if (chartId === 'orders') {
                    const btn = document.querySelector('.chart-card:has(#ordersChart) .chart-controls .btn.active');
                    return btn ? btn.dataset.range : 'weekly';
                }
                return null;
            };
        });
    })();
</script>

<script>
    // Poll admin endpoints and update cards/charts/orders list when changes occur
    (function() {
        let lastStats = {
            totalOrders: parseInt(document.getElementById('total-orders').innerText) || 0,
            completedOrders: parseInt(document.getElementById('completed-orders').innerText) || 0,
            cancelledOrders: parseInt(document.getElementById('cancelled-orders').innerText) || 0,
            totalRevenue: parseFloat(document.getElementById('total-revenue').innerText) || 0,
        };

        async function pollAdmin() {
            try {
                const res = await fetch('/api/admin/stats');
                if (!res.ok) return;
                const stats = await res.json();

                // update cards if changed
                if (stats.totalOrders !== lastStats.totalOrders) {
                    document.getElementById('total-orders').innerText = stats.totalOrders;
                }
                if (stats.completedOrders !== lastStats.completedOrders) {
                    document.getElementById('completed-orders').innerText = stats.completedOrders;
                }
                if (stats.cancelledOrders !== lastStats.cancelledOrders) {
                    document.getElementById('cancelled-orders').innerText = stats.cancelledOrders;
                }
                if (parseFloat(stats.totalRevenue) !== parseFloat(lastStats.totalRevenue)) {
                    document.getElementById('total-revenue').innerText =
                        '₱' + parseFloat(stats.totalRevenue).toFixed(2);
                }

                // if any stat changed, refresh charts and orders list
                if (stats.totalOrders !== lastStats.totalOrders || stats.completedOrders !== lastStats.completedOrders || stats.cancelledOrders !== lastStats.cancelledOrders || parseFloat(stats.totalRevenue) !== parseFloat(lastStats.totalRevenue)) {
                    // reload charts with their active ranges
                    try {
                        const revRangeBtn = document.querySelector('.chart-card:has(#revenueChart) .chart-controls .btn.active');
                        const revRange = revRangeBtn ? revRangeBtn.dataset.range : 'monthly';
                        const ordRangeBtn = document.querySelector('.chart-card:has(#ordersChart) .chart-controls .btn.active');
                        const ordRange = ordRangeBtn ? ordRangeBtn.dataset.range : 'weekly';
                        if (window.loadRevenue) window.loadRevenue(revRange);
                        if (window.loadOrders) window.loadOrders(ordRange);
                        const activeOrdersBtn = document.querySelector('.orders-controls .btn.active');
                        if (window.loadOrdersList) window.loadOrdersList(activeOrdersBtn ? activeOrdersBtn.dataset.range : 'monthly');
                    } catch (e) {}
                }

                lastStats = {
                    totalOrders: stats.totalOrders,
                    completedOrders: stats.completedOrders,
                    cancelledOrders: stats.cancelledOrders,
                    totalRevenue: stats.totalRevenue,
                };
            } catch (e) {
                // ignore
            }
        }

        setInterval(pollAdmin, 5000);
    })();
</script>

<!-- Order Details Modal (shared behavior with Order List page) -->
<div class="modal_orders-list">
    <span>Amount</span>
    <div class="order-transaction-details">
        <div class="orders-details-header">
            <div class="order-amount">
                <span id="order-amount">₱135.00 PHP</span>
            </div>
            <div class="orders-status">
                <iconify-icon id="modal-circle" icon="material-symbols-light:circle" class="orders-pending-icon"></iconify-icon>
                <span id="order-status" data-order-id="">Processing</span>
            </div>
        </div>
        <button id="ready-order" data-order-id="" onclick="readyOrder(this.dataset.orderId)">Change Order Status</button>
    </div>
    <div class="orders-date-payment">
        <div class="orders-transaction-date">
            <span>Transaction Date</span>
            <span id="order-date">02/11/24</span>
        </div>
        <div class="orders-transaction-payment">
            <span>Payment Type</span>
            <span id="payment-type">GCash</span>
        </div>
    </div>
    <span class="transaction-details-txt">Transaction Details</span>
    <div class="orders-transaction-details">
        <div class="orders-id-username">
            <span>Order ID</span>
            <span>Username</span>
            <span>School ID</span>
            <span>Role</span>
        </div>
        <div class="orders-order-details">
            <span id="order-id">SPC2024-69</span>
            <span id="user-name">Romarc Bongcaron</span>
            <span id="user-school-id">2022-2022</span>
            <span id="role-name">STUDENT</span>
        </div>
    </div>
    <span class="transaction-product_list-txt">Product List</span>
    <div class="orders-product_list-qr_code">
        <div id="orders-products-list" class="orders-products-list">
            <!-- <div id="orders-products-txt" class="orders-products-txt">
                    <span></span>
                </div> -->
        </div>
        <div class="orders-qr-code">
            <div id="qrcode"></div>
        </div>
    </div>
    <div class="close-modal4">
        <iconify-icon id="close-details" icon="material-symbols-light:close"></iconify-icon>
    </div>
    <div class="action-container">
        <div class="order-action">
            <button id="complete-order" data-order-id="" onclick="completeOrder(this.dataset.orderId)">Complete Order</button>
            <button id="cancel-order" data-order-id="" onclick="cancelOrder(this.dataset.orderId)">Cancel Order</button>
        </div>
    </div>
</div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const orderlistModal = document.querySelector('.modal_orders-list');
        const closeModal4 = document.querySelector('.close-modal4');

        if (closeModal4) {
            closeModal4.addEventListener('click', () => {
                orderlistModal.classList.remove('active');
            });
        }

        // Delegate click for Details buttons in the admin orders table
        document.body.addEventListener('click', function(e) {
            const btn = e.target.closest('.action-btn');
            if (!btn) return;
            e.preventDefault();
            const orderId = btn.dataset.orderId || (btn.getAttribute('href') || '').split('/').pop();
            if (!orderId) return;
            openOrderModal(orderId);
        });

        function openOrderModal(orderId) {
            document.getElementById('orders-products-list').innerHTML = '';
            document.getElementById('qrcode').innerHTML = '';
            document.getElementById('complete-order').dataset.orderId = orderId;
            document.getElementById('cancel-order').dataset.orderId = orderId;
            document.getElementById('ready-order').dataset.orderId = orderId;
            document.getElementById('order-status').dataset.orderId = orderId;

            fetch(`/order/get/details/${orderId}`)
                .then(r => r.ok ? r.json() : Promise.reject(r))
                .then(data => {
                    orderlistModal.classList.add('active');
                    document.getElementById('order-amount').innerHTML = `₱${data.amount}`;
                    document.getElementById('modal-circle').style.color = data.status_id === 1 ? '#FFD700' : '#008000';
                    document.getElementById('order-status').innerHTML = data.status_name;
                    document.getElementById('order-date').innerHTML = data.date;
                    document.getElementById('payment-type').innerHTML = data.payment_type;
                    document.getElementById('order-id').innerHTML = `SPC2024-${data.id}`;
                    document.getElementById('user-name').innerHTML = data.user.name || '-';
                    document.getElementById('user-school-id').innerHTML = data.user.school_id || '-';
                    document.getElementById('role-name').innerHTML = data.user.role?.name || '-';

                    // load products
                    fetch(`/order/get/product/${orderId}`)
                        .then(r => r.ok ? r.json() : Promise.reject(r))
                        .then(products => {
                            const list = document.getElementById('orders-products-list');
                            products.forEach(cart => {
                                const item = document.createElement('div');
                                item.className = 'orders-products-txt';
                                const span = document.createElement('span');
                                span.textContent = `${cart.product_name} x${cart.product_quantity}`;
                                item.appendChild(span);
                                list.appendChild(item);
                            });
                        }).catch(err => console.error(err));

                    try {
                        new QRCode('qrcode', {
                            text: `${data.id}`,
                            width: 50,
                            height: 50,
                            colorDark: '#000000',
                            colorLight: '#ffffff',
                            correctLevel: QRCode.CorrectLevel.H
                        });
                    } catch (e) {}
                })
                .catch(err => {
                    console.error('Error fetching order details', err);
                });
        }

        window.readyOrder = function(orderId) {
            fetch(`/order/change/status/${orderId}`)
                .then(r => r.ok ? r.json() : Promise.reject(r))
                .then(data => {
                    // refresh page to reflect status change in the table
                    location.reload();
                }).catch(err => console.error(err));
        }

        window.completeOrder = function(orderId) {
            fetch(`/order/complete/${orderId}`)
                .then(r => r.ok ? r.json() : Promise.reject(r))
                .then(id => {
                    location.reload();
                }).catch(err => console.error(err));
        }

        window.cancelOrder = function(orderId) {
            fetch(`/order/cancel/${orderId}`)
                .then(r => r.ok ? r.json() : Promise.reject(r))
                .then(id => {
                    location.reload();
                }).catch(err => console.error(err));
        }
    });
</script>
@endsection