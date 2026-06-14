@extends('layouts.user')

@section('content')
<div class="container">
    <form action="/order/store" id="payment-form" method="POST">
        @csrf
        <div class="cart-content">
            <div class="cart-header">
                <a href="cart">
                    <iconify-icon id="back-btn" icon="material-symbols:arrow-back-ios"></iconify-icon>
                </a>
                <h1 id="mycart-txt">MY CART</h1>
                <a class="close-btn" href="/">
                    <iconify-icon id="close-btn" icon="material-symbols-light:close"></iconify-icon>
                </a>
            </div>
            <input type="hidden" name="totalPrice" value="{{$totalPrice}}">
            <div class="payment-content">
                <button class="payment-containers" id="payment-containers" onclick="selectPayment1()">
                    <div class="payment">
                        <div class="payment-left-side">
                            <img id="payment-image" src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcQSMu0uy7QmaOOqFpYfRv6LdinOVhUfJLiEIkvedIA9Ww&s" alt="">
                            <span id="payment-txt">GCASH</span>
                        </div>
                        <div class="payment-right-side">
                            <input type="radio" name="payment_option" id="payment-btn1" class="payment-btn" value="1" checked>
                            <label for="payment-gcash"></label>
                        </div>
                    </div>
                </button>
                @if(auth()->user()->role_id == 1)
                <button class="payment-containers" id="payment-containers" onclick="selectPayment2()">
                    <div class="payment">
                        <div class="payment-left-side">
                            <img id="payment-image" src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcSRJXmMbrItPLMTeMbSZzS46aE6JBdUvO6EtTa7dLw8LQ&s" alt="">
                            <span id="payment-txt">School Fee</span>
                        </div>
                        <div class="payment-right-side">
                            <input type="radio" name="payment_option" id="payment-btn2" class="payment-btn" value="2">
                            <label for="payment-schoolfee"></label>
                        </div>
                    </div>
                </button>
                @else
                <button class="payment-containers" id="payment-containers" onclick="selectPayment4()">
                    <div class="payment">
                        <div class="payment-left-side">
                            <img id="payment-image" src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcSRJXmMbrItPLMTeMbSZzS46aE6JBdUvO6EtTa7dLw8LQ&s" alt="">
                            <span id="payment-txt">Payroll</span>
                        </div>
                        <div class="payment-right-side">
                            <input type="radio" name="payment_option" id="payment-btn4" class="payment-btn" value="4">
                            <label for="payment-"></label>
                        </div>
                    </div>
                </button>
                @endif
                <button class="payment-containers" id="payment-containers" onclick="selectPayment3()">
                    <div class="payment">
                        <div class="payment-left-side">
                            <img id="payment-image" src="/images/cash.png" alt="">
                            <span id="payment-txt">Cash On Hand</span>
                        </div>
                        <div class="payment-right-side">
                            <input type="radio" name="payment_option" id="payment-btn3" class="payment-btn" value="3">
                            <label for="payment-cod"></label>
                        </div>
                    </div>
                </button>
            </div>

            <div style="opacity: 0;" id="note" class="note">Note: Failure to pay will result in automatic @if (auth()->user()->role_id == 1) addition to your school fee. @else subtraction to your payroll.@endif</div>

            <div class="payments-container">
                <div class="price-txt">
                    <div class="products-selected">
                        <span>Selected Product</span>
                        <span>{{$productSelected}}</span>
                    </div>
                    <div class="products-total">
                        <span id="total-txt">Total</span>
                        <h2>₱{{$totalPrice}}</h2>
                    </div>
                </div>
                <a class="payments-btn" href="qr-code">
                    <button class="pay-now">
                        <span id="pay-txt">Pay Now</span>
                    </button>
                </a>
            </div>
        </div>
    </form>
</div>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        const paymentContainers = document.querySelectorAll(".payment-containers");

        paymentContainers.forEach(btn => {
            btn.addEventListener("click", (e) => {
                e.preventDefault();
            });
        });
    });

    function selectPayment1() {
        const radioButton = document.getElementById('payment-btn1');
        if (radioButton) {
            radioButton.checked = true;
            document.getElementById("note").style.opacity = "0";
        }
    }

    function selectPayment2() {
        const radioButton = document.getElementById('payment-btn2');
        if (radioButton) {
            radioButton.checked = true;
            document.getElementById("note").style.opacity = "0";
        }
    }

    function selectPayment3() {
        const radioButton = document.getElementById('payment-btn3');
        if (radioButton) {
            radioButton.checked = true;
            document.getElementById("note").style.opacity = "1";
        }
    }

    function selectPayment4() {
        const radioButton = document.getElementById('payment-btn4');
        if (radioButton) {
            radioButton.checked = true;
            document.getElementById("note").style.opacity = "0";
        }
    }
</script>
@endsection