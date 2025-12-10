    <!-- Footer -->
    <footer class="bg-white text-black mt-16 w-full">
        <!-- Service Features -->
        <div class="bg-white py-8 border-b">
            <div class="container mx-auto px-4">
                <h3 class="text-lg font-semibold mb-6 text-center">Shop Hoa Tươi Online Phục Vụ:</h3>
                <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-6 text-center">
                    <div>
                        <i class="fas fa-clock text-orange-500 text-3xl mb-2"></i>
                        <h4 class="font-semibold text-black mb-1">Giao Hoa Nhanh</h4>
                        <p class="text-sm text-gray-700">Trong 90 - 120 phút</p>
                    </div>
                    <div>
                        <i class="fas fa-truck text-green-500 text-3xl mb-2"></i>
                        <h4 class="font-semibold text-black mb-1">Miễn Phí Giao Hàng</h4>
                        <p class="text-sm text-gray-700">(Đơn hàng >600k)</p>
                    </div>
                    <div>
                        <i class="fas fa-gift text-green-500 text-3xl mb-2"></i>
                        <h4 class="font-semibold text-black mb-1">Miễn Phí Thiệp</h4>
                        <p class="text-sm text-gray-700">Hoặc Decal In</p>
                    </div>
                    <div>
                        <i class="fas fa-shipping-fast text-green-500 text-3xl mb-2"></i>
                        <h4 class="font-semibold text-black mb-1">Giao Hoa Tận Nơi</h4>
                        <p class="text-sm text-gray-700">Đảm Bảo Hoa Tươi</p>
                    </div>
                    <div>
                        <i class="fas fa-check-circle text-green-500 text-3xl mb-2"></i>
                        <h4 class="font-semibold text-black mb-1">Hoa Giao Đúng Mẫu</h4>
                        <p class="text-sm text-gray-700">Đúng Tone Màu</p>
                    </div>
                    <div>
                        <i class="fas fa-tags text-green-500 text-3xl mb-2"></i>
                        <h4 class="font-semibold text-black mb-1">Nhập mã "Uudais"</h4>
                        <p class="text-sm text-gray-700">Giảm -5% Đơn Online</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Footer -->
        <?php
        // Lấy thông tin địa chỉ chính từ database
        $mainAddress = null;
        $mapEmbedUrl = '';
        
        if (defined('BASE_PATH')) {
            if (!class_exists('Database')) {
                require_once BASE_PATH . '/app/Database/Database.php';
            }
            if (!class_exists('AddressModel')) {
                require_once BASE_PATH . '/app/Models/AddressModel.php';
            }
            
            try {
                $addressModel = new AddressModel();
                $mainAddress = $addressModel->getMain();
                
                if ($mainAddress) {
                    // Xử lý URL Google Maps embed
                    // Ưu tiên 1: Kiểm tra nếu map_url đã là embed URL hợp lệ
                    if (!empty($mainAddress['map_url'])) {
                        $mapUrl = trim($mainAddress['map_url']);
                        
                        // Nếu đã là embed URL (có chứa /embed hoặc output=embed)
                        if (strpos($mapUrl, '/maps/embed') !== false || strpos($mapUrl, 'output=embed') !== false) {
                            $mapEmbedUrl = $mapUrl;
                        }
                        // Nếu là URL Google Maps thông thường, chuyển đổi sang embed
                        elseif (strpos($mapUrl, 'google.com/maps') !== false || strpos($mapUrl, 'maps.google.com') !== false) {
                            // Extract coordinates nếu có trong URL
                            if (preg_match('/([0-9.-]+),([0-9.-]+)/', $mapUrl, $coords)) {
                                $lat = floatval($coords[1]);
                                $lng = floatval($coords[2]);
                                $mapEmbedUrl = "https://www.google.com/maps?q={$lat},{$lng}&hl=vi&z=14&output=embed";
                            }
                            // Extract place ID nếu có
                            elseif (preg_match('/place\/([^\/\?]+)/', $mapUrl, $matches)) {
                                $placeId = urlencode($matches[1]);
                                $mapEmbedUrl = "https://www.google.com/maps/embed/v1/place?key=&q=place_id:{$placeId}&hl=vi";
                            }
                            // Nếu không extract được, thử chuyển đổi URL
                            else {
                                // Thêm output=embed vào URL
                                $separator = strpos($mapUrl, '?') !== false ? '&' : '?';
                                $mapEmbedUrl = $mapUrl . $separator . 'output=embed';
                            }
                        }
                    }
                    
                    // Ưu tiên 2: Nếu không có map_url hợp lệ nhưng có latitude và longitude
                    if (empty($mapEmbedUrl) && !empty($mainAddress['latitude']) && !empty($mainAddress['longitude'])) {
                        $lat = floatval($mainAddress['latitude']);
                        $lng = floatval($mainAddress['longitude']);
                        $mapEmbedUrl = "https://www.google.com/maps?q={$lat},{$lng}&hl=vi&z=14&output=embed";
                    }
                    
                    // Ưu tiên 3: Nếu có địa chỉ, tạo embed từ địa chỉ
                    if (empty($mapEmbedUrl) && !empty($mainAddress['address'])) {
                        $addressQuery = urlencode($mainAddress['address']);
                        $mapEmbedUrl = "https://www.google.com/maps?q={$addressQuery}&hl=vi&z=14&output=embed";
                    }
                    
                    // Đảm bảo URL là embed URL hợp lệ
                    if (!empty($mapEmbedUrl)) {
                        if (strpos($mapEmbedUrl, 'output=embed') === false && strpos($mapEmbedUrl, '/maps/embed') === false) {
                            $separator = strpos($mapEmbedUrl, '?') !== false ? '&' : '?';
                            $mapEmbedUrl = $mapEmbedUrl . $separator . 'output=embed';
                        }
                    }
                }
            } catch (Exception $e) {
                // Fallback về constants nếu có lỗi
            }
        }
        
        // Fallback về constants nếu không có dữ liệu từ database
        $footerAddress = $mainAddress ? $mainAddress['address'] : (defined('CONTACT_ADDRESS_HN') ? CONTACT_ADDRESS_HN : '');
        $footerEmail = $mainAddress ? $mainAddress['email'] : (defined('CONTACT_EMAIL') ? CONTACT_EMAIL : '');
        $footerPhone = $mainAddress ? $mainAddress['phone'] : (defined('CONTACT_PHONE_HN') ? CONTACT_PHONE_HN : '');
        ?>
        
        <div class="w-full py-12 border-t footer-main-content" style="padding-bottom: 0 !important;">
            <div class="container mx-auto px-2 max-w-full">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-2 lg:gap-3 footer-grid" style="width: 100%;">
                    <!-- Column 1: Logo and Contact -->
                    <div class="w-full">
                        <div class="mb-4 flex justify-center">
                            <img src="<?php echo IMAGES_URL; ?>/logo/logo.jpg" 
                                 alt="Hoa Ngoc Anh Logo" 
                                 class="h-48 w-auto object-contain">
                        </div>
                        <ul class="space-y-3 text-sm text-gray-700">
                            <?php if (!empty($footerAddress)): ?>
                            <li class="flex items-start">
                                <div class="w-10 h-10 flex items-center justify-center mr-3 flex-shrink-0">
                                    <i class="fas fa-map-marker-alt text-gray-500 text-lg"></i>
                                </div>
                                <span class="break-words"><?php echo htmlspecialchars($footerAddress); ?></span>
                            </li>
                            <?php endif; ?>
                            
                            <?php if (!empty($footerEmail)): ?>
                            <li class="flex items-center">
                                <div class="w-10 h-10 flex items-center justify-center mr-3 flex-shrink-0">
                                    <i class="fas fa-envelope text-gray-500 text-lg"></i>
                                </div>
                                <a href="mailto:<?php echo htmlspecialchars($footerEmail); ?>" class="hover:text-rose-500 break-all">
                                    <?php echo htmlspecialchars($footerEmail); ?>
                                </a>
                            </li>
                            <?php endif; ?>
                            
                            <?php if (!empty($footerPhone)): ?>
                            <li class="flex items-center">
                                <div class="w-10 h-10 flex items-center justify-center mr-3 flex-shrink-0">
                                    <i class="fas fa-phone text-gray-500 text-lg"></i>
                                </div>
                                <a href="tel:<?php echo str_replace([' ', '-', '(', ')'], '', $footerPhone); ?>" class="hover:text-rose-500">
                                    <?php echo htmlspecialchars($footerPhone); ?>
                                </a>
                            </li>
                            <?php endif; ?>
                        </ul>
                    </div>

                    <!-- Column 2: Description and Fanpage -->
                    <div class="w-full">
                        <h4 class="font-semibold text-black mb-4 text-center">Hoa Ngoc Anh Floral & Gifts | Hoa và Quà tặng ý nghĩa!</h4>
                        <p class="text-sm text-gray-700 mb-6 leading-relaxed">
                            Dịch vụ đặt hoa Online chất lượng, giá hợp lý, sáng tạo mẫu riêng theo nhu cầu của bạn. 
                            Mỗi ngày chúng tôi mang đến những sản phẩm hoa tươi làm đẹp cuộc sống và thay bạn trao gửi thương yêu đến cho người nhận.
                        </p>
                        
                        <!-- Fanpage -->
                        <div>
                            <h4 class="font-semibold text-black mb-2 text-center">FANPAGE</h4>
                            <div class="bg-gray-50 p-4 rounded border border-gray-200">
                                <div class="flex items-center justify-between mb-2">
                                    <div class="flex items-center gap-2">
                                        <i class="fab fa-facebook text-blue-600 text-xl"></i>
                                        <span class="text-sm font-medium text-black">Shop hoa tươi Hoa Ngoc Anh</span>
                                    </div>
                                </div>
                                <p class="text-xs text-gray-600 mb-3">4.268 người theo dõi</p>
                                <div class="flex gap-2">
                                    <a href="https://www.facebook.com/hoangocanh688?mibextid=wwXIfr&rdid=EAnLJSrhikMGHhsp&share_url=https%3A%2F%2Fwww.facebook.com%2Fshare%2F1DU5DdKA88%2F%3Fmibextid%3DwwXIfr#" target="_blank" rel="noopener" class="bg-blue-600 text-white px-4 py-1 rounded text-xs hover:bg-blue-700 transition inline-flex items-center justify-center">
                                        Theo dõi Trang
                                    </a>
                                    <a href="https://www.facebook.com/hoangocanh688?mibextid=wwXIfr&rdid=EAnLJSrhikMGHhsp&share_url=https%3A%2F%2Fwww.facebook.com%2Fshare%2F1DU5DdKA88%2F%3Fmibextid%3DwwXIfr#" target="_blank" rel="noopener" class="bg-gray-200 text-gray-700 px-4 py-1 rounded text-xs hover:bg-gray-300 transition inline-flex items-center justify-center">
                                        Chia sẻ
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Column 3: Policies -->
                    <div class="w-full policies-column">
                        <h4 class="font-semibold text-black mb-4 text-center lg:text-left">Chính sách</h4>
                        <ul class="space-y-2 text-sm text-gray-700 policies-list" style="margin-left: 90px !important;">
                            <li>
                                <a href="#" class="hover:text-rose-500 transition flex items-start justify-start">
                                    <span class="mr-2">▶</span>
                                    <span>Chính Sách Trả Hàng</span>
                                </a>
                            </li>
                            <li>
                                <a href="#" class="hover:text-rose-500 transition flex items-start justify-start">
                                    <span class="mr-2">▶</span>
                                    <span>Chính sách bảo mật</span>
                                </a>
                            </li>
                            <li>
                                <a href="#" class="hover:text-rose-500 transition flex items-start justify-start">
                                    <span class="mr-2">▶</span>
                                    <span>Chính sách vận chuyển</span>
                                </a>
                            </li>
                            <li>
                                <a href="#" class="hover:text-rose-500 transition flex items-start justify-start">
                                    <span class="mr-2">▶</span>
                                    <span>Chính sách thanh toán</span>
                                </a>
                            </li>
                        </ul>
                    </div>

                    <!-- Column 4: Google Map -->
                    <div class="w-full">
                        <?php if (!empty($mapEmbedUrl)): ?>
                            <h4 class="font-semibold text-black mb-4 text-center">Bản Đồ</h4>
                            <div class="w-full h-64 rounded-lg overflow-hidden border border-gray-200 shadow-sm">
                                <iframe 
                                    src="<?php echo htmlspecialchars($mapEmbedUrl); ?>" 
                                    width="100%" 
                                    height="100%" 
                                    style="border:0;" 
                                    allowfullscreen="" 
                                    loading="lazy" 
                                    referrerpolicy="no-referrer-when-downgrade"
                                    title="Bản đồ">
                                </iframe>
                            </div>
                            
                        <?php else: ?>
                            <h4 class="font-semibold text-black mb-4 text-center">Bản Đồ</h4>
                            <div class="w-full h-64 rounded-lg overflow-hidden border border-gray-200 shadow-sm bg-gray-100 flex items-center justify-center">
                                <p class="text-gray-500 text-sm">Bản đồ đang được cập nhật</p>
                            </div>
                        <?php endif; ?>
                    </div>
            </div>
        </div>

        <!-- Bottom Bar -->
        <div class="bg-gray-50 py-4 border-t">
            <div class="container mx-auto px-4">
                <div class="text-center text-sm text-gray-700">
                    <p>&copy; <?php echo date('Y'); ?> <strong>Hoa Ngoc Anh Floral & Gifts</strong>. All Rights Reserved</p>
                </div>
            </div>
        </div>
    </footer>

    <!-- Floating Action Buttons -->
    <div class="floating-action-buttons fixed right-4 bottom-4 flex flex-col gap-3 z-50">
        <!-- Cart - Đầu tiên -->
        <a href="<?php echo APP_URL; ?>/cart.php" 
           class="floating-btn w-14 h-14 bg-orange-500 text-white rounded-full flex items-center justify-center shadow-lg hover:scale-110 transition-transform relative">
            <i class="fas fa-shopping-cart text-2xl"></i>
            <span class="absolute -top-1 -right-1 bg-red-500 text-white text-xs w-5 h-5 rounded-full flex items-center justify-center font-bold" id="cart-count">0</span>
        </a>
        <!-- Facebook - Vị trí 2 -->
        <a href="https://www.facebook.com/hoangocanh688?mibextid=wwXIfr&rdid=EAnLJSrhikMGHhsp&share_url=https%3A%2F%2Fwww.facebook.com%2Fshare%2F1DU5DdKA88%2F%3Fmibextid%3DwwXIfr#" target="_blank" rel="noopener"
           class="floating-btn w-14 h-14 bg-blue-600 text-white rounded-full flex items-center justify-center shadow-lg hover:scale-110 transition-transform">
            <i class="fab fa-facebook text-2xl"></i>
        </a>
        <!-- Facebook Messenger - Vị trí 3 -->
        <a href="https://www.facebook.com/messages/" target="_blank" 
           class="floating-btn w-14 h-14 bg-blue-500 text-white rounded-full flex items-center justify-center shadow-lg hover:scale-110 transition-transform">
            <i class="fab fa-facebook-messenger text-2xl"></i>
        </a>
        <!-- Zalo - Vị trí mới -->
        <a href="https://zalo.me/0389932688" target="_blank" rel="noopener"
           class="floating-btn w-14 h-14 bg-cyan-500 text-white rounded-full flex items-center justify-center shadow-lg hover:scale-110 transition-transform">
            <img src="https://siinstore.com/wp-content/plugins/button-contact-vr/legacy/img/zalo.png" alt="Zalo" class="w-8 h-8 object-contain">
        </a>
        <!-- Gọi điện thoại - Vị trí 4 -->
        <a href="tel:0389932688" 
           class="floating-btn w-14 h-14 bg-green-500 text-white rounded-full flex items-center justify-center shadow-lg hover:scale-110 transition-transform">
            <i class="fas fa-phone-alt text-2xl"></i>
        </a>
        <!-- TikTok - Vị trí 5 (ẩn) -->
        <!--
        <a href="https://www.tiktok.com/" target="_blank" 
           class="floating-btn w-14 h-14 bg-black text-white rounded-full flex items-center justify-center shadow-lg hover:scale-110 transition-transform">
            <i class="fab fa-tiktok text-2xl"></i>
        </a>
        -->
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Custom JS -->
    <script src="<?php echo JS_URL; ?>/main.js"></script>
    
    <?php if (isset($extra_js)): ?>
        <?php foreach ($extra_js as $js): ?>
            <script src="<?php echo JS_URL . '/' . $js; ?>"></script>
        <?php endforeach; ?>
    <?php endif; ?>
</body>
</html>
