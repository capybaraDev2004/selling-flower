<?php
require_once '../config/config.php';

$page_title = 'Giỏ hàng - ' . APP_NAME;
$page_description = 'Giỏ hàng của bạn';

// Giỏ hàng sẽ được load từ localStorage bằng JavaScript
// Không cần xử lý PHP vì hoàn toàn dùng localStorage

include '../includes/header.php';
?>

<!-- Breadcrumb -->
<div class="bg-gray-50 py-4">
    <div class="container mx-auto px-4">
        <div class="breadcrumb">
            <a href="<?php echo APP_URL; ?>"><i class="fas fa-home"></i> Trang chủ</a>
            <span class="separator">/</span>
            <span class="text-gray-800 font-medium">Giỏ hàng</span>
        </div>
    </div>
</div>

<!-- Cart Content -->
<section class="py-12">
    <div class="container mx-auto px-4">
        <div class="flex justify-between items-center mb-8">
            <h1 class="text-3xl font-bold">
                <i class="fas fa-shopping-cart text-rose-500 mr-3"></i>
                Giỏ hàng của bạn
            </h1>
            <div class="flex gap-2">
                <button onclick="debugCart()" class="bg-gray-500 text-white px-4 py-2 rounded-lg text-sm hover:bg-gray-600 transition">
                    <i class="fas fa-bug mr-1"></i> Debug
                </button>
                <button onclick="loadCartFromStorage()" class="bg-blue-500 text-white px-4 py-2 rounded-lg text-sm hover:bg-blue-600 transition">
                    <i class="fas fa-sync mr-1"></i> Reload
                </button>
            </div>
        </div>

        <!-- Empty Cart (sẽ được ẩn nếu có sản phẩm) -->
        <div id="empty-cart" class="bg-white rounded-xl shadow-sm p-12 text-center hidden">
            <i class="fas fa-shopping-cart text-gray-300 text-6xl mb-4"></i>
            <h2 class="text-2xl font-bold text-gray-700 mb-3">Giỏ hàng trống</h2>
            <p class="text-gray-600 mb-6">Bạn chưa có sản phẩm nào trong giỏ hàng</p>
            <a href="<?php echo APP_URL; ?>/shop.php" 
               class="inline-block bg-gradient-to-r from-rose-500 to-pink-500 text-white px-8 py-3 rounded-full font-semibold hover:shadow-lg transition">
                Tiếp tục mua sắm
            </a>
        </div>
        
        <!-- Cart Content -->
        <div id="cart-content" class="hidden">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Cart Items -->
                <div class="lg:col-span-2">
                    <div class="bg-white rounded-xl shadow-sm overflow-hidden">
                        <!-- Cart Header -->
                        <div class="bg-gray-50 px-6 py-4 border-b">
                            <div class="grid grid-cols-12 gap-4 font-semibold text-gray-700">
                                <div class="col-span-6">Sản phẩm</div>
                                <div class="col-span-2 text-center">Đơn giá</div>
                                <div class="col-span-2 text-center">Số lượng</div>
                                <div class="col-span-2 text-right">Tổng</div>
                            </div>
                        </div>

                        <!-- Cart Items -->
                        <div id="cart-items">
                            <!-- Sẽ được load từ localStorage bằng JavaScript -->
                        </div>

                        <!-- Cart Actions -->
                        <div class="p-6 bg-gray-50">
                            <div class="flex justify-between items-center">
                                <a href="<?php echo APP_URL; ?>/shop.php" 
                                   class="text-rose-500 font-semibold hover:text-rose-600 transition">
                                    <i class="fas fa-arrow-left mr-2"></i>
                                    Tiếp tục mua sắm
                                </a>
                                <button onclick="clearCart()" 
                                        class="text-red-500 font-semibold hover:text-red-600 transition">
                                    <i class="fas fa-trash mr-2"></i>
                                    Xóa giỏ hàng
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Cart Summary -->
                <div class="lg:col-span-1">
                    <div class="bg-white rounded-xl shadow-sm p-6 sticky top-24">
                        <h3 class="text-xl font-bold mb-6">Tổng đơn hàng</h3>

                        <!-- Coupon -->
                        <?php /* Tạm thời tắt chức năng mã giảm giá
                        <div class="mb-6">
                            <label class="block text-sm font-semibold mb-2">Mã giảm giá</label>
                            <div class="flex gap-2">
                                <input type="text" 
                                       id="coupon-code"
                                       placeholder="Nhập mã..." 
                                       class="flex-1 px-4 py-2 border-2 border-gray-300 rounded-lg focus:outline-none focus:border-rose-500">
                                <button onclick="applyCoupon()" 
                                        class="bg-rose-500 text-white px-4 py-2 rounded-lg font-semibold hover:bg-rose-600 transition">
                                    Áp dụng
                                </button>
                            </div>
                            <p class="text-xs text-gray-500 mt-2">
                                <i class="fas fa-tag mr-1"></i>
                                Nhập mã "Uudai5" giảm 5%
                            </p>
                        </div>
                        */ ?>

                        <!-- Price Details -->
                        <div id="cart-summary" class="space-y-3 mb-6">
                            <!-- Sẽ được cập nhật bằng JavaScript -->
                        </div>

                        <!-- Checkout Button -->
                        <?php /* Tạm thời tắt chức năng thanh toán
                        <button onclick="checkout()" 
                                class="w-full bg-gradient-to-r from-rose-500 to-pink-500 text-white py-4 rounded-xl font-bold text-lg hover:shadow-lg transition-all hover:-translate-y-1 mb-3">
                            <i class="fas fa-credit-card mr-2"></i>
                            Tiến hành thanh toán
                        </button>

                        <div class="text-center text-sm text-gray-600">
                            <i class="fas fa-shield-alt text-green-500 mr-1"></i>
                            Thanh toán an toàn & bảo mật
                        </div>

                        <!-- Payment Methods -->
                        <div class="mt-6 pt-6 border-t">
                            <p class="text-sm font-semibold mb-3">Chúng tôi chấp nhận:</p>
                            <div class="grid grid-cols-3 gap-2">
                                <div class="border rounded p-2 text-center">
                                    <i class="fas fa-money-bill-wave text-green-600 text-2xl"></i>
                                    <p class="text-xs mt-1">Tiền mặt</p>
                                </div>
                                <div class="border rounded p-2 text-center">
                                    <i class="fas fa-credit-card text-blue-600 text-2xl"></i>
                                    <p class="text-xs mt-1">Thẻ</p>
                                </div>
                                <div class="border rounded p-2 text-center">
                                    <i class="fas fa-mobile-alt text-purple-600 text-2xl"></i>
                                    <p class="text-xs mt-1">Ví điện tử</p>
                                </div>
                            </div>
                        </div>
                        */ ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
// Định nghĩa formatCurrency trước
function formatCurrency(amount) {
    if (isNaN(amount) || amount === null || amount === undefined) {
        amount = 0;
    }
    return new Intl.NumberFormat('vi-VN', {
        style: 'currency',
        currency: 'VND'
    }).format(amount);
}

// Escape HTML helper function
function escapeHtml(text) {
    if (!text) return '';
    const div = document.createElement('div');
    div.textContent = String(text);
    return div.innerHTML;
}

// Load và render giỏ hàng từ localStorage
function loadCartFromStorage() {
    try {
        console.log('=== loadCartFromStorage START ===');
        
        const cartItemsEl = document.getElementById('cart-items');
        const emptyCartEl = document.getElementById('empty-cart');
        const cartContentEl = document.getElementById('cart-content');
        const cartSummaryEl = document.getElementById('cart-summary');
        
        console.log('Elements found:', {
            cartItems: !!cartItemsEl,
            emptyCart: !!emptyCartEl,
            cartContent: !!cartContentEl,
            cartSummary: !!cartSummaryEl
        });
        
        if (!cartItemsEl) {
            console.error('❌ cart-items element not found');
            return;
        }
        
        // Lấy guest_id từ localStorage hoặc Cookie
        let guestId = localStorage.getItem('guest_id') || getCookie('guest_id');
        console.log('Guest ID from storage:', guestId);
        
        // Nếu không có guest_id, tạo mới
        if (!guestId) {
            console.log('No guest ID found, creating new one...');
            if (window.getOrCreateGuestId) {
                guestId = window.getOrCreateGuestId();
            } else {
                // Fallback: tạo guest_id ngay tại đây
                const timestamp = Date.now();
                const randomStr = Math.random().toString(36).substring(2, 15);
                const randomStr2 = Math.random().toString(36).substring(2, 15);
                guestId = `guest_${timestamp}_${randomStr}${randomStr2}`;
                localStorage.setItem('guest_id', guestId);
                
                // Set cookie
                const date = new Date();
                date.setTime(date.getTime() + (365 * 24 * 60 * 60 * 1000));
                const expires = `expires=${date.toUTCString()}`;
                document.cookie = `guest_id=${guestId};${expires};path=/;SameSite=Lax`;
            }
        }
        
        const cartKey = 'cart_' + guestId;
        const productsKey = 'products_' + guestId;
        
        let cart = [];
        let products = {};
        
        try {
            const cartData = localStorage.getItem(cartKey);
            console.log('Cart data:', cartData);
            if (cartData) {
                cart = JSON.parse(cartData);
                console.log('Parsed cart:', cart);
            }
            
            const productsData = localStorage.getItem(productsKey);
            console.log('Products data:', productsData);
            if (productsData) {
                products = JSON.parse(productsData);
                console.log('Parsed products:', products);
            }
        } catch (e) {
            console.error('Lỗi khi đọc giỏ hàng:', e);
            cart = [];
            products = {};
        }
        
        if (!Array.isArray(cart) || cart.length === 0) {
            console.log('Cart is empty');
            
            // Hiển thị empty state
            if (emptyCartEl) {
                emptyCartEl.classList.remove('hidden');
                console.log('Showing empty cart message');
            }
            if (cartContentEl) {
                cartContentEl.classList.add('hidden');
                console.log('Hiding cart content');
            }
            
            // Update summary về 0
            if (typeof updateCartSummary === 'function') {
                updateCartSummary(0, 0);
            }
            
            return;
        }
        
        console.log('Cart has items:', cart.length);
        
        // Ẩn empty state, hiển thị cart content (chắc chắn display: block)
        if (emptyCartEl) {
            emptyCartEl.classList.add('hidden');
            emptyCartEl.style.display = 'none';
            console.log('Hiding empty cart message');
        }
        if (cartContentEl) {
            cartContentEl.classList.remove('hidden');
            cartContentEl.style.display = 'block';
            console.log('Showing cart content');
        }
        
        let html = '';
        let subtotal = 0;
        const appUrl = '<?php echo APP_URL; ?>';
        const imagesUrl = '<?php echo IMAGES_URL; ?>';
        
        console.log('Starting to render cart items...');
        console.log('App URL:', appUrl);
        console.log('Images URL:', imagesUrl);
        
        cart.forEach((item, index) => {
            try {
                const productId = parseInt(item.id);
                const product = products[productId] || {};
                const price = parseFloat(product.sale_price) || parseFloat(product.price) || 0;
                const quantity = parseInt(item.quantity) || 1;
                const itemTotal = price * quantity;
                subtotal += itemTotal;
                
                console.log(`Item ${index}:`, {
                    id: productId,
                    product: product,
                    price: price,
                    quantity: quantity,
                    total: itemTotal
                });
                
                const productName = escapeHtml(product.name || 'Sản phẩm #' + productId);
                const productSlug = escapeHtml(product.slug || '');
                const productImage = escapeHtml(product.image || imagesUrl + '/products/default.jpg');
                
                html += `
                    <div class="cart-item p-4 sm:p-6 border-b hover:bg-gray-50 transition" data-product-id="${productId}">
                        <div class="grid grid-cols-12 gap-3 sm:gap-4 items-center">
                            <div class="col-span-12 sm:col-span-6 flex gap-3 sm:gap-4 items-start">
                                <div class="relative shrink-0">
                                    <a href="${appUrl}/product.php?slug=${productSlug}">
                                        <img src="${productImage}" alt="${productName}" class="w-16 h-16 sm:w-20 sm:h-20 object-cover rounded-lg cursor-pointer hover:opacity-80 transition">
                                    </a>
                                    <button onclick="removeFromCart(${productId})" class="absolute -top-2 -right-2 w-6 h-6 bg-red-500 text-white rounded-full text-xs hover:bg-red-600 transition z-10"><i class="fas fa-times"></i></button>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <a href="${appUrl}/product.php?slug=${productSlug}" class="font-semibold text-gray-800 hover:text-rose-500 transition block">${productName}</a>
                                    <div class="text-sm text-gray-600 mt-1"><span class="text-green-600"><i class="fas fa-check-circle"></i> Còn hàng</span></div>
                                </div>
                            </div>
                            <div class="col-span-4 sm:col-span-2 text-center sm:text-left">
                                <span class="font-semibold text-rose-500 block">${formatCurrency(price)}</span>
                            </div>
                            <div class="col-span-4 sm:col-span-2 flex justify-center">
                                <div class="flex items-center border-2 border-gray-300 rounded-lg">
                                    <button onclick="updateCartQuantity(${productId}, ${quantity - 1})" class="w-8 h-8 flex items-center justify-center hover:bg-gray-100 transition"><i class="fas fa-minus text-sm"></i></button>
                                    <input type="number" value="${quantity}" min="1" class="w-12 text-center border-0 focus:outline-none font-semibold" onchange="updateCartQuantity(${productId}, this.value)">
                                    <button onclick="updateCartQuantity(${productId}, ${quantity + 1})" class="w-8 h-8 flex items-center justify-center hover:bg-gray-100 transition"><i class="fas fa-plus text-sm"></i></button>
                                </div>
                            </div>
                            <div class="col-span-4 sm:col-span-2 text-right">
                                <span class="font-bold text-lg text-gray-800">${formatCurrency(itemTotal)}</span>
                            </div>
                        </div>
                    </div>
                `;
            } catch (e) {
                console.error('Error processing item:', e, item);
            }
        });
        
        console.log('Setting HTML to cart-items...');
        console.log('HTML length:', html.length);
        if (!html || html.trim() === '') {
            console.warn('HTML rỗng sau render, fallback debug content');
            html = '<div class="p-6 text-center text-red-500 bg-red-50 rounded-lg">Không render được sản phẩm (HTML trống)</div>';
        }
        cartItemsEl.innerHTML = html;
        cartItemsEl.style.display = 'block';
        cartItemsEl.classList.remove('hidden');
        console.log('✅ HTML set successfully, children length:', cartItemsEl.children.length);
        
        // Nếu vì lý do gì đó vẫn không có child, dùng fallback đơn giản
        if (cartItemsEl.children.length === 0) {
            console.warn('Cart items không có child sau khi render. Dùng fallback render đơn giản.');
            const cartItems = cart.map(item => {
                const p = products[item.id] || {};
                const price = p.sale_price || p.price || 0;
                return `
                    <div class="p-4 border-b flex justify-between items-center">
                        <div class="flex items-center gap-3">
                            <img src="${escapeHtml(p.image || (imagesUrl + '/products/default.jpg'))}" alt="${escapeHtml(p.name || '')}" class="w-12 h-12 object-cover rounded">
                            <div>
                                <div class="font-semibold">${escapeHtml(p.name || 'Sản phẩm #' + item.id)}</div>
                                <div class="text-sm text-gray-500">SL: ${item.quantity}</div>
                            </div>
                        </div>
                        <div class="font-bold text-rose-500">${formatCurrency(price * item.quantity)}</div>
                    </div>
                `;
            }).join('');
            cartItemsEl.innerHTML = cartItems || '<div class="p-4 text-center text-gray-500">Giỏ hàng trống</div>';
            console.log('✅ Fallback render applied. Children length:', cartItemsEl.children.length);
        }
        
        // Đảm bảo container hiển thị
        if (cartContentEl) {
            cartContentEl.classList.remove('hidden');
            cartContentEl.style.display = 'block';
            console.log('✅ cart-content forced to show');
        }
        // Ép hiển thị cart-items container
        cartItemsEl.style.visibility = 'visible';
        cartItemsEl.style.opacity = '1';
        cartItemsEl.style.minHeight = '100px';
        
        console.log('Subtotal:', subtotal);
        if (typeof updateCartSummary === 'function') {
            updateCartSummary(subtotal, 0);
            console.log('✅ Summary updated');
        } else {
            console.error('❌ updateCartSummary function not found');
        }
        
        console.log('=== loadCartFromStorage END - SUCCESS ===');
    } catch (e) {
        console.error('=== loadCartFromStorage END - ERROR ===');
        console.error('❌ Error in loadCartFromStorage:', e);
        console.error('Stack:', e.stack);
        
        const cartItemsEl = document.getElementById('cart-items');
        if (cartItemsEl) {
            cartItemsEl.innerHTML = '<div class="p-6 text-center text-red-500">Có lỗi xảy ra khi tải giỏ hàng. Vui lòng thử lại.</div>';
        }
        
        // Show error in UI
        const emptyCartEl = document.getElementById('empty-cart');
        const cartContentEl = document.getElementById('cart-content');
        if (emptyCartEl) emptyCartEl.classList.add('hidden');
        if (cartContentEl) cartContentEl.classList.remove('hidden');
    }
}

function updateCartSummary(subtotal, discount = 0) {
    const shippingFee = subtotal >= 600000 ? 0 : 30000;
    const total = subtotal + shippingFee - discount;
    
    let html = `
        <div class="flex justify-between text-gray-700">
            <span>Tạm tính:</span>
            <span class="font-semibold">${formatCurrency(subtotal)}</span>
        </div>
        <div class="flex justify-between text-gray-700">
            <span>Phí vận chuyển:</span>
            <span class="font-semibold ${shippingFee == 0 ? 'text-green-600' : ''}">
                ${shippingFee == 0 ? 'Miễn phí' : formatCurrency(shippingFee)}
            </span>
        </div>
    `;
    
    if (subtotal < 600000) {
        html += `
            <div class="text-xs text-orange-600 bg-orange-50 p-2 rounded">
                <i class="fas fa-info-circle mr-1"></i>
                Mua thêm ${formatCurrency(600000 - subtotal)} để được miễn phí vận chuyển
            </div>
        `;
    }
    
    if (discount > 0) {
        html += `
            <div class="flex justify-between text-green-600">
                <span>Giảm giá:</span>
                <span class="font-semibold">-${formatCurrency(discount)}</span>
            </div>
        `;
    }
    
    html += `
        <div class="border-t pt-3">
            <div class="flex justify-between items-center">
                <span class="text-lg font-semibold">Tổng cộng:</span>
                <span class="text-2xl font-bold text-rose-500">
                    ${formatCurrency(total)}
                </span>
            </div>
        </div>
    `;
    
    document.getElementById('cart-summary').innerHTML = html;
}

// Helper: Get cookie
function getCookie(name) {
    const value = `; ${document.cookie}`;
    const parts = value.split(`; ${name}=`);
    if (parts.length === 2) return parts.pop().split(';').shift();
    return null;
}

// Debug function - gọi từ console để test
window.debugCart = function() {
    console.log('=== DEBUG CART ===');
    
    const guestId = localStorage.getItem('guest_id') || getCookie('guest_id');
    console.log('Guest ID:', guestId);
    
    if (!guestId) {
        console.error('No guest ID found!');
        return;
    }
    
    const cartKey = 'cart_' + guestId;
    const productsKey = 'products_' + guestId;
    
    const cartData = localStorage.getItem(cartKey);
    const productsData = localStorage.getItem(productsKey);
    
    console.log('Cart Key:', cartKey);
    console.log('Cart Data (raw):', cartData);
    
    console.log('Products Key:', productsKey);
    console.log('Products Data (raw):', productsData);
    
    try {
        const cart = cartData ? JSON.parse(cartData) : [];
        const products = productsData ? JSON.parse(productsData) : {};
        
        console.log('Cart (parsed):', cart);
        console.log('Products (parsed):', products);
        
        console.log('Cart length:', cart.length);
        console.log('Products count:', Object.keys(products).length);
        
        // Test getCartItems if available
        if (window.getCartItems) {
            console.log('getCartItems():', window.getCartItems());
        }
    } catch (e) {
        console.error('Error parsing data:', e);
    }
    
    console.log('=== END DEBUG ===');
};

// Override removeFromCart để reload giỏ hàng
window.removeFromCart = function(productId) {
    console.log('Removing product from cart:', productId);
    
    try {
        productId = parseInt(productId);
        if (isNaN(productId)) {
            console.error('Product ID không hợp lệ');
            if (window.showToast) showToast('Có lỗi xảy ra!', 'error');
            return false;
        }
        
        const guestId = localStorage.getItem('guest_id') || getCookie('guest_id');
        if (!guestId) {
            console.error('No guest ID');
            if (window.showToast) showToast('Không tìm thấy giỏ hàng!', 'error');
            return false;
        }
        
        const cartKey = `cart_${guestId}`;
        let cart = [];
        
        try {
            const cartData = localStorage.getItem(cartKey);
            if (cartData) {
                cart = JSON.parse(cartData);
            }
        } catch (e) {
            console.error('Error reading cart:', e);
            if (window.showToast) showToast('Có lỗi khi đọc giỏ hàng!', 'error');
            return false;
        }
        
        // Xóa sản phẩm khỏi giỏ hàng
        cart = cart.filter(item => parseInt(item.id) !== productId);
        
        // Lưu lại
        localStorage.setItem(cartKey, JSON.stringify(cart));
        localStorage.setItem(`${cartKey}_updated`, new Date().toISOString());
        
        // Cập nhật số lượng trong header
        if (window.updateCartCount) {
            window.updateCartCount();
        }
        
        // Reload giỏ hàng
        loadCartFromStorage();
        
        // Hiển thị thông báo
        if (window.showToast) {
            window.showToast('Đã xóa sản phẩm khỏi giỏ hàng!', 'warning');
        }
        
        return true;
    } catch (error) {
        console.error('Lỗi trong removeFromCart:', error);
        if (window.showToast) showToast('Có lỗi xảy ra!', 'error');
        return false;
    }
};

// Override updateCartQuantity để reload giỏ hàng
window.updateCartQuantity = function(productId, quantity) {
    console.log('Updating cart quantity:', productId, quantity);
    
    try {
        productId = parseInt(productId);
        quantity = parseInt(quantity);
        
        if (isNaN(productId) || isNaN(quantity)) {
            console.error('Dữ liệu không hợp lệ');
            return false;
        }
        
        if (quantity < 1) {
            window.removeFromCart(productId);
            return;
        }
        
        const guestId = localStorage.getItem('guest_id') || getCookie('guest_id');
        if (!guestId) {
            console.error('No guest ID');
            if (window.showToast) showToast('Không tìm thấy giỏ hàng!', 'error');
            return false;
        }
        
        const cartKey = `cart_${guestId}`;
        let cart = [];
        
        try {
            const cartData = localStorage.getItem(cartKey);
            if (cartData) {
                cart = JSON.parse(cartData);
            }
        } catch (e) {
            console.error('Error reading cart:', e);
            if (window.showToast) showToast('Có lỗi khi đọc giỏ hàng!', 'error');
            return false;
        }
        
        // Tìm và cập nhật số lượng
        const item = cart.find(item => parseInt(item.id) === productId);
        if (item) {
            item.quantity = quantity;
            item.updatedAt = new Date().toISOString();
        } else {
            console.error('Không tìm thấy sản phẩm trong giỏ hàng');
            return false;
        }
        
        // Lưu lại
        localStorage.setItem(cartKey, JSON.stringify(cart));
        localStorage.setItem(`${cartKey}_updated`, new Date().toISOString());
        
        // Cập nhật số lượng trong header
        if (window.updateCartCount) {
            window.updateCartCount();
        }
        
        // Reload giỏ hàng
        loadCartFromStorage();
        
        return true;
    } catch (error) {
        console.error('Lỗi trong updateCartQuantity:', error);
        if (window.showToast) showToast('Có lỗi xảy ra!', 'error');
        return false;
    }
};

// Override clearCart để reload giỏ hàng
window.clearCart = function() {
    console.log('Clearing cart');
    
    try {
        if (!confirm('Bạn có chắc muốn xóa tất cả sản phẩm khỏi giỏ hàng?')) {
            return false;
        }
        
        const guestId = localStorage.getItem('guest_id') || getCookie('guest_id');
        if (!guestId) {
            console.error('No guest ID');
            if (window.showToast) showToast('Không tìm thấy giỏ hàng!', 'error');
            return false;
        }
        
        const cartKey = `cart_${guestId}`;
        const productsKey = `products_${guestId}`;
        
        // Xóa giỏ hàng nhưng giữ thông tin sản phẩm để dùng lại
        localStorage.setItem(cartKey, JSON.stringify([]));
        localStorage.setItem(`${cartKey}_updated`, new Date().toISOString());
        
        // Cập nhật số lượng trong header
        if (window.updateCartCount) {
            window.updateCartCount();
        }
        
        // Reload giỏ hàng
        loadCartFromStorage();
        
        if (window.showToast) {
            showToast('Đã xóa tất cả sản phẩm!', 'warning');
        }
        
        return true;
    } catch (error) {
        console.error('Lỗi trong clearCart:', error);
        if (window.showToast) showToast('Có lỗi xảy ra!', 'error');
        return false;
    }
};

// Load giỏ hàng khi trang được tải
// Đảm bảo chạy sau khi DOM và main.js đã load
(function() {
    console.log('=== Cart page script initialized ===');
    console.log('Document ready state:', document.readyState);
    
    let cartLoaded = false;
    let attempts = 0;
    const maxAttempts = 10;
    let forced = false;
    
    function initCartPage() {
        attempts++;
        console.log(`[Attempt ${attempts}/${maxAttempts}] Trying to init cart page...`);
        
        if (cartLoaded) {
            console.log('Cart already loaded, skipping...');
            return;
        }
        
        // Kiểm tra xem các element đã tồn tại chưa
        const cartItemsEl = document.getElementById('cart-items');
        const emptyCartEl = document.getElementById('empty-cart');
        const cartContentEl = document.getElementById('cart-content');
        
        // Force remove hidden ngay lập tức
        if (cartContentEl) {
            cartContentEl.classList.remove('hidden');
            cartContentEl.style.display = 'block';
        }
        
        console.log('Elements check:', {
            cartItems: !!cartItemsEl,
            emptyCart: !!emptyCartEl,
            cartContent: !!cartContentEl
        });
        
        if (!cartItemsEl) {
            console.warn('cart-items element not found yet');
            
            if (attempts < maxAttempts) {
                console.log('Will retry in 500ms...');
                setTimeout(initCartPage, 500);
            } else {
                console.error('Max attempts reached, giving up');
            }
            return;
        }
        
        console.log('✅ All elements found, loading cart...');
        cartLoaded = true;
        
        // Gọi hàm load giỏ hàng
        try {
            loadCartFromStorage();
            console.log('✅ Cart loaded successfully');
        } catch (error) {
            console.error('❌ Error loading cart:', error);
        }
    }
    
    // Strategy 1: Nếu DOM đã ready, chạy ngay
    if (document.readyState === 'complete' || document.readyState === 'interactive') {
        console.log('Document already ready, loading cart...');
        setTimeout(initCartPage, 100);
    }
    
    // Strategy 2: Chờ DOMContentLoaded
    if (document.readyState === 'loading') {
        console.log('Waiting for DOMContentLoaded...');
        document.addEventListener('DOMContentLoaded', function() {
            console.log('DOMContentLoaded fired');
            setTimeout(initCartPage, 200);
        });
    }
    
    // Strategy 3: Backup - chờ window load hoàn toàn
    window.addEventListener('load', function() {
        console.log('Window load complete');
        setTimeout(function() {
            if (!cartLoaded) {
                console.log('Cart not loaded yet, trying again...');
                initCartPage();
            }
        }, 300);
    });
})();

/* Tạm thời tắt chức năng mã giảm giá và thanh toán
function applyCoupon() {
    const code = document.getElementById('coupon-code').value;
    if (code.toLowerCase() === 'uudai5') {
        const guestId = localStorage.getItem('guest_id');
        const cartKey = `cart_${guestId}`;
        const productsKey = `products_${guestId}`;
        
        let cart = [];
        let products = {};
        
        try {
            const cartData = localStorage.getItem(cartKey);
            if (cartData) cart = JSON.parse(cartData);
            const productsData = localStorage.getItem(productsKey);
            if (productsData) products = JSON.parse(productsData);
        } catch (e) {}
        
        let subtotal = 0;
        cart.forEach(item => {
            const product = products[item.id] || {};
            const price = product.sale_price || product.price || 0;
            subtotal += price * item.quantity;
        });
        
        const discount = subtotal * 0.05;
        updateCartSummary(subtotal, discount);
        showToast('Áp dụng mã giảm giá thành công! Giảm 5%', 'success');
    } else {
        showToast('Mã giảm giá không hợp lệ!', 'error');
    }
}

function checkout() {
    showToast('Chức năng thanh toán đang được phát triển!', 'warning');
    // window.location.href = '<?php echo APP_URL; ?>/checkout.php';
}
*/
</script>

<?php include '../includes/footer.php'; ?>

