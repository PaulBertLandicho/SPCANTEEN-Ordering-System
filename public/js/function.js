/* =========================
   GLOBAL SAFE STATE (NO DUPLICATES)
========================= */

window.currentUrl = window.location.href;

window.appState = {
    currentProductId: null,
    currentQuantity: 1,
};

/* =========================
   PASSWORD TOGGLE
========================= */

function togglePassword(icon, inputId) {
    const input = document.getElementsByName(inputId)[0];
    if (!input) return;

    if (input.type === "password") {
        input.type = "text";
        icon.className = "fa-solid fa-eye-slash";
    } else {
        input.type = "password";
        icon.className = "fa-solid fa-eye";
    }
}

/* =========================
   PROFILE PREVIEW
========================= */

function previewProfilePicture(input) {
    if (!input.files || !input.files[0]) return;

    const reader = new FileReader();
    reader.onload = function (e) {
        const img = document.getElementById("avatar");
        if (img) img.src = e.target.result;
    };
    reader.readAsDataURL(input.files[0]);
}

/* =========================
   SAFE FETCH
========================= */

async function safeFetch(url, options = {}) {
    const res = await fetch(url, {
        headers: {
            "X-Requested-With": "XMLHttpRequest",
            Accept: "application/json",
        },
        ...options,
    });

    const text = await res.text();

    try {
        return JSON.parse(text);
    } catch (e) {
        console.error("❌ Non-JSON response:", text);
        throw new Error("Server returned HTML instead of JSON");
    }
}

/* =========================
   MODAL OPEN
========================= */

function openModal(product) {
    const sheet = document.querySelector(".bottom-sheet");
    if (!sheet) return;

    const img = document.getElementById("selling-image");
    const name = document.querySelector(".name");
    const price = document.querySelector(".price");
    const qty = document.getElementById("modal-quantity");

    fetch(`/favorite/check/${product.id}`)
        .then((r) => r.json())
        .then((isFav) => {
            const icon = document.getElementById("heart-icon");
            if (icon) {
                icon.style.color = isFav ? "red" : "lightgray";
            }
        });

    if (img) img.src = "images/product/" + product.image;
    if (name) name.textContent = product.name;
    if (price) price.textContent = "₱" + product.price;
    if (qty) qty.textContent = 1;

    window.appState.currentProductId = product.id;
    window.appState.currentQuantity = 1;

    sheet.classList.add("show");
    document.body.style.overflowY = "hidden";
}

/* =========================
   CLOSE MODAL
========================= */

function hideBottomSheet() {
    const sheet = document.querySelector(".bottom-sheet");
    if (!sheet) return;

    sheet.classList.remove("show");
    document.body.style.overflowY = "auto";

    window.appState.currentProductId = null;
    window.appState.currentQuantity = 1;
}

/* =========================
   FAVORITE TOGGLE
========================= */

async function toggleFavorite() {
    const id = window.appState.currentProductId;
    if (!id) return;

    try {
        const res = await fetch(`/favorite/toggle/${id}`, {
            method: "POST",
            headers: {
                "X-CSRF-TOKEN": document.querySelector(
                    'meta[name="csrf-token"]',
                ).content,
                Accept: "application/json",
            },
        });

        const data = await res.json();

        const icon = document.getElementById("heart-icon");
        if (!icon) return;

        icon.style.color = data === true ? "red" : "lightgray";
    } catch (err) {
        console.error("Favorite toggle failed:", err);
    }
}
/* =========================
   CART COUNT UPDATE
========================= */

function updateCartCount() {
    fetch(`/cart/show/product/inside`)
        .then((r) => r.json())
        .then((data) => {
            const el = document.getElementById("in-cart");
            if (el) el.innerHTML = data;
        })
        .catch(console.error);
}

/* =========================
   QUANTITY UPDATE
========================= */

function updateQuantityUI() {
    const span = document.getElementById("modal-quantity");
    if (span) span.textContent = window.appState.currentQuantity;
}

/* =========================
   CLICK HANDLER (NO DUPLICATES)
========================= */

document.addEventListener("click", function (e) {
    const showBtn = e.target.closest(".show-modal");
    const cartBtn = e.target.closest(".add-cart");
    const favBtn = e.target.closest("#heart-button");
    const overlay = e.target.closest(".sheet-overlay");

    if (showBtn) {
        let product = null;

        try {
            product = JSON.parse(showBtn.dataset.product || "{}");
        } catch (e) {
            console.error("Invalid product JSON:", showBtn.dataset.product);
            return;
        }

        if (!product || !product.id) return;
        openModal(product);
    }

    if (cartBtn) {
        let product = null;

        try {
            product = JSON.parse(cartBtn.dataset.product || "{}");
        } catch (e) {
            console.error("Invalid product JSON:", cartBtn.dataset.product);
            return;
        }

        if (!product || !product.id) return;

        safeFetch(`/cart/store/single/product/${product.id}`)
            .then(updateCartCount)
            .catch(console.error);
    }
    if (favBtn) {
        if (!window.appState.currentProductId) return;
        toggleFavorite();
    }

    if (overlay) {
        hideBottomSheet();
    }
});

/* =========================
   DOM READY BUTTONS
========================= */

document.addEventListener("DOMContentLoaded", function () {
    const plus = document.getElementById("quantity-plus");
    const minus = document.getElementById("quantity-minus");
    const addCart = document.getElementById("add-2-cart");

    if (plus) {
        plus.addEventListener("click", (e) => {
            e.preventDefault();
            window.appState.currentQuantity++;
            updateQuantityUI();
        });
    }

    if (minus) {
        minus.addEventListener("click", (e) => {
            e.preventDefault();
            if (window.appState.currentQuantity > 1) {
                window.appState.currentQuantity--;
                updateQuantityUI();
            }
        });
    }

    if (addCart) {
        addCart.addEventListener("click", (e) => {
            e.preventDefault();

            const id = window.appState.currentProductId;
            if (!id) return;

            fetch(
                `/cart/store/product/${id}?quantity=${window.appState.currentQuantity}`,
            )
                .then((r) => r.json())
                .then(() => {
                    updateCartCount();
                    hideBottomSheet();
                })
                .catch(console.error);
        });
    }

    updateCartCount();
});
