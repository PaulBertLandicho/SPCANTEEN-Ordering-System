const currentUrl = window.location.href;
console.log(currentUrl);
/* Show and Hide Password */

function togglePassword(icon, inputId) {
    var input = document.getElementsByName(inputId)[0];
    if (input.type === "password") {
        input.type = "text";
        icon.className = "fa-solid fa-eye-slash";
    } else {
        input.type = "password";
        icon.className = "fa-solid fa-eye";
    }
}

/* Profile Picture (Need pa e fix) */

function previewProfilePicture(input) {
    if (input.files && input.files[0]) {
        var reader = new FileReader();

        reader.onload = function (e) {
            document.getElementById("avatar").src = e.target.result;
        };

        reader.readAsDataURL(input.files[0]);
    }
}

/* Bottom Sheet Modal */

// Run bottom-sheet code only on the site's root path (works with any host/origin)
if (window.location.pathname === "/") {
    let categoryId = 0;

    document.addEventListener("DOMContentLoaded", function () {
        const showModalBtns = document.querySelectorAll(".show-modal");
        const bottomSheet = document.querySelector(".bottom-sheet");
        const addCartBtn = document.querySelectorAll(".add-cart");
        const sheetOverlay = bottomSheet.querySelector(".sheet-overlay");
        const sheetContent = bottomSheet.querySelector(".content");
        const dragIcon = bottomSheet.querySelector(".drag-icon");

        let currentQuantity = 1;
        let productId = undefined;
        let favorite = false;

        let isDragging = false,
            startY,
            startHeight;

        const showBottomSheet = () => {
            bottomSheet.classList.add("show");
            document.body.style.overflowY = "hidden";
            updateSheetHeight(50);
            minusButtonColor(currentQuantity);
            modifyFavButton(favorite);
        };

        const updateSheetHeight = (height) => {
            sheetContent.style.height = `${height}vh`;
            bottomSheet.classList.toggle("fullscreen", height === 100);
        };

        const hideBottomSheet = () => {
            bottomSheet.classList.remove("show");
            document.body.style.overflowY = "auto";
            document.getElementById("modal-quantity").innerHTML = 1;
            currentQuantity = 1;
            productId = undefined;
            favorite = false;
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
            sheetHeight < 25
                ? hideBottomSheet()
                : sheetHeight > 75
                  ? updateSheetHeight(100)
                  : updateSheetHeight(50);
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

                document.getElementById("selling-image").src =
                    "images/product/" + product.image;
                document
                    .getElementById("heart-icon")
                    .classList.remove("active");
                document.querySelector(".name").textContent = product.name;
                document.querySelector(".price").textContent =
                    "₱" + product.price;
                document.getElementById("modal-quantity").innerHTML = 1;
                productId = product.id;
                fetch(`/favorite/show/${productId}`)
                    .then((response) => response.json())
                    .then((data) => {
                        favorite = data;
                        modifyFavButton(favorite);
                    })
                    .catch((error) => {
                        console.error("Error:", error);
                    });

                showBottomSheet();
            });
        });

        //Single Add Cart

        addCartBtn.forEach((btn) => {
            btn.addEventListener("click", () => {
                const product = JSON.parse(btn.dataset.product);
                productId = product.id;

                fetch(`/cart/store/single/product/${productId}`)
                    .then((response) => response.json())
                    .then((data) => {
                        updateInCart(data);
                    })
                    .catch((error) => {
                        console.error("Error:", error);
                    });
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

        minusButtonColor(currentQuantity);
        addButton.addEventListener("click", function (e) {
            e.preventDefault(e);
            currentQuantity++;
            quantitySpan.textContent = currentQuantity;
            minusButtonColor(currentQuantity);
            updateInputQuanity(currentQuantity);
        });

        minusButton.addEventListener("click", function (e) {
            e.preventDefault();
            if (currentQuantity > 1) {
                currentQuantity--;
                quantitySpan.textContent = currentQuantity;
                minusButtonColor(currentQuantity);
                updateInputQuanity(currentQuantity);
            }
        });

        //Update header quantity(Inside cart Icon)
        function updateInCart(value) {
            document.getElementById("in-cart").innerHTML = value;
        }

        //Show header quantity(Inside cart Icon)
        fetch(`/cart/show/product/inside`)
            .then((response) => response.json())
            .then((data) => {
                updateInCart(data);
            })
            .catch((error) => {
                console.error("Error:", error);
            });

        //Add to cart function
        const addCartButton = document.getElementById("add-2-cart");
        addCartButton.addEventListener("click", (e) => {
            e.preventDefault();

            fetch(
                `/cart/store/product/${productId}?quantity=${currentQuantity}`,
            )
                .then((response) => response.json())
                .then((data) => {
                    fetch(`/cart/show/product/inside`)
                        .then((response) => response.json())
                        .then((data) => {
                            updateInCart(data);
                        })
                        .catch((error) => {
                            console.error("Error:", error);
                        });

                    hideBottomSheet();
                })
                .catch((error) => {
                    console.error("Error:", error);
                });
        });

        //Add Favorite Function

        const favButton = document.getElementById("heart-button");
        favButton.addEventListener("click", (e) => {
            e.preventDefault();

            fetch(`/favorite/add/${productId}`)
                .then((response) => response.json())
                .then((data) => {
                    modifyFavButton(data);
                })
                .catch((error) => {
                    console.error("Error:", error);
                });
        });

        //Favorite Color Function
        function modifyFavButton(fav) {
            if (fav == true) {
                document.getElementById("heart-icon").style.color = "maroon";
            } else {
                document.getElementById("heart-icon").style.color = "lightgray";
            }
        }

        // setTimeout(() => {
        //   if ((searchInput.value).length > 0 ) {
        //     console.log(searchInput.value);
        //     const productsContainer = document.getElementById('products');

        //     fetch(`/product/search/${searchInput.value}`)
        //     .then(response => response.json())
        //     .then(data => {
        //       console.log(data);
        //       productsContainer.innerHTML = "";

        //       data.forEach(product => {
        //         updateProductList(product);
        //       });

        //       reloadJavaScript();

        //     })
        //     .catch(error => {
        //       console.error('Error:', error);
        //     });
        //     reloadJavaScript();
        //   }
        // }, 0);
    });

    // User Category
    document.addEventListener("DOMContentLoaded", function () {
        const categoryLinks = document.querySelectorAll(".category a");
        const productsContainer = document.getElementById("products");

        categoryLinks.forEach((link) => {
            link.addEventListener("click", function (e) {
                recreateBottomSheet();
                e.preventDefault();

                categoryId = this.dataset.categoryId;
                //console.log(categoryId);

                fetch(`/product/category/${categoryId}`)
                    .then((response) => response.json())
                    .then((data) => {
                        fetch(`/product/category/name/${categoryId}`)
                            .then((response) => response.json())
                            .then((data) => {
                                document.getElementById(
                                    "categories",
                                ).innerHTML = data;
                            })
                            .catch((error) => {
                                console.error("Error:", error);
                            });

                        productsContainer.innerHTML = "";

                        data.forEach((product) => {
                            updateProductList(product);
                        });

                        reloadJavaScript();
                    })
                    .catch((error) => {
                        console.error("Error:", error);
                    });
            });
        });
    });

    //Dynamic Search
    document.addEventListener("DOMContentLoaded", function () {
        dynamicSearch(categoryId);
    });

    //================================================================================

    function updateProductList(product) {
        const productsContainer = document.getElementById("products");

        const productContainer = document.createElement("div");
        productContainer.classList.add("product-container");

        const productContent = document.createElement("div");
        productContent.classList.add("product-content");

        const productImage = document.createElement("div");
        productImage.classList.add("product-image");

        const showModalButton = document.createElement("button");
        showModalButton.classList.add("show-modal");
        showModalButton.dataset.product = JSON.stringify(product);

        const productImageElement = document.createElement("img");
        productImageElement.id = "product-image";
        productImageElement.src = `images/product/${product.image}`;
        productImageElement.alt = product.name;

        showModalButton.appendChild(productImageElement);

        const addCart = document.createElement("button");
        addCart.classList.add("add-cart");
        addCart.id = "add-cart";
        addCart.dataset.product = JSON.stringify(product);

        const addIcon = document.createElement("iconify-icon");
        addIcon.id = "add-icon";
        addIcon.icon = "ph:plus";

        addCart.appendChild(addIcon);

        productImage.appendChild(showModalButton);
        productImage.appendChild(addCart);

        const productsInfo = document.createElement("div");
        productsInfo.classList.add("products-info");

        const productInfo = document.createElement("div");
        productInfo.classList.add("product-info");

        const productTime = document.createElement("div");
        productTime.classList.add("product-time");

        const timerIcon = document.createElement("iconify-icon");
        timerIcon.id = "timer-icon";
        timerIcon.icon = "svg-spinners:clock";

        const productTimeElement = document.createElement("span");
        productTimeElement.id = "product-time";
        productTimeElement.textContent = `${product.time} mins`;

        productTime.appendChild(timerIcon);
        productTime.appendChild(productTimeElement);

        const productNamePrice = document.createElement("div");
        productNamePrice.classList.add("product-name-price");

        const productNameElement = document.createElement("h1");
        productNameElement.id = "product-name";
        productNameElement.textContent = product.name;

        productNamePrice.appendChild(productNameElement);

        const productPriceElement = document.createElement("span");
        productPriceElement.id = "products-price";
        productPriceElement.textContent = `₱${product.price}`;

        productNamePrice.appendChild(productPriceElement);

        productInfo.appendChild(productTime);
        productInfo.appendChild(productNamePrice);

        // const productPrice = document.createElement('div');
        // productPrice.classList.add('product-price');

        productsInfo.appendChild(productInfo);
        // productsInfo.appendChild(productPrice);

        productContent.appendChild(productImage);
        productContent.appendChild(productsInfo);

        productContainer.appendChild(productContent);
        productsContainer.appendChild(productContainer);
    }

    function reloadJavaScript() {
        const showModalBtns = document.querySelectorAll(".show-modal");
        const bottomSheet = document.querySelector(".bottom-sheet");
        const addCartBtn = document.querySelectorAll(".add-cart");
        const sheetOverlay = bottomSheet.querySelector(".sheet-overlay");
        const sheetContent = bottomSheet.querySelector(".content");
        const dragIcon = bottomSheet.querySelector(".drag-icon");

        let currentQuantity = 1;
        let productId = undefined;
        let favorite = false;

        let isDragging = false,
            startY,
            startHeight;

        const showBottomSheet = () => {
            bottomSheet.classList.add("show");
            document.body.style.overflowY = "hidden";
            updateSheetHeight(50);
            minusButtonColor(currentQuantity);
            modifyFavButton(favorite);
        };

        const updateSheetHeight = (height) => {
            sheetContent.style.height = `${height}vh`;
            bottomSheet.classList.toggle("fullscreen", height === 100);
        };

        const hideBottomSheet = () => {
            bottomSheet.classList.remove("show");
            document.body.style.overflowY = "auto";
            document.getElementById("modal-quantity").innerHTML = 1;
            currentQuantity = 1;
            productId = undefined;
            favorite = false;
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
            sheetHeight < 25
                ? hideBottomSheet()
                : sheetHeight > 75
                  ? updateSheetHeight(100)
                  : updateSheetHeight(50);
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

                document.getElementById("selling-image").src =
                    "images/product/" + product.image;
                document
                    .getElementById("heart-icon")
                    .classList.remove("active");
                document.querySelector(".name").textContent = product.name;
                document.querySelector(".price").textContent =
                    "₱" + product.price;
                document.getElementById("modal-quantity").innerHTML = 1;
                productId = product.id;
                fetch(`/favorite/show/${productId}`)
                    .then((response) => response.json())
                    .then((data) => {
                        favorite = data;
                        modifyFavButton(favorite);
                    })
                    .catch((error) => {
                        console.error("Error:", error);
                    });

                showBottomSheet();
            });
        });

        //Single Add Cart

        addCartBtn.forEach((btn) => {
            btn.addEventListener("click", () => {
                const product = JSON.parse(btn.dataset.product);
                productId = product.id;

                fetch(`/cart/store/single/product/${productId}`)
                    .then((response) => response.json())
                    .then((data) => {
                        updateInCart(data);
                    })
                    .catch((error) => {
                        console.error("Error:", error);
                    });
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

        minusButtonColor(currentQuantity);
        addButton.addEventListener("click", function (e) {
            e.preventDefault(e);
            currentQuantity++;
            quantitySpan.textContent = currentQuantity;
            minusButtonColor(currentQuantity);
            updateInputQuanity(currentQuantity);
        });

        minusButton.addEventListener("click", function (e) {
            e.preventDefault();
            if (currentQuantity > 1) {
                currentQuantity--;
                quantitySpan.textContent = currentQuantity;
                minusButtonColor(currentQuantity);
                updateInputQuanity(currentQuantity);
            }
        });

        //Update header quantity(Inside cart Icon)
        function updateInCart(value) {
            document.getElementById("in-cart").innerHTML = value;
        }

        //Show header quantity(Inside cart Icon)
        fetch(`/cart/show/product/inside`)
            .then((response) => response.json())
            .then((data) => {
                updateInCart(data);
            })
            .catch((error) => {
                console.error("Error:", error);
            });

        //Add to cart function
        const addCartButton = document.getElementById("add-2-cart");
        addCartButton.addEventListener("click", (e) => {
            e.preventDefault();

            fetch(
                `/cart/store/product/${productId}?quantity=${currentQuantity}`,
            )
                .then((response) => response.json())
                .then((data) => {
                    fetch(`/cart/show/product/inside`)
                        .then((response) => response.json())
                        .then((data) => {
                            updateInCart(data);
                        })
                        .catch((error) => {
                            console.error("Error:", error);
                        });

                    hideBottomSheet();
                })
                .catch((error) => {
                    console.error("Error:", error);
                });
        });

        //Add Favorite Function

        const favButton = document.getElementById("heart-button");
        favButton.addEventListener("click", (e) => {
            e.preventDefault();

            fetch(`/favorite/add/${productId}`)
                .then((response) => response.json())
                .then((data) => {
                    modifyFavButton(data);
                })
                .catch((error) => {
                    console.error("Error:", error);
                });
        });

        //Favorite Color Function
        function modifyFavButton(fav) {
            if (fav == true) {
                document.getElementById("heart-icon").style.color = "maroon";
            } else {
                document.getElementById("heart-icon").style.color = "lightgray";
            }
        }

        //Dynamic Search

        // const searchInput = document.getElementById("searchInput");
        // const searchForm = document.getElementById("search-form");

        // searchForm.addEventListener("submit", (e) => {
        //   e.preventDefault()
        //   searchInput.blur();
        //   const productsContainer = document.getElementById('products');

        //   fetch(`/product/search/${searchInput.value}`)
        //   .then(response => response.json())
        //   .then(data => {
        //     console.log(data);
        //     productsContainer.innerHTML = "";

        //     data.forEach(product => {
        //       updateProductList(product);
        //     });

        //     reloadJavaScript();

        //   })
        //   .catch(error => {
        //     console.error('Error:', error);
        //   });
        //   //reloadJavaScript();
        // });
    }

    function recreateBottomSheet() {
        const form = document.getElementById("bottom-sheet");
        form.innerHTML = "";

        const sheetOverlay = document.createElement("div");
        sheetOverlay.classList.add("sheet-overlay");

        const content = document.createElement("div");
        content.classList.add("content");

        const header = document.createElement("div");
        header.classList.add("header");

        const dragIcon = document.createElement("div");
        dragIcon.classList.add("drag-icon");

        const headerIcon = document.createElement("span");
        headerIcon.classList.add("header-icon");

        dragIcon.appendChild(headerIcon);
        header.appendChild(dragIcon);

        const body = document.createElement("div");
        body.classList.add("body");

        const sellingImage = document.createElement("img");
        sellingImage.id = "selling-image";
        sellingImage.src = "images/product/default.jpg";
        sellingImage.alt = "default";

        const heartIcon = document.createElement("button");
        heartIcon.classList.add("heart-button");
        heartIcon.id = "heart-button";

        const heartIconify = document.createElement("iconify-icon");
        heartIconify.id = "heart-icon";
        heartIconify.icon = "material-symbols:favorite";

        heartIcon.appendChild(heartIconify);

        const productDetail = document.createElement("div");
        productDetail.classList.add("product-detail");

        const name = document.createElement("h2");
        name.classList.add("name");
        name.textContent = "No Product";

        const price = document.createElement("p");
        price.classList.add("price");
        price.textContent = "₱0";

        productDetail.appendChild(name);
        productDetail.appendChild(price);

        const modalBtns = document.createElement("div");
        modalBtns.classList.add("modal-btns");

        const quantityBtns = document.createElement("div");
        quantityBtns.classList.add("quantity-btns");

        const quantityMinus = document.createElement("button");
        quantityMinus.classList.add("quantity-minus");
        quantityMinus.id = "quantity-minus";

        const quantityMinusIcon = document.createElement("iconify-icon");
        quantityMinusIcon.icon = "ph:minus";

        quantityMinus.appendChild(quantityMinusIcon);

        const inputQuantity = document.createElement("input");
        inputQuantity.type = "number";
        inputQuantity.id = "input-quantity";
        inputQuantity.min = 1;
        inputQuantity.value = 1;
        inputQuantity.style.display = "none";

        const modalQuantity = document.createElement("span");
        modalQuantity.id = "modal-quantity";
        modalQuantity.textContent = "1";

        const quantityPlus = document.createElement("button");
        quantityPlus.classList.add("quantity-plus");
        quantityPlus.id = "quantity-plus";

        const quantityPlusIcon = document.createElement("iconify-icon");
        quantityPlusIcon.icon = "ph:plus";

        quantityPlus.appendChild(quantityPlusIcon);

        quantityBtns.appendChild(quantityMinus);
        quantityBtns.appendChild(inputQuantity);
        quantityBtns.appendChild(modalQuantity);
        quantityBtns.appendChild(quantityPlus);

        const addToCart = document.createElement("button");
        addToCart.type = "submit";
        addToCart.classList.add("add-to-cart");
        addToCart.id = "add-2-cart";
        addToCart.dataset.product = "";
        addToCart.textContent = "Add to cart";

        modalBtns.appendChild(quantityBtns);
        modalBtns.appendChild(addToCart);

        body.appendChild(sellingImage);
        body.appendChild(heartIcon);
        body.appendChild(productDetail);
        body.appendChild(modalBtns);

        content.appendChild(header);
        content.appendChild(body);

        form.appendChild(sheetOverlay);
        form.appendChild(content);
    }

    function dynamicSearch(category) {
        const searchInput = document.getElementById("searchInput");
        const searchForm = document.getElementById("search-form");
        //console.log(categoryId);

        searchForm.addEventListener("submit", (e) => {
            e.preventDefault();
            searchInput.blur();
            const productsContainer = document.getElementById("products");

            if (searchInput.value) {
                fetch(
                    `/product/search/${searchInput.value}?categoryId=${categoryId}`,
                )
                    .then((response) => response.json())
                    .then((data) => {
                        console.log(data);
                        productsContainer.innerHTML = "";

                        data.forEach((product) => {
                            updateProductList(product);
                        });

                        reloadJavaScript();
                    })
                    .catch((error) => {
                        console.error("Error:", error);
                    });
            } else {
                fetch(`/product/category/${categoryId}`)
                    .then((response) => response.json())
                    .then((data) => {
                        //console.log("without value:" + categoryId);
                        productsContainer.innerHTML = "";

                        data.forEach((product) => {
                            updateProductList(product);
                        });

                        reloadJavaScript();
                    })
                    .catch((error) => {
                        console.error("Error:", error);
                    });
            }

            //reloadJavaScripts();
        });
    }

    //================================================================================
}
