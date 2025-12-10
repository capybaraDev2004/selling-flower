<?php
require_once '../config/config.php';

$page_title = 'Đặt hàng - ' . APP_NAME;
$page_description = 'Xác nhận thông tin đơn hàng';

include '../includes/header.php';
?>

<main class="page-main w-full bg-gray-50 min-h-screen">
    <!-- Breadcrumb -->
    <div class="py-4" style="background: linear-gradient(90deg, #f43f5e 0%, #e11d48 100%);">
        <div class="container mx-auto px-4">
            <div class="breadcrumb text-white font-semibold">
                <a href="<?php echo APP_URL; ?>" class="text-white hover:text-white">
                    <i class="fas fa-home"></i> Trang chủ
                </a>
                <span class="separator px-2">/</span>
                <a href="<?php echo APP_URL; ?>/cart.php" class="text-white hover:text-white">
                    Giỏ hàng
                </a>
                <span class="separator px-2">/</span>
                <span class="text-white">Đặt hàng</span>
            </div>
        </div>
    </div>

    <section class="py-10">
        <div class="container mx-auto px-4">
            <div class="flex justify-between items-center mb-6">
                <div>
                    <p class="text-sm text-gray-500 mb-1 uppercase tracking-wide">Bước 2</p>
                    <h1 class="text-3xl font-bold text-gray-900">Xác nhận đơn hàng</h1>
                    <p class="text-gray-600 mt-1">Kiểm tra sản phẩm, nhập thông tin nhận hàng và phương thức thanh toán.</p>
                </div>
                <a href="<?php echo APP_URL; ?>/cart.php" class="text-rose-500 hover:text-rose-600 font-semibold flex items-center gap-2">
                    <i class="fas fa-arrow-left"></i> Quay lại giỏ hàng
                </a>
            </div>

            <!-- Thông tin nhận hàng -->
            <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6 mb-8">
                <div class="flex items-center gap-3 mb-4">
                    <div class="w-10 h-10 rounded-full bg-rose-100 text-rose-600 flex items-center justify-center font-bold">1</div>
                    <div>
                        <h2 class="text-xl font-bold text-gray-900">Thông tin người nhận</h2>
                        <p class="text-sm text-gray-500">Điền thông tin để chúng tôi liên hệ giao hàng.</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div>
                        <label class="block text-sm font-semibold text-gray-800 mb-2">Họ tên *</label>
                        <input type="text" id="fullname" class="w-full rounded-xl border border-gray-200 px-4 py-3 focus:ring-2 focus:ring-rose-500 focus:border-rose-500 transition bg-gray-50" placeholder="VD: Nguyễn Văn A">
                        <p class="text-sm text-red-500 mt-2 hidden" id="error-fullname"></p>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-800 mb-2">Số điện thoại *</label>
                        <input type="tel" id="phone" class="w-full rounded-xl border border-gray-200 px-4 py-3 focus:ring-2 focus:ring-rose-500 focus:border-rose-500 transition bg-gray-50" placeholder="VD: 0912345678">
                        <p class="text-sm text-red-500 mt-2 hidden" id="error-phone"></p>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-800 mb-2">Email *</label>
                        <input type="email" id="email" class="w-full rounded-xl border border-gray-200 px-4 py-3 focus:ring-2 focus:ring-rose-500 focus:border-rose-500 transition bg-gray-50" placeholder="VD: ban@vidu.com">
                        <p class="text-sm text-red-500 mt-2 hidden" id="error-email"></p>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-800 mb-2">Địa chỉ nhận hàng *</label>
                        <input type="text" id="address" class="w-full rounded-xl border border-gray-200 px-4 py-3 focus:ring-2 focus:ring-rose-500 focus:border-rose-500 transition bg-gray-50" placeholder="Số nhà, đường, phường/xã, quận/huyện, tỉnh/thành">
                        <p class="text-sm text-red-500 mt-2 hidden" id="error-address"></p>
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-sm font-semibold text-gray-800 mb-2">Ghi chú (tùy chọn)</label>
                        <textarea id="note" rows="3" class="w-full rounded-xl border border-gray-200 px-4 py-3 focus:ring-2 focus:ring-rose-500 focus:border-rose-500 transition bg-gray-50" placeholder="Ví dụ: Giao trong giờ hành chính, gọi trước khi giao..."></textarea>
                    </div>
                </div>
            </div>

            <!-- Sản phẩm & tổng tiền -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Danh sách sản phẩm -->
                <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6">
                    <div class="flex items-center gap-3 mb-4">
                        <div class="w-10 h-10 rounded-full bg-emerald-100 text-emerald-600 flex items-center justify-center font-bold">2</div>
                        <div>
                            <h2 class="text-xl font-bold text-gray-900">Sản phẩm trong đơn</h2>
                            <p class="text-sm text-gray-500">Mỗi sản phẩm hiển thị trên một hàng để bạn dễ kiểm tra.</p>
                        </div>
                    </div>
                    <div id="order-items" class="space-y-4">
                        <!-- Render bằng JavaScript -->
                    </div>
                    <div id="empty-order" class="hidden text-center py-10">
                        <i class="fas fa-shopping-bag text-4xl text-gray-300 mb-3"></i>
                        <p class="text-lg font-semibold text-gray-700 mb-2">Chưa có sản phẩm trong giỏ.</p>
                        <a href="<?php echo APP_URL; ?>/shop.php" class="inline-block bg-gradient-to-r from-rose-500 to-pink-500 text-white px-6 py-3 rounded-full font-semibold hover:shadow-lg transition">Tiếp tục mua sắm</a>
                    </div>
                </div>

                <!-- Tổng tiền & thanh toán -->
                <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6">
                    <div class="flex items-center gap-3 mb-4">
                        <div class="w-10 h-10 rounded-full bg-indigo-100 text-indigo-600 flex items-center justify-center font-bold">3</div>
                        <div>
                            <h2 class="text-xl font-bold text-gray-900">Tổng quan đơn hàng</h2>
                            <p class="text-sm text-gray-500">Kiểm tra tổng tiền và chọn phương thức thanh toán.</p>
                        </div>
                    </div>

                    <div class="border border-gray-100 rounded-xl overflow-hidden">
                        <div class="divide-y divide-gray-100">
                            <div class="flex items-center justify-between px-4 py-3 bg-gray-50">
                                <span class="text-gray-700 font-semibold">Tạm tính</span>
                                <span class="text-gray-900 font-bold" id="order-subtotal">0 ₫</span>
                            </div>
                            <div class="flex items-center justify-between px-4 py-3">
                                <span class="text-gray-700 font-semibold">Phí giao hàng</span>
                                <span class="text-emerald-600 font-semibold">Miễn phí</span>
                            </div>
                            <div class="flex items-center justify-between px-4 py-4 bg-gray-50">
                                <span class="text-lg font-bold text-gray-900">Tổng thanh toán</span>
                                <span class="text-2xl font-extrabold text-rose-600" id="order-total">0 ₫</span>
                            </div>
                        </div>
                    </div>

                    <div class="mt-6">
                        <h3 class="text-sm font-semibold text-gray-800 mb-3 uppercase tracking-wide">Phương thức thanh toán *</h3>
                        <div class="space-y-3">
                            <label class="flex items-center gap-3 p-4 border border-gray-200 rounded-xl cursor-pointer hover:border-rose-500 transition">
                                <input type="radio" name="payment-method" value="cod" class="text-rose-500 focus:ring-rose-500">
                                <div>
                                    <p class="font-semibold text-gray-900">Thanh toán khi nhận hàng (COD)</p>
                                    <p class="text-sm text-gray-500">Nhận hàng, kiểm tra rồi thanh toán.</p>
                                </div>
                            </label>
                            <label class="flex items-center gap-3 p-4 border border-gray-200 rounded-xl cursor-pointer hover:border-rose-500 transition">
                                <input type="radio" name="payment-method" value="bankplus" class="text-rose-500 focus:ring-rose-500">
                                <div>
                                    <p class="font-semibold text-gray-900">Chuyển khoản qua BankPlus</p>
                                    <p class="text-sm text-gray-500">Thanh toán nhanh qua BankPlus, xác nhận tự động.</p>
                                </div>
                            </label>
                        </div>
                        <p class="text-sm text-red-500 mt-2 hidden" id="error-payment"></p>
                    </div>

                    <div class="mt-6">
                        <button id="place-order-btn" class="w-full bg-gradient-to-r from-rose-500 to-rose-600 text-white font-semibold py-4 rounded-xl text-lg hover:shadow-lg transition transform hover:-translate-y-0.5 flex items-center justify-center gap-2">
                            <i class="fas fa-check-circle"></i> Đặt hàng
                        </button>
                        <p id="submit-feedback" class="text-sm text-emerald-600 mt-3 hidden">Thông tin hợp lệ. Chức năng đặt hàng sẽ được kích hoạt sau khi tích hợp backend.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>
</main>

<script>
// Utility
function formatCurrency(amount) {
    const safeAmount = isNaN(amount) || amount === null ? 0 : Number(amount);
    return new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(safeAmount);
}

function getCookie(name) {
    const value = `; ${document.cookie}`;
    const parts = value.split(`; ${name}=`);
    if (parts.length === 2) return parts.pop().split(';').shift();
    return null;
}

function getGuestId() {
    let guestId = localStorage.getItem('guest_id') || getCookie('guest_id');
    if (!guestId) {
        const ts = Date.now();
        const randomStr = Math.random().toString(36).substring(2, 15);
        guestId = `guest_${ts}_${randomStr}`;
        localStorage.setItem('guest_id', guestId);
        const date = new Date();
        date.setTime(date.getTime() + (365 * 24 * 60 * 60 * 1000));
        document.cookie = `guest_id=${guestId};expires=${date.toUTCString()};path=/;SameSite=Lax`;
    }
    return guestId;
}

function getCartData() {
    const guestId = getGuestId();
    const cartKey = `cart_${guestId}`;
    const productsKey = `products_${guestId}`;
    let cart = [];
    let products = {};

    try {
        const cartData = localStorage.getItem(cartKey);
        const productsData = localStorage.getItem(productsKey);
        cart = cartData ? JSON.parse(cartData) : [];
        products = productsData ? JSON.parse(productsData) : {};
    } catch (error) {
        console.error('Lỗi đọc giỏ hàng:', error);
    }

    return { cart, products };
}

function renderOrderItems() {
    const itemsEl = document.getElementById('order-items');
    const emptyEl = document.getElementById('empty-order');
    const subtotalEl = document.getElementById('order-subtotal');
    const totalEl = document.getElementById('order-total');

    if (!itemsEl || !subtotalEl || !totalEl) return;

    const { cart, products } = getCartData();

    if (!Array.isArray(cart) || cart.length === 0) {
        itemsEl.innerHTML = '';
        emptyEl.classList.remove('hidden');
        return;
    }

    emptyEl.classList.add('hidden');
    let subtotal = 0;

    const appUrl = '<?php echo APP_URL; ?>';
    const imagesUrl = '<?php echo IMAGES_URL; ?>';

    const html = cart.map(item => {
        const productId = parseInt(item.id);
        const product = products[productId] || {};
        const price = parseFloat(product.sale_price) || parseFloat(product.price) || 0;
        const quantity = parseInt(item.quantity) || 1;
        const lineTotal = price * quantity;
        subtotal += lineTotal;

        const name = product.name || `Sản phẩm #${productId}`;
        const image = product.image || `${imagesUrl}/products/default.jpg`;
        const slug = product.slug || '';

        return `
            <div class="flex items-start gap-4 border border-gray-100 rounded-xl p-4 hover:border-rose-200 transition">
                <div class="w-20 h-20 rounded-lg overflow-hidden bg-gray-50 border border-gray-100 flex-shrink-0">
                    <img src="${image}" alt="${name}" class="w-full h-full object-cover">
                </div>
                <div class="flex-1">
                    <div class="flex justify-between gap-3 flex-wrap">
                        <a href="${appUrl}/product.php?slug=${slug}" class="font-semibold text-gray-900 hover:text-rose-500">${name}</a>
                        <span class="text-rose-600 font-bold">${formatCurrency(price)}</span>
                    </div>
                    <div class="text-sm text-gray-500 mt-1">Số lượng: <span class="font-semibold text-gray-800">${quantity}</span></div>
                    <div class="text-sm text-gray-700 font-semibold mt-1">Thành tiền: <span class="text-rose-600">${formatCurrency(lineTotal)}</span></div>
                </div>
            </div>
        `;
    }).join('');

    itemsEl.innerHTML = html;
    subtotalEl.textContent = formatCurrency(subtotal);
    totalEl.textContent = formatCurrency(subtotal);
}

function clearErrors() {
    ['fullname', 'phone', 'email', 'address', 'payment'].forEach(key => {
        const el = document.getElementById(`error-${key}`);
        if (el) {
            el.classList.add('hidden');
            el.textContent = '';
        }
    });
}

function showError(key, message) {
    const el = document.getElementById(`error-${key}`);
    if (el) {
        el.textContent = message;
        el.classList.remove('hidden');
    }
}

function validateForm() {
    clearErrors();
    const fullname = document.getElementById('fullname').value.trim();
    const phone = document.getElementById('phone').value.trim();
    const email = document.getElementById('email').value.trim();
    const address = document.getElementById('address').value.trim();
    const note = document.getElementById('note').value.trim();
    const payment = document.querySelector('input[name="payment-method"]:checked');

    let valid = true;

    if (!fullname) {
        showError('fullname', 'Vui lòng nhập họ tên.');
        valid = false;
    }
    if (!phone || !/^[0-9]{9,11}$/.test(phone)) {
        showError('phone', 'Số điện thoại không hợp lệ (9-11 số).');
        valid = false;
    }
    if (!email || !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
        showError('email', 'Email không hợp lệ.');
        valid = false;
    }
    if (!address) {
        showError('address', 'Vui lòng nhập địa chỉ nhận hàng.');
        valid = false;
    }
    if (!payment) {
        showError('payment', 'Vui lòng chọn phương thức thanh toán.');
        valid = false;
    }

    return {
        valid,
        payload: {
            fullname,
            phone,
            email,
            address,
            note,
            payment: payment ? payment.value : null
        }
    };
}

document.addEventListener('DOMContentLoaded', () => {
    renderOrderItems();

    const placeOrderBtn = document.getElementById('place-order-btn');
    const feedbackEl = document.getElementById('submit-feedback');

    if (placeOrderBtn) {
        placeOrderBtn.addEventListener('click', (event) => {
            event.preventDefault();
            const result = validateForm();
            if (!result.valid) {
                feedbackEl.classList.add('hidden');
                const firstError = document.querySelector('p.text-red-500:not(.hidden)');
                if (firstError) {
                    firstError.scrollIntoView({ behavior: 'smooth', block: 'center' });
                }
                return;
            }

            feedbackEl.classList.remove('hidden');
            feedbackEl.textContent = 'Thông tin hợp lệ. Chức năng đặt hàng sẽ được kích hoạt sau khi tích hợp backend.';
            console.log('Payload chuẩn bị gửi:', result.payload);
        });
    }
});
</script>

<?php include '../includes/footer.php'; ?>

