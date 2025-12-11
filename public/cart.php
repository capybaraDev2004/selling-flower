<?php
require_once '../config/config.php';

$page_title = 'Giỏ hàng - ' . APP_NAME;
$page_description = 'Giỏ hàng của bạn';

// Giỏ hàng sẽ được load từ localStorage bằng JavaScript
// Không cần xử lý PHP vì hoàn toàn dùng localStorage

include '../includes/header.php';
?>

<main class="page-main w-full">
<!-- Breadcrumb -->
    <div class="py-1" style="background: linear-gradient(90deg, #f43f5e 0%, #e11d48 100%);">
    <div class="container mx-auto px-4">
            <div class="breadcrumb text-white font-semibold">
                <a href="<?php echo APP_URL; ?>" class="text-white hover:text-white">
                    <i class="fas fa-home"></i> Trang chủ
                </a>
                <span class="separator px-2">/</span>
                <span class="text-white">Giỏ hàng</span>
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
                <div class="cart-list-wrapper">
                    <div class="bg-white rounded-xl shadow-lg overflow-hidden border border-gray-100 cart-card">
                <!-- Cart Items -->
                        <div id="cart-items" class="cart-grid p-4 sm:p-6">
                            <!-- Sẽ được load từ localStorage bằng JavaScript -->
                        </div>

                        <!-- Cart Actions -->
                        <div class="p-6 bg-gray-50 border-t border-gray-100 space-y-4">
                            <div class="flex justify-between items-center flex-wrap gap-3">
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
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                <a href="https://zalo.me/0389932688" target="_blank"
                                   class="text-center bg-blue-500 text-white py-3 rounded-xl font-semibold text-base hover:shadow-lg transition-all hover:-translate-y-0.5">
                                    <i class="fas fa-bolt mr-2"></i> Đặt nhanh qua Zalo
                                </a>
                                <a href="tel:0389932688"
                                   class="text-center bg-green-500 text-white py-3 rounded-xl font-semibold text-base hover:shadow-lg transition-all hover:-translate-y-0.5">
                                    <i class="fas fa-phone-alt mr-2"></i> Gọi đặt nhanh
                                </a>
                                <a href="<?php echo APP_URL; ?>/checkout.php"
                                   class="text-center bg-gradient-to-r from-rose-500 to-rose-600 text-white py-3 rounded-xl font-semibold text-base hover:shadow-lg transition-all hover:-translate-y-0.5">
                                    <i class="fas fa-check-circle mr-2"></i> Đặt đơn hàng
                                </a>
                            </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
</main>

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

// Load và render giỏ hàng từ localStorage (tên riêng để không đè lên main.js)
function renderCartPageFromStorage() {
    try {
        console.log('=== renderCartPageFromStorage START ===');
        
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
            
            // Ẩn block empty cũ, hiển thị cart-content với thông báo thân thiện
            if (emptyCartEl) {
                emptyCartEl.classList.add('hidden');
            }
            if (cartContentEl) {
                cartContentEl.classList.remove('hidden');
                cartContentEl.style.display = 'block';
            }
            if (cartItemsEl) {
                cartItemsEl.innerHTML = `
                    <div class="p-6 text-center text-gray-700 bg-white rounded-xl border border-gray-200 shadow-sm">
                        <p class="font-semibold text-lg mb-2">Chưa có sản phẩm nào trong giỏ hàng</p>
                        <p class="text-sm text-gray-500 mb-4">Tiếp tục mua sắm để thêm sản phẩm vào giỏ.</p>
                        <a href="<?php echo APP_URL; ?>/shop.php" class="inline-block bg-gradient-to-r from-rose-500 to-pink-500 text-white px-6 py-3 rounded-full font-semibold hover:shadow-lg transition">
                            Tiếp tục mua sắm
                        </a>
                    </div>
                `;
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
                
                // Lấy dữ liệu từ database - chấp nhận cả giá trị 0 nếu đó là giá trị thực
                let rating = null;
                if (product.rating !== undefined && product.rating !== null) {
                    rating = parseFloat(product.rating);
                } else if (product.rating_avg !== undefined && product.rating_avg !== null) {
                    rating = parseFloat(product.rating_avg);
                }
                
                let reviewsCount = null;
                if (product.reviews !== undefined && product.reviews !== null) {
                    reviewsCount = parseInt(product.reviews);
                } else if (product.reviews_count !== undefined && product.reviews_count !== null) {
                    reviewsCount = parseInt(product.reviews_count);
                } else if (product.rating_count !== undefined && product.rating_count !== null) {
                    reviewsCount = parseInt(product.rating_count);
                }
                
                let sold = null;
                if (product.sold !== undefined && product.sold !== null) {
                    sold = parseInt(product.sold);
                } else if (product.purchases !== undefined && product.purchases !== null) {
                    sold = parseInt(product.purchases);
                } else if (product.sold_count !== undefined && product.sold_count !== null) {
                    sold = parseInt(product.sold_count);
                }
                
                console.log(`Product ${productId} data:`, {
                    rating: rating,
                    reviewsCount: reviewsCount,
                    sold: sold,
                    fullProduct: product
                });
                
                const originalPrice = parseFloat(product.price) || 0;
                const salePrice = product.sale_price ? parseFloat(product.sale_price) : null;
                const hasSale = salePrice !== null && salePrice > 0 && salePrice < originalPrice;
                const displayPrice = hasSale ? salePrice : originalPrice;
                const displayOriginalPrice = hasSale ? originalPrice : null;
                
                // Tạo stars HTML với Font Awesome - hiển thị nếu có rating (kể cả 0)
                const hasRating = rating !== null && !isNaN(rating);
                const starsHtml = hasRating ? Array.from({length: 5}).map((_, i) => {
                    return i < Math.round(rating) 
                        ? '<i class="fas fa-star text-yellow-400"></i>' 
                        : '<i class="far fa-star text-yellow-400"></i>';
                }).join('') : '';
                
                html += `
                    <div class="cart-item cart-item-row bg-white rounded-xl border border-gray-200 shadow-sm hover:shadow-md transition-all duration-300 overflow-hidden" data-product-id="${productId}">
                        <div class="flex flex-col h-full">
                            <!-- Hình ảnh ở hàng 1 -->
                            <div class="relative w-full overflow-hidden bg-gray-50">
                                <a href="${appUrl}/product.php?slug=${productSlug}" class="block relative group">
                                    <img src="${productImage}" alt="${productName}" class="w-full h-48 sm:h-56 lg:h-64 xl:h-72 object-cover transition-transform duration-300 group-hover:scale-105">
                                    <div class="absolute inset-0 bg-black opacity-0 group-hover:opacity-10 transition-opacity duration-300"></div>
                                </a>
                                <button onclick="removeFromCart(${productId})" class="absolute top-3 right-3 w-8 h-8 bg-red-500 hover:bg-red-600 text-white rounded-full text-sm transition-all duration-200 z-10 flex items-center justify-center shadow-lg hover:shadow-xl hover:scale-110">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                            <!-- Thông tin sản phẩm ở hàng 2 -->
                            <div class="flex flex-col p-4 sm:p-5 lg:p-6 flex-1">
                                <!-- Tên sản phẩm -->
                                <a href="${appUrl}/product.php?slug=${productSlug}" class="font-bold text-gray-900 hover:text-rose-500 transition-colors duration-200 block text-lg sm:text-xl lg:text-2xl leading-tight mb-3 flex items-start">
                                    ${productName}
                                </a>
                                
                                ${hasRating ? `
                                <!-- Rating và Reviews - hiển thị nếu có dữ liệu (kể cả 0) -->
                                <div class="flex items-center gap-2 mb-2">
                                    <div class="flex items-center gap-1 text-yellow-400 text-sm lg:text-base">
                                        ${starsHtml}
                                    </div>
                                    <span class="text-sm lg:text-base text-gray-900 font-medium">${rating.toFixed(1)}</span>
                                    ${reviewsCount !== null && !isNaN(reviewsCount) ? `<span class="text-sm lg:text-base text-gray-500">(${reviewsCount} đánh giá)</span>` : ''}
                                </div>
                                ` : ''}
                                
                                ${sold !== null && !isNaN(sold) ? `
                                <!-- Đã bán - hiển thị nếu có dữ liệu (kể cả 0) -->
                                <div class="flex items-center gap-2 mb-3">
                                    <i class="fas fa-check-circle text-green-600"></i>
                                    <span class="text-sm lg:text-base text-gray-900">Đã bán ${sold}</span>
                                </div>
                                ` : ''}
                                
                                <!-- Giá - cùng một dòng -->
                                <div class="flex items-center gap-3">
                                    <span class="text-rose-500 font-bold text-xl lg:text-2xl">${formatCurrency(displayPrice)}</span>
                                    ${hasSale && displayOriginalPrice ? `<span class="text-sm lg:text-base text-gray-400 line-through">${formatCurrency(displayOriginalPrice)}</span>` : ''}
                                </div>

                                <!-- Số lượng & thành tiền -->
                                <div class="mt-4 pt-4 border-t border-gray-200 border-dashed flex items-center justify-center gap-6 flex-wrap text-center">
                                    <div class="flex items-center gap-2">
                                        <button class="w-9 h-9 flex items-center justify-center rounded-lg border border-gray-200 hover:border-rose-400 hover:text-rose-500 transition"
                                                onclick="updateCartQuantity(${productId}, ${quantity} - 1)">
                                            <i class="fas fa-minus"></i>
                                        </button>
                                        <input type="number" min="1" value="${quantity}"
                                               class="w-16 text-center rounded-lg border border-gray-200 focus:border-rose-500 focus:ring-1 focus:ring-rose-400 py-2"
                                               onchange="updateCartQuantity(${productId}, this.value)">
                                        <button class="w-9 h-9 flex items-center justify-center rounded-lg border border-gray-200 hover:border-rose-400 hover:text-rose-500 transition"
                                                onclick="updateCartQuantity(${productId}, ${quantity} + 1)">
                                            <i class="fas fa-plus"></i>
                                        </button>
                                    </div>
                                    <div class="text-center">
                                        <div class="text-base font-semibold text-gray-700">Thành tiền</div>
                                        <div class="text-2xl font-extrabold text-rose-600">${formatCurrency(itemTotal)}</div>
                                    </div>
                                </div>
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
                const qty = parseInt(item.quantity) || 1;
                const lineTotal = price * qty;
                return `
                    <div class="p-4 border-b">
                        <div class="flex justify-between items-center gap-3">
                            <div class="flex items-center gap-3">
                                <img src="${escapeHtml(p.image || (imagesUrl + '/products/default.jpg'))}" alt="${escapeHtml(p.name || '')}" class="w-12 h-12 object-cover rounded">
                                <div class="min-h-[44px] flex items-start flex-col justify-start">
                                    <div class="font-semibold leading-snug">${escapeHtml(p.name || 'Sản phẩm #' + item.id)}</div>
                                    <div class="text-sm text-gray-500">Giá: ${formatCurrency(price)}</div>
                                </div>
                            </div>
                            <div class="text-right">
                                <div class="text-sm text-gray-500 mb-1">Số lượng</div>
                                <div class="flex items-center gap-2 justify-end">
                                    <button class="w-8 h-8 flex items-center justify-center rounded border border-gray-200"
                                            onclick="updateCartQuantity(${parseInt(item.id)}, ${qty} - 1)">-</button>
                                    <input type="number" min="1" value="${qty}" class="w-14 text-center rounded border border-gray-200"
                                           onchange="updateCartQuantity(${parseInt(item.id)}, this.value)">
                                    <button class="w-8 h-8 flex items-center justify-center rounded border border-gray-200"
                                            onclick="updateCartQuantity(${parseInt(item.id)}, ${qty} + 1)">+</button>
                                </div>
                                <div class="mt-2 font-bold text-rose-500">${formatCurrency(lineTotal)}</div>
                            </div>
                        </div>
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
        
        console.log('=== renderCartPageFromStorage END - SUCCESS ===');
    } catch (e) {
        console.error('=== renderCartPageFromStorage END - ERROR ===');
        console.error('❌ Error in renderCartPageFromStorage:', e);
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

// Override removeFromCart để reload giỏ hàng - Đảm bảo override sau khi main.js load
(function() {
    function overrideRemoveFromCart() {
window.removeFromCart = function(productId) {
            console.log('[Cart Page] Removing product from cart:', productId);
            
            try {
    productId = parseInt(productId);
                if (isNaN(productId)) {
                    console.error('Product ID không hợp lệ');
                    if (window.showToast) window.showToast('Có lỗi xảy ra!', 'error');
                    return false;
                }
    
                const guestId = localStorage.getItem('guest_id') || getCookie('guest_id');
    if (!guestId) {
        console.error('No guest ID');
                    if (window.showToast) window.showToast('Không tìm thấy giỏ hàng!', 'error');
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
                    if (window.showToast) window.showToast('Có lỗi khi đọc giỏ hàng!', 'error');
                    return false;
    }
    
    // Xóa sản phẩm khỏi giỏ hàng
                const initialLength = cart.length;
    cart = cart.filter(item => parseInt(item.id) !== productId);
            
                if (cart.length === initialLength) {
                    console.warn('Product not found in cart:', productId);
                    if (window.showToast) window.showToast('Không tìm thấy sản phẩm trong giỏ hàng!', 'error');
                    return false;
                }
    
    // Lưu lại
    localStorage.setItem(cartKey, JSON.stringify(cart));
                localStorage.setItem(`${cartKey}_updated`, new Date().toISOString());
            
                console.log('[Cart Page] Cart updated, new length:', cart.length);
    
    // Cập nhật số lượng trong header
    if (window.updateCartCount) {
        window.updateCartCount();
    }
    
                // Lưu flag vào sessionStorage để hiển thị thông báo sau khi reload
                sessionStorage.setItem('cart_item_removed', 'true');
    
                // Reload trang ngay lập tức
                console.log('[Cart Page] Reloading page after removal...');
                window.location.reload();
                
                return true;
            } catch (error) {
                console.error('Lỗi trong removeFromCart:', error);
                if (window.showToast) window.showToast('Có lỗi xảy ra!', 'error');
                return false;
            }
        };
        console.log('[Cart Page] removeFromCart function overridden successfully');
    }
    
    // Override ngay lập tức
    overrideRemoveFromCart();
    
    // Override lại sau khi window load để đảm bảo override được hàm từ main.js
    if (window.addEventListener) {
        window.addEventListener('load', function() {
            setTimeout(overrideRemoveFromCart, 100);
        });
    }
    
    // Override lại sau khi DOM ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', function() {
            setTimeout(overrideRemoveFromCart, 200);
        });
    } else {
        setTimeout(overrideRemoveFromCart, 100);
    }
})();

// Override updateCartQuantity để luôn reload trang sau cập nhật
(function() {
    function setUpdateCartQuantity() {
        window.updateCartQuantity = function(productId, quantity) {
            console.log('Updating cart quantity (force reload):', productId, quantity);
            
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
                
                // Lưu flag để hiển thị thông báo sau khi reload
                sessionStorage.setItem('cart_qty_updated', 'true');
                
                // Reload giỏ hàng để đồng bộ UI
                window.location.reload();
                
                return true;
            } catch (error) {
                console.error('Lỗi trong updateCartQuantity:', error);
                if (window.showToast) showToast('Có lỗi xảy ra!', 'error');
                return false;
            }
        };
    }
    
    // Override ngay lập tức
    setUpdateCartQuantity();
    // Override lại sau khi main.js load/DOM ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', () => setTimeout(setUpdateCartQuantity, 50));
    } else {
        setTimeout(setUpdateCartQuantity, 50);
    }
    window.addEventListener('load', () => setTimeout(setUpdateCartQuantity, 100));
})();

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
        renderCartPageFromStorage();
        
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
        
        // Hiển thị thông báo các hành động sau reload
        const messageFlags = [
            { key: 'cart_item_removed', text: 'Đã xóa sản phẩm khỏi giỏ hàng thành công!', type: 'success' },
            { key: 'cart_qty_updated', text: 'Đã cập nhật số lượng sản phẩm!', type: 'success' }
        ];
        messageFlags.forEach(flag => {
            if (sessionStorage.getItem(flag.key) === 'true') {
                sessionStorage.removeItem(flag.key);
                setTimeout(function() {
                    if (window.showToast) {
                        window.showToast(flag.text, flag.type, { closable: true });
                    } else {
                        // Fallback với nút đóng
                        const banner = document.createElement('div');
                        banner.className = 'fixed top-4 right-4 z-50 bg-green-500 text-white px-4 py-3 rounded-lg shadow-lg flex items-start gap-3';
                        banner.innerHTML = `
                            <div class="font-semibold">${flag.text}</div>
                            <button aria-label="Đóng" class="ml-2 text-white font-bold">✕</button>
                        `;
                        document.body.appendChild(banner);
                        const closeBtn = banner.querySelector('button');
                        closeBtn.addEventListener('click', () => banner.remove());
                        setTimeout(() => banner.remove(), 3500);
                    }
                }, 500);
            }
        });
        
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
            renderCartPageFromStorage();
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


