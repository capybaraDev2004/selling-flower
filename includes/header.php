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
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Baloo+2:wght@600;700&display=swap" rel="stylesheet">
    
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
    <header id="main-header" class="bg-white shadow-md sticky top-0 z-50 transition-transform duration-300 ease-in-out">
        <div class="container mx-auto px-4">
            <div class="header-content flex items-center justify-between py-2">
                <!-- Logo - Bên trái -->
                <div class="header-logo flex-shrink-0">
                    <a href="<?php echo APP_URL; ?>" class="flex items-center">
                        <img src="<?php echo IMAGES_URL; ?>/logo/logo.jpg" 
                             alt="Hoa Ngoc Anh Logo" 
                             class="header-logo-img object-contain hover:opacity-90 transition-opacity">
                    </a>
                </div>

                <!-- Tiêu đề - Ở giữa -->
                <div class="header-title flex-1 flex justify-center items-center px-4">
                    <div class="flex flex-col leading-tight text-center">
                        <span class="text-xl md:text-2xl lg:text-3xl font-extrabold text-transparent bg-clip-text bg-gradient-to-r from-rose-500 to-pink-500" style="font-family: 'Baloo 2', 'Inter', sans-serif;">
                            HOA NGỌC ANH
                        </span>
                        <span class="text-xs md:text-sm lg:text-base text-rose-500 font-semibold" style="font-family: 'Inter', sans-serif;">
                            Hoa đẹp khởi nguồn cảm xúc
                        </span>
                    </div>
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
                    <button id="mobile-menu-toggle" class="text-2xl text-gray-700 hover:text-rose-500 transition">
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
        (function() {
            const header = document.getElementById('main-header');
            let lastScroll = window.pageYOffset || document.documentElement.scrollTop;
            let upAccum = 0;
            let downAccum = 0;
            let hidden = false;
            const hideThreshold = 100;      // chỉ bắt đầu ẩn sau khi vượt 100px
            const upShowThreshold = 400;     // phải cuộn lên ít nhất 80px mới hiện lại
            const downHideThreshold = 100;   // cuộn xuống thêm 20px thì ẩn

            function apply(show) {
                header.style.transform = show ? 'translateY(0)' : 'translateY(-100%)';
                hidden = !show;
            }

            function onScroll() {
                const current = window.pageYOffset || document.documentElement.scrollTop;
                const delta = current - lastScroll;

                // Khi gần đầu trang: luôn hiện và reset tích lũy
                if (current < hideThreshold) {
                    apply(true);
                    upAccum = 0;
                    downAccum = 0;
                    lastScroll = current;
                    return;
                }

                if (delta > 0) { // cuộn xuống
                    downAccum += delta;
                    upAccum = 0;
                    if (!hidden && downAccum > downHideThreshold) {
                        apply(false);
                    }
                } else if (delta < 0) { // cuộn lên
                    upAccum += -delta;
                    downAccum = 0;
                    if (hidden && upAccum > upShowThreshold) {
                        apply(true);
                    }
                }

                lastScroll = current < 0 ? 0 : current;
            }

            window.addEventListener('scroll', onScroll, { passive: true });
        })();
    </script>
