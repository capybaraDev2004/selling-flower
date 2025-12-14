<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="<?php echo $page_description ?? 'Hoa Ngọc Anh - Hoa và quà tặng ý nghĩa. Dịch vụ đặt hoa online chất lượng, giao hàng nhanh trong 90-120 phút.'; ?>">
    <meta name="keywords" content="hoa tươi, hoa sinh nhật, hoa khai trương, hoa tốt nghiệp, shop hoa, đặt hoa online">
    <link rel="icon" type="image/svg+xml" href="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'%3E%3Cpath fill='%23f43f5e' d='M50 20c-5 0-10 3-12 8-2-5-7-8-12-8-8 0-14 6-14 14 0 8 6 14 14 14 5 0 10-3 12-8 2 5 7 8 12 8 8 0 14-6 14-14 0-8-6-14-14-14zm0 20c-3 0-6-3-6-6s3-6 6-6 6 3 6 6-3 6-6 6z'/%3E%3Ccircle fill='%23ec4899' cx='50' cy='50' r='8'/%3E%3Cpath fill='%23f43f5e' d='M50 60c-8 0-15 4-18 10h36c-3-6-10-10-18-10zm0-5c5 0 9-4 9-9s-4-9-9-9-9 4-9 9 4 9 9 9z'/%3E%3C/svg%3E">
    <title><?php echo $page_title ?? APP_NAME; ?></title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Baloo+2:wght@600;700&family=Playfair+Display:wght@600;700;800&family=Cormorant+Garamond:wght@400;500;600;700&family=Great+Vibes&family=Dancing+Script:wght@400;500;600;700&family=Allura&family=Parisienne&family=Satisfy&family=Fleur+De+Leah&family=Love+Light&family=Beau+Rivage&family=Charm:wght@400;700&display=swap" rel="stylesheet">
    
    <!-- Custom CSS -->
    <link rel="stylesheet" href="<?php echo CSS_URL; ?>/style.css">
    
    <?php if (isset($extra_css)): ?>
        <?php foreach ($extra_css as $css): ?>
            <link rel="stylesheet" href="<?php echo CSS_URL . '/' . $css; ?>">
        <?php endforeach; ?>
    <?php endif; ?>
</head>
<body class="font-inter">
    <!-- Main Header -->
    <header id="main-header" class="bg-white shadow-md fixed top-0 left-0 right-0 z-50">
        <div class="container mx-auto px-4">
            <div class="header-content flex items-center justify-between py-2">
                <!-- Logo - Bên trái -->
                <div class="header-logo flex-shrink-0">
                    <a href="<?php echo APP_URL; ?>" class="flex items-center">
                        <div class="header-logo-placeholder" style="width: 88px; height: 88px;"></div>
                    </a>
                </div>

                <!-- Tiêu đề - Ở giữa -->
                <div class="header-title flex-1 flex justify-center items-center px-4">
                    <a href="<?php echo APP_URL; ?>" class="header-title-link group">
                        <div class="flex flex-col items-center justify-center leading-tight text-center relative">
                            <!-- Icon hoa trang trí -->
                            <div class="header-title-icon-wrapper mb-1">
                                <i class="fas fa-spa header-title-icon text-rose-400"></i>
                            </div>
                            
                            <!-- Tiêu đề chính -->
                            <h1 class="header-title-main text-xl md:text-2xl lg:text-3xl xl:text-4xl font-bold relative">
                                <span class="header-title-text">HOA NGỌC ANH</span>
                                <span class="header-title-glow"></span>
                            </h1>
                            
                            <!-- Tagline -->
                            <p class="header-title-tagline text-xs md:text-sm lg:text-base text-rose-500/90 font-medium mt-1 tracking-wide">
                                <i class="fas fa-heart text-rose-400 text-[0.65em] mr-1.5"></i>
                                <span>HOA ĐẸP KHỞI NGUỒN CẢM XÚC</span>
                                <i class="fas fa-heart text-rose-400 text-[0.65em] ml-1.5"></i>
                            </p>
                            
                            <!-- Decorative line -->
                            <div class="header-title-underline mt-1.5"></div>
                        </div>
                    </a>
                </div>

                <!-- Search Bar - Bên phải -->
                <div class="header-search flex-shrink-0 hidden lg:flex">
                    <form action="<?php echo APP_URL; ?>/shop.php" method="get" class="relative w-64">
                        <input type="text" 
                               name="q"
                               placeholder="Tìm kiếm sản phẩm..." 
                               class="w-full px-4 py-2 pr-12 border-2 border-gray-300 rounded-full focus:outline-none focus:border-rose-500 transition text-sm">
                        <button type="submit" class="absolute right-2 top-1/2 -translate-y-1/2 bg-rose-500 text-white w-8 h-8 rounded-full hover:bg-rose-600 transition flex items-center justify-center">
                            <i class="fas fa-search text-sm"></i>
                        </button>
                    </form>
                </div>

                <!-- Mobile Menu Toggle - Chỉ hiện trên mobile -->
                <div class="header-mobile-menu flex-shrink-0 lg:hidden">
                    <button id="mobile-menu-toggle" type="button" class="text-2xl text-gray-700 hover:text-rose-500 transition relative z-50">
                        <i class="fas fa-bars"></i>
                    </button>
                </div>
            </div>

            <!-- Navigation -->
            <?php include 'navigation.php'; ?>
        </div>
    </header>

    <!-- Mobile Search -->
    <div class="lg:hidden bg-white py-3 px-4 shadow-sm">
        <form action="<?php echo APP_URL; ?>/shop.php" method="get" class="relative">
            <input type="text" 
                   name="q"
                   placeholder="Tìm kiếm sản phẩm..." 
                   class="w-full px-4 py-2 pr-12 border-2 border-gray-300 rounded-full focus:outline-none focus:border-rose-500">
            <button type="submit" class="absolute right-2 top-1/2 -translate-y-1/2 bg-rose-500 text-white w-8 h-8 rounded-full">
                <i class="fas fa-search"></i>
            </button>
        </form>
    </div>

    <script>
        // Mobile Menu Toggle - Khởi tạo ngay
        (function() {
            function initMobileMenuToggle() {
                const mobileMenuToggle = document.getElementById('mobile-menu-toggle');
                const mobileMenu = document.getElementById('mobile-menu');
                
                if (mobileMenuToggle && mobileMenu) {
                    mobileMenuToggle.addEventListener('click', function(e) {
                        e.preventDefault();
                        e.stopPropagation();
                        
                        const isOpen = mobileMenu.classList.toggle('open');
                        if (isOpen) {
                            mobileMenu.classList.remove('hidden');
                            document.body.classList.add('mobile-menu-open');
                        } else {
                            mobileMenu.classList.add('hidden');
                            document.body.classList.remove('mobile-menu-open');
                        }
                        console.log('Mobile menu toggled, open:', isOpen);
                    });
                    console.log('Mobile menu toggle initialized');
                } else {
                    console.warn('Mobile menu elements not found:', {
                        toggle: !!mobileMenuToggle,
                        menu: !!mobileMenu
                    });
                }
            }
            
            // Chạy ngay khi DOM sẵn sàng
            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', initMobileMenuToggle);
            } else {
                initMobileMenuToggle();
            }
        })();
        
        (function() {
            'use strict';
            
            let header = null;
            let lastScrollTop = 0;
            let isScrolling = false;
            let scrollTimer = null;
            
            function initHeaderScroll() {
                header = document.getElementById('main-header');
                if (!header) {
                    console.error('[Header Scroll] Header element not found!');
                    return false;
                }
                
                console.log('[Header Scroll] Header found:', header);
                
                // Tính toán và set padding-top cho body
                function updateBodyPadding() {
                    if (header) {
                        const headerHeight = header.offsetHeight;
                        document.body.style.paddingTop = headerHeight + 'px';
                        console.log('[Header Scroll] Body padding set to:', headerHeight + 'px');
                    }
                }
                
                updateBodyPadding();
                document.body.classList.add('has-fixed-header');
                
                // Đảm bảo header không có class hidden ban đầu
                header.classList.remove('header-hidden');
                
                // Lấy scroll position ban đầu
                lastScrollTop = window.pageYOffset || document.documentElement.scrollTop;
                console.log('[Header Scroll] Initial scroll position:', lastScrollTop);
                
                function showHeader() {
                    if (header && header.classList.contains('header-hidden')) {
                        header.classList.remove('header-hidden');
                        console.log('[Header Scroll] Header SHOWN');
                    }
                }
                
                function hideHeader() {
                    if (header && !header.classList.contains('header-hidden')) {
                        header.classList.add('header-hidden');
                        console.log('[Header Scroll] Header HIDDEN');
                    }
                }
                
                function handleScroll() {
                    const currentScrollTop = window.pageYOffset || document.documentElement.scrollTop;
                    const scrollDelta = currentScrollTop - lastScrollTop;
                    
                    // Ở đầu trang (top): luôn hiện header
                    if (currentScrollTop <= 10) {
                        showHeader();
                        lastScrollTop = currentScrollTop;
                        return;
                    }
                    
                    // Cuộn xuống (scrollDelta > 0): ẩn header
                    if (scrollDelta > 10) {
                        hideHeader();
                    }
                    // Cuộn lên (scrollDelta < 0): hiện header
                    else if (scrollDelta < -10) {
                        showHeader();
                    }
                    
                    lastScrollTop = currentScrollTop;
                }
                
                // Thêm event listener với debounce
                window.addEventListener('scroll', function() {
                    if (!isScrolling) {
                        window.requestAnimationFrame(function() {
                            handleScroll();
                            isScrolling = false;
                        });
                        isScrolling = true;
                    }
                }, { passive: true });
                
                // Xử lý resize
                let resizeTimer;
                window.addEventListener('resize', function() {
                    clearTimeout(resizeTimer);
                    resizeTimer = setTimeout(function() {
                        updateBodyPadding();
                    }, 100);
                });
                
                // Test ngay
                handleScroll();
                
                console.log('[Header Scroll] Initialized successfully');
                return true;
            }
            
            // Chạy khi DOM ready
            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', function() {
                    setTimeout(initHeaderScroll, 100);
                });
            } else {
                setTimeout(initHeaderScroll, 100);
            }
        })();
    </script>
