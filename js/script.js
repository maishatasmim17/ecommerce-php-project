document.addEventListener('DOMContentLoaded', () => {

  // ======== (Optional) Demo Test Data ========
  if (!localStorage.getItem('cartItems')) {
     localStorage.setItem('cartItems', JSON.stringify(['product1', 'product2']));
  }
  if (!localStorage.getItem('wishlistItems')) {
     localStorage.setItem('wishlistItems', JSON.stringify(['productA']));
  }

  // Function to update the cart and wishlist counters in the header
  function updateHeaderCounts() {
     const cartItems = JSON.parse(localStorage.getItem('cartItems')) || [];
     const wishlistItems = JSON.parse(localStorage.getItem('wishlistItems')) || [];

     const cartCount = document.querySelector('#cart-count');
     const wishlistCount = document.querySelector('#wishlist-count');

     if (cartCount) cartCount.textContent = `(${cartItems.length})`;
     if (wishlistCount) wishlistCount.textContent = `(${wishlistItems.length})`;
  }

  // Functions to add items (you can call these from buttons)
  function addToCart(productId) {
     let cart = JSON.parse(localStorage.getItem('cartItems')) || [];
     cart.push(productId);
     localStorage.setItem('cartItems', JSON.stringify(cart));
     updateHeaderCounts();
  }

  function addToWishlist(productId) {
     let wishlist = JSON.parse(localStorage.getItem('wishlistItems')) || [];
     wishlist.push(productId);
     localStorage.setItem('wishlistItems', JSON.stringify(wishlist));
     updateHeaderCounts();
  }

  // ======== Load Header and Footer ========
  fetch('header.html')
     .then(res => res.text())
     .then(data => {
        document.getElementById('header-placeholder').innerHTML = data;

        // Select elements after header loads
        const navbar = document.querySelector('.header .flex .navbar');
        const profile = document.querySelector('.header .flex .profile');
        const menuBtn = document.querySelector('#menu-btn');
        const userBtn = document.querySelector('#user-btn');

        // Menu button toggle
        if (menuBtn) {
           menuBtn.onclick = () => {
              navbar.classList.toggle('active');
              profile.classList.remove('active');
           };
        }

        // User/profile button toggle
        if (userBtn) {
           userBtn.onclick = () => {
              profile.classList.toggle('active');
              navbar.classList.remove('active');
           };
        }

        // Hide menus on scroll
        window.onscroll = () => {
           profile.classList.remove('active');
           navbar.classList.remove('active');
        };

        // Update cart/wishlist counts now that header is loaded
        updateHeaderCounts();
     });

  fetch('footer.html')
     .then(res => res.text())
     .then(data => {
        document.getElementById('footer-placeholder').innerHTML = data;
     });

  // Expose these functions globally if you want to call them from HTML buttons
  window.addToCart = addToCart;
  window.addToWishlist = addToWishlist;

});

let menuBtn = document.querySelector('#menu-btn');
let userBtn = document.querySelector('#user-btn');
let navbar = document.querySelector('.navbar');
let profile = document.querySelector('.profile');

menuBtn.onclick = () => {
  navbar.classList.toggle('active');
  profile.classList.remove('active');
};

userBtn.onclick = () => {
  profile.classList.toggle('active');
  navbar.classList.remove('active');
};

// Optional: Hide both when clicking outside
window.onscroll = () => {
  navbar.classList.remove('active');
  profile.classList.remove('active');
};
