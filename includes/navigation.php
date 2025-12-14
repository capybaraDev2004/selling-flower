<?php
// Xác định menu item đang active
$current_page = basename($_SERVER['PHP_SELF']);
$current_category = isset($_GET['cat']) ? $_GET['cat'] : '';
$active_menu = '';

// Kiểm tra trang chủ
if ($current_page === 'index.php' || (empty($current_page) && empty($current_category))) {
    $active_menu = 'home';
}
// Kiểm tra trang shop
elseif ($current_page === 'shop.php') {
    $active_menu = 'shop';
}
// Kiểm tra trang category và category slug
elseif ($current_page === 'category.php' && !empty($current_category)) {
    // Map category slug với menu item
    $category_menu_map = [
        'hoa-sinh-nhat' => 'hoa-sinh-nhat',
        'bo-hoa-sinh-nhat' => 'hoa-sinh-nhat',
        'gio-hoa-sinh-nhat' => 'hoa-sinh-nhat',
        'lang-hoa-sinh-nhat' => 'hoa-sinh-nhat',
        'hoa-sinh-nhat-nguoi-yeu' => 'hoa-sinh-nhat',
        'hoa-sinh-nhat-tang-vo' => 'hoa-sinh-nhat',
        'hoa-sinh-nhat-tang-me' => 'hoa-sinh-nhat',
        'hoa-8-3' => 'hoa-8-3',
        'hoa-khai-truong' => 'hoa-khai-truong',
        'ke-hoa-khai-truong' => 'hoa-khai-truong',
        'lang-hoa-khai-truong' => 'hoa-khai-truong',
        'gio-hoa-khai-truong' => 'hoa-khai-truong',
        'hoa-tot-nghiep' => 'hoa-tot-nghiep',
        'hoa-chia-buon' => 'hoa-chia-buon',
        'ke-hoa-chia-buon' => 'hoa-chia-buon',
        'gio-hoa-chia-buon' => 'hoa-chia-buon',
        'bo-hoa' => 'bo-hoa',
        'hoa-lan-ho-diep' => 'hoa-lan-ho-diep',
    ];
    
    $active_menu = $category_menu_map[$current_category] ?? '';
}

// Helper function để thêm class active cho desktop - Style đồng nhất cho tất cả
function isActiveMenu($menu_key, $active_menu, $isDropdown = false) {
    // Base classes đồng nhất cho TẤT CẢ nút: cùng padding, height, border-radius
    $baseClasses = 'font-medium transition-all duration-200 inline-flex items-center justify-center';
    $baseClasses .= ' px-4 py-2 rounded-lg';
    $baseClasses .= ' min-h-[44px] h-[44px]';
    
    if ($isDropdown) {
        $baseClasses .= ' gap-1.5';
    }
    
    // Active state: nền hồng gradient, chữ trắng (chỉ khi trang được mở)
    if ($menu_key === $active_menu) {
        return 'text-white bg-gradient-to-r from-rose-500 to-pink-500 font-semibold ' . $baseClasses;
    }
    // Mặc định: chữ hồng, nền trắng. Hover: nền hồng gradient, chữ trắng
    return 'text-rose-500 hover:bg-gradient-to-r hover:from-rose-500 hover:to-pink-500 hover:text-white ' . $baseClasses;
}

// Helper function để thêm class active cho mobile - Style đồng nhất
function isActiveMenuMobile($menu_key, $active_menu) {
    // Active state: nền hồng, chữ trắng (chỉ khi trang được mở)
    if ($menu_key === $active_menu) {
        return 'bg-gradient-to-r from-rose-500 to-pink-500 text-white font-semibold';
    }
    // Mặc định: chữ hồng. Hover: nền hồng, chữ trắng
    return 'text-rose-500 hover:bg-gradient-to-r hover:from-rose-500 hover:to-pink-500 hover:text-white';
}
?>
<!-- Desktop Navigation -->
<nav class="hidden lg:block border-t">
    <ul class="main-nav flex items-center justify-center gap-8 py-3">
        <li>
            <a href="<?php echo APP_URL; ?>" class="<?php echo isActiveMenu('home', $active_menu); ?>">
                Giới Thiệu
            </a>
        </li>
        <li>
            <a href="<?php echo APP_URL; ?>/shop.php" class="<?php echo isActiveMenu('shop', $active_menu); ?>">
                Shop
            </a>
        </li>
        <li class="relative group">
            <a href="<?php echo APP_URL; ?>/category.php?cat=hoa-sinh-nhat" class="nav-dropdown-trigger <?php echo isActiveMenu('hoa-sinh-nhat', $active_menu, true); ?>">
                Hoa Sinh Nhật
                <i class="fas fa-chevron-down text-xs"></i>
            </a>
            <div class="nav-dropdown-menu">
                <a href="<?php echo APP_URL; ?>/category.php?cat=bo-hoa-sinh-nhat" class="nav-dropdown-item">Bó Hoa Sinh Nhật</a>
                <a href="<?php echo APP_URL; ?>/category.php?cat=gio-hoa-sinh-nhat" class="nav-dropdown-item">Giỏ Hoa Sinh Nhật</a>
                <a href="<?php echo APP_URL; ?>/category.php?cat=lang-hoa-sinh-nhat" class="nav-dropdown-item">Lẵng Hoa Sinh Nhật</a>
                <a href="<?php echo APP_URL; ?>/category.php?cat=hoa-sinh-nhat-nguoi-yeu" class="nav-dropdown-item">Hoa Sinh Nhật Người Yêu</a>
                <a href="<?php echo APP_URL; ?>/category.php?cat=hoa-sinh-nhat-tang-vo" class="nav-dropdown-item">Hoa Sinh Nhật Tặng Vợ</a>
                <a href="<?php echo APP_URL; ?>/category.php?cat=hoa-sinh-nhat-tang-me" class="nav-dropdown-item">Hoa Sinh Nhật Tặng Mẹ</a>
            </div>
        </li>
        <li>
            <a href="<?php echo APP_URL; ?>/category.php?cat=hoa-8-3" class="<?php echo isActiveMenu('hoa-8-3', $active_menu); ?>">
                Hoa 8/3
            </a>
        </li>
        <li class="relative group">
            <a href="<?php echo APP_URL; ?>/category.php?cat=hoa-khai-truong" class="nav-dropdown-trigger <?php echo isActiveMenu('hoa-khai-truong', $active_menu, true); ?>">
                Hoa Khai Trương
                <i class="fas fa-chevron-down text-xs"></i>
            </a>
            <div class="nav-dropdown-menu">
                <a href="<?php echo APP_URL; ?>/category.php?cat=ke-hoa-khai-truong" class="nav-dropdown-item">Kệ Hoa Khai Trương</a>
                <a href="<?php echo APP_URL; ?>/category.php?cat=lang-hoa-khai-truong" class="nav-dropdown-item">Lẵng Hoa Khai Trương</a>
                <a href="<?php echo APP_URL; ?>/category.php?cat=gio-hoa-khai-truong" class="nav-dropdown-item">Giỏ Hoa Khai Trương</a>
            </div>
        </li>
        <li>
            <a href="<?php echo APP_URL; ?>/category.php?cat=hoa-tot-nghiep" class="<?php echo isActiveMenu('hoa-tot-nghiep', $active_menu); ?>">
                Hoa Tốt Nghiệp
            </a>
        </li>
        <li class="relative group">
            <a href="<?php echo APP_URL; ?>/category.php?cat=hoa-chia-buon" class="nav-dropdown-trigger <?php echo isActiveMenu('hoa-chia-buon', $active_menu, true); ?>">
                Hoa Chia Buồn
                <i class="fas fa-chevron-down text-xs"></i>
            </a>
            <div class="nav-dropdown-menu">
                <a href="<?php echo APP_URL; ?>/category.php?cat=ke-hoa-chia-buon" class="nav-dropdown-item">Kệ Hoa Chia Buồn</a>
                <a href="<?php echo APP_URL; ?>/category.php?cat=gio-hoa-chia-buon" class="nav-dropdown-item">Giỏ Hoa Chia Buồn</a>
            </div>
        </li>
        <li>
            <a href="<?php echo APP_URL; ?>/category.php?cat=bo-hoa" class="<?php echo isActiveMenu('bo-hoa', $active_menu); ?>">
                Bó Hoa
            </a>
        </li>
        <li>
            <a href="<?php echo APP_URL; ?>/category.php?cat=hoa-lan-ho-diep" class="<?php echo isActiveMenu('hoa-lan-ho-diep', $active_menu); ?>">
                Hoa Lan Hồ Điệp
            </a>
        </li>
    </ul>
</nav>

<!-- Mobile Navigation -->
<nav id="mobile-menu" class="lg:hidden hidden border-t">
    <ul class="py-2">
        <li><a href="<?php echo APP_URL; ?>" class="block px-4 py-2 transition-all duration-200 <?php echo isActiveMenuMobile('home', $active_menu); ?>">Giới Thiệu</a></li>
        <li><a href="<?php echo APP_URL; ?>/shop.php" class="block px-4 py-2 transition-all duration-200 <?php echo isActiveMenuMobile('shop', $active_menu); ?>">Shop</a></li>
        <li>
            <button class="w-full text-left px-4 py-2 transition-all duration-200 flex justify-between items-center <?php echo isActiveMenuMobile('hoa-sinh-nhat', $active_menu); ?>" onclick="toggleMobileSubmenu('sinh-nhat')">
                Hoa Sinh Nhật
                <i class="fas fa-chevron-down text-xs"></i>
            </button>
            <ul id="submenu-sinh-nhat" class="hidden bg-gray-50">
                <li><a href="<?php echo APP_URL; ?>/category.php?cat=bo-hoa-sinh-nhat" class="block px-8 py-2 transition-all duration-200 hover:bg-gradient-to-r hover:from-rose-500 hover:to-pink-500 hover:text-white">Bó Hoa Sinh Nhật</a></li>
                <li><a href="<?php echo APP_URL; ?>/category.php?cat=gio-hoa-sinh-nhat" class="block px-8 py-2 transition-all duration-200 hover:bg-gradient-to-r hover:from-rose-500 hover:to-pink-500 hover:text-white">Giỏ Hoa Sinh Nhật</a></li>
                <li><a href="<?php echo APP_URL; ?>/category.php?cat=lang-hoa-sinh-nhat" class="block px-8 py-2 transition-all duration-200 hover:bg-gradient-to-r hover:from-rose-500 hover:to-pink-500 hover:text-white">Lẵng Hoa Sinh Nhật</a></li>
                <li><a href="<?php echo APP_URL; ?>/category.php?cat=hoa-sinh-nhat-nguoi-yeu" class="block px-8 py-2 transition-all duration-200 hover:bg-gradient-to-r hover:from-rose-500 hover:to-pink-500 hover:text-white">Hoa Sinh Nhật Người Yêu</a></li>
                <li><a href="<?php echo APP_URL; ?>/category.php?cat=hoa-sinh-nhat-tang-vo" class="block px-8 py-2 transition-all duration-200 hover:bg-gradient-to-r hover:from-rose-500 hover:to-pink-500 hover:text-white">Hoa Sinh Nhật Tặng Vợ</a></li>
                <li><a href="<?php echo APP_URL; ?>/category.php?cat=hoa-sinh-nhat-tang-me" class="block px-8 py-2 transition-all duration-200 hover:bg-gradient-to-r hover:from-rose-500 hover:to-pink-500 hover:text-white">Hoa Sinh Nhật Tặng Mẹ</a></li>
            </ul>
        </li>
        <li><a href="<?php echo APP_URL; ?>/category.php?cat=hoa-8-3" class="block px-4 py-2 transition-all duration-200 <?php echo isActiveMenuMobile('hoa-8-3', $active_menu); ?>">Hoa 8/3</a></li>
        <li>
            <button class="w-full text-left px-4 py-2 transition-all duration-200 flex justify-between items-center <?php echo isActiveMenuMobile('hoa-khai-truong', $active_menu); ?>" onclick="toggleMobileSubmenu('khai-truong')">
                Hoa Khai Trương
                <i class="fas fa-chevron-down text-xs"></i>
            </button>
            <ul id="submenu-khai-truong" class="hidden bg-gray-50">
                <li><a href="<?php echo APP_URL; ?>/category.php?cat=ke-hoa-khai-truong" class="block px-8 py-2 transition-all duration-200 hover:bg-gradient-to-r hover:from-rose-500 hover:to-pink-500 hover:text-white">Kệ Hoa Khai Trương</a></li>
                <li><a href="<?php echo APP_URL; ?>/category.php?cat=lang-hoa-khai-truong" class="block px-8 py-2 transition-all duration-200 hover:bg-gradient-to-r hover:from-rose-500 hover:to-pink-500 hover:text-white">Lẵng Hoa Khai Trương</a></li>
                <li><a href="<?php echo APP_URL; ?>/category.php?cat=gio-hoa-khai-truong" class="block px-8 py-2 transition-all duration-200 hover:bg-gradient-to-r hover:from-rose-500 hover:to-pink-500 hover:text-white">Giỏ Hoa Khai Trương</a></li>
            </ul>
        </li>
        <li><a href="<?php echo APP_URL; ?>/category.php?cat=hoa-tot-nghiep" class="block px-4 py-2 transition-all duration-200 <?php echo isActiveMenuMobile('hoa-tot-nghiep', $active_menu); ?>">Hoa Tốt Nghiệp</a></li>
        <li>
            <button class="w-full text-left px-4 py-2 transition-all duration-200 flex justify-between items-center <?php echo isActiveMenuMobile('hoa-chia-buon', $active_menu); ?>" onclick="toggleMobileSubmenu('chia-buon')">
                Hoa Chia Buồn
                <i class="fas fa-chevron-down text-xs"></i>
            </button>
            <ul id="submenu-chia-buon" class="hidden bg-gray-50">
                <li><a href="<?php echo APP_URL; ?>/category.php?cat=ke-hoa-chia-buon" class="block px-8 py-2 transition-all duration-200 hover:bg-gradient-to-r hover:from-rose-500 hover:to-pink-500 hover:text-white">Kệ Hoa Chia Buồn</a></li>
                <li><a href="<?php echo APP_URL; ?>/category.php?cat=gio-hoa-chia-buon" class="block px-8 py-2 transition-all duration-200 hover:bg-gradient-to-r hover:from-rose-500 hover:to-pink-500 hover:text-white">Giỏ Hoa Chia Buồn</a></li>
            </ul>
        </li>
        <li><a href="<?php echo APP_URL; ?>/category.php?cat=bo-hoa" class="block px-4 py-2 transition-all duration-200 <?php echo isActiveMenuMobile('bo-hoa', $active_menu); ?>">Bó Hoa</a></li>
        <li><a href="<?php echo APP_URL; ?>/category.php?cat=hoa-lan-ho-diep" class="block px-4 py-2 transition-all duration-200 <?php echo isActiveMenuMobile('hoa-lan-ho-diep', $active_menu); ?>">Hoa Lan Hồ Điệp</a></li>
    </ul>
</nav>

