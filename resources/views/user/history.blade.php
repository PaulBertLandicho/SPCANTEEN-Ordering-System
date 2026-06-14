@extends('layouts.user')

@section('content')
<div class="content">
    <div class="history-content">
        <div class="history-header">
            <span id="history-header-txt">MY HISTORY</span>
        </div>
        <div class="history">
            @if ($orders->isEmpty())
            <div class="container-empty">
                <img src="images/empties.png" alt="Empty">
                <div class="empty-text">No History</div>
            </div>
            @else
            @foreach ($orders as $order)
            <button class="history-container show-modal" data-order-id="{{$order->id}}">
                <img id="history-product-image" src="images/product/{{ $order->carts->random()->product->image }}" alt="Order {{$order->id}}">
                <div class="history-details">
                    <div class="history-name-date">
                        <span id="history-product-name">SPC2024-{{$order->id}}</span>
                        <span id="history-product-date">{{$order->created_at->format('F d Y')}}</span>
                    </div>
                    <div class="history-item-price">
                        <span id="history-product-item">{{$order->totalCarts}} Items</span>
                        <span id="history-product-price">₱{{$order->amount}}</span>
                    </div>
                </div>
            </button>
            @endforeach
            @endif
        </div>
    </div>
    @include('layouts.components.user.user_navbar')
    @foreach ($orders as $order)
    <form class="bottom-sheet" id="bottom-sheet-{{$order->id}}">
        <div class="sheet-overlay"></div>
        <div class="content">
            <div class="header">
                <div class="drag-icon"><span class="header-icon"></span></div>
            </div>
            <div class="body">
                <div class="modal-user-history">
                    @foreach ($order->carts as $cart)
                    <div class="modal-history-container">
                        <img id="history-product-image" src="images/product/{{$cart->product->image}}" alt="{{$cart->product->name}}">
                        <div class="history-details">
                            <div class="history-name-date">
                                <span id="history-product-name">{{$cart->product->name}}</span>
                                <span id="history-product-date">{{$cart->created_at->format('F d Y')}}</span>
                            </div>
                            <div class="history-item-price">
                                <span id="history-product-item">Quantity: {{$cart->quantity}}</span>
                                <span id="history-product-price">₱{{$cart->quantity * $cart->product->price}}</span>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </form>
    @endforeach
</div>
<script>
    const showModalBtns = document.querySelectorAll(".show-modal");


    showModalBtns.forEach(btn => {
        btn.addEventListener("click", () => {
            const orderId = btn.dataset.orderId;

            const bottomSheet = document.querySelector(`#bottom-sheet-${orderId}`);
            const sheetOverlay = bottomSheet.querySelector(".sheet-overlay");
            const sheetContent = bottomSheet.querySelector(".content");
            const dragIcon = bottomSheet.querySelector(".drag-icon");

            let isDragging = false,
                startY, startHeight;

            const showBottomSheet = () => {
                bottomSheet.classList.add("show");
                document.body.style.overflowY = "hidden";
                updateSheetHeight(50);
            }

            const updateSheetHeight = (height) => {
                sheetContent.style.height = `${height}vh`;
                bottomSheet.classList.toggle("fullscreen", height === 100);
            }

            const hideBottomSheet = () => {
                bottomSheet.classList.remove("show");
                document.body.style.overflowY = "auto";
            }

            const dragStart = (e) => {
                isDragging = true;
                startY = e.pageY || e.touches?.[0].pageY;
                startHeight = parseInt(sheetContent.style.height);
                bottomSheet.classList.add("dragging");
            }

            const dragging = (e) => {
                if (!isDragging) return;
                const delta = startY - (e.pageY || e.touches?.[0].pageY);
                const newHeight = startHeight + delta / window.innerHeight * 100;
                updateSheetHeight(newHeight);
            }

            const dragStop = () => {
                isDragging = false;
                bottomSheet.classList.remove("dragging");
                const sheetHeight = parseInt(sheetContent.style.height);
                sheetHeight < 25 ? hideBottomSheet() : sheetHeight > 75 ? updateSheetHeight(100) : updateSheetHeight(50);
            }

            dragIcon.addEventListener("mousedown", dragStart);
            document.addEventListener("mousemove", dragging);
            document.addEventListener("mouseup", dragStop);
            dragIcon.addEventListener("touchstart", dragStart);
            document.addEventListener("touchmove", dragging);
            document.addEventListener("touchend", dragStop);
            sheetOverlay.addEventListener("click", hideBottomSheet);

            showBottomSheet();
        });
    });
</script>
@endsection