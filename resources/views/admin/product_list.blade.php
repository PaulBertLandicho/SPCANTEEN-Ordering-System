@extends('layouts.admin')

<script src="https://cdn.jsdelivr.net/npm/fuse.js@6"></script>
@section('content1')
<div class="content1">
    <h1>Product List</h1>
    <div class="add-header">
        <button class="add-product-btn open-modal1">
            <i id="add-btn" class="fa-regular fa-square-plus"></i>
            <span id="add-txt">Add Product</span>
        </button>
        <div class="search-container">
            <select id="category-select" class="sorting-categories">
                <option value="0">All Categories</option>
                @foreach($categories as $cat)
                <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                @endforeach
            </select>
            <input id="search" type="text" name="search" placeholder="Search...">
            <button type="submit" class="search-button">
                <i class="fas fa-search"></i>
            </button>
        </div>
    </div>
    <div id="product-data" data-products='@json($products)'></div>
    <div class="product-list">
        @if ($products->isEmpty())
        <div class="container-empty">
            <img src="images/empties.png" alt="Empty">
            <div class="empty-text">No products added</div>
        </div>
        @else
        @foreach ($products as $product)
        <div class="product" data-product-id="{{$product->id}}">
            <img id="product-img" src="images/product/{{$product->image}}" alt="{{$product->name}}">
            @if ($product->availability == 0)
            <div class="label" style="background: linear-gradient(to top, gray, transparent);">
                @else
                <div class="label" style="background: linear-gradient(to top, maroon, transparent);">
                    @endif
                    <h3>{{$product->name}}</h3>
                    <div class="icon-container">
                        <div class="edit-delete-btns">
                            <div class="edit-button">
                                <button class="open-modal2" data-product-id="{{$product->id}}">
                                    @if ($product->availability == 0)
                                    <iconify-icon style="color: darkgray;" icon="tabler:edit" class="tabler-edit"></iconify-icon>
                                    @else
                                    <iconify-icon icon="tabler:edit" class="tabler-edit"></iconify-icon>
                                    @endif
                                </button>
                            </div>
                            <form action="{{ route('products.destroy', $product->id) }}" method="POST">
                                @csrf
                                <button type="submit" class="delete-button">
                                    @if ($product->availability == 0)
                                    <iconify-icon style="color: darkgray;" icon="mdi:trash-outline" class="trash-outline"></iconify-icon>
                                    @else
                                    <iconify-icon icon="mdi:trash-outline" class="trash-outline"></iconify-icon>
                                    @endif
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
            @endif
        </div>

        <!--------- Add Product Modal -------->
        <div class="modal_product-list">
            <form action="/addproduct" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-container-add">
                    <input type="file" id="image" name="image" accept="image/*" onchange="previewProductImage(this);"
                        style="display: none;">
                    <div class="modal-icon">
                        <div class="img-box-fill">
                            <img id="img-box-fill" src="images/product/default.jpg">
                        </div>
                        <label for="image">
                            <div class="add-icon">
                                <span>Insert Image</span>
                            </div>
                        </label>
                    </div>
                    <div class="modal-content">
                        <div class="product-info">
                            <div class="product-name">
                                <input id="product-name" name="name" type="text" placeholder="Product Name" style="text-align: center;" required>
                            </div>
                            <div class="product-price">
                                <input id="product-price" name="price" type="text" placeholder="₱ 0.00" style="text-align: center;" required>
                            </div>
                            <div class="product-price">
                                <input id="product-measurement" name="measurement" type="number" placeholder="Measurement" style="text-align: center;">
                            </div>
                            <div class="product-name" style="justify-content: center;">
                                <select name="size" style="text-align: center;">
                                    <option value="">Select Size</option>
                                    <option value="Small">Small</option>
                                    <option value="Medium">Medium</option>
                                    <option value="Large">Large</option>
                                </select>
                                <select name="unit" style="text-align:center; margin-left:10px;">
                                    <option value="">Select Unit</option>
                                    <option value="ml">ml</option>
                                    <option value="g">g</option>
                                    <option value="oz">oz</option>
                                    <option value="pcs">pcs</option>
                                </select>
                            </div>
                            <div class="product-time">
                                <input id="product-time" name="time" type="text" placeholder="Estimated Time (In Minutes)" style="text-align: center;" required>
                            </div>
                            <div class="product-categories">
                                <label id="select-category">Select Category</label>
                                <select id="product-categories" name="category_id" id="product">
                                    <option value="1">Breakfast</option>
                                    <option value="2">Lunch</option>
                                    <option value="3">Snack</option>
                                    <option value="4">Beverage</option>
                                    <option value="5">Dinner</option>
                                    <option value="6">Dessert</option>
                                    <option value="7">Healthy</option>
                                </select>
                            </div>
                        </div>
                        <div class="save-btn">
                            <button id="save" type="submit">Save</button>
                        </div>
                        <div class="close-modal1">
                            <iconify-icon id="close" icon="material-symbols-light:close"></iconify-icon>
                        </div>
                    </div>
                </div>
            </form>
        </div>

        <!--------- Edit Product Modal -------->
        <div class="modal_edit-list">
            <form action="/addproduct" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-container-edit">
                    <input type="file" id="images" name="images" accept="image/*" onchange="previewProductImageEdit(this);"
                        style="display: none;">
                    <div class="modal-icon">
                        <div class="img-box-fill-edit">
                            <img id="img-box-fill-edit" src="images/product/default.jpg">
                        </div>
                        <label for="images">
                            <div class="add-icon">
                                <span>Change Image</span>
                            </div>
                        </label>
                    </div>
                    <div class="modal-content">
                        <div class="product-info">
                            <div class="product-name">
                                <input id="product-name" name="name" type="text" placeholder="Product Name" style="text-align: center;">
                            </div>
                            <div class="product-price">
                                <input id="product-price" name="price" type="text" placeholder="₱ 0.00" style="text-align: center;">
                            </div>
                            <div class="product-price">
                                <input id="product-measurement" name="measurement" type="number" placeholder="Measurement" style="text-align: center;">
                            </div>
                            <div class="product-name" style="justify-content: center;">
                                <select id="product-size" name="size" style="text-align: center;">
                                    <option value="">Select Size</option>
                                    <option value="Small">Small</option>
                                    <option value="Medium">Medium</option>
                                    <option value="Large">Large</option>
                                </select>
                                <select id="product-unit" name="unit" style="text-align:center; margin-left:10px;">
                                    <option value="">Select Unit</option>
                                    <option value="ml">ml</option>
                                    <option value="g">g</option>
                                    <option value="oz">oz</option>
                                    <option value="pcs">pcs</option>
                                </select>
                            </div>
                            <div class="product-time">
                                <input id="product-time" name="time" type="text" placeholder="Estimated Time (In Minutes)" style="text-align: center;">
                            </div>
                            <div class="product-availablity" id="product-availablity">
                                <div class="available">
                                    <input id="avail" name="availability" value="1" type="radio" class="availability-btn">
                                    <label for="avail">Available</label>
                                </div>
                                <div class="not-available">
                                    <input id="not-avail" name="availability" value="0" type="radio" class="availability-btn">
                                    <label for="not-avail">Not Available</label>
                                </div>
                            </div>
                            <div class="product-categories">
                                <label id="select-category">Select Category</label>
                                <select id="product-categories" name="category_id" id="product">
                                    <option value="1">Breakfast</option>
                                    <option value="2">Lunch</option>
                                    <option value="3">Snack</option>
                                    <option value="4">Beverage</option>
                                    <option value="5">Dinner</option>
                                    <option value="6">Dessert</option>
                                    <option value="7">Healthy</option>
                                </select>
                            </div>
                        </div>
                        <div class="save-btn">
                            <button id="save" type="submit">Save</button>
                        </div>
                        <div class="close-modal2">
                            <iconify-icon id="close" icon="material-symbols-light:close"></iconify-icon>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const openModal1 = document.querySelector(".open-modal1");
            const closeModal1 = document.querySelector(".close-modal1");
            const productlistModal = document.querySelector(".modal_product-list");
            const editlistModal = document.querySelector(".modal_edit-list");
            const editForm = editlistModal.querySelector("form");

            if (openModal1 && closeModal1 && productlistModal) {
                openModal1.addEventListener("click", () => {
                    productlistModal.classList.add("active");
                });

                closeModal1.addEventListener("click", () => {
                    productlistModal.classList.remove("active");
                });
            }

            const openModal2Buttons = document.querySelectorAll(".open-modal2");
            openModal2Buttons.forEach((btn) => {
                btn.addEventListener("click", async (event) => {
                    event.preventDefault();

                    const productId = btn.dataset.productId;

                    const response = await fetch(`/products/${productId}`);
                    const productData = await response.json();

                    const availabilityInput = document.querySelector("#product-availablity input[value='" + productData.availability + "']");
                    console.log(productData.availability);

                    if (availabilityInput) {
                        availabilityInput.checked = true;
                    }

                    editForm.querySelector("#product-name").value = productData.name;
                    editForm.querySelector("#product-price").value = productData.price;
                    editForm.querySelector("#product-time").value = productData.time;
                    editForm.querySelector("#product-categories").value = productData.category_id;
                    editForm.querySelector("#product-size").value =
                        productData.size ?? "";

                    editForm.querySelector("#product-measurement").value =
                        productData.measurement ?? "";

                    editForm.querySelector("#product-unit").value =
                        productData.unit ?? "";
                    document.getElementById("img-box-fill-edit").src = `images/product/${productData.image}`;

                    editForm.action = `/products/edit/${productId}`;

                    editlistModal.classList.add("active");

                });
            });

            const closeModal2 = document.querySelector(".close-modal2");
            if (closeModal2) {
                closeModal2.addEventListener("click", () => {
                    editlistModal.classList.remove("active");
                });
            }
        });

        // Category filter: fetch products by category and re-render list
        const categorySelect = document.getElementById('category-select');
        const productListContainer = document.querySelector('.product-list');

        function renderProducts(products) {
            productListContainer.innerHTML = '';
            if (!products || products.length === 0) {
                productListContainer.innerHTML = `
                    <div class="container-empty">
                        <img src="images/empties.png" alt="Empty">
                        <div class="empty-text">No products added</div>
                    </div>`;
                return;
            }

            products.forEach(product => {
                const availability = product.availability == 1 ? 'linear-gradient(to top, maroon, transparent)' : 'linear-gradient(to top, gray, transparent)';
                const img = product.image ? `images/product/${product.image}` : 'images/product/default.jpg';
                const html = `
                    <div class="product" data-product-id="${product.id}">
                        <img id="product-img" src="${img}" alt="${product.name}">
                        <div class="label" style="background: ${availability};">
                            <h3>${product.name}</h3>
                            <div class="icon-container">
                                <div class="edit-delete-btns">
                                    <div class="edit-button">
                                        <button class="open-modal2" data-product-id="${product.id}">
                                            <iconify-icon icon="tabler:edit" class="tabler-edit"></iconify-icon>
                                        </button>
                                    </div>
                                    <form action="/products/destroy/${product.id}" method="POST">
                                        @csrf
                                        <button type="submit" class="delete-button">
                                            <iconify-icon icon="mdi:trash-outline" class="trash-outline"></iconify-icon>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>`;
                productListContainer.insertAdjacentHTML('beforeend', html);
            });

            // re-attach edit buttons listeners for newly rendered items
            const openModal2ButtonsNew = document.querySelectorAll('.open-modal2');
            openModal2ButtonsNew.forEach((btn) => {
                btn.addEventListener('click', async (event) => {
                    event.preventDefault();
                    const productId = btn.dataset.productId;
                    const response = await fetch(`/products/${productId}`);
                    const productData = await response.json();
                    const availabilityInput = document.querySelector("#product-availablity input[value='" + productData.availability + "']");
                    if (availabilityInput) availabilityInput.checked = true;
                    const editlistModal = document.querySelector('.modal_edit-list');
                    const editForm = editlistModal.querySelector('form');
                    editForm.querySelector('#product-name').value = productData.name;
                    editForm.querySelector('#product-price').value = productData.price;
                    editForm.querySelector('#product-time').value = productData.time;
                    editForm.querySelector('#product-categories').value = productData.category_id;
                    document.getElementById('img-box-fill-edit').src = `images/product/${productData.image}`;
                    editForm.querySelector('#product-size').value = productData.size ?? "";
                    editForm.querySelector('#product-measurement').value = productData.measurement ?? "";
                    editForm.querySelector('#product-unit').value = productData.unit ?? "";
                    editForm.action = `/products/edit/${productId}`;
                    editlistModal.classList.add('active');
                });
            });
        }

        categorySelect.addEventListener('change', function() {
            const catId = this.value;
            fetch(`/product/category/${catId}`)
                .then(r => r.ok ? r.json() : Promise.reject(r))
                .then(data => {
                    // ensure names are title-cased like original
                    data.forEach(p => p.name = p.name ? p.name.replace(/\b\w/g, c => c.toUpperCase()) : p.name);
                    renderProducts(data);

                    // update client-side search dataset to the currently displayed products
                    products = data.map(product => ({
                        id: product.id,
                        name: product.name
                    }));
                    fuse = new Fuse(products, options);

                    // clear any existing search and show all results for the selected category
                    const searchEl = document.getElementById('search');
                    if (searchEl) searchEl.value = '';
                    displayAllResults();
                })
                .catch(err => console.error('Failed to fetch products by category', err));
        });

        function previewProductImage(input) {
            if (input.files && input.files[0]) {
                const reader = new FileReader();

                reader.onload = function(e) {
                    document.getElementById('img-box-fill').src = e.target.result;
                }

                reader.readAsDataURL(input.files[0]);
            }
        }

        function previewProductImageEdit(input) {
            if (input.files && input.files[0]) {
                const reader = new FileReader();

                reader.onload = function(e) {
                    document.getElementById('img-box-fill-edit').src = e.target.result;
                }

                reader.readAsDataURL(input.files[0]);
            }
        }

        const productDataElement = document.getElementById('product-data');
        let products = JSON.parse(productDataElement.getAttribute('data-products')).map(product => ({
            id: product.id,
            name: product.name,
        }));

        const options = {
            keys: ['id', 'name'],
            threshold: 0.4
        };

        let fuse = new Fuse(products, options);

        const displayResults = (results) => {
            const containers = document.querySelectorAll('.product');
            containers.forEach(container => container.style.display = 'none');

            results.forEach(result => {
                const container = document.querySelector('.product[data-product-id="' + result.item.id + '"]');
                if (container) container.style.display = '';
            });
        };

        const displayAllResults = () => {
            const containers = document.querySelectorAll('.product');
            containers.forEach(container => container.style.display = '');
        };

        document.getElementById('search').addEventListener('input', (e) => {
            const query = e.target.value;
            if (query.trim() === '') {
                displayAllResults();
            } else {
                const results = fuse.search(query);
                displayResults(results);
            }
        });

        displayAllResults();

        document.getElementById('search').addEventListener('keydown', (e) => {
            if (e.key === 'Enter') e.target.blur();
        });
    </script>
    @endsection