<?php
if (!defined('APP_URL')) {
    require_once '../../config/config.php';
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title ?? 'Admin Dashboard'; ?> - <?php echo APP_NAME; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="<?php echo CSS_URL; ?>/admin.css">
    <link rel="icon" type="image/svg+xml" href="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'%3E%3Cpath fill='%23f43f5e' d='M50 20c-5 0-10 3-12 8-2-5-7-8-12-8-8 0-14 6-14 14 0 8 6 14 14 14 5 0 10-3 12-8 2 5 7 8 12 8 8 0 14-6 14-14 0-8-6-14-14-14zm0 20c-3 0-6-3-6-6s3-6 6-6 6 3 6 6-3 6-6 6z'/%3E%3Ccircle fill='%23ec4899' cx='50' cy='50' r='8'/%3E%3Cpath fill='%23f43f5e' d='M50 60c-8 0-15 4-18 10h36c-3-6-10-10-18-10zm0-5c5 0 9-4 9-9s-4-9-9-9-9 4-9 9 4 9 9 9z'/%3E%3C/svg%3E">

</head>
<body>
    <div class="admin-wrapper">
        <!-- Sidebar -->
        <aside class="admin-sidebar">
            <div class="sidebar-header">
                <img src="<?php echo IMAGES_URL; ?>/logo/logo.jpg" alt="Logo" class="sidebar-logo">
                <h5 class="sidebar-title">Admin Panel</h5>
            </div>
            
            <nav class="sidebar-nav">
                <a href="<?php echo APP_URL; ?>/admin/index.php" class="nav-item <?php echo basename($_SERVER['PHP_SELF']) == 'index.php' ? 'active' : ''; ?>">
                    <i class="fas fa-home"></i>
                    <span>Dashboard</span>
                </a>
                <a href="<?php echo APP_URL; ?>/admin/sliders.php" class="nav-item <?php echo basename($_SERVER['PHP_SELF']) == 'sliders.php' ? 'active' : ''; ?>">
                    <i class="fas fa-images"></i>
                    <span>Quản lý Slider</span>
                </a>
                <a href="<?php echo APP_URL; ?>/admin/products.php" class="nav-item <?php echo basename($_SERVER['PHP_SELF']) == 'products.php' ? 'active' : ''; ?>">
                    <i class="fas fa-box"></i>
                    <span>Quản lý Sản phẩm</span>
                </a>
                <a href="<?php echo APP_URL; ?>/admin/categories.php" class="nav-item <?php echo basename($_SERVER['PHP_SELF']) == 'categories.php' ? 'active' : ''; ?>">
                    <i class="fas fa-folder"></i>
                    <span>Quản lý Danh mục</span>
                </a>
                <a href="<?php echo APP_URL; ?>/admin/reviews.php" class="nav-item <?php echo basename($_SERVER['PHP_SELF']) == 'reviews.php' ? 'active' : ''; ?>">
                    <i class="fas fa-star"></i>
                    <span>Quản lý Đánh giá</span>
                </a>
                <a href="<?php echo APP_URL; ?>/admin/addresses.php" class="nav-item <?php echo basename($_SERVER['PHP_SELF']) == 'addresses.php' ? 'active' : ''; ?>">
                    <i class="fas fa-map-marker-alt"></i>
                    <span>Quản lý Địa chỉ</span>
                </a>
            </nav>
            
            <!-- Nút về trang chủ -->
            <div style="border-top: 1px solid #e0e0e0; margin-top: 10px; padding-top: 10px;">
                <a href="<?php echo APP_URL; ?>/index.php" class="nav-item" style="color: #dc3545; text-decoration: none; display: flex; align-items: center; gap: 10px; padding: 10px 15px; border-radius: 5px; transition: background-color 0.3s;" onmouseover="this.style.backgroundColor='#fff5f5'" onmouseout="this.style.backgroundColor='transparent'">
                    <i class="fas fa-globe" style="color: #dc3545;"></i>
                    <span style="color: #dc3545; font-weight: 500;">Về trang chủ</span>
                </a>
            </div>
        </aside>
        
        <!-- Main Content -->
        <div class="admin-main">
            <!-- Top Header -->
            <header class="admin-header">
                <div class="header-left">
                    <button class="sidebar-toggle d-md-none">
                        <i class="fas fa-bars"></i>
                    </button>
                    <h4 class="page-title"><?php echo $page_title ?? 'Dashboard'; ?></h4>
                </div>
                <div class="header-right">
                    <div class="user-info">
                        <span class="user-name"><?php echo htmlspecialchars($_SESSION['admin_name'] ?? 'Admin'); ?></span>
                        <div class="user-menu">
                            <a href="<?php echo APP_URL; ?>/admin/logout.php" class="logout-btn">
                                <i class="fas fa-sign-out-alt"></i> Đăng xuất
                            </a>
                        </div>
                    </div>
                </div>
            </header>
            
            <!-- Content Area -->
            <main class="admin-content">

