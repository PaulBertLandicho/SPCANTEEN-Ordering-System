@extends('layouts.user')

@section('content')
<div class="container">
    <div class="cart-content">
        <div class="cart-header">
            <a href="/">
                <iconify-icon id="back-btn" icon="material-symbols:arrow-back-ios"></iconify-icon>
            </a>
            <h1 id="mycart-txt">MY CART</h1>
            <a class="close-btn" href="/">
                <iconify-icon id="close-btn" icon="material-symbols-light:close"></iconify-icon>
            </a>
        </div>
        <div class="cart">
            <div class="cart-info">
                <div class="cart-row">
                    @if ($carts->isEmpty())
                    <div class="container-empty">
                        <img src="images/empties.png" alt="Empty">
                        <div class="empty-text">No cart added</div>
                    </div>
                    @else
                    @foreach ($carts as $cart)
                    <div class="cart-container" id="cart-container-{{$cart->id}}">
                        <div class="cart-contents">
                            <div class="cart-infos">
                                <div class="cart-image">
                                    <img id="cart-image" src="images/product/{{$cart->product->image}}" alt="">
                                </div>
                                <div class="content-details">
                                    <div class="cart-details">
                                        <div class="cart-detail">
                                            <h1 id="cart-name">{{$cart->product->name}}</h1>
                                            <span id="cart-time">{{$cart->product->time}} min</span>
                                        </div>
                                        <span id="cart-size" style="display:block; font-size:13px; color:gray;">
                                            • {{ $cart->product->size }} - {{ $cart->product->measurement }}{{ $cart->product->unit }}
                                        </span>
                                        <button class="cart-delete" data-cart-id="{{$cart->id}}">
                                            <iconify-icon icon="ion:trash-sharp"></iconify-icon>
                                        </button>
                                    </div>
                                    <div class="content-button">
                                        <div class="cart-price">
                                            <h3 id="cart-price-{{$cart->id}}">₱{{$cart->sum}}</h3>
                                        </div>
                                        <div class="quantity-button">
                                            <button class="plus-icon" id="plus-icon-{{$cart->id}}" data-cart-id="{{$cart->id}}">
                                                <iconify-icon id="quantity-icons" icon="mdi:plus"></iconify-icon>
                                            </button>
                                            <span class="cart-quantity" id="cart-quantity-{{$cart->id}}">{{$cart->quantity}}</span>
                                            <button class="minus-icon" id="minus-icon-{{$cart->id}}" data-cart-id="{{$cart->id}}">
                                                <iconify-icon id="quantity-icons" icon="mdi:minus"></iconify-icon>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                    @endif
                </div>
            </div>
        </div>
        @if (!$carts->isEmpty())
        <div class="price-container">
            <div class="price-txt">
                <div class="products-selected">
                    <span>Total Quantity</span>
                    <span id="total-quantity">{{$totalQuantity}}</span>
                </div>
                <div class="products-total">
                    <span id="total-txt">Total Price</span>
                    <h2 id="total-price">₱{{$totalPrice}}</h2>
                </div>
            </div>
            <a class="order-btn" href="payment">
                <button class="order-now">
                    <span id="order-txt">Order Now</span>
                </button>
            </a>
        </div>
        @endif
    </div>
</div>
@endsection
<script>
    function goBack() {
        window.history.back();
    }

    //Modify Cart
    document.addEventListener('DOMContentLoaded', function() {
        //Modify Minus Button
        function disableMinusButton(cartId) {
            const minusButton = document.getElementById(`minus-icon-${cartId}`);
            minusButton.style.cursor = "default";
        }

        function enableMinusButton(cartId) {
            const minusButton = document.getElementById(`minus-icon-${cartId}`);
            minusButton.style.cursor = "pointer";
        }

        //update total quantity
        function getTotalQuantity() {
            fetch(`/cart/get/total/quantity`)
                .then(response => response.json())
                .then(data => {
                    const quantitySpan = document.getElementById("total-quantity").innerHTML = data;
                })
                .catch(error => {
                    console.error('Error:', error);
                });
        }

        //update total price
        function getTotalPrice() {
            fetch(`/cart/get/total/price`)
                .then(response => response.json())
                .then(data => {
                    const quantitySpan = document.getElementById("total-price").innerHTML = `₱${data}`;
                })
                .catch(error => {
                    console.error('Error:', error);
                });
        }

        //Delete Cart
        const deleteButtons = document.querySelectorAll(".cart-delete");
        deleteButtons.forEach(btn => {
            btn.addEventListener("click", () => {
                const cartId = btn.dataset.cartId;
                console.log(cartId);

                fetch(`/cart/delete/${cartId}`)
                    .then(response => response.json())
                    .then(data => {
                        const container = document.getElementById(`cart-container-${data.deletedCartId}`);
                        container.remove();
                        console.log(data);

                        getTotalQuantity();
                        getTotalPrice();

                    })
                    .catch(error => {
                        console.error('Error:', error);
                    });
            });
        });

        //Plus Quantity
        const plusButton = document.querySelectorAll(".plus-icon");
        plusButton.forEach(btn => {
            btn.addEventListener("click", () => {
                const cartId = btn.dataset.cartId;
                // console.log(cartId);
                const cartQuantity = document.getElementById(`cart-quantity-${cartId}`);
                let quantity = parseInt(cartQuantity.textContent, 10)
                quantity++;

                cartQuantity.innerHTML = quantity;
                enableMinusButton(cartId);

                fetch(`/cart/quantity/add/${cartId}?quantity=${quantity}`)
                    .then(response => response.json())
                    .then(data => {
                        document.getElementById(`cart-price-${cartId}`).innerHTML = `₱${data}`;
                        getTotalQuantity();
                        getTotalPrice();
                    })
                    .catch(error => {
                        console.error('Error:', error);
                    });
            });
        });

        //Minus Quantity
        const minusButton = document.querySelectorAll(".minus-icon");
        minusButton.forEach(btn => {
            btn.addEventListener("click", () => {
                const cartId = btn.dataset.cartId;
                // console.log(cartId);
                const cartQuantity = document.getElementById(`cart-quantity-${cartId}`);
                let quantity = parseInt(cartQuantity.textContent, 10);
                if (quantity > 1) {
                    quantity--;

                    cartQuantity.innerHTML = quantity;

                    fetch(`/cart/quantity/minus/${cartId}?quantity=${quantity}`)
                        .then(response => response.json())
                        .then(data => {
                            document.getElementById(`cart-price-${cartId}`).innerHTML = `₱${data}`;
                            getTotalQuantity();
                            getTotalPrice();
                        })
                        .catch(error => {
                            console.error('Error:', error);
                        });
                } else {
                    disableMinusButton(cartId);
                }
            });
        });
    });
</script>