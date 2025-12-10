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
            <div class="flex justify-between items-center mb-6" id="checkout-header">
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
            <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6 mb-8" id="checkout-form-section">
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
                        <label class="block text-sm font-semibold text-gray-800 mb-2">Số điện thoại *(Ưu tiên số điện thoại có dùng zalo)</label>
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
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6" id="checkout-main-grid">
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

            <!-- Trang thành công -->
            <div id="order-success" class="hidden">
                <div class="bg-white rounded-2xl shadow-xl border border-gray-100 p-8 mt-6">
                    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                        <div>
                            <p class="text-sm text-emerald-600 font-semibold uppercase tracking-wide">Đặt hàng thành công</p>
                            <h2 class="text-3xl font-bold text-gray-900 mt-1" id="success-title">Cảm ơn bạn đã đặt hàng!</h2>
                            <p class="mt-2 text-gray-600" id="success-subtitle">Đơn hàng của bạn đang được xử lý.</p>
                        </div>
                        <div class="text-right">
                            <p class="text-sm text-gray-500">Mã đơn hàng</p>
                            <p class="text-2xl font-extrabold text-rose-600" id="success-order-code">#000000</p>
                            <p class="text-sm text-gray-500" id="success-status">Trạng thái: Chờ xác nhận</p>
                        </div>
                    </div>

                    <div class="mt-6 grid grid-cols-1 lg:grid-cols-3 gap-6">
                        <div class="lg:col-span-2 space-y-4" id="success-items">
                            <!-- Sản phẩm sẽ render bằng JS -->
                        </div>
                        <div class="bg-gray-50 border border-gray-100 rounded-2xl p-6">
                            <h3 class="text-lg font-bold text-gray-900 mb-4">Thông tin đơn hàng</h3>
                            <div class="space-y-2 text-sm">
                                <div class="flex justify-between"><span class="text-gray-600">Tên khách:</span><span id="success-customer-name" class="font-semibold text-gray-900"></span></div>
                                <div class="flex justify-between"><span class="text-gray-600">Số điện thoại:</span><span id="success-customer-phone" class="font-semibold text-gray-900"></span></div>
                                <div class="flex justify-between"><span class="text-gray-600">Email:</span><span id="success-customer-email" class="font-semibold text-gray-900"></span></div>
                                <div class="flex justify-between"><span class="text-gray-600">Địa chỉ:</span><span id="success-customer-address" class="font-semibold text-gray-900 text-right"></span></div>
                                <div class="flex justify-between"><span class="text-gray-600">Thanh toán:</span><span id="success-payment-method" class="font-semibold text-gray-900"></span></div>
                            </div>
                            <div class="mt-4 border-t border-gray-200 pt-4 space-y-2 text-sm">
                                <div class="flex justify-between"><span class="text-gray-600">Tạm tính:</span><span id="success-subtotal" class="font-semibold text-gray-900"></span></div>
                                <div class="flex justify-between"><span class="text-gray-600">Phí giao:</span><span class="font-semibold text-emerald-600">Miễn phí</span></div>
                                <div class="flex justify-between text-lg font-bold">
                                    <span class="text-gray-900">Tổng thanh toán:</span>
                                    <span id="success-total" class="text-rose-600"></span>
                                </div>
                            </div>
                            <p id="success-note" class="mt-4 text-sm font-semibold text-red-600 leading-relaxed"></p>
                        </div>
                    </div>

                    <div id="success-qr" class="hidden mt-6 flex flex-col lg:flex-row gap-6 items-start">
                        <div class="flex-1">
                            <div class="bg-amber-50 border border-amber-200 text-amber-800 rounded-xl p-4 text-sm leading-relaxed">
                                <p class="font-semibold mb-1">Lưu ý thanh toán qua QR</p>
                                <p>Vui lòng quét mã để thanh toán. Chúng tôi sẽ xác nhận lại sau khi đơn hàng được thanh toán.</p>
                            </div>
                        </div>
                        <div class="bg-white border border-gray-200 rounded-2xl p-4 shadow-sm">
                            <img src="<?php echo APP_URL; ?>/assets/images/qr/qr.jpg" alt="QR thanh toán" class="w-56 h-56 object-contain">
                            <p class="mt-2 text-center text-sm text-gray-600">Quét QR để thanh toán</p>
                        </div>
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

function generateOrderCode() {
    const now = new Date();
    const yyyy = now.getFullYear().toString();
    const mm = String(now.getMonth() + 1).padStart(2, '0');
    const dd = String(now.getDate()).padStart(2, '0');
    const rand = Math.random().toString(36).substring(2, 6).toUpperCase();
    return `DH${yyyy}${mm}${dd}-${rand}`;
}

function renderSuccess(order) {
    const successWrap = document.getElementById('order-success');
    const successItems = document.getElementById('success-items');
    const qrBlock = document.getElementById('success-qr');
    const noteEl = document.getElementById('success-note');

    if (!successWrap || !successItems || !noteEl) return;

    document.getElementById('checkout-form-section')?.classList.add('hidden');
    document.getElementById('checkout-main-grid')?.classList.add('hidden');
    successWrap.classList.remove('hidden');

    document.getElementById('success-order-code').textContent = order.code;
    document.getElementById('success-status').textContent = 'Trạng thái: Chờ xác nhận';
    document.getElementById('success-customer-name').textContent = order.customer_name;
    document.getElementById('success-customer-phone').textContent = order.customer_phone;
    document.getElementById('success-customer-email').textContent = order.customer_email;
    document.getElementById('success-customer-address').textContent = order.customer_address;
    document.getElementById('success-payment-method').textContent = order.payment_method === 'cod' ? 'Thanh toán khi nhận hàng (COD)' : 'Chuyển khoản qua QR';
    document.getElementById('success-subtotal').textContent = formatCurrency(order.subtotal);
    document.getElementById('success-total').textContent = formatCurrency(order.total);

    const itemsHtml = order.items.map(item => `
        <div class="flex items-start gap-4 border border-gray-100 rounded-xl p-4">
            <div class="w-16 h-16 rounded-lg overflow-hidden bg-gray-50 border border-gray-100 flex-shrink-0">
                <img src="${item.image}" alt="${item.name}" class="w-full h-full object-cover">
            </div>
            <div class="flex-1">
                <div class="flex justify-between gap-3">
                    <div>
                        <p class="font-semibold text-gray-900">${item.name}</p>
                        <p class="text-sm text-gray-500">SL: ${item.quantity}</p>
                    </div>
                    <div class="text-right">
                        <p class="text-sm text-gray-500">Đơn giá</p>
                        <p class="font-semibold text-gray-900">${formatCurrency(item.price)}</p>
                    </div>
                </div>
                <p class="text-sm text-gray-700 mt-1">Thành tiền: <span class="text-rose-600 font-bold">${formatCurrency(item.line_total)}</span></p>
            </div>
        </div>
    `).join('');

    successItems.innerHTML = itemsHtml;

    const codNote = 'Chúng tôi sẽ xác nhận lại với bạn trong thời gian tới, vui lòng để ý điện thoại.';
    const qrNote = 'Đơn hàng đã ghi nhận. Vui lòng thanh toán qua mã QR, chúng tôi sẽ xác nhận lại sau khi đơn hàng được thanh toán.';

    if (order.payment_method === 'cod') {
        noteEl.textContent = codNote;
        noteEl.classList.remove('text-amber-700');
        noteEl.classList.add('text-red-600');
        qrBlock.classList.add('hidden');
    } else {
        noteEl.textContent = qrNote;
        noteEl.classList.remove('text-red-600');
        noteEl.classList.add('text-amber-700');
        qrBlock.classList.remove('hidden');
    }
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

            const { cart, products } = getCartData();
            const items = cart.map(item => {
                const product = products[item.id] || {};
                const price = parseFloat(product.sale_price) || parseFloat(product.price) || 0;
                const quantity = parseInt(item.quantity) || 1;
                const lineTotal = price * quantity;
                return {
                    id: item.id,
                    name: product.name || `Sản phẩm #${item.id}`,
                    image: product.image || '<?php echo IMAGES_URL; ?>/products/default.jpg',
                    slug: product.slug || '',
                    quantity,
                    price,
                    line_total: lineTotal
                };
            });

            fetch('<?php echo APP_URL; ?>/api/create_order.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    fullname: result.payload.fullname,
                    phone: result.payload.phone,
                    email: result.payload.email,
                    address: result.payload.address,
                    note: result.payload.note,
                    payment_method: result.payload.payment ? result.payload.payment.toUpperCase() : '',
                    items
                })
            })
            .then(res => res.json())
            .then(data => {
                if (!data.success) {
                    throw new Error(data.error || data.message || 'Không thể tạo đơn hàng');
                }

                const order = {
                    code: data.order.code,
                    status: data.order.status,
                    payment_method: data.order.payment_method,
                    customer_name: result.payload.fullname,
                    customer_phone: result.payload.phone,
                    customer_email: result.payload.email,
                    customer_address: result.payload.address,
                    subtotal: data.order.subtotal,
                    total: data.order.total,
                    items
                };

                // Hiển thị trang thành công
                renderSuccess(order);

                // Xoá giỏ hàng local sau khi tạo đơn
                const guestId = localStorage.getItem('guest_id') || getCookie('guest_id');
                if (guestId) {
                    localStorage.removeItem(`cart_${guestId}`);
                    localStorage.removeItem(`products_${guestId}`);
                    if (window.updateCartCount) {
                        window.updateCartCount();
                    }
                }

                // Scroll lên đầu trang để thấy kết quả
                window.scrollTo({ top: 0, behavior: 'smooth' });
            })
            .catch(err => {
                console.error(err);
                feedbackEl.classList.remove('hidden');
                feedbackEl.classList.add('text-red-600');
                feedbackEl.textContent = 'Tạo đơn không thành công. Vui lòng thử lại.';
            });
        });
    }
});
</script>

<?php include '../includes/footer.php'; ?>

