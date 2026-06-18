@extends('layouts.admin')

<script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>
<script src="https://cdn.jsdelivr.net/npm/fuse.js@6"></script>
@section('content1')
<div class="content1">
    <h1 style="margin-bottom: 5px;">Order List</h1>
    <span style="margin-left: 7px; font-size: 30px;">{{$formattedDate}}</span>
    <div class="add-header-orders">
        <button class="qr-btn open-modal3">
            <iconify-icon icon="mdi:line-scan" id="qr-code-scanner"></iconify-icon>
            <span>QR Scanner</span>
        </button>
        <div class="search-container-orders">
            <input id="search" type="text" name="search" placeholder="Search...">
            <button type="submit" class="search-button-orders">
                <i class="fas fa-search"></i>
            </button>
        </div>
    </div>
    <div id="order-data" data-orders='@json($orders)'></div>
    <div class="orders">
        @if($orders->isEmpty())
        <div class="container-empty">
            <img src="images/empties.png" alt="Empty">
            <div class="empty-text">No orders found</div>
        </div>
        @else
        @foreach($orders as $order)
        <div class="transaction-container1" id="transcation-container-{{$order->id}}" data-order-id="SPC2024-{{$order->id}}" data-user-name="{{$order->username}}">
            <div class="orders-detail">
                @if($order->status_id === 1)
                <iconify-icon id="circle-main-{{$order->id}}" icon="material-symbols-light:circle" style="color: #FFD700;"></iconify-icon>
                @else
                <iconify-icon id="circle-main-{{$order->id}}" icon="material-symbols-light:circle" style="color: #008000;"></iconify-icon>
                @endif
            </div>
            <div class="orders-details">
                <span id="orders-header">Status</span>
                <span id="status-name-{{$order->id}}">{{$order->status->name}}</span>
            </div>
            <div class="orders-details">
                <span id="orders-header">Amount</span>
                <span>₱ {{$order->amount}}</span>
            </div>
            <div class="orders-details">
                <span id="orders-header">School ID</span>
                <span>{{$order->user->school_id}}</span>
            </div>
            <div class="orders-details">
                <span id="orders-header">Order ID</span>
                <span>SPC2024-{{$order->id}}</span>
            </div>
            <div class="orders-detailed open-modal4" data-order-id="{{$order->id}}">
                <span id="orders-details">Details</span>
            </div>
        </div>
        @endforeach
        @endif
    </div>

    <!--------- QR Scanner Modal -------->
    <div class="modal_qr-scanner">
        <div class="close-modal3">
            <iconify-icon id="close" icon="material-symbols-light:close"></iconify-icon>
        </div>
        <div class="modal-container">
            <span id="id-not-found" style="color: red; margin-bottom: 10px; display:none;">Order is not found or not yet prepared.</span>
            <div id="reader"></div>
        </div>
    </div>

    <!--------- Order Details Modal -------->
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
    let scanningLocked = false;
    let lastScannedCode = null;

    const openModal3 = document.querySelector(".open-modal3");
    const closeModal3 = document.querySelector(".close-modal3");
    const qrscannerModal = document.querySelector(".modal_qr-scanner");
    const orderlistModal = document.querySelector(".modal_orders-list");

    if (openModal3 && closeModal3 && qrscannerModal) {
        openModal3.addEventListener("click", () => {
            qrscannerModal.classList.add("active");

            function onScanSuccess(decodedText, decodedResult) {

                // 🚫 BLOCK MULTIPLE SCANS
                if (scanningLocked) return;

                // 🚫 BLOCK SAME QR REPEATED TRIGGERS
                if (decodedText === lastScannedCode) return;

                scanningLocked = true;
                lastScannedCode = decodedText;

                // auto unlock after 3 seconds
                setTimeout(() => {
                    scanningLocked = false;
                }, 3000);

                const orderId = decodedText;

                function renderProducts(products) {
                    const list = document.getElementById('orders-products-list');

                    // 🚫 HARD RESET (MOST IMPORTANT LINE)
                    list.innerHTML = '';

                    const seen = new Set();

                    products.forEach(p => {
                        const key = `${p.product_id}-${p.product_name} ${p.product_quantity} ${p.product_size} ${p.product_measurement} ${p.product_unit}`;

                        if (seen.has(key)) return;
                        seen.add(key);

                        const div = document.createElement('div');
                        div.className = 'orders-products-txt';
                        div.innerHTML = `<span>${p.product_name} x${p.product_quantity} ${p.product_size} ${p.product_measurement} ${p.product_unit}</span>`;

                        list.appendChild(div);
                    });
                }
                document.getElementById("complete-order").dataset.orderId = orderId;
                document.getElementById("cancel-order").dataset.orderId = orderId;
                document.getElementById("ready-order").dataset.orderId = orderId;
                document.getElementById("order-status").dataset.orderId = orderId;

                fetch(`/order/get/details/scan/${orderId}`)
                    .then(response => response.json())
                    .then(data => {
                        qrscannerModal.classList.remove("active");
                        orderlistModal.classList.add("active");
                        document.getElementById("order-amount").innerHTML = `₱${data.amount}`;
                        if (data.status_id === 1) {
                            document.getElementById("modal-circle").style.color = "#FFD700";
                        } else {
                            document.getElementById("modal-circle").style.color = "#008000";
                        }
                        document.getElementById("order-status").innerHTML = data.status_name;
                        document.getElementById("order-date").innerHTML = data.date;
                        document.getElementById("payment-type").innerHTML = data.payment_type;
                        document.getElementById("order-id").innerHTML = `SPC2024-${data.id}`;
                        document.getElementById("user-name").innerHTML = data.user.name;
                        document.getElementById("user-school-id").innerHTML = data.user.school_id;
                        document.getElementById("role-name").innerHTML = data.user.role.name;

                        //Get Products
                        fetch(`/order/get/product/${orderId}`)
                            .then(response => response.json())
                            .then(data => {
                                //console.log(data);
                                data.forEach((cart) => {
                                    const ordersProductsList = document.getElementById('orders-products-list');

                                    if (ordersProductsList) {
                                        const newProductItem = document.createElement('div');
                                        newProductItem.classList.add('orders-products-txt');

                                        const productNameSpan = document.createElement('span');
                                        productNameSpan.textContent =
                                            `${cart.product_name} x${cart.product_quantity} : ${cart.product_size} - ${cart.product_measurement}${cart.product_unit}`;

                                        newProductItem.appendChild(productNameSpan);
                                        ordersProductsList.appendChild(newProductItem);
                                    }
                                });
                            })
                            .catch(error => {
                                console.error('Error:', error);
                            });

                        var qrcode = new QRCode("qrcode", {
                            text: `${data.id}`,
                            width: 50,
                            height: 50,
                            colorDark: "maroon",
                            colorLight: "#ffffff",
                            correctLevel: QRCode.CorrectLevel.H
                        });

                    })
                    .catch(error => {
                        document.getElementById("id-not-found").style.display = "block";
                        setTimeout(function() {
                            console.log("hu");
                            document.getElementById("id-not-found").style.display = "none";
                        }, 2500);
                    });

            }

            let html5QrcodeScanner = new Html5QrcodeScanner(
                "reader", {
                    fps: 1,
                    qrbox: {
                        width: 200,
                        height: 200
                    }
                },
                /* verbose= */
                false);
            html5QrcodeScanner.render(onScanSuccess);
        });

        closeModal3.addEventListener("click", () => {
            qrscannerModal.classList.remove("active");
        });
    }

    const openModal4Buttons = document.querySelectorAll(".open-modal4");
    openModal4Buttons.forEach((btn) => {
        btn.addEventListener("click", () => {
            document.getElementById("orders-products-list").innerHTML = "";
            document.getElementById("qrcode").innerHTML = "";
            document.getElementById("complete-order").dataset.orderId = "";
            document.getElementById("cancel-order").dataset.orderId = "";
            document.getElementById("ready-order").dataset.orderId = "";
            document.getElementById("order-status").dataset.orderId = "";

            orderlistModal.classList.add("active");
            const orderId = btn.dataset.orderId;

            document.getElementById("complete-order").dataset.orderId = orderId;
            document.getElementById("cancel-order").dataset.orderId = orderId;
            document.getElementById("ready-order").dataset.orderId = orderId;
            document.getElementById("order-status").dataset.orderId = orderId;

            fetch(`/order/get/details/${orderId}`)
                .then(response => response.json())
                .then(data => {
                    document.getElementById("order-amount").innerHTML = `₱${data.amount}`;
                    if (data.status_id === 1) {
                        document.getElementById("modal-circle").style.color = "#FFD700";
                    } else {
                        document.getElementById("modal-circle").style.color = "#008000";
                    }
                    document.getElementById("order-status").innerHTML = data.status_name;
                    document.getElementById("order-date").innerHTML = data.date;
                    document.getElementById("payment-type").innerHTML = data.payment_type;
                    document.getElementById("order-id").innerHTML = `SPC2024-${data.id}`;
                    document.getElementById("user-name").innerHTML = data.user.name;
                    document.getElementById("user-school-id").innerHTML = data.user.school_id;
                    document.getElementById("role-name").innerHTML = data.user.role.name;

                    //Get Products
                    fetch(`/order/get/product/${orderId}`)
                        .then(response => response.json())
                        .then(data => {
                            //console.log(data);
                            data.forEach((cart) => {
                                const ordersProductsList = document.getElementById('orders-products-list');

                                if (ordersProductsList) {
                                    const newProductItem = document.createElement('div');
                                    newProductItem.classList.add('orders-products-txt');

                                    const productNameSpan = document.createElement('span');
                                    productNameSpan.textContent =
                                        `${cart.product_name} x${cart.product_quantity} : ${cart.product_size} - ${cart.product_measurement}${cart.product_unit}`;

                                    newProductItem.appendChild(productNameSpan);
                                    ordersProductsList.appendChild(newProductItem);
                                }
                            });
                        })
                        .catch(error => {
                            console.error('Error:', error);
                        });

                    var qrcode = new QRCode("qrcode", {
                        text: `${data.id}`,
                        width: 50,
                        height: 50,
                        colorDark: "maroon",
                        colorLight: "#ffffff",
                        correctLevel: QRCode.CorrectLevel.H
                    });


                })
                .catch(error => {
                    console.error('Error:', error);
                });

        });
    });

    const closeModal4 = document.querySelector(".close-modal4");
    if (closeModal4) {
        closeModal4.addEventListener("click", () => {
            orderlistModal.classList.remove("active");
        });
    }

    function readyOrder(orderId) {
        fetch(`/order/change/status/${orderId}`)
            .then(response => response.json())
            .then(data => {
                if (data == 1) {
                    document.getElementById(`modal-circle`).style.color = '#FFD700';
                    document.getElementById(`order-status`).innerHTML = 'Preparing';
                    document.getElementById(`status-name-${orderId}`).innerHTML = 'Preparing';
                    document.getElementById(`circle-main-${orderId}`).style.color = '#FFD700';
                } else {
                    document.getElementById(`modal-circle`).style.color = '#008000';
                    document.getElementById(`order-status`).innerHTML = 'Prepared';
                    document.getElementById(`status-name-${orderId}`).innerHTML = 'Prepared';
                    document.getElementById(`circle-main-${orderId}`).style.color = '#008000';
                }
            })
            .catch(error => {
                console.error('Error:', error);
            });
    }

    function completeOrder(orderId) {
        fetch(`/order/complete/${orderId}`)
            .then(response => response.json())
            .then(data => {
                document.getElementById(`transcation-container-${data}`).style.display = 'none';
                orderlistModal.classList.remove("active");
            })
            .catch(error => {
                console.error('Error:', error);
            });
    }

    function cancelOrder(orderId) {
        fetch(`/order/cancel/${orderId}`)
            .then(response => response.json())
            .then(data => {
                document.getElementById(`transcation-container-${data}`).style.display = 'none';
                orderlistModal.classList.remove("active");
            })
            .catch(error => {
                console.error('Error:', error);
            });
    }


    const orderDataElement = document.getElementById('order-data');
    const orders = JSON.parse(orderDataElement.getAttribute('data-orders')).map(order => ({
        id: `SPC2024-${order.id}`,
        user: order.username,
        status: order.status_name,
        school_id: order.school_id,
    }));

    const options = {
        keys: ['id', 'user', 'status', 'school_id'],
        threshold: 0.1
    };

    const fuse = new Fuse(orders, options);

    const displayResults = (results) => {
        const containers = document.querySelectorAll('.transaction-container1');
        containers.forEach(container => container.style.display = 'none');

        results.forEach(result => {
            const container = document.querySelector(`.transaction-container1[data-order-id="${result.item.id}"]`);
            // console.log(result.item);
            if (container) {
                container.style.display = '';
            }
        });
    };

    const displayAllResults = () => {
        const containers = document.querySelectorAll('.transaction-container1');
        containers.forEach(container => container.style.display = '');
    };

    document.getElementById('search').addEventListener('input', (e) => {
        const query = e.target.value;

        if (query.trim() === '') {
            displayAllResults();
        } else {
            const results = fuse.search(query);
            // console.log(results);
            displayResults(results);
        }
    });

    displayAllResults();

    document.getElementById('search').addEventListener('keydown', (e) => {
        if (e.key === 'Enter') {
            e.target.blur();
        }
    });
</script>
<script>
    (function() {

        function getCurrentMaxId() {
            const containers = document.querySelectorAll('.transaction-container1');
            let max = 0;

            containers.forEach(c => {
                const id = c.id.replace('transcation-container-', '');
                const n = parseInt(id, 10);
                if (!isNaN(n) && n > max) max = n;
            });

            return max;
        }

        let lastId = getCurrentMaxId();

        async function pollNewOrders() {
            try {
                const res = await fetch(`/api/orders/latest?last_id=${lastId}`);
                const orders = await res.json();

                if (!orders.length) return;

                orders.forEach(order => {
                    addOrderToDOM(order);
                    lastId = Math.max(lastId, order.id);
                });

            } catch (err) {
                console.error("Polling error:", err);
            }
        }

        function addOrderToDOM(order) {
            const container = document.querySelector('.orders');

            const div = document.createElement('div');
            div.className = 'transaction-container1';
            div.id = `transcation-container-${order.id}`;
            div.setAttribute('data-order-id', `SPC2024-${order.id}`);
            div.setAttribute('data-user-name', order.customer_name);

            div.innerHTML = `
            <div class="orders-detail">
                <iconify-icon 
                    id="circle-main-${order.id}" 
                    icon="material-symbols-light:circle"
                    style="color: ${order.status_id == 1 ? '#FFD700' : '#008000'}">
                </iconify-icon>
            </div>

            <div class="orders-details">
                <span>Status</span>
                <span id="status-name-${order.id}">${order.status}</span>
            </div>

            <div class="orders-details">
                <span>Amount</span>
                <span>₱ ${order.total}</span>
            </div>

            <div class="orders-details">
                <span>School ID</span>
                <span>${order.school_id ?? '-'}</span>
            </div>

            <div class="orders-details">
                <span>Order ID</span>
                <span>SPC2024-${order.id}</span>
            </div>

            <div class="orders-detailed open-modal4" data-order-id="${order.id}">
                <span>Details</span>
            </div>
        `;

            container.prepend(div);

            div.querySelector('.open-modal4').addEventListener('click', () => {
                openOrderModal(order.id);
            });
        }

        setInterval(pollNewOrders, 3000);

    })();
</script>
@endsection