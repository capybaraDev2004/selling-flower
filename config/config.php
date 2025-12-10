<?php
/**
 * File cấu hình chính của ứng dụng
 * Chứa các hằng số và cấu hình toàn cục
 */

// Cấu hình chung
define('APP_NAME', 'Hoa Ngọc Anh - Hoa và Quà tặng ý nghĩa');
define('APP_URL', 'http://localhost/hoaNgocAnh/public');
define('BASE_PATH', dirname(__DIR__));

// Cấu hình đường dẫn
define('ASSETS_URL', APP_URL . '/assets');
define('CSS_URL', ASSETS_URL . '/css');
define('JS_URL', ASSETS_URL . '/js');
define('IMAGES_URL', ASSETS_URL . '/images');

// Admin URL
define('ADMIN_URL', APP_URL . '/admin');

// Cấu hình cơ sở dữ liệu (sẽ sử dụng sau)
define('DB_HOST', 'localhost');
define('DB_NAME', 'hoa_ngoc_anh');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_CHARSET', 'utf8mb4');

// Thông tin liên hệ
define('CONTACT_PHONE_HN', '+84 392 690 630');
define('CONTACT_PHONE_HCM', '0966 312 360');
define('CONTACT_EMAIL', 'contact@hoangocanh.com');
define('CONTACT_ADDRESS_HN', '177 Trung Kính, phường Yên Hoà, quận Cầu Giấy, TP Hà Nội');
define('CONTACT_ADDRESS_HCM', '151 Nguyễn Duy Trinh, TP Thủ Đức, thành phố Hồ Chí Minh');

// Cấu hình phân trang
define('PRODUCTS_PER_PAGE', 12);

// Múi giờ
date_default_timezone_set('Asia/Ho_Chi_Minh');

// Bật hiển thị lỗi (development)
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Helper function để format giá tiền
function formatPrice($price) {
    // Chống lỗi khi $price null/không phải số
    if ($price === null || $price === '' || !is_numeric($price)) {
        $price = 0;
    }
    $priceSafe = (float)$price;
    return number_format($priceSafe, 0, ',', '.') . ' ₫';
}

// Helper function để tính phần trăm giảm giá
function calculateDiscount($original_price, $sale_price) {
    if ($original_price === null || $sale_price === null) return 0;
    if (!is_numeric($original_price) || !is_numeric($sale_price)) return 0;
    if ($original_price <= 0) return 0;
    $discount = (($original_price - $sale_price) / $original_price) * 100;
    return (int)round($discount);
}

// Helper function để tạo slug từ tiêu đề
function createSlug($str) {
    $str = preg_replace("/(à|á|ạ|ả|ã|â|ầ|ấ|ậ|ẩ|ẫ|ă|ằ|ắ|ặ|ẳ|ẵ)/", 'a', $str);
    $str = preg_replace("/(è|é|ẹ|ẻ|ẽ|ê|ề|ế|ệ|ể|ễ)/", 'e', $str);
    $str = preg_replace("/(ì|í|ị|ỉ|ĩ)/", 'i', $str);
    $str = preg_replace("/(ò|ó|ọ|ỏ|õ|ô|ồ|ố|ộ|ổ|ỗ|ơ|ờ|ớ|ợ|ở|ỡ)/", 'o', $str);
    $str = preg_replace("/(ù|ú|ụ|ủ|ũ|ư|ừ|ứ|ự|ử|ữ)/", 'u', $str);
    $str = preg_replace("/(ỳ|ý|ỵ|ỷ|ỹ)/", 'y', $str);
    $str = preg_replace("/(đ)/", 'd', $str);
    $str = preg_replace("/(À|Á|Ạ|Ả|Ã|Â|Ầ|Ấ|Ậ|Ẩ|Ẫ|Ă|Ằ|Ắ|Ặ|Ẳ|Ẵ)/", 'A', $str);
    $str = preg_replace("/(È|É|Ẹ|Ẻ|Ẽ|Ê|Ề|Ế|Ệ|Ể|Ễ)/", 'E', $str);
    $str = preg_replace("/(Ì|Í|Ị|Ỉ|Ĩ)/", 'I', $str);
    $str = preg_replace("/(Ò|Ó|Ọ|Ỏ|Õ|Ô|Ồ|Ố|Ộ|Ổ|Ỗ|Ơ|Ờ|Ớ|Ợ|Ở|Ỡ)/", 'O', $str);
    $str = preg_replace("/(Ù|Ú|Ụ|Ủ|Ũ|Ư|Ừ|Ứ|Ự|Ử|Ữ)/", 'U', $str);
    $str = preg_replace("/(Ỳ|Ý|Ỵ|Ỷ|Ỹ)/", 'Y', $str);
    $str = preg_replace("/(Đ)/", 'D', $str);
    $str = preg_replace("/[^a-zA-Z0-9\s]/", '', $str);
    $str = preg_replace("/\s+/", '-', $str);
    $str = strtolower($str);
    return $str;
}

// Helper function để tạo slug unique
function createUniqueSlug($str, $productModel, $excludeId = null) {
    $baseSlug = createSlug($str);
    $slug = $baseSlug;
    $counter = 1;
    
    // Kiểm tra slug có tồn tại không
    while ($productModel->findSlugExists($slug, $excludeId)) {
        $slug = $baseSlug . '-' . $counter;
        $counter++;
    }
    
    return $slug;
}

