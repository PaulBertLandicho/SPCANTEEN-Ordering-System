@extends('layouts.user')

<script type="text/javascript" src="js/jquery.min.js"></script>
<script type="text/javascript" src="js/qrcode.js"></script>
@section('content')
<div class="container">
    <div class="cart-content">
        <div class="code-content">
            <div class="status-order-container">
                <span id="status-order" data-status="{{ $order->status_id }}" style="font-size: 34px;">{{$order->status->name}}</span>
            </div>
            @if($order->status_id == 2)
            <span id="user-school-id" style="font-size: 20px; margin-top: 100px">{{Auth::user()->school_id}}</span>
            <div class="code-container">
                <div id="qrcode"></div>
            </div>
            <h3 id="code-txt">Scan this code to see your order.</h3>
            <span id="code-txt">Your order is ready for pickup.</span>
            @else
            <img id="prep-animation" src="images/Preparing.gif" alt="">
            <span id="preparing-txt">We already preparing your order <br> please wait.</span>
            @endif

            <div id="reviewModal" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%;
background:rgba(0,0,0,0.5); justify-content:center; align-items:center;">

                <div style="background:#fff; padding:20px; border-radius:10px; width:350px;">
                    <h3>Rate your order</h3>

                    <input type="hidden" id="review_product_id">
                    <input type="hidden" id="rating" value="0">
                    <div id="stars">
                        ⭐⭐⭐⭐⭐
                    </div>

                    <div class="stars" id="starContainer">
                        <span class="star" data-rating="1">⭐</span>
                        <span class="star" data-rating="2">⭐</span>
                        <span class="star" data-rating="3">⭐</span>
                        <span class="star" data-rating="4">⭐</span>
                        <span class="star" data-rating="5">⭐</span>
                    </div>
                    <textarea id="feedback" placeholder="Write feedback..." style="width:100%; margin-top:10px;"></textarea>

                    <button onclick="submitReview()" style="margin-top:10px;">Submit</button>
                </div>
            </div>
            @php
            $firstItem = $order->items?->first();
            @endphp
            <style>
                .stars {
                    display: flex;
                    gap: 8px;
                    font-size: 30px;
                    cursor: pointer;
                }

                .star {
                    opacity: 0.3;
                    transition: 0.2s;
                }

                .star.active {
                    opacity: 1;
                    transform: scale(1.2);
                }
            </style>

            <script>
                window.reviewProductId = null;
            </script>

            <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>


            <script type="text/javascript">
                // Poll order status every 3 seconds and update UI in-place
                (function() {
                    const orderId = "{{ $order->id }}";
                    let currentStatus = parseInt("{{ $order->status_id }}", 10);
                    let qrCreated = false;

                    // set initial status color from server-provided status
                    const initStatusEl = document.getElementById('status-order');
                    if (initStatusEl) {
                        if (currentStatus === 1) initStatusEl.style.color = '#FFD700';
                        else if (currentStatus === 2 || currentStatus === 3) initStatusEl.style.color = '#008000';
                        else if (currentStatus === 4) initStatusEl.style.color = '#FF0000';
                        else initStatusEl.style.color = '#000000';
                    }

                    function showQRCode() {
                        const qrcodeEl = document.getElementById('qrcode');
                        const codeTxt = document.getElementById('code-txt');
                        const prepAnim = document.getElementById('prep-animation');
                        const prepTxt = document.getElementById('preparing-txt');
                        const schoolEl = document.getElementById('user-school-id');

                        if (prepAnim) prepAnim.style.display = 'none';
                        if (prepTxt) prepTxt.style.display = 'none';
                        if (codeTxt) codeTxt.style.display = '';
                        if (schoolEl) schoolEl.style.display = '';

                        if (!qrCreated && qrcodeEl) {
                            try {
                                new QRCode(qrcodeEl, orderId);
                                qrCreated = true;
                            } catch (e) {
                                console.error('QR code error', e);
                            }
                        }
                    }

                    const stars = document.querySelectorAll('.star');
                    const ratingInput = document.getElementById('rating');

                    stars.forEach(star => {
                        star.addEventListener('click', () => {
                            const rating = parseInt(star.dataset.rating);

                            // set hidden input value
                            ratingInput.value = rating;

                            // reset stars
                            stars.forEach(s => s.classList.remove('active'));

                            // activate correct stars
                            stars.forEach(s => {
                                if (parseInt(s.dataset.rating) <= rating) {
                                    s.classList.add('active');
                                }
                            });
                        });
                    });

                    function showPreparing() {
                        const qrcodeEl = document.getElementById('qrcode');
                        const codeTxt = document.getElementById('code-txt');
                        const prepAnim = document.getElementById('prep-animation');
                        const prepTxt = document.getElementById('preparing-txt');
                        const schoolEl = document.getElementById('user-school-id');

                        if (qrcodeEl) qrcodeEl.innerHTML = '';
                        if (codeTxt) codeTxt.style.display = 'none';
                        if (prepAnim) prepAnim.style.display = '';
                        if (prepTxt) prepTxt.style.display = '';
                        if (schoolEl) schoolEl.style.display = 'none';
                        qrCreated = false;
                    }

                    let successShown = false;

                    async function pollStatus() {
                        try {
                            const res = await fetch(`/order/status/${orderId}`);
                            if (!res.ok) return; // ignore errors
                            const data = await res.json();
                            if (!data || typeof data.status_id === 'undefined') return;

                            const newStatus = parseInt(data.status_id, 10);
                            if (newStatus === currentStatus) return;

                            // update current status and UI
                            currentStatus = newStatus;
                            const statusEl = document.getElementById('status-order');
                            if (statusEl && data.status_name) {
                                statusEl.innerText = data.status_name;
                                // set color: preparing -> #FFD700; prepared/successful -> #008000; cancelled -> #FF0000
                                if (newStatus === 1) statusEl.style.color = '#FFD700';
                                else if (newStatus === 2 || newStatus === 3) statusEl.style.color = '#008000';
                                else if (newStatus === 4) statusEl.style.color = '#FF0000';
                                else statusEl.style.color = '#000000';
                            }

                            if (newStatus === 2) {
                                // prepared -> show QR (try twice then reload as fallback)
                                showQRCode();
                                // try again shortly in case QR lib wasn't ready
                                setTimeout(() => {
                                    if (!qrCreated) showQRCode();
                                }, 800);
                                // if still not created after a short while, reload page to ensure QR appears
                                setTimeout(() => {
                                    if (!qrCreated) location.reload();
                                }, 3000);
                            } else if (newStatus === 1) {
                                // preparing -> show animation
                                showPreparing();
                            } else if (newStatus === 3) {
                                if (!successShown) {
                                    successShown = true;

                                    document.getElementById('reviewModal').style.display = 'flex';

                                    // OPTIONAL: auto redirect after submit
                                }


                            } else if (newStatus === 4) {
                                // cancelled -> alert and redirect
                                if (statusEl) statusEl.innerText = data.status_name || 'Cancelled';
                                alert('Your order was cancelled.');
                                window.location.href = '/';
                            }
                        } catch (e) {
                            // network error — ignore and continue
                        }
                    }

                    // initialize UI based on current status
                    if (currentStatus === 2) showQRCode();
                    if (currentStatus === 1) showPreparing();

                    // start polling
                    setInterval(pollStatus, 3000);
                })();

                async function submitReview() {
                    const rating = document.getElementById('rating').value;
                    const feedback = document.getElementById('feedback').value;

                    if (!window.reviewProductId) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Missing Product',
                            text: 'Product ID not found. Cannot submit review.'
                        });
                        return;
                    }

                    const res = await fetch('/review/store', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({
                            order_id: "{{ $order->id }}",
                            product_id: window.reviewProductId,
                            rating: rating,
                            feedback: feedback
                        })
                    });

                    const data = await res.json();

                    if (data.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Thank you!',
                            text: 'Your review has been submitted.',
                            timer: 1500,
                            showConfirmButton: false
                        });

                        document.getElementById('reviewModal').style.display = 'none';

                        setTimeout(() => {
                            window.location.href = '/';
                        }, 1600);
                    }
                }
            </script>
        </div>
    </div>
</div>
<div id="review-data"
    data-product-id="{{ $firstItem ? $firstItem->product_id : '' }}">
</div>

<script>
    window.reviewProductId =
        document.getElementById('review-data').dataset.productId;
</script>
@endsection