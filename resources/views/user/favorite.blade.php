@extends('layouts.user')

@section('page', 'favorite')
@section('content')
<div class="content">
    <div class="favorites-header">
        <div class="header">
            <div class="favorites-header-txt">
                <span id="favorites-header-txt">MY FAVORITE</span>
            </div>
            <div class="profile">
                <div class="user-cart">
                    <a href="cart">
                        <iconify-icon id="cart" icon="uil:cart"></iconify-icon>
                        <span class="quantity" id="in-cart">{{$productCount}}</span>
                    </a>
                </div>
                <div class="user-avatar">
                    <a href="/profile"><img id="profile" src="{{ asset('images/profile/' . Auth::user()->image) }}" alt="Profile Picture"></a>
                </div>
            </div>
        </div>
        <div class="search-bar">
            <form action="" class="search-form">
                <input type="text" id="searchInput" placeholder="Search product . . . ." name="search">
                <button id="search-btn" type="submit">
                    <iconify-icon id="search-icon" icon="iconamoon:search-thin"></iconify-icon>
                </button>
            </form>
        </div>
    </div>
    <div class="history" id="favorite-products">
        @if ($favorites->isEmpty())
        <div class="container-empty">
            <img src="images/empties.png" alt="Empty">
            <div class="empty-text">No Favorite</div>
        </div>
        @else
        @foreach ($favorites as $favorite)
        <div class="product-container" id="product-container-{{$favorite->product->id}}">
            <div class="product-content">
                <div class="product-image">
                    <button class="show-modal" data-product="{{$favorite->product}}">
                        <img id="product-image" src="images/product/{{$favorite->product->image}}" alt="{{$favorite->product->name}}">
                    </button>
                    <button class="favorite-heart-container" id="favorite-heart-container" data-product-id="{{$favorite->product->id}}">
                        <iconify-icon id="favorite-heart-icon" icon="material-symbols:favorite"></iconify-icon>
                    </button>
                </div>
                <div class="products-info">
                    <div class="product-info">
                        <div class="product-time">
                            <iconify-icon id="timer-icon" icon="svg-spinners:clock"></iconify-icon>
                            <span id="product-time">{{$favorite->product->time}} mins</span>
                        </div>
                        <div class="product-name-price">
                            <h1 id="product-name">{{$favorite->product->name}}</h1>
                            <span id="products-price">₱{{$favorite->product->price}}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endforeach
        @endif
    </div>
    @include('layouts.components.user.user_navbar')
    <form class="bottom-sheet" id="bottom-sheet">
        <div class="sheet-overlay"></div>
        <div class="content">
            <div class="header">
                <div class="drag-icon"><span class="header-icon"></span></div>
            </div>
            <div class="body">
                <img id="selling-image" src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcTV1mn8AmFrmldZhG7Lc_uTy-NbSemRXlv0FwYOpQY-Hg&s" alt="default">
                <!-- <button class="favorite-add-button" id="heart-button">
                    <iconify-icon id="favorite-add-icon" icon="ph:plus"></iconify-icon>
                </button> -->
                <div class="product-detail">
                    <h2 class="name">No Product</h2>
                    <p class="price">₱0</p>
                </div>
                <div class="modal-btns">
                    <div class="quantity-btns">
                        <button class="quantity-minus" id="quantity-minus">
                            <iconify-icon icon="ph:minus"></iconify-icon>
                        </button>
                        <input type="number" id="input-quantity" min="1" value="1" style="display: none;">
                        <span id="modal-quantity">1</span>
                        <button class="quantity-plus" id="quantity-plus">
                            <iconify-icon icon="ph:plus"></iconify-icon>
                        </button>
                    </div>
                    <button type="submit" class="add-to-cart" id="add-2-cart" data-product-id="">
                        Add to cart
                    </button>
                </div>
                @if($favorite->product->display_size)
                <div class="variant-options">
                    <span style="color: gray;">
                        • Select Size: {{ $favorite->product->display_size }}
                    </span>
                </div>
                @endif
            </div>
        </div>
    </form>
</div>
<script>
    let currentQuantity = 1;

    const showModalBtns = document.querySelectorAll(".show-modal");
    const bottomSheet = document.querySelector(".bottom-sheet");
    const sheetOverlay = bottomSheet.querySelector(".sheet-overlay");
    const sheetContent = bottomSheet.querySelector(".content");
    const dragIcon = bottomSheet.querySelector(".drag-icon");

    let isDragging = false,
        startY, startHeight;

    const showBottomSheet = () => {
        bottomSheet.classList.add("show");
        document.body.style.overflowY = "hidden";
        updateSheetHeight(50);
        minusButtonColor(currentQuantity);
    };

    const updateSheetHeight = (height) => {
        sheetContent.style.height = `${height}vh`;
        bottomSheet.classList.toggle("fullscreen", height === 100);
    };



    const dragStart = (e) => {
        isDragging = true;
        startY = e.pageY || e.touches?.[0].pageY;
        startHeight = parseInt(sheetContent.style.height);
        bottomSheet.classList.add("dragging");
    };

    const dragging = (e) => {
        if (!isDragging) return;
        const delta = startY - (e.pageY || e.touches?.[0].pageY);
        const newHeight = startHeight + (delta / window.innerHeight) * 100;
        updateSheetHeight(newHeight);
    };

    const dragStop = () => {
        isDragging = false;
        bottomSheet.classList.remove("dragging");
        const sheetHeight = parseInt(sheetContent.style.height);
        sheetHeight < 25 ?
            hideBottomSheet() :
            sheetHeight > 75 ?
            updateSheetHeight(100) :
            updateSheetHeight(50);
    };

    dragIcon.addEventListener("mousedown", dragStart);
    document.addEventListener("mousemove", dragging);
    document.addEventListener("mouseup", dragStop);
    dragIcon.addEventListener("touchstart", dragStart);
    document.addEventListener("touchmove", dragging);
    document.addEventListener("touchend", dragStop);
    sheetOverlay.addEventListener("click", hideBottomSheet);

    showModalBtns.forEach((btn) => {
        btn.addEventListener("click", () => {
            const product = JSON.parse(btn.dataset.product);

            document.getElementById("selling-image").src = "images/product/" + product.image;
            document.querySelector(".name").textContent = product.name;
            document.querySelector(".price").textContent = "₱" + product.price;
            document.getElementById("modal-quantity").innerHTML = 1;
            document.getElementById("add-2-cart").setAttribute('data-product-id', product.id);

            showBottomSheet();
        });
    });

    //quantity Plus and Minus Function
    const minusButton = document.getElementById("quantity-minus");
    const addButton = document.getElementById("quantity-plus");
    const quantitySpan = document.getElementById("modal-quantity");

    currentQuantity = parseInt(quantitySpan.textContent, 10);

    function minusButtonColor(currentQuantity) {
        if (currentQuantity > 1) {
            minusButton.style.backgroundColor = "maroon";
            minusButton.style.cursor = "pointer";
        } else {
            minusButton.style.backgroundColor = "#D3D3D3";
            minusButton.style.cursor = "default";
        }
    }

    function updateInputQuanity(currentQuantity) {
        const quantityInput = document.getElementById("input-quantity");
        quantityInput.value = currentQuantity;

        const newQuantity = quantityInput.value;
    }

    function updateInCart(value) {
        document.getElementById("in-cart").innerHTML = value;
    }

    minusButtonColor(currentQuantity);
    addButton.addEventListener('click', function(e) {
        e.preventDefault();
        currentQuantity++;
        quantitySpan.textContent = currentQuantity;
        minusButtonColor(currentQuantity);
        updateInputQuanity(currentQuantity)
    });

    minusButton.addEventListener('click', function(e) {
        e.preventDefault();
        if (currentQuantity > 1) {
            currentQuantity--;
            quantitySpan.textContent = currentQuantity;
            minusButtonColor(currentQuantity);
            updateInputQuanity(currentQuantity)
        }
    });

    //Remove Favorite
    const favButton = document.querySelectorAll(".favorite-heart-container");

    favButton.forEach(btn => {
        btn.addEventListener("click", () => {
            const productId = btn.dataset.productId;
            //console.log(productId);
            fetch(`/favorite/remove/${productId}`)
                .then(response => response.json())
                .then(data => {
                    const container = document.getElementById(`product-container-${data}`);
                    container.remove();
                })
                .catch(error => {
                    console.error('Error:', error);
                });

        });
    });

    //Add product
    const addToCart = document.getElementById("add-2-cart");
    addToCart.addEventListener('click', function(e) {
        e.preventDefault();
        const productId = this.dataset.productId;

        fetch(`/cart/store/product/${productId}?quantity=${currentQuantity}`)
            .then(response => response.json())
            .then(data => {
                fetch(`/cart/show/product/inside`)
                    .then(response => response.json())
                    .then(data => {
                        updateInCart(data);
                    })
                    .catch(error => {
                        console.error('Error:', error);
                    });
            })
            .catch(error => {
                console.error('Error:', error);
            });
        hideBottomSheet();
    });
</script>
@endsection