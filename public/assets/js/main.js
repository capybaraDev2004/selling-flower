/**
 * Main JavaScript cho Siin Store
 */

// =====================================
// Global Variables
// =====================================
let cart = [];
let wishlist = [];
let guestId = null;

// =====================================
// Guest Identification với Cookie
// =====================================
function getCookie(name) {
    const value = `; ${document.cookie}`;
    const parts = value.split(`; ${name}=`);
    if (parts.length === 2) return parts.pop().split(';').shift();
    return null;
}

function setCookie(name, value, days = 365) {
    const date = new Date();
    date.setTime(date.getTime() + (days * 24 * 60 * 60 * 1000));
    const expires = `expires=${date.toUTCString()}`;
    document.cookie = `${name}=${value};${expires};path=/;SameSite=Lax`;
}

function getOrCreateGuestId() {
    // Ưu tiên 1: Kiểm tra Cookie
    guestId = getCookie('guest_id');
    
    // Ưu tiên 2: Kiểm tra localStorage (fallback)
    if (!guestId) {
    guestId = localStorage.getItem('guest_id');
    }
    
    // Nếu chưa có, tạo mới
    if (!guestId) {
        // Tạo mã định danh duy nhất: timestamp + random string
        const timestamp = Date.now();
        const randomStr = Math.random().toString(36).substring(2, 15);
        const randomStr2 = Math.random().toString(36).substring(2, 15);
        guestId = `guest_${timestamp}_${randomStr}${randomStr2}`;
        
        // Lưu thời gian tạo
        const createdAt = new Date().toISOString();
        localStorage.setItem('guest_created_at', createdAt);
    }
    
    // Lưu vào cả Cookie và localStorage để đồng bộ
    setCookie('guest_id', guestId, 365); // Cookie tồn tại 1 năm
    localStorage.setItem('guest_id', guestId);
    
    return guestId;
}

// =====================================
// Initialize
// =====================================
document.addEventListener('DOMContentLoaded', function() {
    console.log('=== Initializing App ===');
    
    // Tạo hoặc lấy mã định danh người dùng
    const guestIdResult = getOrCreateGuestId();
    console.log('Guest ID initialized:', guestIdResult);
    
    initMobileMenu();
    initCart();
    initSearch();
    initScrollEffects();
    loadCartFromStorage();
    updateCartCount();
    
    console.log('=== App Initialized ===');
    console.log('Cart:', cart);
    console.log('Cart count:', getCartCount());
});

// =====================================
// Mobile Menu
// =====================================
function initMobileMenu() {
    const mobileMenuToggle = document.getElementById('mobile-menu-toggle');
    const mobileMenu = document.getElementById('mobile-menu');
    
    if (mobileMenuToggle && mobileMenu) {
        mobileMenuToggle.addEventListener('click', function() {
            mobileMenu.classList.toggle('hidden');
        });
    }
}

function toggleMobileSubmenu(menuId) {
    const submenu = document.getElementById('submenu-' + menuId);
    if (submenu) {
        submenu.classList.toggle('hidden');
    }
}

// =====================================
// Shopping Cart Functions
// =====================================
function initCart() {
    // Đảm bảo có mã định danh
    if (!guestId) {
        getOrCreateGuestId();
    }
    
    // Load cart from localStorage theo mã định danh
    const cartKey = `cart_${guestId}`;
    const savedCart = localStorage.getItem(cartKey);
    if (savedCart) {
        try {
            cart = JSON.parse(savedCart);
        } catch (e) {
            console.error('Lỗi khi đọc giỏ hàng:', e);
            cart = [];
        }
    } else {
        cart = [];
    }
}

function addToCart(productId, quantity = 1, productData = null) {
    try {
    // Đảm bảo có mã định danh
    if (!guestId) {
        getOrCreateGuestId();
    }
    
        // Validate và chuyển đổi sang số nguyên
    productId = parseInt(productId);
        if (isNaN(productId) || productId <= 0) {
            console.error('Product ID không hợp lệ:', productId);
            showToast('Có lỗi xảy ra khi thêm sản phẩm!', 'error');
            return false;
        }
        
    quantity = parseInt(quantity) || 1;
        if (quantity <= 0) {
            quantity = 1;
        }
    
    // Nếu có productData từ button, lưu thông tin sản phẩm
    if (productData) {
        const productsKey = `products_${guestId}`;
        let products = {};
        try {
            const saved = localStorage.getItem(productsKey);
            if (saved) {
                products = JSON.parse(saved);
            }
        } catch (e) {
            console.error('Lỗi khi đọc thông tin sản phẩm:', e);
                products = {};
        }
        
        // Lấy rating - ưu tiên rating, sau đó rating_avg
        let rating = null;
        if (productData.rating !== undefined && productData.rating !== null) {
            rating = parseFloat(productData.rating);
        } else if (productData.rating_avg !== undefined && productData.rating_avg !== null) {
            rating = parseFloat(productData.rating_avg);
        }
        if (isNaN(rating)) rating = null;
        
        // Lấy reviews - ưu tiên reviews, sau đó reviews_count, cuối cùng rating_count
        let reviews = null;
        if (productData.reviews !== undefined && productData.reviews !== null) {
            reviews = parseInt(productData.reviews);
        } else if (productData.reviews_count !== undefined && productData.reviews_count !== null) {
            reviews = parseInt(productData.reviews_count);
        } else if (productData.rating_count !== undefined && productData.rating_count !== null) {
            reviews = parseInt(productData.rating_count);
        }
        if (isNaN(reviews)) reviews = null;
        
        // Lấy sold - ưu tiên sold, sau đó purchases, cuối cùng sold_count
        let sold = null;
        if (productData.sold !== undefined && productData.sold !== null) {
            sold = parseInt(productData.sold);
        } else if (productData.purchases !== undefined && productData.purchases !== null) {
            sold = parseInt(productData.purchases);
        } else if (productData.sold_count !== undefined && productData.sold_count !== null) {
            sold = parseInt(productData.sold_count);
        }
        if (isNaN(sold)) sold = null;
        
        products[productId] = {
            id: productId,
            name: productData.name || '',
            slug: productData.slug || '',
            image: productData.image || '',
            price: parseFloat(productData.price) || 0,
            sale_price: productData.sale_price ? parseFloat(productData.sale_price) : null,
            rating: rating,
            reviews: reviews,
            sold: sold,
            updatedAt: new Date().toISOString()
        };
        
        console.log('Saving product to localStorage:', products[productId]);
        
        localStorage.setItem(productsKey, JSON.stringify(products));
    }
    
    // Tìm sản phẩm trong giỏ hàng
    const existingItem = cart.find(item => item.id === productId);
    
    if (existingItem) {
        existingItem.quantity += quantity;
            existingItem.updatedAt = new Date().toISOString();
    } else {
        cart.push({
            id: productId,
            quantity: quantity,
            addedAt: new Date().toISOString()
        });
    }
    
    saveCart();
    updateCartCount();
    showToast('Đã thêm sản phẩm vào giỏ hàng!', 'success');
        return true;
    } catch (error) {
        console.error('Lỗi trong addToCart:', error);
        showToast('Có lỗi xảy ra khi thêm sản phẩm!', 'error');
        return false;
    }
}

function removeFromCart(productId) {
    try {
    productId = parseInt(productId);
        if (isNaN(productId)) {
            console.error('Product ID không hợp lệ:', productId);
            return false;
        }
        
        const beforeLength = cart.length;
    cart = cart.filter(item => item.id !== productId);
        
        if (cart.length < beforeLength) {
    saveCart();
    updateCartCount();
    showToast('Đã xóa sản phẩm khỏi giỏ hàng!', 'warning');
            return true;
        } else {
            showToast('Không tìm thấy sản phẩm trong giỏ hàng!', 'error');
            return false;
        }
    } catch (error) {
        console.error('Lỗi trong removeFromCart:', error);
        showToast('Có lỗi xảy ra khi xóa sản phẩm!', 'error');
        return false;
    }
}

function updateCartQuantity(productId, quantity) {
    try {
    productId = parseInt(productId);
        quantity = parseInt(quantity);
        
        if (isNaN(productId) || isNaN(quantity)) {
            console.error('Dữ liệu không hợp lệ:', {productId, quantity});
            return false;
        }
        
    const item = cart.find(item => item.id === productId);
    if (item) {
            if (quantity <= 0) {
            removeFromCart(productId);
        } else {
                item.quantity = quantity;
                item.updatedAt = new Date().toISOString();
            saveCart();
            updateCartCount();
                return true;
            }
        } else {
            console.error('Không tìm thấy sản phẩm trong giỏ hàng:', productId);
            return false;
        }
    } catch (error) {
        console.error('Lỗi trong updateCartQuantity:', error);
        return false;
    }
}

function clearCart() {
    try {
        if (confirm('Bạn có chắc muốn xóa tất cả sản phẩm khỏi giỏ hàng?')) {
    cart = [];
    saveCart();
    updateCartCount();
    showToast('Đã xóa tất cả sản phẩm khỏi giỏ hàng!', 'warning');
            return true;
        }
        return false;
    } catch (error) {
        console.error('Lỗi trong clearCart:', error);
        showToast('Có lỗi xảy ra!', 'error');
        return false;
    }
}

function saveCart() {
    try {
    if (!guestId) {
        getOrCreateGuestId();
    }
        
    const cartKey = `cart_${guestId}`;
        const cartData = JSON.stringify(cart);
        localStorage.setItem(cartKey, cartData);
        
        // Lưu timestamp cập nhật cuối
        localStorage.setItem(`${cartKey}_updated`, new Date().toISOString());
        
        return true;
    } catch (error) {
        console.error('Lỗi khi lưu giỏ hàng:', error);
        // Có thể là localStorage đầy
        if (error.name === 'QuotaExceededError') {
            showToast('Bộ nhớ trình duyệt đã đầy! Vui lòng xóa bớt dữ liệu cũ.', 'error');
        }
        return false;
    }
}

function loadCartFromStorage() {
    try {
    if (!guestId) {
        getOrCreateGuestId();
    }
        
    const cartKey = `cart_${guestId}`;
    const savedCart = localStorage.getItem(cartKey);
        
    if (savedCart) {
        try {
                const parsed = JSON.parse(savedCart);
                
                // Validate dữ liệu giỏ hàng
                if (Array.isArray(parsed)) {
                    cart = parsed.filter(item => {
                        return item && 
                               typeof item.id === 'number' && 
                               typeof item.quantity === 'number' && 
                               item.quantity > 0;
                    });
                } else {
                    console.warn('Dữ liệu giỏ hàng không hợp lệ, reset giỏ hàng');
                    cart = [];
                }
        } catch (e) {
                console.error('Lỗi khi parse giỏ hàng:', e);
                cart = [];
            }
        } else {
            cart = [];
        }
        
        return cart;
    } catch (error) {
        console.error('Lỗi trong loadCartFromStorage:', error);
        cart = [];
        return cart;
    }
}

function updateCartCount() {
    try {
        const cartCount = cart.reduce((total, item) => {
            const qty = parseInt(item.quantity) || 0;
            return total + qty;
        }, 0);
        
        const cartCountElements = document.querySelectorAll('#cart-count');
        cartCountElements.forEach(element => {
            if (element) {
                element.textContent = cartCount;
                
                // Thêm animation khi có thay đổi
                element.classList.add('scale-125');
                setTimeout(() => {
                    element.classList.remove('scale-125');
                }, 200);
            }
        });
        
        return cartCount;
    } catch (error) {
        console.error('Lỗi trong updateCartCount:', error);
        return 0;
    }
}

function getCartTotal() {
    try {
        if (!guestId) {
            getOrCreateGuestId();
        }
        
        const productsKey = `products_${guestId}`;
        let products = {};
        
        try {
            const saved = localStorage.getItem(productsKey);
            if (saved) {
                products = JSON.parse(saved);
            }
        } catch (e) {
            console.error('Lỗi khi đọc thông tin sản phẩm:', e);
            return 0;
        }
        
        let total = 0;
        cart.forEach(item => {
            const productInfo = products[item.id];
            if (productInfo) {
                const price = productInfo.sale_price || productInfo.price;
                total += price * item.quantity;
            }
        });
        
        return total;
    } catch (error) {
        console.error('Lỗi trong getCartTotal:', error);
        return 0;
    }
}

function getCartItems() {
    try {
        if (!guestId) {
            getOrCreateGuestId();
        }
        
        const productsKey = `products_${guestId}`;
        let products = {};
        
        try {
            const saved = localStorage.getItem(productsKey);
            if (saved) {
                products = JSON.parse(saved);
            }
        } catch (e) {
            console.error('Lỗi khi đọc thông tin sản phẩm:', e);
            return [];
        }
        
        // Kết hợp thông tin sản phẩm với giỏ hàng
        const cartItems = cart.map(item => {
            const productInfo = products[item.id] || {};
            return {
                id: item.id,
                quantity: item.quantity,
                addedAt: item.addedAt,
                updatedAt: item.updatedAt || item.addedAt,
                name: productInfo.name || 'Sản phẩm không xác định',
                slug: productInfo.slug || '',
                image: productInfo.image || '',
                price: productInfo.price || 0,
                sale_price: productInfo.sale_price || null,
                effectivePrice: productInfo.sale_price || productInfo.price || 0,
                subtotal: (productInfo.sale_price || productInfo.price || 0) * item.quantity
            };
        });
        
        return cartItems;
    } catch (error) {
        console.error('Lỗi trong getCartItems:', error);
        return [];
    }
}

function getCartCount() {
    return cart.reduce((total, item) => total + (parseInt(item.quantity) || 0), 0);
}

function isInCart(productId) {
    productId = parseInt(productId);
    return cart.some(item => item.id === productId);
}

// =====================================
// Wishlist Functions
// =====================================
function addToWishlist(productId) {
    if (!wishlist.includes(productId)) {
        wishlist.push(productId);
        localStorage.setItem('wishlist', JSON.stringify(wishlist));
        showToast('Đã thêm vào danh sách yêu thích!', 'success');
    } else {
        showToast('Sản phẩm đã có trong danh sách yêu thích!', 'warning');
    }
}

function removeFromWishlist(productId) {
    wishlist = wishlist.filter(id => id !== productId);
    localStorage.setItem('wishlist', JSON.stringify(wishlist));
    showToast('Đã xóa khỏi danh sách yêu thích!', 'warning');
}

// =====================================
// Quick View Modal
// =====================================
function quickView(productId) {
    // Sẽ được implement khi có API backend
    console.log('Quick view product:', productId);
    showToast('Tính năng xem nhanh đang được phát triển!', 'warning');
}

// =====================================
// Search Functions
// =====================================
function initSearch() {
    const searchInputs = document.querySelectorAll('input[type="text"][placeholder*="Tìm kiếm"]');
    
    searchInputs.forEach(input => {
        input.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                performSearch(this.value);
            }
        });
    });
}

function performSearch(query) {
    if (query.trim() !== '') {
        window.location.href = `${window.location.origin}/search.php?q=${encodeURIComponent(query)}`;
    }
}

// =====================================
// Toast Notification
// =====================================
function showToast(message, type = 'success') {
    // Remove existing toasts
    const existingToasts = document.querySelectorAll('.toast');
    existingToasts.forEach(toast => toast.remove());
    
    // Create toast element
    const toast = document.createElement('div');
    toast.className = `toast ${type}`;
    toast.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        background: white;
        padding: 16px 24px;
        border-radius: 12px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        z-index: 10000;
        display: flex;
        align-items: center;
        min-width: 300px;
        animation: slideInRight 0.3s ease;
    `;
    
    // Icon based on type
    let icon = '';
    let bgColor = '';
    switch(type) {
        case 'success':
            icon = '<i class="fas fa-check-circle text-green-500 mr-2"></i>';
            bgColor = 'border-l-4 border-green-500';
            break;
        case 'error':
            icon = '<i class="fas fa-times-circle text-red-500 mr-2"></i>';
            bgColor = 'border-l-4 border-red-500';
            break;
        case 'warning':
            icon = '<i class="fas fa-exclamation-circle text-yellow-500 mr-2"></i>';
            bgColor = 'border-l-4 border-yellow-500';
            break;
    }
    
    toast.className = `toast ${type} ${bgColor}`;
    toast.innerHTML = `
        <div class="flex items-center">
            ${icon}
            <span class="text-gray-800 font-medium">${message}</span>
        </div>
    `;
    
    document.body.appendChild(toast);
    
    // Auto remove after 3 seconds
    setTimeout(() => {
        toast.style.animation = 'slideOutRight 0.3s ease';
        setTimeout(() => toast.remove(), 300);
    }, 3000);
}

// Add CSS animations if not exists
if (!document.getElementById('toast-animations')) {
    const style = document.createElement('style');
    style.id = 'toast-animations';
    style.textContent = `
        @keyframes slideInRight {
            from {
                transform: translateX(100%);
                opacity: 0;
            }
            to {
                transform: translateX(0);
                opacity: 1;
            }
        }
        @keyframes slideOutRight {
            from {
                transform: translateX(0);
                opacity: 1;
            }
            to {
                transform: translateX(100%);
                opacity: 0;
            }
        }
    `;
    document.head.appendChild(style);
}

// =====================================
// Scroll Effects
// =====================================
function initScrollEffects() {
    // Sticky header effect
    let lastScroll = 0;
    const header = document.querySelector('header');
    
    window.addEventListener('scroll', () => {
        const currentScroll = window.pageYOffset;
        
        if (currentScroll <= 0) {
            header.classList.remove('scroll-up');
            return;
        }
        
        if (currentScroll > lastScroll && !header.classList.contains('scroll-down')) {
            // Scroll Down
            header.classList.remove('scroll-up');
            header.classList.add('scroll-down');
        } else if (currentScroll < lastScroll && header.classList.contains('scroll-down')) {
            // Scroll Up
            header.classList.remove('scroll-down');
            header.classList.add('scroll-up');
        }
        
        lastScroll = currentScroll;
    });
    
    // Fade-in animation on scroll
    const observerOptions = {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
    };
    
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.style.opacity = '1';
                entry.target.style.transform = 'translateY(0)';
            }
        });
    }, observerOptions);
    
    document.querySelectorAll('.fade-in-on-scroll').forEach(el => {
        el.style.opacity = '0';
        el.style.transform = 'translateY(20px)';
        el.style.transition = 'opacity 0.6s ease, transform 0.6s ease';
        observer.observe(el);
    });
}

// =====================================
// Image Lazy Loading
// =====================================
if ('loading' in HTMLImageElement.prototype) {
    const images = document.querySelectorAll('img[loading="lazy"]');
    images.forEach(img => {
        img.src = img.dataset.src || img.src;
    });
} else {
    // Fallback for browsers that don't support lazy loading
    const script = document.createElement('script');
    script.src = 'https://cdnjs.cloudflare.com/ajax/libs/lazysizes/5.3.2/lazysizes.min.js';
    document.body.appendChild(script);
}

// =====================================
// Format Currency
// =====================================
function formatCurrency(amount) {
    return new Intl.NumberFormat('vi-VN', {
        style: 'currency',
        currency: 'VND'
    }).format(amount);
}

// =====================================
// Smooth Scroll to Top
// =====================================
function scrollToTop() {
    window.scrollTo({
        top: 0,
        behavior: 'smooth'
    });
}

// Show scroll to top button
window.addEventListener('scroll', () => {
    const scrollBtn = document.getElementById('scroll-to-top');
    if (scrollBtn) {
        if (window.pageYOffset > 300) {
            scrollBtn.classList.remove('hidden');
        } else {
            scrollBtn.classList.add('hidden');
        }
    }
});

// =====================================
// Product Filter & Sort
// =====================================
function filterProducts(category) {
    console.log('Filter by category:', category);
    // Sẽ được implement khi có API backend
}

function sortProducts(sortBy) {
    console.log('Sort by:', sortBy);
    // Sẽ được implement khi có API backend
}

// =====================================
// Newsletter Subscription
// =====================================
function subscribeNewsletter(email) {
    if (validateEmail(email)) {
        // Sẽ được implement khi có API backend
        showToast('Đăng ký nhận tin thành công!', 'success');
        return true;
    } else {
        showToast('Email không hợp lệ!', 'error');
        return false;
    }
}

function validateEmail(email) {
    const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return re.test(email);
}

// =====================================
// Export functions to global scope
// =====================================
window.toggleMobileSubmenu = toggleMobileSubmenu;
window.addToCart = addToCart;
window.removeFromCart = removeFromCart;
window.updateCartQuantity = updateCartQuantity;
window.clearCart = clearCart;
window.addToWishlist = addToWishlist;
window.removeFromWishlist = removeFromWishlist;
window.quickView = quickView;
window.scrollToTop = scrollToTop;
window.filterProducts = filterProducts;
window.sortProducts = sortProducts;
window.getCartItems = getCartItems;
window.getCartTotal = getCartTotal;
window.getCartCount = getCartCount;
window.isInCart = isInCart;
window.getCookie = getCookie;
window.setCookie = setCookie;
window.getOrCreateGuestId = getOrCreateGuestId;

