// Sélection des éléments
const cartBtn = document.querySelector('.cart-icon');
const closeCartBtn = document.querySelector('.close-cart');
const cartOverlay = document.querySelector('.cart-overlay');
const addToCartBtns = document.querySelectorAll('.add-to-cart, .add-to-cart-js');
const cartContent = document.querySelector('.cart-content');
const cartTotal = document.querySelector('.cart-total-amount');
const cartCount = document.querySelector('.cart-count');

// Panier (objet associatif : id => quantité)
let cart = {};

// Charger le panier depuis le serveur au chargement de la page
function loadCartFromServer() {
    fetch('/ghost/deep/api/cart_api.php?action=get')
        .then(response => response.json())
        .then(data => {
            cart = data.cart || {};
            localStorage.setItem('cart', JSON.stringify(cart));
            updateCartDisplay();
            updateCartIcon();
        });
}
loadCartFromServer();

// Ouvrir le panier
cartBtn.addEventListener('click', () => {
    cartOverlay.style.display = 'flex';
});

// Fermer le panier
closeCartBtn.addEventListener('click', () => {
    cartOverlay.style.display = 'none';
});

// Ajouter au panier via API serveur
function addToCart(productId, qty = 1) {
    fetch('/ghost/deep/api/cart_api.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: `action=add&id=${productId}&qty=${qty}`
    })
        .then(response => response.json())
        .then(data => {
            cart = data.cart || {};
            localStorage.setItem('cart', JSON.stringify(cart));
            updateCartDisplay();
            updateCartIcon();
            showAlert("Produit ajouté au panier !");
        });
}

// Mettre à jour la quantité via API serveur
function updateCartItem(productId, qty) {
    fetch('/ghost/deep/api/cart_api.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: `action=update&id=${productId}&qty=${qty}`
    })
        .then(response => response.json())
        .then(data => {
            cart = data.cart || {};
            localStorage.setItem('cart', JSON.stringify(cart));
            updateCartDisplay();
            updateCartIcon();
        });
}

// Supprimer un article via API serveur
function removeCartItem(productId) {
    fetch('/ghost/deep/api/cart_api.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: `action=remove&id=${productId}`
    })
        .then(response => response.json())
        .then(data => {
            cart = data.cart || {};
            localStorage.setItem('cart', JSON.stringify(cart));
            updateCartDisplay();
            updateCartIcon();
        });
}

// Gestion du clic sur les boutons "Ajouter au panier"
addToCartBtns.forEach(btn => {
    btn.addEventListener('click', function (e) {
        e.preventDefault();
        const productId = this.getAttribute('data-id');
        addToCart(productId);
    });
});

// Met à jour l'affichage du panier
function updateCartDisplay() {
    cartContent.innerHTML = '';
    let total = 0;
    let count = 0;

    // Pour chaque produit dans le panier, il faut récupérer les infos (nom, prix, image)
    // Ici, on stocke dans un attribut data-* sur le bouton "Ajouter au panier"
    // Sinon, il faut faire une requête AJAX pour récupérer les infos produit

    Object.keys(cart).forEach(id => {
        const qty = cart[id];
        // Recherche le bouton correspondant pour récupérer les infos produit
        const btn = document.querySelector(`[data-id="${id}"]`);
        if (!btn) return;
        const name = btn.getAttribute('data-name') || 'Produit';
        const price = parseFloat(btn.getAttribute('data-price')) || 0;
        const image = btn.getAttribute('data-image') || '';

        total += price * qty;
        count += qty;

        const cartItem = document.createElement('div');
        cartItem.classList.add('cart-item');
        cartItem.innerHTML = `
        <div class="cart-item-flex">
                <div class="cart-item-info">
                <img src="../admin/${image}" alt="${name}">
                </div>
                <div class="cart-item-info" >
                    <div class="cart-item-title">${name}</div>
                    <div class="cart-item-price">${price.toFixed(2)}$</div>
                        <div class="cart-item-quantity">
                            <button class="quantity-btn minus" data-id="${id}">-</button>
                            <span class="quantity">${qty}</span>
                            <button class="quantity-btn plus" data-id="${id}">+</button>
                        </div>
                    <div class="remove-item" data-id="${id}">Supprimer</div>
                </div>
          </div>
        `;
        cartContent.appendChild(cartItem);
    });

    cartTotal.textContent = `${total.toFixed(2)}$`;
    cartCount.textContent = count;

    // Gérer les boutons + et -
    cartContent.querySelectorAll('.minus').forEach(btn => {
        btn.addEventListener('click', () => {
            const id = btn.getAttribute('data-id');
            if (cart[id] > 1) {
                updateCartItem(id, cart[id] - 1);
            }
        });
    });

    cartContent.querySelectorAll('.plus').forEach(btn => {
        btn.addEventListener('click', () => {
            const id = btn.getAttribute('data-id');
            updateCartItem(id, cart[id] + 1);
        });
    });

    cartContent.querySelectorAll('.remove-item').forEach(btn => {
        btn.addEventListener('click', () => {
            const id = btn.getAttribute('data-id');
            removeCartItem(id);
        });
    });
}

// Afficher une alerte
function showAlert(message) {
    const alert = document.createElement('div');
    alert.classList.add('alert');
    alert.textContent = message;
    alert.style.position = 'fixed';
    alert.style.bottom = '20px';
    alert.style.right = '20px';
    alert.style.backgroundColor = '#4CAF50';
    alert.style.color = 'white';
    alert.style.padding = '15px';
    alert.style.borderRadius = '5px';
    alert.style.zIndex = '1000';
    alert.style.animation = 'slideIn 0.5s, fadeOut 0.5s 2.5s';

    document.body.appendChild(alert);

    setTimeout(() => {
        alert.remove();
    }, 3000);
}

// Style pour l'animation de l'alerte
const style = document.createElement('style');
style.textContent = `
    @keyframes slideIn {
        from { transform: translateX(100%); opacity: 0; }
        to { transform: translateX(0); opacity: 1; }
    }
    @keyframes fadeOut {
        from { opacity: 1; }
        to { opacity: 0; }
    }
`;
document.head.appendChild(style);

// Met à jour le compteur du panier dans l'icône
function updateCartIcon() {
    let count = 0;
    Object.values(cart).forEach(qty => {
        count += qty;
    });
    if (cartCount) cartCount.textContent = count;
}