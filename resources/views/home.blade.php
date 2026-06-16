@extends('layouts.layout')

@section('content')
@if (!Cookie::has('seenIntro'))
@section('css', 'css/auth.css')
<div class="onboarding-carousel">
    <div class="onboarding-slide" style="background-color: #ADD8E6;">
        <img src="images/login.png" style="width: 300px; margin-top: 180px;">
        <span style="color: black; font-size: 22px;">Welcome to <b style="color: maroon;">SPCanteen</b></span>
        <span style="color: gray; font-size: 17px; opacity: 1; margin-top: 8px;">Create and Setup <br> your account.</span>
    </div>

    <div class="onboarding-slide" style="background-color: #ADD8E6;">
        <img src="images/time.png" style="width: 300px; margin-top: 180px;">
        <span style="color: black; font-size: 22px;">Save Time</span>
        <span style="color: gray; font-size: 17px; opacity: 1; margin-top: 8px;">You can order anytime <br> inside the campus.</span>
    </div>

    <div class="onboarding-slide" style="background-color: #ADD8E6;">
        <img src="images/qrcode.png" style="width: 300px; margin-top: 180px;">
        <span style="color: black; font-size: 22px;">QR Code</span>
        <span style="color: gray; font-size: 17px; opacity: 1; margin-top: 8px;">You will get a QR Code <br> when your order has been prepared.</span>
    </div>

    <div class="onboarding-slide" style="background-color: #ADD8E6;">
        <img src="images/coffee.png" style="width: 300px; margin-top: 180px;">
        <span style="color: black; font-size: 22px;">QR Scanner</span>
        <span style="color: gray; font-size: 17px; opacity: 1; margin-top: 8px;">You can user your QR Code <br> to get your order in the canteen.</span>
    </div>

    <div style="width: 100%; position: absolute; margin-top: 640px;">
        <div class="slider">
            <input type="radio" name="slide" id="img1" checked>
            <input type="radio" name="slide" id="img2">
            <input type="radio" name="slide" id="img3">
            <input type="radio" name="slide" id="img4">
        </div>

        <div class="dots">
            <label for="img1" class="active"></label>
            <label for="img2"></label>
            <label for="img3"></label>
            <label for="img4"></label>
        </div>
    </div>

    <div style="position: absolute; width: 100%; display: flex; justify-content: center; margin-top: 750px;">
        <button type="button" onclick="switchPage()" style="background-color: maroon; color: #FFFFFF; font-size: 18px; border: none; padding: 20px 130px 20px 130px; border-radius: 50px;">Get Started</button>

    </div>

</div>

<script>
    const slides = document.querySelectorAll('.onboarding-slide');
    const dots = document.querySelectorAll('.dots label');

    let currentSlide = 0;

    function showSlide(index) {
        slides.forEach((slide, i) => {
            if (i === index) {
                slide.style.display = 'block';
            } else {
                slide.style.display = 'none';
            }
        });
    }

    function updateDots(index) {
        dots.forEach((dot, i) => {
            if (i === index) {
                dot.classList.add('active');
            } else {
                dot.classList.remove('active');
            }
        });
    }

    function nextSlide() {
        currentSlide = (currentSlide + 1) % slides.length;
        showSlide(currentSlide);
        updateDots(currentSlide);
    }

    function prevSlide() {
        currentSlide = (currentSlide - 1 + slides.length) % slides.length;
        showSlide(currentSlide);
        updateDots(currentSlide);
    }

    document.querySelector('.slider').addEventListener('click', (event) => {
        if (event.target.type === 'radio') {
            const index = Array.from(event.target.parentNode.children).indexOf(event.target);
            currentSlide = index;
            showSlide(currentSlide);
            updateDots(currentSlide);
        }
    });

    document.querySelector('.onboarding-carousel').addEventListener('scroll', () => {
        const index = Math.round(document.querySelector('.onboarding-carousel').scrollLeft / window.innerWidth);
        currentSlide = index;
        updateDots(currentSlide);
    });

    function switchPage() {
        setTimeout(function() {
            window.location.href = '/after-intro';
        }, 100); // Adjust the timeout as needed (500ms = 0.5s)
    }
</script>
@else
@auth
@section('css', 'css/user.css')
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
<div class="container">
    <div class="dashboard-header">
        <div class="header">
            <div class="username">
                <h2 id="username" class="username-text" title="Hello {{ Auth::user()->name }}!" style="display:inline-block; max-width:40ch; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; vertical-align:middle;">Hello {{Auth::user()->name}}!</h2>
            </div>
            <div class="profile">
                <div class="user-cart">
                    <a href="cart">
                        <iconify-icon id="cart" icon="uil:cart"></iconify-icon>
                        <span class="quantity" id="in-cart">0</span>
                    </a>
                </div>
                <div class="user-avatar">
                    <a href="/profile"><img id="profile" src="{{ asset('images/profile/' . Auth::user()->image) }}" alt="Profile Picture"></a>
                </div>
            </div>
        </div>
        @include('layouts.components.user.categories')
        <div class="search-bar">
            <form class="search-form" id="search-form">
                <input type="text" id="searchInput" placeholder="Search product . . . ." name="search">
                <button id="search-btn" type="submit">
                    <iconify-icon id="search-icon" icon="iconamoon:search-thin"></iconify-icon>
                </button>
            </form>
        </div>
        <div class="row-txt">
            <span id="recommended">Recommended</span>
            <span id="categories">All</span>
        </div>
        <form class="bottom-sheet" id="bottom-sheet">
            <div class="sheet-overlay"></div>
            <div class="content">
                <div class="header">
                    <div class="drag-icon"><span class="header-icon"></span></div>
                </div>
                <div class="body">
                    <img id="selling-image" src="images/product/default.jpg" alt="default">
                    <button type="button" class="heart-button" id="heart-button">
                        <iconify-icon id="heart-icon" icon="material-symbols:favorite"></iconify-icon>
                    </button>
                    <div class="product-detail">
                        <h2 class="name">No Product</h2>
                        <p class="price">₱0</p>
                    </div>
                    <div class="modal-btns">
                        <div class="quantity-btns">
                            <button type="button" class="quantity-minus" id="quantity-minus">
                                <iconify-icon icon="ph:minus"></iconify-icon>
                            </button>
                            <input type="number" id="input-quantity" min="1" value="1" style="display: none;">
                            <span id="modal-quantity">1</span>
                            <button type="button" class="quantity-plus" id="quantity-plus">
                                <iconify-icon icon="ph:plus"></iconify-icon>
                            </button>
                        </div>
                        <button type="submit" class="add-to-cart" id="add-2-cart" data-product="">
                            Add to cart
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>
    <div class="products" id="products">
        @foreach ($products as $product)
        <div class="product-container" data-category-id="{{ $product->category_id ?? 0 }}">
            <div class="product-content">
                <div class="product-image">
                    <button class="show-modal" data-product='@json($product)'><img id="product-image" src="images/product/{{$product->image}}" alt="{{$product->name}}"></button>
                    <button class="add-cart" data-product='@json($product)'>
                        <iconify-icon id="add-icon" icon="ph:plus"></iconify-icon>
                    </button>
                </div>
                <div class="products-info">
                    <div class="product-info">
                        <div class="product-time">
                            <iconify-icon id="timer-icon" icon="svg-spinners:clock"></iconify-icon>
                            <span id="product-time">{{$product->time}} mins</span>
                        </div>
                        <div class="product-name-price">
                            <h1 id="product-name">{{$product->name}}</h1>
                            <span id="products-price">₱{{$product->price}}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>
    <div id="no-products" class="no-products" style="display:none; text-align:center; padding:18px 10px; color:gray;">
        No product added
    </div>
    @include('layouts.components.user.user_navbar')
    <script>
        // Client-side search + category filter: filter product cards by name and selected category
        (function() {
            const form = document.getElementById('search-form');
            const input = document.getElementById('searchInput');
            const categoryLinks = document.querySelectorAll('.categories .category a');
            const categoryLabel = document.getElementById('categories');
            let selectedCategoryId = 0; // 0 = All
            if (!input) return;
            if (form) form.addEventListener('submit', e => e.preventDefault());

            function filterProducts() {
                const q = input.value.trim().toLowerCase();
                const cards = document.querySelectorAll('.product-container');
                cards.forEach(card => {
                    const nameEl = card.querySelector('#product-name');
                    const name = nameEl ? nameEl.textContent.trim().toLowerCase() : '';
                    const catId = parseInt(card.dataset.categoryId || 0, 10);
                    const matchesQuery = (q === '' || name.indexOf(q) !== -1);
                    const matchesCategory = (selectedCategoryId === 0 || catId === selectedCategoryId);
                    if (matchesQuery && matchesCategory) card.style.display = '';
                    else card.style.display = 'none';
                });

                // show/hide "no products" message
                const noEl = document.getElementById('no-products');
                if (noEl) {
                    const visibleCount = Array.from(cards).filter(c => c.style.display !== 'none').length;
                    const catText = categoryLabel ? categoryLabel.textContent.trim() : 'All';
                    if (visibleCount === 0) {
                        if (q !== '') noEl.textContent = `No products match "${q}" in ${catText}`;
                        else noEl.textContent = (selectedCategoryId === 0) ? 'No products available' : `No products added in ${catText}`;
                        noEl.style.display = '';
                    } else {
                        noEl.style.display = 'none';
                    }
                }
            }

            // Wire category clicks
            categoryLinks.forEach(link => {
                link.addEventListener('click', (e) => {
                    e.preventDefault();
                    const parent = link.closest('.category');
                    const id = parseInt(link.getAttribute('data-category-id') || '0', 10);
                    selectedCategoryId = id;

                    // update active class
                    document.querySelectorAll('.categories .category').forEach(c => c.classList.remove('active'));
                    if (parent) parent.classList.add('active');

                    // update label text
                    const txtEl = parent ? parent.querySelector('.icon-text') : null;
                    if (categoryLabel && txtEl) categoryLabel.textContent = txtEl.textContent || 'All';

                    filterProducts();
                });
            });

            input.addEventListener('input', filterProducts);
            input.addEventListener('keydown', (e) => {
                if (e.key === 'Enter') e.target.blur();
            });

            // Initialize: ensure "All" category is active
            const allLink = document.querySelector('.categories .category a[data-category-id="0"]');
            if (allLink) {
                const allParent = allLink.closest('.category');
                if (allParent) allParent.classList.add('active');
            }
        })();
    </script>
</div>
@else
@section('css', 'css/auth.css')
@section('title', 'SPCanteen - Login')

<div id="splash-screen">
    <div class="logo1">
        <img class="futuristic-heading" src="/images/SPCanteen.png" alt="SPCanteen.png">
    </div>
</div>
<div class="content">
    <div class="container">
        <div class="row">
            <div class="login-form">
                <div class="logo1">
                    <img id="logo" src="/images/SPCanteen.png" alt="SPCanteen.png">
                </div>
                <div class="form">
                    @error('name')
                    <p style="color: red; margin-left: 20px; position: absolute;">{{$message}}</p>
                    @enderror
                    @error('password')
                    <p style="color: red; margin-left: 20px; position: absolute;">{{$message}}</p>
                    @enderror
                    <form action="/login" method="POST">
                        @csrf
                        <div class="input-container">
                            <input type="text" name="name" class="input-field" required>
                            <label>Username</label>
                            <i class="fa-solid fa-user"></i>
                        </div>
                        <div class="input-container">
                            <input type="password" name="password" class="input-field" required>
                            <label>Password</label>
                            <i class="fa-solid fa-eye" onclick="togglePassword(this, 'password')"></i>
                        </div>
                        <div class="forgot-password">
                            <a id="forgot-password" href="#">Forgot Password?</a>
                        </div>
                        <div class="login">
                            <input type="submit" class="btn" value="LOGIN">
                        </div>
                    </form>
                    <div class="register">
                        <p id="register-txt">Don't have an account? <a id="register-btn" href="register">Register</a></p>
                    </div>
                </div>
            </div>
        </div>
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
@endauth
@endif
@endsection