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
                                // successful -> redirect to home
                                window.location.href = '/';
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
            </script>
        </div>
    </div>
</div>
@endsection