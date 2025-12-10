-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Dec 10, 2025 at 02:16 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `hoa_ngoc_anh`
--

-- --------------------------------------------------------

--
-- Table structure for table `addresses`
--

CREATE TABLE `addresses` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `address` text NOT NULL,
  `main` tinyint(1) NOT NULL,
  `ward` varchar(255) DEFAULT NULL,
  `district` varchar(255) DEFAULT NULL,
  `city` varchar(255) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `map_url` varchar(800) NOT NULL,
  `latitude` decimal(10,8) DEFAULT NULL,
  `longitude` decimal(11,8) DEFAULT NULL,
  `type` enum('office','shop','warehouse') DEFAULT 'shop',
  `display_order` int(11) DEFAULT 0,
  `status` enum('active','inactive') DEFAULT 'active',
  `note` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `addresses`
--

INSERT INTO `addresses` (`id`, `name`, `address`, `main`, `ward`, `district`, `city`, `phone`, `email`, `map_url`, `latitude`, `longitude`, `type`, `display_order`, `status`, `note`, `created_at`, `updated_at`) VALUES
(13, 'Tiệm hoa Ngọc Anh', 'CT1 / 62 Trần Bình, Từ Liêm, Hà Nội', 1, 'Trần Bình', 'Từ Liêm', 'Hà Nội', '0389932688', 'admin@gmail.com', 'https://www.google.com/maps/place/62%2F58+P.+Tr%E1%BA%A7n+B%C3%ACnh/@21.034887,105.7756583,21z/data=!4m7!3m6!1s0x313454b67dd09d69:0x5a7a8a1900f21b98!4b1!8m2!3d21.03496!4d105.7758!16s%2Fg%2F11x2gyn571?entry=ttu&g_ep=EgoyMDI1MTIwMi4wIKXMDSoASAFQAw%3D%3D', NULL, NULL, 'shop', 1, 'active', '', '2025-12-08 15:42:29', '2025-12-08 16:30:53');

-- --------------------------------------------------------

--
-- Table structure for table `blog_posts`
--

CREATE TABLE `blog_posts` (
  `id` int(11) NOT NULL,
  `author_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `excerpt` text DEFAULT NULL,
  `content` text NOT NULL,
  `featured_image` varchar(255) DEFAULT NULL,
  `category` varchar(100) DEFAULT NULL,
  `tags` varchar(255) DEFAULT NULL,
  `views` int(11) DEFAULT 0,
  `status` enum('draft','published','archived') DEFAULT 'draft',
  `published_at` datetime DEFAULT NULL,
  `meta_title` varchar(255) DEFAULT NULL,
  `meta_description` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `parent_id` int(11) DEFAULT NULL,
  `display_order` int(11) DEFAULT 0,
  `status` enum('active','inactive') DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`id`, `name`, `slug`, `description`, `image`, `parent_id`, `display_order`, `status`, `created_at`, `updated_at`) VALUES
(1, 'Hoa Sinh Nhật', 'hoa-sinh-nhat', 'Các loại hoa tươi dành cho sinh nhật', NULL, NULL, 1, 'active', '2025-12-07 09:36:44', '2025-12-07 09:36:44'),
(2, 'Hoa 8/3', 'hoa-8-3', 'Hoa tươi ngày Quốc tế Phụ nữ', NULL, NULL, 2, 'active', '2025-12-07 09:36:44', '2025-12-07 09:36:44'),
(3, 'Hoa Khai Trương', 'hoa-khai-truong', 'Hoa chúc mừng khai trương', NULL, NULL, 3, 'active', '2025-12-07 09:36:44', '2025-12-07 09:36:44'),
(4, 'Hoa Tốt Nghiệp', 'hoa-tot-nghiep', 'Hoa chúc mừng tốt nghiệp', NULL, NULL, 4, 'active', '2025-12-07 09:36:44', '2025-12-07 09:36:44'),
(5, 'Hoa Chia Buồn', 'hoa-chia-buon', 'Hoa viếng, chia buồn', NULL, NULL, 5, 'active', '2025-12-07 09:36:44', '2025-12-07 09:36:44'),
(6, 'Bó Hoa', 'bo-hoa', 'Các loại bó hoa tươi', NULL, NULL, 6, 'active', '2025-12-07 09:36:44', '2025-12-07 09:36:44'),
(7, 'Hoa Lan Hồ Điệp', 'hoa-lan-ho-diep', 'Hoa lan hồ điệp cao cấp', NULL, NULL, 7, 'active', '2025-12-07 09:36:44', '2025-12-07 09:36:44'),
(8, 'Bó Hoa Sinh Nhật', 'bo-hoa-sinh-nhat', 'Bó hoa tươi cho sinh nhật', NULL, 1, 1, 'active', '2025-12-07 09:36:44', '2025-12-07 09:36:44'),
(9, 'Giỏ Hoa Sinh Nhật', 'gio-hoa-sinh-nhat', 'Giỏ hoa tươi cho sinh nhật', NULL, 1, 2, 'active', '2025-12-07 09:36:44', '2025-12-07 09:36:44'),
(10, 'Lẵng Hoa Sinh Nhật', 'lang-hoa-sinh-nhat', 'Lẵng hoa tươi cho sinh nhật', NULL, 1, 3, 'active', '2025-12-07 09:36:44', '2025-12-07 09:36:44'),
(11, 'Hoa Sinh Nhật Người Yêu', 'hoa-sinh-nhat-nguoi-yeu', 'Hoa sinh nhật tặng người yêu', NULL, 1, 4, 'active', '2025-12-07 09:36:44', '2025-12-07 09:36:44'),
(12, 'Hoa Sinh Nhật Tặng Vợ', 'hoa-sinh-nhat-tang-vo', 'Hoa sinh nhật tặng vợ', NULL, 1, 5, 'active', '2025-12-07 09:36:44', '2025-12-07 09:36:44'),
(13, 'Hoa Sinh Nhật Tặng Mẹ', 'hoa-sinh-nhat-tang-me', 'Hoa sinh nhật tặng mẹ', NULL, 1, 6, 'active', '2025-12-07 09:36:44', '2025-12-07 09:36:44'),
(14, 'Kệ Hoa Khai Trương', 'ke-hoa-khai-truong', 'Kệ hoa khai trương', NULL, 3, 1, 'active', '2025-12-07 09:36:44', '2025-12-07 09:36:44'),
(15, 'Lẵng Hoa Khai Trương', 'lang-hoa-khai-truong', 'Lẵng hoa khai trương', NULL, 3, 2, 'active', '2025-12-07 09:36:44', '2025-12-07 09:36:44'),
(16, 'Giỏ Hoa Khai Trương', 'gio-hoa-khai-truong', 'Giỏ hoa khai trương', NULL, 3, 3, 'active', '2025-12-07 09:36:44', '2025-12-07 09:36:44'),
(17, 'Kệ Hoa Chia Buồn', 'ke-hoa-chia-buon', 'Kệ hoa chia buồn', NULL, 5, 1, 'active', '2025-12-07 09:36:44', '2025-12-07 09:36:44'),
(18, 'Giỏ Hoa Chia Buồn', 'gio-hoa-chia-buon', 'Giỏ hoa chia buồn', NULL, 5, 2, 'active', '2025-12-07 09:36:44', '2025-12-07 09:36:44');

-- --------------------------------------------------------

--
-- Table structure for table `coupons`
--

CREATE TABLE `coupons` (
  `id` int(11) NOT NULL,
  `code` varchar(50) NOT NULL,
  `description` text DEFAULT NULL,
  `discount_type` enum('percentage','fixed') NOT NULL,
  `discount_value` decimal(10,2) NOT NULL,
  `min_order_value` decimal(10,2) DEFAULT 0.00,
  `max_discount_amount` decimal(10,2) DEFAULT NULL,
  `usage_limit` int(11) DEFAULT NULL,
  `used_count` int(11) DEFAULT 0,
  `start_date` datetime NOT NULL,
  `end_date` datetime NOT NULL,
  `status` enum('active','inactive') DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `coupons`
--

INSERT INTO `coupons` (`id`, `code`, `description`, `discount_type`, `discount_value`, `min_order_value`, `max_discount_amount`, `usage_limit`, `used_count`, `start_date`, `end_date`, `status`, `created_at`, `updated_at`) VALUES
(4, 'Uudais', 'Giảm 5% cho đơn hàng online', 'percentage', 5.00, 0.00, NULL, NULL, 0, '2025-12-07 16:36:44', '2026-12-07 16:36:44', 'active', '2025-12-07 09:36:44', '2025-12-07 09:36:44'),
(5, 'WELCOME10', 'Giảm 10% cho khách hàng mới', 'percentage', 10.00, 100000.00, NULL, NULL, 0, '2025-12-07 16:36:44', '2026-06-07 16:36:44', 'active', '2025-12-07 09:36:44', '2025-12-07 09:36:44'),
(6, 'FREESHIP', 'Miễn phí vận chuyển', 'fixed', 50000.00, 500000.00, NULL, NULL, 0, '2025-12-07 16:36:44', '2026-03-07 16:36:44', 'active', '2025-12-07 09:36:44', '2025-12-07 09:36:44');

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `order_code` varchar(32) NOT NULL,
  `customer_name` varchar(150) NOT NULL,
  `customer_phone` varchar(20) NOT NULL,
  `customer_email` varchar(150) NOT NULL,
  `shipping_address` text NOT NULL,
  `note` text DEFAULT NULL,
  `payment_method` enum('COD','BANKPLUS') NOT NULL DEFAULT 'COD',
  `status` enum('PENDING','CONFIRMED','SHIPPING','COMPLETED','CANCELLED') NOT NULL DEFAULT 'PENDING',
  `subtotal` decimal(18,2) NOT NULL DEFAULT 0.00,
  `shipping_fee` decimal(18,2) NOT NULL DEFAULT 0.00,
  `total` decimal(18,2) NOT NULL DEFAULT 0.00,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`id`, `order_code`, `customer_name`, `customer_phone`, `customer_email`, `shipping_address`, `note`, `payment_method`, `status`, `subtotal`, `shipping_fee`, `total`, `created_at`, `updated_at`) VALUES
(1, 'DH20251210-171225-11E6', 'Nguyễn Tiến Toán', '0352135115', 'admin1231231aasdsa23@gmail.com', 'Cao Bằng', '', 'COD', 'PENDING', 14640000.00, 0.00, 14640000.00, '2025-12-10 17:12:25', '2025-12-10 17:12:25'),
(2, 'DH20251210-190122-996C', 'Nguyễn Tiến Toán', '0352135115', 'admin1231231aasdsa23@gmail.com', 'CT1 / 62 Trần Bình, Từ Liêm, Hà Nội', '', 'BANKPLUS', 'COMPLETED', 2520000.00, 0.00, 2520000.00, '2025-12-10 19:01:22', '2025-12-10 19:29:56');

-- --------------------------------------------------------

--
-- Table structure for table `order_items`
--

CREATE TABLE `order_items` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `order_id` bigint(20) UNSIGNED NOT NULL,
  `product_id` bigint(20) UNSIGNED DEFAULT NULL,
  `product_name` varchar(255) NOT NULL,
  `product_slug` varchar(255) DEFAULT NULL,
  `product_image` varchar(255) DEFAULT NULL,
  `price` decimal(18,2) NOT NULL DEFAULT 0.00,
  `quantity` int(10) UNSIGNED NOT NULL DEFAULT 1,
  `line_total` decimal(18,2) NOT NULL DEFAULT 0.00,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `order_items`
--

INSERT INTO `order_items` (`id`, `order_id`, `product_id`, `product_name`, `product_slug`, `product_image`, `price`, `quantity`, `line_total`, `created_at`) VALUES
(1, 1, 3, 'Lẵng Hoa Sinh Nhật Hồng', 'lang-hoa-sinh-nhat-hong', 'http://localhost/hoaNgocAnh/public/assets/images/products/hoa3.jpg', 650000.00, 2, 1300000.00, '2025-12-10 17:12:25'),
(2, 1, 30, 'Lẵng Hoa Tốt Nghiệp', 'lang-hoa-tot-nghiep', 'http://localhost/hoaNgocAnh/public/assets/images/products/hoa5.jpg', 550000.00, 4, 2200000.00, '2025-12-10 17:12:25'),
(3, 1, 34, 'Giỏ Hoa Chia Buồn', 'gio-hoa-chia-buon', 'http://localhost/hoaNgocAnh/public/assets/images/products/hoa4.jpg', 1200000.00, 7, 8400000.00, '2025-12-10 17:12:25'),
(4, 1, 33, 'Kệ Hoa Chia Buồn Trắng Vàng', 'ke-hoa-chia-buon-trang-vang', 'http://localhost/hoaNgocAnh/public/assets/images/products/hoa3.jpg', 1600000.00, 1, 1600000.00, '2025-12-10 17:12:25'),
(5, 1, 39, 'Bó Hoa Hồng Hồng', 'bo-hoa-hong-hong', 'http://localhost/hoaNgocAnh/public/assets/images/products/hoa4.jpg', 380000.00, 3, 1140000.00, '2025-12-10 17:12:25'),
(6, 2, 49, 'Hoa Lan Hồ Điệp Đỏ', 'hoa-lan-ho-diep-do', 'http://localhost/hoaNgocAnh/public/assets/images/products/hoa4.jpg', 820000.00, 1, 820000.00, '2025-12-10 19:01:22'),
(7, 2, 48, 'Hoa Lan Hồ Điệp Mix', 'hoa-lan-ho-diep-mix', 'http://localhost/hoaNgocAnh/public/assets/images/products/hoa3.jpg', 850000.00, 2, 1700000.00, '2025-12-10 19:01:22');

-- --------------------------------------------------------

--
-- Table structure for table `password_resets`
--

CREATE TABLE `password_resets` (
  `id` int(11) NOT NULL,
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `expires_at` datetime NOT NULL,
  `used` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `id` int(11) NOT NULL,
  `category_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `sku` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `price` decimal(10,2) NOT NULL,
  `sale_price` decimal(10,2) DEFAULT NULL,
  `stock_quantity` int(11) DEFAULT 0,
  `featured` tinyint(1) DEFAULT 0,
  `status` enum('active','inactive','out_of_stock') DEFAULT 'active',
  `views` int(11) DEFAULT 0,
  `sold_count` int(11) DEFAULT 10,
  `rating_avg` decimal(3,2) DEFAULT 4.70,
  `rating_count` int(11) DEFAULT 0,
  `meta_title` varchar(255) DEFAULT NULL,
  `meta_description` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id`, `category_id`, `name`, `slug`, `sku`, `description`, `price`, `sale_price`, `stock_quantity`, `featured`, `status`, `views`, `sold_count`, `rating_avg`, `rating_count`, `meta_title`, `meta_description`, `created_at`, `updated_at`) VALUES
(1, 8, 'Bó Hoa Hồng Đỏ Sinh Nhật', 'bo-hoa-hong-do-sinh-nhat', 'SP1', 'Bó hoa hồng đỏ tươi thắm, kèm lá phụ và baby breath trắng, phù hợp tặng sinh nhật', 500000.00, 450000.00, 50, 0, 'active', 0, 5, 5.00, 0, 'abc', 'abc', '2025-12-07 09:36:50', '2025-12-08 13:55:14'),
(2, 1, 'Giỏ Hoa Sinh Nhật Mix Nhiều Màu', 'gio-hoa-sinh-nhat-mix-nhieu-mau', 'SP2', 'Giỏ hoa mix nhiều loại hoa tươi với màu sắc rực rỡ', 600000.00, 550000.00, 30, 1, 'active', 0, 5, 4.50, 0, NULL, NULL, '2025-12-07 09:36:50', '2025-12-08 13:57:01'),
(3, 1, 'Lẵng Hoa Sinh Nhật Hồng', 'lang-hoa-sinh-nhat-hong', 'SP3', 'Lẵng hoa hồng hồng đẹp mắt, sang trọng', 700000.00, 650000.00, 25, 0, 'active', 0, 5, 5.00, 0, NULL, NULL, '2025-12-07 09:36:50', '2025-12-08 13:55:14'),
(4, 1, 'Bó Hoa Hồng Trắng Sinh Nhật', 'bo-hoa-hong-trang-sinh-nhat', 'SP4', 'Bó hoa hồng trắng tinh khôi, thanh lịch', 450000.00, 400000.00, 40, 0, 'active', 0, 5, 5.00, 0, NULL, NULL, '2025-12-07 09:36:50', '2025-12-08 13:55:14'),
(5, 1, 'Giỏ Hoa Sinh Nhật Vàng', 'gio-hoa-sinh-nhat-vang', 'SP5', 'Giỏ hoa vàng rực rỡ, tươi vui', 550000.00, 500000.00, 35, 0, 'active', 0, 5, 5.00, 0, NULL, NULL, '2025-12-07 09:36:50', '2025-12-08 13:55:14'),
(6, 1, 'Bó Hoa Hồng Đỏ Lớn Sinh Nhật', 'bo-hoa-hong-do-lon-sinh-nhat', 'SP6', 'Bó hoa hồng đỏ kích thước lớn, ấn tượng', 800000.00, 750000.00, 20, 1, 'active', 0, 5, 5.00, 0, NULL, NULL, '2025-12-07 09:36:50', '2025-12-08 13:55:14'),
(7, 1, 'Lẵng Hoa Sinh Nhật Mix', 'lang-hoa-sinh-nhat-mix', 'SP7', 'Lẵng hoa mix nhiều loại, đa dạng màu sắc', 650000.00, 600000.00, 28, 0, 'active', 0, 5, 5.00, 0, NULL, NULL, '2025-12-07 09:36:50', '2025-12-08 13:55:14'),
(8, 13, 'Bó Hoa Hồng Hồng Sinh Nhật', 'bo-hoa-hong-hong-sinh-nhat', 'SP8', 'Bó hoa hồng màu hồng ngọt ngào', 480000.00, 430000.00, 45, 0, 'active', 0, 5, 5.00, 0, 'abc', 'abc', '2025-12-07 09:36:50', '2025-12-08 13:55:14'),
(9, 1, 'Giỏ Hoa Sinh Nhật Đỏ Trắng', 'gio-hoa-sinh-nhat-do-trang', 'SP9', 'Giỏ hoa kết hợp đỏ và trắng hài hòa', 580000.00, 530000.00, 32, 0, 'active', 0, 5, 5.00, 0, NULL, NULL, '2025-12-07 09:36:50', '2025-12-08 13:55:14'),
(10, 1, 'Bó Hoa Sinh Nhật Đặc Biệt', 'bo-hoa-sinh-nhat-dac-biet', 'SP10', 'Bó hoa sinh nhật đặc biệt với thiết kế độc đáo', 900000.00, 850000.00, 15, 1, 'active', 0, 5, 5.00, 0, NULL, NULL, '2025-12-07 09:36:50', '2025-12-08 13:55:14'),
(11, 2, 'Bó Hoa 8/3 Hồng Đỏ', 'bo-hoa-8-3-hong-do', 'SP11', 'Bó hoa chúc mừng ngày Quốc tế Phụ nữ', 350000.00, 300000.00, 60, 0, 'active', 0, 5, 5.00, 0, NULL, NULL, '2025-12-07 09:36:50', '2025-12-08 13:55:14'),
(12, 2, 'Giỏ Hoa 8/3 Mix', 'gio-hoa-8-3-mix', 'SP12', 'Giỏ hoa 8/3 đa dạng màu sắc', 450000.00, 400000.00, 50, 0, 'active', 0, 5, 5.00, 0, NULL, NULL, '2025-12-07 09:36:50', '2025-12-08 13:55:14'),
(13, 2, 'Bó Hoa 8/3 Hồng', 'bo-hoa-8-3-hong', 'SP13', 'Bó hoa hồng ngọt ngào cho ngày 8/3', 380000.00, 330000.00, 55, 0, 'active', 0, 5, 5.00, 0, NULL, NULL, '2025-12-07 09:36:50', '2025-12-08 13:55:14'),
(14, 16, 'Lẵng Hoa 8/3 Đặc Biệt', 'lang-hoa-83-dac-biet', 'SP14', 'Lẵng hoa 8/3 đặc biệt, sang trọng', 550000.00, 500000.00, 30, 1, 'active', 0, 5, 5.00, 0, 'abc', 'abc', '2025-12-07 09:36:50', '2025-12-08 13:55:14'),
(15, 2, 'Bó Hoa 8/3 Vàng', 'bo-hoa-8-3-vang', 'SP15', 'Bó hoa vàng tươi vui', 320000.00, 280000.00, 65, 0, 'active', 0, 5, 5.00, 0, NULL, NULL, '2025-12-07 09:36:50', '2025-12-08 13:55:14'),
(16, 2, 'Giỏ Hoa 8/3 Trắng Hồng', 'gio-hoa-8-3-trang-hong', 'SP16', 'Giỏ hoa trắng hồng thanh lịch', 420000.00, 370000.00, 48, 0, 'active', 0, 5, 5.00, 0, NULL, NULL, '2025-12-07 09:36:50', '2025-12-08 13:55:14'),
(17, 2, 'Bó Hoa 8/3 Mix Nhiều Màu', 'bo-hoa-8-3-mix-nhieu-mau', 'SP17', 'Bó hoa mix nhiều màu rực rỡ', 400000.00, 350000.00, 52, 0, 'active', 0, 5, 5.00, 0, NULL, NULL, '2025-12-07 09:36:50', '2025-12-08 13:55:14'),
(18, 2, 'Lẵng Hoa 8/3 Cao Cấp', 'lang-hoa-8-3-cao-cap', 'SP18', 'Lẵng hoa 8/3 cao cấp, đẹp mắt', 600000.00, 550000.00, 25, 1, 'active', 0, 5, 5.00, 0, NULL, NULL, '2025-12-07 09:36:50', '2025-12-08 13:55:14'),
(19, 3, 'Kệ Hoa Khai Trương Lớn', 'ke-hoa-khai-truong-lon', 'SP19', 'Kệ hoa khai trương sang trọng, kích thước lớn', 2000000.00, 1800000.00, 20, 1, 'active', 0, 5, 5.00, 0, NULL, NULL, '2025-12-07 09:36:50', '2025-12-08 13:55:14'),
(20, 3, 'Kệ Hoa Khai Trương Vàng', 'ke-hoa-khai-truong-vang', 'SP20', 'Kệ hoa khai trương màu vàng rực rỡ', 1800000.00, 1650000.00, 22, 1, 'active', 0, 5, 5.00, 0, NULL, NULL, '2025-12-07 09:36:50', '2025-12-08 13:55:14'),
(21, 3, 'Lẵng Hoa Khai Trương', 'lang-hoa-khai-truong', 'SP21', 'Lẵng hoa khai trương đẹp mắt', 1200000.00, 1100000.00, 30, 0, 'active', 0, 5, 5.00, 0, NULL, NULL, '2025-12-07 09:36:50', '2025-12-08 13:55:14'),
(22, 3, 'Kệ Hoa Khai Trương Mix', 'ke-hoa-khai-truong-mix', 'SP22', 'Kệ hoa khai trương mix nhiều màu', 1900000.00, 1750000.00, 18, 1, 'active', 0, 5, 5.00, 0, NULL, NULL, '2025-12-07 09:36:50', '2025-12-08 13:55:14'),
(23, 3, 'Giỏ Hoa Khai Trương', 'gio-hoa-khai-truong', 'SP23', 'Giỏ hoa khai trương sang trọng', 1000000.00, 900000.00, 35, 0, 'active', 0, 5, 5.00, 0, NULL, NULL, '2025-12-07 09:36:50', '2025-12-08 13:55:14'),
(24, 3, 'Kệ Hoa Khai Trương Đỏ Vàng', 'ke-hoa-khai-truong-do-vang', 'SP24', 'Kệ hoa khai trương đỏ vàng nổi bật', 2100000.00, 1950000.00, 15, 1, 'active', 0, 5, 5.00, 0, NULL, NULL, '2025-12-07 09:36:50', '2025-12-08 13:55:14'),
(25, 3, 'Lẵng Hoa Khai Trương Lớn', 'lang-hoa-khai-truong-lon', 'SP25', 'Lẵng hoa khai trương kích thước lớn', 1500000.00, 1400000.00, 25, 0, 'active', 0, 5, 5.00, 0, NULL, NULL, '2025-12-07 09:36:50', '2025-12-08 13:55:14'),
(26, 3, 'Kệ Hoa Khai Trương Đặc Biệt', 'ke-hoa-khai-truong-dac-biet', 'SP26', 'Kệ hoa khai trương đặc biệt, ấn tượng', 2500000.00, 2300000.00, 12, 1, 'active', 0, 5, 5.00, 0, NULL, NULL, '2025-12-07 09:36:50', '2025-12-08 13:55:14'),
(27, 4, 'Bó Hoa Tốt Nghiệp Mix', 'bo-hoa-tot-nghiep-mix', 'SP27', 'Bó hoa chúc mừng tốt nghiệp', 450000.00, 400000.00, 35, 0, 'active', 0, 5, 5.00, 0, NULL, NULL, '2025-12-07 09:36:50', '2025-12-08 13:55:14'),
(28, 4, 'Giỏ Hoa Tốt Nghiệp', 'gio-hoa-tot-nghiep', 'SP28', 'Giỏ hoa tốt nghiệp đẹp mắt', 500000.00, 450000.00, 32, 0, 'active', 0, 5, 5.00, 0, 'abc', 'abc', '2025-12-07 09:36:50', '2025-12-10 07:38:08'),
(29, 4, 'Bó Hoa Tốt Nghiệp Vàng', 'bo-hoa-tot-nghiep-vang', 'SP29', 'Bó hoa vàng chúc mừng tốt nghiệp', 420000.00, 380000.00, 38, 0, 'active', 0, 5, 5.00, 0, NULL, NULL, '2025-12-07 09:36:50', '2025-12-08 13:55:14'),
(30, 4, 'Lẵng Hoa Tốt Nghiệp', 'lang-hoa-tot-nghiep', 'SP30', 'Lẵng hoa tốt nghiệp sang trọng', 600000.00, 550000.00, 28, 1, 'active', 0, 5, 5.00, 0, NULL, NULL, '2025-12-07 09:36:50', '2025-12-08 13:55:14'),
(31, 4, 'Bó Hoa Tốt Nghiệp Đặc Biệt', 'bo-hoa-tot-nghiep-dac-biet', 'SP31', 'Bó hoa tốt nghiệp đặc biệt', 550000.00, 500000.00, 30, 0, 'active', 0, 5, 5.00, 0, NULL, NULL, '2025-12-07 09:36:50', '2025-12-08 13:55:14'),
(32, 18, 'Kệ Hoa Chia Buồn Trắng', 'ke-hoa-chia-buon-trang', 'SP32', 'Kệ hoa chia buồn màu trắng', 1500000.00, 150000.00, 25, 0, 'active', 0, 5, 5.00, 0, 'abc', 'anc', '2025-12-07 09:36:50', '2025-12-10 06:37:42'),
(33, 17, 'Kệ Hoa Chia Buồn Trắng Vàng', 'ke-hoa-chia-buon-trang-vang', 'SP33', 'Kệ hoa chia buồn trắng vàng', 1600000.00, NULL, 22, 0, 'active', 0, 5, 5.00, 0, 'abc', 'anc', '2025-12-07 09:36:50', '2025-12-10 06:38:03'),
(34, 5, 'Giỏ Hoa Chia Buồn', 'gio-hoa-chia-buon', 'SP34', 'Giỏ hoa chia buồn trang trọng', 1200000.00, 180000.00, 30, 0, 'active', 0, 5, 5.00, 0, NULL, NULL, '2025-12-07 09:36:50', '2025-12-10 06:48:03'),
(35, 5, 'Kệ Hoa Chia Buồn Lớn', 'ke-hoa-chia-buon-lon', 'SP35', 'Kệ hoa chia buồn kích thước lớn', 1800000.00, 180000.00, 18, 0, 'active', 0, 5, 5.00, 0, NULL, NULL, '2025-12-07 09:36:50', '2025-12-10 06:47:52'),
(36, 5, 'Lẵng Hoa Chia Buồn', 'lang-hoa-chia-buon', 'SP36', 'Lẵng hoa chia buồn thanh lịch', 1400000.00, 180000.00, 28, 0, 'active', 0, 5, 5.00, 0, NULL, NULL, '2025-12-07 09:36:50', '2025-12-10 06:47:59'),
(37, 6, 'Bó Hoa Hồng Trắng', 'bo-hoa-hong-trang', 'SP37', 'Bó hoa hồng trắng tinh khôi', 400000.00, 1800000.00, 40, 0, 'active', 0, 5, 5.00, 0, NULL, NULL, '2025-12-07 09:36:50', '2025-12-10 06:48:06'),
(38, 6, 'Bó Hoa Hồng Đỏ', 'bo-hoa-hong-do', 'SP38', 'Bó hoa hồng đỏ tươi thắm', 450000.00, 400000.00, 45, 1, 'active', 0, 5, 5.00, 0, NULL, NULL, '2025-12-07 09:36:50', '2025-12-08 13:55:14'),
(39, 6, 'Bó Hoa Hồng Hồng', 'bo-hoa-hong-hong', 'SP39', 'Bó hoa hồng màu hồng ngọt ngào', 420000.00, 380000.00, 42, 0, 'active', 0, 5, 5.00, 0, NULL, NULL, '2025-12-07 09:36:50', '2025-12-08 13:55:14'),
(40, 6, 'Bó Hoa Mix Nhiều Màu', 'bo-hoa-mix-nhieu-mau', 'SP40', 'Bó hoa mix nhiều màu rực rỡ', 480000.00, 430000.00, 38, 0, 'active', 0, 5, 5.00, 0, NULL, NULL, '2025-12-07 09:36:50', '2025-12-08 13:55:14'),
(41, 6, 'Bó Hoa Hồng Vàng', 'bo-hoa-hong-vang', 'SP41', 'Bó hoa hồng vàng tươi vui', 430000.00, 390000.00, 40, 0, 'active', 0, 5, 5.00, 0, NULL, NULL, '2025-12-07 09:36:50', '2025-12-08 13:55:14'),
(42, 6, 'Bó Hoa Đỏ Trắng', 'bo-hoa-do-trang', 'SP42', 'Bó hoa kết hợp đỏ trắng', 460000.00, 420000.00, 36, 0, 'active', 0, 5, 5.00, 0, NULL, NULL, '2025-12-07 09:36:50', '2025-12-08 13:55:14'),
(43, 6, 'Bó Hoa Đặc Biệt', 'bo-hoa-dac-biet', 'SP43', 'Bó hoa đặc biệt với thiết kế độc đáo', 550000.00, 500000.00, 30, 1, 'active', 0, 5, 5.00, 0, NULL, NULL, '2025-12-07 09:36:50', '2025-12-08 13:55:14'),
(44, 7, 'Hoa Lan Hồ Điệp Trắng', 'hoa-lan-ho-diep-trang', 'SP44', 'Chậu lan hồ điệp trắng cao cấp', 800000.00, 750000.00, 15, 1, 'active', 0, 5, 5.00, 0, NULL, NULL, '2025-12-07 09:36:50', '2025-12-08 13:55:14'),
(45, 7, 'Hoa Lan Hồ Điệp Hồng', 'hoa-lan-ho-diep-hong', 'SP45', 'Chậu lan hồ điệp hồng đẹp mắt', 850000.00, 800000.00, 12, 1, 'active', 0, 5, 5.00, 0, NULL, NULL, '2025-12-07 09:36:50', '2025-12-08 13:55:14'),
(46, 7, 'Hoa Lan Hồ Điệp Vàng', 'hoa-lan-ho-diep-vang', 'SP46', 'Chậu lan hồ điệp vàng rực rỡ', 820000.00, 770000.00, 14, 0, 'active', 0, 5, 5.00, 0, NULL, NULL, '2025-12-07 09:36:50', '2025-12-08 13:55:14'),
(47, 7, 'Hoa Lan Hồ Điệp Tím', 'hoa-lan-ho-diep-tim', 'SP47', 'Chậu lan hồ điệp tím thanh lịch', 880000.00, 830000.00, 10, 1, 'active', 0, 5, 5.00, 0, NULL, NULL, '2025-12-07 09:36:50', '2025-12-08 13:55:14'),
(48, 7, 'Hoa Lan Hồ Điệp Mix', 'hoa-lan-ho-diep-mix', 'SP48', 'Chậu lan hồ điệp mix nhiều màu', 900000.00, 850000.00, 8, 1, 'active', 0, 5, 5.00, 0, NULL, NULL, '2025-12-07 09:36:50', '2025-12-08 13:55:14'),
(49, 7, 'Hoa Lan Hồ Điệp Đỏ', 'hoa-lan-ho-diep-do', 'SP49', 'Chậu lan hồ điệp đỏ nổi bật', 870000.00, 820000.00, 11, 0, 'active', 0, 5, 5.00, 0, NULL, NULL, '2025-12-07 09:36:50', '2025-12-08 13:55:14');

-- --------------------------------------------------------

--
-- Table structure for table `product_attributes`
--

CREATE TABLE `product_attributes` (
  `id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `attribute_name` varchar(100) NOT NULL,
  `attribute_value` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `product_attributes`
--

INSERT INTO `product_attributes` (`id`, `product_id`, `attribute_name`, `attribute_value`) VALUES
(1, 1, 'Loại hoa', 'Hồng đỏ, Baby trắng, Lá phụ'),
(2, 1, 'Kích thước', 'Cao 50cm x Rộng 40cm'),
(3, 1, 'Màu sắc', 'Đỏ chủ đạo'),
(4, 1, 'Dịp sử dụng', 'Sinh nhật, Kỷ niệm, Tặng người yêu'),
(5, 1, 'Bao bì', 'Giấy kraft cao cấp, ruy băng lụa'),
(6, 1, 'Hoa tươi', '100% hoa tươi nhập khẩu'),
(7, 2, 'Loại hoa', 'Mix nhiều loại hoa tươi'),
(8, 2, 'Kích thước', 'Cao 45cm x Rộng 35cm'),
(9, 2, 'Màu sắc', 'Nhiều màu rực rỡ'),
(10, 2, 'Dịp sử dụng', 'Sinh nhật, Chúc mừng'),
(11, 2, 'Bao bì', 'Giỏ mây, giấy bọc cao cấp'),
(12, 2, 'Hoa tươi', '100% hoa tươi'),
(13, 3, 'Loại hoa', 'Hồng hồng, Baby breath, Lá xanh'),
(14, 3, 'Kích thước', 'Cao 55cm x Rộng 45cm'),
(15, 3, 'Màu sắc', 'Hồng ngọt ngào'),
(16, 3, 'Dịp sử dụng', 'Sinh nhật, Tặng bạn gái'),
(17, 3, 'Bao bì', 'Lẵng mây, ruy băng hồng'),
(18, 3, 'Hoa tươi', '100% hoa tươi nhập khẩu'),
(19, 4, 'Loại hoa', 'Hồng trắng, Baby breath'),
(20, 4, 'Kích thước', 'Cao 48cm x Rộng 38cm'),
(21, 4, 'Màu sắc', 'Trắng tinh khôi'),
(22, 4, 'Dịp sử dụng', 'Sinh nhật, Tặng mẹ'),
(23, 4, 'Bao bì', 'Giấy trắng cao cấp, ruy băng trắng'),
(24, 4, 'Hoa tươi', '100% hoa tươi'),
(25, 5, 'Loại hoa', 'Hoa vàng, Baby breath, Lá xanh'),
(26, 5, 'Kích thước', 'Cao 50cm x Rộng 40cm'),
(27, 5, 'Màu sắc', 'Vàng rực rỡ'),
(28, 5, 'Dịp sử dụng', 'Sinh nhật, Chúc mừng'),
(29, 5, 'Bao bì', 'Giỏ mây, giấy vàng'),
(30, 5, 'Hoa tươi', '100% hoa tươi'),
(31, 6, 'Loại hoa', 'Hồng đỏ lớn, Baby breath, Lá phụ'),
(32, 6, 'Kích thước', 'Cao 60cm x Rộng 50cm'),
(33, 6, 'Màu sắc', 'Đỏ nổi bật'),
(34, 6, 'Dịp sử dụng', 'Sinh nhật đặc biệt, Kỷ niệm'),
(35, 6, 'Bao bì', 'Giấy kraft cao cấp, ruy băng đỏ'),
(36, 6, 'Hoa tươi', '100% hoa tươi nhập khẩu'),
(37, 7, 'Loại hoa', 'Mix nhiều loại hoa'),
(38, 7, 'Kích thước', 'Cao 52cm x Rộng 42cm'),
(39, 7, 'Màu sắc', 'Đa dạng màu sắc'),
(40, 7, 'Dịp sử dụng', 'Sinh nhật, Chúc mừng'),
(41, 7, 'Bao bì', 'Lẵng mây, giấy bọc đẹp'),
(42, 7, 'Hoa tươi', '100% hoa tươi'),
(43, 8, 'Loại hoa', 'Hồng hồng, Baby breath'),
(44, 8, 'Kích thước', 'Cao 46cm x Rộng 36cm'),
(45, 8, 'Màu sắc', 'Hồng ngọt ngào'),
(46, 8, 'Dịp sử dụng', 'Sinh nhật, Tặng người yêu'),
(47, 8, 'Bao bì', 'Giấy hồng, ruy băng lụa'),
(48, 8, 'Hoa tươi', '100% hoa tươi'),
(49, 9, 'Loại hoa', 'Hồng đỏ, Hồng trắng, Baby breath'),
(50, 9, 'Kích thước', 'Cao 50cm x Rộng 40cm'),
(51, 9, 'Màu sắc', 'Đỏ và trắng hài hòa'),
(52, 9, 'Dịp sử dụng', 'Sinh nhật, Kỷ niệm'),
(53, 9, 'Bao bì', 'Giỏ mây, giấy bọc cao cấp'),
(54, 9, 'Hoa tươi', '100% hoa tươi'),
(55, 10, 'Loại hoa', 'Mix cao cấp nhiều loại'),
(56, 10, 'Kích thước', 'Cao 65cm x Rộng 55cm'),
(57, 10, 'Màu sắc', 'Nhiều màu đặc biệt'),
(58, 10, 'Dịp sử dụng', 'Sinh nhật đặc biệt, Kỷ niệm quan trọng'),
(59, 10, 'Bao bì', 'Bao bì cao cấp, ruy băng lụa'),
(60, 10, 'Hoa tươi', '100% hoa tươi nhập khẩu cao cấp'),
(61, 11, 'Loại hoa', 'Hồng đỏ, Baby breath, Lá xanh'),
(62, 11, 'Kích thước', 'Cao 45cm x Rộng 35cm'),
(63, 11, 'Màu sắc', 'Đỏ và hồng'),
(64, 11, 'Dịp sử dụng', 'Ngày 8/3, Tặng phụ nữ'),
(65, 11, 'Bao bì', 'Giấy đỏ, ruy băng lụa'),
(66, 11, 'Hoa tươi', '100% hoa tươi'),
(67, 12, 'Loại hoa', 'Mix nhiều loại hoa'),
(68, 12, 'Kích thước', 'Cao 50cm x Rộng 40cm'),
(69, 12, 'Màu sắc', 'Nhiều màu rực rỡ'),
(70, 12, 'Dịp sử dụng', 'Ngày 8/3, Chúc mừng'),
(71, 12, 'Bao bì', 'Giỏ mây, giấy bọc đẹp'),
(72, 12, 'Hoa tươi', '100% hoa tươi'),
(73, 13, 'Loại hoa', 'Hồng hồng, Baby breath'),
(74, 13, 'Kích thước', 'Cao 48cm x Rộng 38cm'),
(75, 13, 'Màu sắc', 'Hồng ngọt ngào'),
(76, 13, 'Dịp sử dụng', 'Ngày 8/3, Tặng bạn gái'),
(77, 13, 'Bao bì', 'Giấy hồng, ruy băng'),
(78, 13, 'Hoa tươi', '100% hoa tươi'),
(79, 14, 'Loại hoa', 'Mix cao cấp'),
(80, 14, 'Kích thước', 'Cao 55cm x Rộng 45cm'),
(81, 14, 'Màu sắc', 'Nhiều màu đẹp mắt'),
(82, 14, 'Dịp sử dụng', 'Ngày 8/3 đặc biệt'),
(83, 14, 'Bao bì', 'Lẵng mây cao cấp'),
(84, 14, 'Hoa tươi', '100% hoa tươi nhập khẩu'),
(85, 15, 'Loại hoa', 'Hoa vàng, Baby breath'),
(86, 15, 'Kích thước', 'Cao 46cm x Rộng 36cm'),
(87, 15, 'Màu sắc', 'Vàng tươi vui'),
(88, 15, 'Dịp sử dụng', 'Ngày 8/3, Chúc mừng'),
(89, 15, 'Bao bì', 'Giấy vàng, ruy băng'),
(90, 15, 'Hoa tươi', '100% hoa tươi'),
(91, 16, 'Loại hoa', 'Hồng trắng, Hồng hồng'),
(92, 16, 'Kích thước', 'Cao 50cm x Rộng 40cm'),
(93, 16, 'Màu sắc', 'Trắng và hồng'),
(94, 16, 'Dịp sử dụng', 'Ngày 8/3, Tặng mẹ'),
(95, 16, 'Bao bì', 'Giỏ mây, giấy bọc'),
(96, 16, 'Hoa tươi', '100% hoa tươi'),
(97, 17, 'Loại hoa', 'Mix nhiều màu'),
(98, 17, 'Kích thước', 'Cao 48cm x Rộng 38cm'),
(99, 17, 'Màu sắc', 'Nhiều màu rực rỡ'),
(100, 17, 'Dịp sử dụng', 'Ngày 8/3, Chúc mừng'),
(101, 17, 'Bao bì', 'Giấy bọc đẹp'),
(102, 17, 'Hoa tươi', '100% hoa tươi'),
(103, 18, 'Loại hoa', 'Mix cao cấp đặc biệt'),
(104, 18, 'Kích thước', 'Cao 58cm x Rộng 48cm'),
(105, 18, 'Màu sắc', 'Nhiều màu sang trọng'),
(106, 18, 'Dịp sử dụng', 'Ngày 8/3 cao cấp'),
(107, 18, 'Bao bì', 'Lẵng cao cấp, ruy băng lụa'),
(108, 18, 'Hoa tươi', '100% hoa tươi nhập khẩu'),
(109, 19, 'Loại hoa', 'Mix nhiều loại hoa lớn'),
(110, 19, 'Kích thước', 'Cao 150cm x Rộng 100cm'),
(111, 19, 'Màu sắc', 'Nhiều màu nổi bật'),
(112, 19, 'Dịp sử dụng', 'Khai trương, Khánh thành'),
(113, 19, 'Bao bì', 'Kệ hoa chuyên dụng'),
(114, 19, 'Hoa tươi', '100% hoa tươi nhập khẩu'),
(115, 20, 'Loại hoa', 'Hoa vàng, Hoa cam, Lá xanh'),
(116, 20, 'Kích thước', 'Cao 140cm x Rộng 90cm'),
(117, 20, 'Màu sắc', 'Vàng và cam rực rỡ'),
(118, 20, 'Dịp sử dụng', 'Khai trương, Chúc mừng'),
(119, 20, 'Bao bì', 'Kệ hoa vàng'),
(120, 20, 'Hoa tươi', '100% hoa tươi'),
(121, 21, 'Loại hoa', 'Mix hoa tươi'),
(122, 21, 'Kích thước', 'Cao 80cm x Rộng 60cm'),
(123, 21, 'Màu sắc', 'Nhiều màu'),
(124, 21, 'Dịp sử dụng', 'Khai trương'),
(125, 21, 'Bao bì', 'Lẵng mây lớn'),
(126, 21, 'Hoa tươi', '100% hoa tươi'),
(127, 22, 'Loại hoa', 'Mix đa dạng'),
(128, 22, 'Kích thước', 'Cao 145cm x Rộng 95cm'),
(129, 22, 'Màu sắc', 'Nhiều màu đẹp'),
(130, 22, 'Dịp sử dụng', 'Khai trương, Khánh thành'),
(131, 22, 'Bao bì', 'Kệ hoa mix'),
(132, 22, 'Hoa tươi', '100% hoa tươi'),
(133, 23, 'Loại hoa', 'Mix hoa tươi'),
(134, 23, 'Kích thước', 'Cao 70cm x Rộng 50cm'),
(135, 23, 'Màu sắc', 'Nhiều màu'),
(136, 23, 'Dịp sử dụng', 'Khai trương'),
(137, 23, 'Bao bì', 'Giỏ mây lớn'),
(138, 23, 'Hoa tươi', '100% hoa tươi'),
(139, 24, 'Loại hoa', 'Hoa đỏ, Hoa vàng'),
(140, 24, 'Kích thước', 'Cao 155cm x Rộng 105cm'),
(141, 24, 'Màu sắc', 'Đỏ và vàng nổi bật'),
(142, 24, 'Dịp sử dụng', 'Khai trương lớn'),
(143, 24, 'Bao bì', 'Kệ hoa đỏ vàng'),
(144, 24, 'Hoa tươi', '100% hoa tươi nhập khẩu'),
(145, 25, 'Loại hoa', 'Mix hoa tươi lớn'),
(146, 25, 'Kích thước', 'Cao 90cm x Rộng 70cm'),
(147, 25, 'Màu sắc', 'Nhiều màu'),
(148, 25, 'Dịp sử dụng', 'Khai trương'),
(149, 25, 'Bao bì', 'Lẵng lớn'),
(150, 25, 'Hoa tươi', '100% hoa tươi'),
(151, 26, 'Loại hoa', 'Mix cao cấp đặc biệt'),
(152, 26, 'Kích thước', 'Cao 180cm x Rộng 120cm'),
(153, 26, 'Màu sắc', 'Nhiều màu sang trọng'),
(154, 26, 'Dịp sử dụng', 'Khai trương đặc biệt'),
(155, 26, 'Bao bì', 'Kệ hoa cao cấp'),
(156, 26, 'Hoa tươi', '100% hoa tươi nhập khẩu cao cấp'),
(157, 27, 'Loại hoa', 'Mix nhiều loại hoa'),
(158, 27, 'Kích thước', 'Cao 50cm x Rộng 40cm'),
(159, 27, 'Màu sắc', 'Nhiều màu rực rỡ'),
(160, 27, 'Dịp sử dụng', 'Tốt nghiệp, Chúc mừng'),
(161, 27, 'Bao bì', 'Giấy bọc đẹp'),
(162, 27, 'Hoa tươi', '100% hoa tươi'),
(169, 29, 'Loại hoa', 'Hoa vàng, Baby breath'),
(170, 29, 'Kích thước', 'Cao 48cm x Rộng 38cm'),
(171, 29, 'Màu sắc', 'Vàng tươi vui'),
(172, 29, 'Dịp sử dụng', 'Tốt nghiệp, Chúc mừng'),
(173, 29, 'Bao bì', 'Giấy vàng'),
(174, 29, 'Hoa tươi', '100% hoa tươi'),
(175, 30, 'Loại hoa', 'Mix cao cấp'),
(176, 30, 'Kích thước', 'Cao 60cm x Rộng 50cm'),
(177, 30, 'Màu sắc', 'Nhiều màu đẹp'),
(178, 30, 'Dịp sử dụng', 'Tốt nghiệp đặc biệt'),
(179, 30, 'Bao bì', 'Lẵng mây cao cấp'),
(180, 30, 'Hoa tươi', '100% hoa tươi nhập khẩu'),
(181, 31, 'Loại hoa', 'Mix đặc biệt'),
(182, 31, 'Kích thước', 'Cao 52cm x Rộng 42cm'),
(183, 31, 'Màu sắc', 'Nhiều màu'),
(184, 31, 'Dịp sử dụng', 'Tốt nghiệp'),
(185, 31, 'Bao bì', 'Giấy bọc đẹp'),
(186, 31, 'Hoa tươi', '100% hoa tươi'),
(199, 34, 'Loại hoa', 'Hoa trắng'),
(200, 34, 'Kích thước', 'Cao 100cm x Rộng 70cm'),
(201, 34, 'Màu sắc', 'Trắng'),
(202, 34, 'Dịp sử dụng', 'Chia buồn'),
(203, 34, 'Bao bì', 'Giỏ mây trắng'),
(204, 34, 'Hoa tươi', '100% hoa tươi'),
(205, 35, 'Loại hoa', 'Hoa trắng lớn'),
(206, 35, 'Kích thước', 'Cao 140cm x Rộng 100cm'),
(207, 35, 'Màu sắc', 'Trắng'),
(208, 35, 'Dịp sử dụng', 'Chia buồn lớn'),
(209, 35, 'Bao bì', 'Kệ hoa lớn'),
(210, 35, 'Hoa tươi', '100% hoa tươi'),
(211, 36, 'Loại hoa', 'Hoa trắng'),
(212, 36, 'Kích thước', 'Cao 110cm x Rộng 80cm'),
(213, 36, 'Màu sắc', 'Trắng'),
(214, 36, 'Dịp sử dụng', 'Chia buồn'),
(215, 36, 'Bao bì', 'Lẵng trắng'),
(216, 36, 'Hoa tươi', '100% hoa tươi'),
(217, 37, 'Loại hoa', 'Hồng trắng, Baby breath'),
(218, 37, 'Kích thước', 'Cao 45cm x Rộng 35cm'),
(219, 37, 'Màu sắc', 'Trắng tinh khôi'),
(220, 37, 'Dịp sử dụng', 'Tặng quà, Chúc mừng'),
(221, 37, 'Bao bì', 'Giấy trắng, ruy băng'),
(222, 37, 'Hoa tươi', '100% hoa tươi'),
(223, 38, 'Loại hoa', 'Hồng đỏ, Baby breath'),
(224, 38, 'Kích thước', 'Cao 48cm x Rộng 38cm'),
(225, 38, 'Màu sắc', 'Đỏ tươi thắm'),
(226, 38, 'Dịp sử dụng', 'Tặng người yêu, Kỷ niệm'),
(227, 38, 'Bao bì', 'Giấy đỏ, ruy băng lụa'),
(228, 38, 'Hoa tươi', '100% hoa tươi'),
(229, 39, 'Loại hoa', 'Hồng hồng, Baby breath'),
(230, 39, 'Kích thước', 'Cao 46cm x Rộng 36cm'),
(231, 39, 'Màu sắc', 'Hồng ngọt ngào'),
(232, 39, 'Dịp sử dụng', 'Tặng quà, Chúc mừng'),
(233, 39, 'Bao bì', 'Giấy hồng, ruy băng'),
(234, 39, 'Hoa tươi', '100% hoa tươi'),
(235, 40, 'Loại hoa', 'Mix nhiều loại'),
(236, 40, 'Kích thước', 'Cao 50cm x Rộng 40cm'),
(237, 40, 'Màu sắc', 'Nhiều màu rực rỡ'),
(238, 40, 'Dịp sử dụng', 'Tặng quà, Chúc mừng'),
(239, 40, 'Bao bì', 'Giấy bọc đẹp'),
(240, 40, 'Hoa tươi', '100% hoa tươi'),
(241, 41, 'Loại hoa', 'Hồng vàng, Baby breath'),
(242, 41, 'Kích thước', 'Cao 47cm x Rộng 37cm'),
(243, 41, 'Màu sắc', 'Vàng tươi vui'),
(244, 41, 'Dịp sử dụng', 'Tặng quà, Chúc mừng'),
(245, 41, 'Bao bì', 'Giấy vàng, ruy băng'),
(246, 41, 'Hoa tươi', '100% hoa tươi'),
(247, 42, 'Loại hoa', 'Hồng đỏ, Hồng trắng'),
(248, 42, 'Kích thước', 'Cao 49cm x Rộng 39cm'),
(249, 42, 'Màu sắc', 'Đỏ và trắng'),
(250, 42, 'Dịp sử dụng', 'Tặng quà, Kỷ niệm'),
(251, 42, 'Bao bì', 'Giấy đỏ trắng'),
(252, 42, 'Hoa tươi', '100% hoa tươi'),
(253, 43, 'Loại hoa', 'Mix cao cấp'),
(254, 43, 'Kích thước', 'Cao 55cm x Rộng 45cm'),
(255, 43, 'Màu sắc', 'Nhiều màu đẹp'),
(256, 43, 'Dịp sử dụng', 'Tặng quà đặc biệt'),
(257, 43, 'Bao bì', 'Bao bì cao cấp'),
(258, 43, 'Hoa tươi', '100% hoa tươi nhập khẩu'),
(259, 44, 'Loại hoa', 'Lan hồ điệp trắng'),
(260, 44, 'Kích thước', 'Cao 60cm x Rộng 40cm'),
(261, 44, 'Màu sắc', 'Trắng tinh khôi'),
(262, 44, 'Dịp sử dụng', 'Tặng quà cao cấp, Kỷ niệm'),
(263, 44, 'Bao bì', 'Chậu gốm cao cấp'),
(264, 44, 'Hoa tươi', '100% lan hồ điệp nhập khẩu'),
(265, 45, 'Loại hoa', 'Lan hồ điệp hồng'),
(266, 45, 'Kích thước', 'Cao 65cm x Rộng 45cm'),
(267, 45, 'Màu sắc', 'Hồng đẹp mắt'),
(268, 45, 'Dịp sử dụng', 'Tặng quà cao cấp'),
(269, 45, 'Bao bì', 'Chậu gốm hồng'),
(270, 45, 'Hoa tươi', '100% lan hồ điệp nhập khẩu'),
(271, 46, 'Loại hoa', 'Lan hồ điệp vàng'),
(272, 46, 'Kích thước', 'Cao 62cm x Rộng 42cm'),
(273, 46, 'Màu sắc', 'Vàng rực rỡ'),
(274, 46, 'Dịp sử dụng', 'Tặng quà, Chúc mừng'),
(275, 46, 'Bao bì', 'Chậu gốm vàng'),
(276, 46, 'Hoa tươi', '100% lan hồ điệp nhập khẩu'),
(277, 47, 'Loại hoa', 'Lan hồ điệp tím'),
(278, 47, 'Kích thước', 'Cao 63cm x Rộng 43cm'),
(279, 47, 'Màu sắc', 'Tím thanh lịch'),
(280, 47, 'Dịp sử dụng', 'Tặng quà cao cấp'),
(281, 47, 'Bao bì', 'Chậu gốm tím'),
(282, 47, 'Hoa tươi', '100% lan hồ điệp nhập khẩu'),
(283, 48, 'Loại hoa', 'Lan hồ điệp mix'),
(284, 48, 'Kích thước', 'Cao 68cm x Rộng 48cm'),
(285, 48, 'Màu sắc', 'Nhiều màu đẹp'),
(286, 48, 'Dịp sử dụng', 'Tặng quà đặc biệt'),
(287, 48, 'Bao bì', 'Chậu gốm cao cấp'),
(288, 48, 'Hoa tươi', '100% lan hồ điệp nhập khẩu'),
(289, 49, 'Loại hoa', 'Lan hồ điệp đỏ'),
(290, 49, 'Kích thước', 'Cao 64cm x Rộng 44cm'),
(291, 49, 'Màu sắc', 'Đỏ nổi bật'),
(292, 49, 'Dịp sử dụng', 'Tặng quà, Kỷ niệm'),
(293, 49, 'Bao bì', 'Chậu gốm đỏ'),
(294, 49, 'Hoa tươi', '100% lan hồ điệp nhập khẩu'),
(307, 32, 'Loại hoa', 'Hoa trắng, Lá xanh'),
(308, 32, 'Kích thước', 'Cao 120cm x Rộng 80cm'),
(309, 32, 'Màu sắc', 'Trắng trang trọng'),
(310, 32, 'Dịp sử dụng', 'Chia buồn, Viếng'),
(311, 32, 'Bao bì', 'Kệ hoa trắng'),
(312, 32, 'Hoa tươi', '100% hoa tươi'),
(313, 33, 'Loại hoa', 'Hoa trắng, Hoa vàng'),
(314, 33, 'Kích thước', 'Cao 130cm x Rộng 90cm'),
(315, 33, 'Màu sắc', 'Trắng và vàng'),
(316, 33, 'Dịp sử dụng', 'Chia buồn'),
(317, 33, 'Bao bì', 'Kệ hoa trắng vàng'),
(318, 33, 'Hoa tươi', '100% hoa tươi'),
(319, 28, 'Loại hoa', 'Mix hoa tươi'),
(320, 28, 'Kích thước', 'Cao 55cm x Rộng 45cm'),
(321, 28, 'Màu sắc', 'Nhiều màu'),
(322, 28, 'Dịp sử dụng', 'Tốt nghiệp'),
(323, 28, 'Bao bì', 'Giỏ mây'),
(324, 28, 'Hoa tươi', '100% hoa tươi');

-- --------------------------------------------------------

--
-- Table structure for table `product_images`
--

CREATE TABLE `product_images` (
  `id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `image_url` varchar(255) NOT NULL,
  `is_primary` tinyint(1) DEFAULT 0,
  `display_order` int(11) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `product_images`
--

INSERT INTO `product_images` (`id`, `product_id`, `image_url`, `is_primary`, `display_order`, `created_at`) VALUES
(6, 2, '/assets/images/products/hoa2.png', 1, 0, '2025-12-07 09:36:50'),
(7, 2, '/assets/images/products/hoa1.png', 0, 1, '2025-12-07 09:36:50'),
(8, 2, '/assets/images/products/hoa3.jpg', 0, 2, '2025-12-07 09:36:50'),
(9, 2, '/assets/images/products/hoa4.jpg', 0, 3, '2025-12-07 09:36:50'),
(10, 2, '/assets/images/products/hoa5.jpg', 0, 4, '2025-12-07 09:36:50'),
(11, 3, '/assets/images/products/hoa3.jpg', 1, 0, '2025-12-07 09:36:50'),
(12, 3, '/assets/images/products/hoa1.png', 0, 1, '2025-12-07 09:36:50'),
(13, 3, '/assets/images/products/hoa2.png', 0, 2, '2025-12-07 09:36:50'),
(14, 3, '/assets/images/products/hoa4.jpg', 0, 3, '2025-12-07 09:36:50'),
(15, 3, '/assets/images/products/hoa5.jpg', 0, 4, '2025-12-07 09:36:50'),
(16, 4, '/assets/images/products/hoa4.jpg', 1, 0, '2025-12-07 09:36:50'),
(17, 4, '/assets/images/products/hoa1.png', 0, 1, '2025-12-07 09:36:50'),
(18, 4, '/assets/images/products/hoa2.png', 0, 2, '2025-12-07 09:36:50'),
(19, 4, '/assets/images/products/hoa3.jpg', 0, 3, '2025-12-07 09:36:50'),
(20, 5, '/assets/images/products/hoa5.jpg', 1, 0, '2025-12-07 09:36:50'),
(21, 5, '/assets/images/products/hoa1.png', 0, 1, '2025-12-07 09:36:50'),
(22, 5, '/assets/images/products/hoa2.png', 0, 2, '2025-12-07 09:36:50'),
(23, 5, '/assets/images/products/hoa3.jpg', 0, 3, '2025-12-07 09:36:50'),
(24, 5, '/assets/images/products/hoa4.jpg', 0, 4, '2025-12-07 09:36:50'),
(25, 6, '/assets/images/products/hoa1.png', 1, 0, '2025-12-07 09:36:50'),
(26, 6, '/assets/images/products/hoa2.png', 0, 1, '2025-12-07 09:36:50'),
(27, 6, '/assets/images/products/hoa3.jpg', 0, 2, '2025-12-07 09:36:50'),
(28, 6, '/assets/images/products/hoa4.jpg', 0, 3, '2025-12-07 09:36:50'),
(29, 6, '/assets/images/products/hoa5.jpg', 0, 4, '2025-12-07 09:36:50'),
(30, 7, '/assets/images/products/hoa2.png', 1, 0, '2025-12-07 09:36:50'),
(31, 7, '/assets/images/products/hoa1.png', 0, 1, '2025-12-07 09:36:50'),
(32, 7, '/assets/images/products/hoa3.jpg', 0, 2, '2025-12-07 09:36:50'),
(33, 7, '/assets/images/products/hoa4.jpg', 0, 3, '2025-12-07 09:36:50'),
(35, 8, '/assets/images/products/hoa1.png', 0, 1, '2025-12-07 09:36:50'),
(36, 8, '/assets/images/products/hoa2.png', 0, 2, '2025-12-07 09:36:50'),
(37, 8, '/assets/images/products/hoa4.jpg', 0, 3, '2025-12-07 09:36:50'),
(38, 8, '/assets/images/products/hoa5.jpg', 0, 4, '2025-12-07 09:36:50'),
(39, 9, '/assets/images/products/hoa4.jpg', 1, 0, '2025-12-07 09:36:50'),
(40, 9, '/assets/images/products/hoa1.png', 0, 1, '2025-12-07 09:36:50'),
(41, 9, '/assets/images/products/hoa2.png', 0, 2, '2025-12-07 09:36:50'),
(42, 9, '/assets/images/products/hoa3.jpg', 0, 3, '2025-12-07 09:36:50'),
(43, 10, '/assets/images/products/hoa5.jpg', 1, 0, '2025-12-07 09:36:50'),
(44, 10, '/assets/images/products/hoa1.png', 0, 1, '2025-12-07 09:36:50'),
(45, 10, '/assets/images/products/hoa2.png', 0, 2, '2025-12-07 09:36:50'),
(46, 10, '/assets/images/products/hoa3.jpg', 0, 3, '2025-12-07 09:36:50'),
(47, 10, '/assets/images/products/hoa4.jpg', 0, 4, '2025-12-07 09:36:50'),
(48, 11, '/assets/images/products/hoa1.png', 1, 0, '2025-12-07 09:36:50'),
(49, 11, '/assets/images/products/hoa2.png', 0, 1, '2025-12-07 09:36:50'),
(50, 11, '/assets/images/products/hoa3.jpg', 0, 2, '2025-12-07 09:36:50'),
(51, 11, '/assets/images/products/hoa4.jpg', 0, 3, '2025-12-07 09:36:50'),
(52, 12, '/assets/images/products/hoa2.png', 1, 0, '2025-12-07 09:36:50'),
(53, 12, '/assets/images/products/hoa1.png', 0, 1, '2025-12-07 09:36:50'),
(54, 12, '/assets/images/products/hoa3.jpg', 0, 2, '2025-12-07 09:36:50'),
(55, 12, '/assets/images/products/hoa4.jpg', 0, 3, '2025-12-07 09:36:50'),
(56, 12, '/assets/images/products/hoa5.jpg', 0, 4, '2025-12-07 09:36:50'),
(57, 13, '/assets/images/products/hoa3.jpg', 1, 0, '2025-12-07 09:36:50'),
(58, 13, '/assets/images/products/hoa1.png', 0, 1, '2025-12-07 09:36:50'),
(59, 13, '/assets/images/products/hoa2.png', 0, 2, '2025-12-07 09:36:50'),
(60, 13, '/assets/images/products/hoa4.jpg', 0, 3, '2025-12-07 09:36:50'),
(62, 14, '/assets/images/products/hoa1.png', 0, 1, '2025-12-07 09:36:50'),
(63, 14, '/assets/images/products/hoa2.png', 0, 2, '2025-12-07 09:36:50'),
(64, 14, '/assets/images/products/hoa3.jpg', 0, 3, '2025-12-07 09:36:50'),
(65, 14, '/assets/images/products/hoa5.jpg', 0, 4, '2025-12-07 09:36:50'),
(66, 15, '/assets/images/products/hoa5.jpg', 1, 0, '2025-12-07 09:36:50'),
(67, 15, '/assets/images/products/hoa1.png', 0, 1, '2025-12-07 09:36:50'),
(68, 15, '/assets/images/products/hoa2.png', 0, 2, '2025-12-07 09:36:50'),
(69, 15, '/assets/images/products/hoa3.jpg', 0, 3, '2025-12-07 09:36:50'),
(70, 16, '/assets/images/products/hoa1.png', 1, 0, '2025-12-07 09:36:50'),
(71, 16, '/assets/images/products/hoa2.png', 0, 1, '2025-12-07 09:36:50'),
(72, 16, '/assets/images/products/hoa3.jpg', 0, 2, '2025-12-07 09:36:50'),
(73, 16, '/assets/images/products/hoa4.jpg', 0, 3, '2025-12-07 09:36:50'),
(74, 16, '/assets/images/products/hoa5.jpg', 0, 4, '2025-12-07 09:36:50'),
(75, 17, '/assets/images/products/hoa2.png', 1, 0, '2025-12-07 09:36:50'),
(76, 17, '/assets/images/products/hoa1.png', 0, 1, '2025-12-07 09:36:50'),
(77, 17, '/assets/images/products/hoa3.jpg', 0, 2, '2025-12-07 09:36:50'),
(78, 17, '/assets/images/products/hoa4.jpg', 0, 3, '2025-12-07 09:36:50'),
(79, 18, '/assets/images/products/hoa3.jpg', 1, 0, '2025-12-07 09:36:50'),
(80, 18, '/assets/images/products/hoa1.png', 0, 1, '2025-12-07 09:36:50'),
(81, 18, '/assets/images/products/hoa2.png', 0, 2, '2025-12-07 09:36:50'),
(82, 18, '/assets/images/products/hoa4.jpg', 0, 3, '2025-12-07 09:36:50'),
(83, 18, '/assets/images/products/hoa5.jpg', 0, 4, '2025-12-07 09:36:50'),
(84, 19, '/assets/images/products/hoa4.jpg', 1, 0, '2025-12-07 09:36:50'),
(85, 19, '/assets/images/products/hoa1.png', 0, 1, '2025-12-07 09:36:50'),
(86, 19, '/assets/images/products/hoa2.png', 0, 2, '2025-12-07 09:36:50'),
(87, 19, '/assets/images/products/hoa3.jpg', 0, 3, '2025-12-07 09:36:50'),
(88, 19, '/assets/images/products/hoa5.jpg', 0, 4, '2025-12-07 09:36:50'),
(89, 20, '/assets/images/products/hoa5.jpg', 1, 0, '2025-12-07 09:36:50'),
(90, 20, '/assets/images/products/hoa1.png', 0, 1, '2025-12-07 09:36:50'),
(91, 20, '/assets/images/products/hoa2.png', 0, 2, '2025-12-07 09:36:50'),
(92, 20, '/assets/images/products/hoa3.jpg', 0, 3, '2025-12-07 09:36:50'),
(93, 21, '/assets/images/products/hoa1.png', 1, 0, '2025-12-07 09:36:50'),
(94, 21, '/assets/images/products/hoa2.png', 0, 1, '2025-12-07 09:36:50'),
(95, 21, '/assets/images/products/hoa3.jpg', 0, 2, '2025-12-07 09:36:50'),
(96, 21, '/assets/images/products/hoa4.jpg', 0, 3, '2025-12-07 09:36:50'),
(97, 21, '/assets/images/products/hoa5.jpg', 0, 4, '2025-12-07 09:36:50'),
(98, 22, '/assets/images/products/hoa2.png', 1, 0, '2025-12-07 09:36:50'),
(99, 22, '/assets/images/products/hoa1.png', 0, 1, '2025-12-07 09:36:50'),
(100, 22, '/assets/images/products/hoa3.jpg', 0, 2, '2025-12-07 09:36:50'),
(101, 22, '/assets/images/products/hoa4.jpg', 0, 3, '2025-12-07 09:36:50'),
(102, 23, '/assets/images/products/hoa3.jpg', 1, 0, '2025-12-07 09:36:50'),
(103, 23, '/assets/images/products/hoa1.png', 0, 1, '2025-12-07 09:36:50'),
(104, 23, '/assets/images/products/hoa2.png', 0, 2, '2025-12-07 09:36:50'),
(105, 23, '/assets/images/products/hoa4.jpg', 0, 3, '2025-12-07 09:36:50'),
(106, 23, '/assets/images/products/hoa5.jpg', 0, 4, '2025-12-07 09:36:50'),
(107, 24, '/assets/images/products/hoa4.jpg', 1, 0, '2025-12-07 09:36:50'),
(108, 24, '/assets/images/products/hoa1.png', 0, 1, '2025-12-07 09:36:50'),
(109, 24, '/assets/images/products/hoa2.png', 0, 2, '2025-12-07 09:36:50'),
(110, 24, '/assets/images/products/hoa3.jpg', 0, 3, '2025-12-07 09:36:50'),
(111, 25, '/assets/images/products/hoa5.jpg', 1, 0, '2025-12-07 09:36:50'),
(112, 25, '/assets/images/products/hoa1.png', 0, 1, '2025-12-07 09:36:50'),
(113, 25, '/assets/images/products/hoa2.png', 0, 2, '2025-12-07 09:36:50'),
(114, 25, '/assets/images/products/hoa3.jpg', 0, 3, '2025-12-07 09:36:50'),
(115, 25, '/assets/images/products/hoa4.jpg', 0, 4, '2025-12-07 09:36:50'),
(116, 26, '/assets/images/products/hoa1.png', 1, 0, '2025-12-07 09:36:50'),
(117, 26, '/assets/images/products/hoa2.png', 0, 1, '2025-12-07 09:36:50'),
(118, 26, '/assets/images/products/hoa3.jpg', 0, 2, '2025-12-07 09:36:50'),
(119, 26, '/assets/images/products/hoa4.jpg', 0, 3, '2025-12-07 09:36:50'),
(120, 26, '/assets/images/products/hoa5.jpg', 0, 4, '2025-12-07 09:36:50'),
(121, 27, '/assets/images/products/hoa2.png', 1, 0, '2025-12-07 09:36:50'),
(122, 27, '/assets/images/products/hoa1.png', 0, 1, '2025-12-07 09:36:50'),
(123, 27, '/assets/images/products/hoa3.jpg', 0, 2, '2025-12-07 09:36:50'),
(124, 27, '/assets/images/products/hoa4.jpg', 0, 3, '2025-12-07 09:36:50'),
(126, 28, '/assets/images/products/hoa1.png', 0, 1, '2025-12-07 09:36:50'),
(127, 28, '/assets/images/products/hoa2.png', 0, 2, '2025-12-07 09:36:50'),
(128, 28, '/assets/images/products/hoa4.jpg', 0, 3, '2025-12-07 09:36:50'),
(129, 28, '/assets/images/products/hoa5.jpg', 0, 4, '2025-12-07 09:36:50'),
(130, 29, '/assets/images/products/hoa4.jpg', 1, 0, '2025-12-07 09:36:50'),
(131, 29, '/assets/images/products/hoa1.png', 0, 1, '2025-12-07 09:36:50'),
(132, 29, '/assets/images/products/hoa2.png', 0, 2, '2025-12-07 09:36:50'),
(133, 29, '/assets/images/products/hoa3.jpg', 0, 3, '2025-12-07 09:36:50'),
(134, 30, '/assets/images/products/hoa5.jpg', 1, 0, '2025-12-07 09:36:50'),
(135, 30, '/assets/images/products/hoa1.png', 0, 1, '2025-12-07 09:36:50'),
(136, 30, '/assets/images/products/hoa2.png', 0, 2, '2025-12-07 09:36:50'),
(137, 30, '/assets/images/products/hoa3.jpg', 0, 3, '2025-12-07 09:36:50'),
(138, 30, '/assets/images/products/hoa4.jpg', 0, 4, '2025-12-07 09:36:50'),
(139, 31, '/assets/images/products/hoa1.png', 1, 0, '2025-12-07 09:36:50'),
(140, 31, '/assets/images/products/hoa2.png', 0, 1, '2025-12-07 09:36:50'),
(141, 31, '/assets/images/products/hoa3.jpg', 0, 2, '2025-12-07 09:36:50'),
(142, 31, '/assets/images/products/hoa4.jpg', 0, 3, '2025-12-07 09:36:50'),
(144, 32, '/assets/images/products/hoa1.png', 0, 1, '2025-12-07 09:36:51'),
(145, 32, '/assets/images/products/hoa3.jpg', 0, 2, '2025-12-07 09:36:51'),
(146, 32, '/assets/images/products/hoa4.jpg', 0, 3, '2025-12-07 09:36:51'),
(147, 32, '/assets/images/products/hoa5.jpg', 0, 4, '2025-12-07 09:36:51'),
(149, 33, '/assets/images/products/hoa1.png', 0, 1, '2025-12-07 09:36:51'),
(150, 33, '/assets/images/products/hoa2.png', 0, 2, '2025-12-07 09:36:51'),
(151, 33, '/assets/images/products/hoa4.jpg', 0, 3, '2025-12-07 09:36:51'),
(152, 34, '/assets/images/products/hoa4.jpg', 1, 0, '2025-12-07 09:36:51'),
(153, 34, '/assets/images/products/hoa1.png', 0, 1, '2025-12-07 09:36:51'),
(154, 34, '/assets/images/products/hoa2.png', 0, 2, '2025-12-07 09:36:51'),
(155, 34, '/assets/images/products/hoa3.jpg', 0, 3, '2025-12-07 09:36:51'),
(156, 34, '/assets/images/products/hoa5.jpg', 0, 4, '2025-12-07 09:36:51'),
(157, 35, '/assets/images/products/hoa5.jpg', 1, 0, '2025-12-07 09:36:51'),
(158, 35, '/assets/images/products/hoa1.png', 0, 1, '2025-12-07 09:36:51'),
(159, 35, '/assets/images/products/hoa2.png', 0, 2, '2025-12-07 09:36:51'),
(160, 35, '/assets/images/products/hoa3.jpg', 0, 3, '2025-12-07 09:36:51'),
(161, 36, '/assets/images/products/hoa1.png', 1, 0, '2025-12-07 09:36:51'),
(162, 36, '/assets/images/products/hoa2.png', 0, 1, '2025-12-07 09:36:51'),
(163, 36, '/assets/images/products/hoa3.jpg', 0, 2, '2025-12-07 09:36:51'),
(164, 36, '/assets/images/products/hoa4.jpg', 0, 3, '2025-12-07 09:36:51'),
(165, 36, '/assets/images/products/hoa5.jpg', 0, 4, '2025-12-07 09:36:51'),
(166, 37, '/assets/images/products/hoa2.png', 1, 0, '2025-12-07 09:36:51'),
(167, 37, '/assets/images/products/hoa1.png', 0, 1, '2025-12-07 09:36:51'),
(168, 37, '/assets/images/products/hoa3.jpg', 0, 2, '2025-12-07 09:36:51'),
(169, 37, '/assets/images/products/hoa4.jpg', 0, 3, '2025-12-07 09:36:51'),
(170, 38, '/assets/images/products/hoa3.jpg', 1, 0, '2025-12-07 09:36:51'),
(171, 38, '/assets/images/products/hoa1.png', 0, 1, '2025-12-07 09:36:51'),
(172, 38, '/assets/images/products/hoa2.png', 0, 2, '2025-12-07 09:36:51'),
(173, 38, '/assets/images/products/hoa4.jpg', 0, 3, '2025-12-07 09:36:51'),
(174, 38, '/assets/images/products/hoa5.jpg', 0, 4, '2025-12-07 09:36:51'),
(175, 39, '/assets/images/products/hoa4.jpg', 1, 0, '2025-12-07 09:36:51'),
(176, 39, '/assets/images/products/hoa1.png', 0, 1, '2025-12-07 09:36:51'),
(177, 39, '/assets/images/products/hoa2.png', 0, 2, '2025-12-07 09:36:51'),
(178, 39, '/assets/images/products/hoa3.jpg', 0, 3, '2025-12-07 09:36:51'),
(179, 40, '/assets/images/products/hoa5.jpg', 1, 0, '2025-12-07 09:36:51'),
(180, 40, '/assets/images/products/hoa1.png', 0, 1, '2025-12-07 09:36:51'),
(181, 40, '/assets/images/products/hoa2.png', 0, 2, '2025-12-07 09:36:51'),
(182, 40, '/assets/images/products/hoa3.jpg', 0, 3, '2025-12-07 09:36:51'),
(183, 40, '/assets/images/products/hoa4.jpg', 0, 4, '2025-12-07 09:36:51'),
(184, 41, '/assets/images/products/hoa1.png', 1, 0, '2025-12-07 09:36:51'),
(185, 41, '/assets/images/products/hoa2.png', 0, 1, '2025-12-07 09:36:51'),
(186, 41, '/assets/images/products/hoa3.jpg', 0, 2, '2025-12-07 09:36:51'),
(187, 41, '/assets/images/products/hoa4.jpg', 0, 3, '2025-12-07 09:36:51'),
(188, 42, '/assets/images/products/hoa2.png', 1, 0, '2025-12-07 09:36:51'),
(189, 42, '/assets/images/products/hoa1.png', 0, 1, '2025-12-07 09:36:51'),
(190, 42, '/assets/images/products/hoa3.jpg', 0, 2, '2025-12-07 09:36:51'),
(191, 42, '/assets/images/products/hoa4.jpg', 0, 3, '2025-12-07 09:36:51'),
(192, 42, '/assets/images/products/hoa5.jpg', 0, 4, '2025-12-07 09:36:51'),
(193, 43, '/assets/images/products/hoa3.jpg', 1, 0, '2025-12-07 09:36:51'),
(194, 43, '/assets/images/products/hoa1.png', 0, 1, '2025-12-07 09:36:51'),
(195, 43, '/assets/images/products/hoa2.png', 0, 2, '2025-12-07 09:36:51'),
(196, 43, '/assets/images/products/hoa4.jpg', 0, 3, '2025-12-07 09:36:51'),
(197, 44, '/assets/images/products/hoa4.jpg', 1, 0, '2025-12-07 09:36:51'),
(198, 44, '/assets/images/products/hoa1.png', 0, 1, '2025-12-07 09:36:51'),
(199, 44, '/assets/images/products/hoa2.png', 0, 2, '2025-12-07 09:36:51'),
(200, 44, '/assets/images/products/hoa3.jpg', 0, 3, '2025-12-07 09:36:51'),
(201, 44, '/assets/images/products/hoa5.jpg', 0, 4, '2025-12-07 09:36:51'),
(202, 45, '/assets/images/products/hoa5.jpg', 1, 0, '2025-12-07 09:36:51'),
(203, 45, '/assets/images/products/hoa1.png', 0, 1, '2025-12-07 09:36:51'),
(204, 45, '/assets/images/products/hoa2.png', 0, 2, '2025-12-07 09:36:51'),
(205, 45, '/assets/images/products/hoa3.jpg', 0, 3, '2025-12-07 09:36:51'),
(206, 46, '/assets/images/products/hoa1.png', 1, 0, '2025-12-07 09:36:51'),
(207, 46, '/assets/images/products/hoa2.png', 0, 1, '2025-12-07 09:36:51'),
(208, 46, '/assets/images/products/hoa3.jpg', 0, 2, '2025-12-07 09:36:51'),
(209, 46, '/assets/images/products/hoa4.jpg', 0, 3, '2025-12-07 09:36:51'),
(210, 46, '/assets/images/products/hoa5.jpg', 0, 4, '2025-12-07 09:36:51'),
(211, 47, '/assets/images/products/hoa2.png', 1, 0, '2025-12-07 09:36:51'),
(212, 47, '/assets/images/products/hoa1.png', 0, 1, '2025-12-07 09:36:51'),
(213, 47, '/assets/images/products/hoa3.jpg', 0, 2, '2025-12-07 09:36:51'),
(214, 47, '/assets/images/products/hoa4.jpg', 0, 3, '2025-12-07 09:36:51'),
(215, 48, '/assets/images/products/hoa3.jpg', 1, 0, '2025-12-07 09:36:51'),
(216, 48, '/assets/images/products/hoa1.png', 0, 1, '2025-12-07 09:36:51'),
(217, 48, '/assets/images/products/hoa2.png', 0, 2, '2025-12-07 09:36:51'),
(218, 48, '/assets/images/products/hoa4.jpg', 0, 3, '2025-12-07 09:36:51'),
(219, 48, '/assets/images/products/hoa5.jpg', 0, 4, '2025-12-07 09:36:51'),
(220, 49, '/assets/images/products/hoa4.jpg', 1, 0, '2025-12-07 09:36:51'),
(221, 49, '/assets/images/products/hoa1.png', 0, 1, '2025-12-07 09:36:51'),
(222, 49, '/assets/images/products/hoa2.png', 0, 2, '2025-12-07 09:36:51'),
(223, 49, '/assets/images/products/hoa3.jpg', 0, 3, '2025-12-07 09:36:51'),
(233, 14, 'http://localhost/hoaNgocAnh/public/assets/images/products/hoa4.jpg', 1, 0, '2025-12-08 08:36:53'),
(234, 8, 'http://localhost/hoaNgocAnh/public/assets/images/products/hoa3.jpg', 1, 0, '2025-12-08 08:37:05'),
(237, 1, '/assets/images/products/img_6936ceb90aa227.25983440_1765199545.png', 1, 0, '2025-12-08 13:12:25'),
(239, 32, 'http://localhost/hoaNgocAnh/public/assets/images/products/hoa2.png', 1, 0, '2025-12-10 06:37:42'),
(240, 33, 'http://localhost/hoaNgocAnh/public/assets/images/products/hoa3.jpg', 1, 0, '2025-12-10 06:38:03'),
(241, 28, '/assets/images/products/img_693923601badc9.08149057_1765352288.jpg', 1, 0, '2025-12-10 07:38:08');

-- --------------------------------------------------------

--
-- Table structure for table `reviews`
--

CREATE TABLE `reviews` (
  `id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `order_id` int(11) DEFAULT NULL,
  `main` tinyint(1) NOT NULL,
  `rating` int(11) NOT NULL CHECK (`rating` between 1 and 5),
  `comment` text DEFAULT NULL,
  `customer_name` varchar(255) NOT NULL,
  `status` enum('pending','approved','rejected') DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `reviews`
--

INSERT INTO `reviews` (`id`, `product_id`, `user_id`, `order_id`, `main`, `rating`, `comment`, `customer_name`, `status`, `created_at`, `updated_at`) VALUES
(1, 1, NULL, NULL, 1, 5, 'Sản phẩm vượt mong đợi, chất lượng rất tốt và đóng gói cẩn thận.', 'Nguyễn Văn A', 'approved', '2025-12-08 03:12:53', '2025-12-08 03:22:05'),
(2, 1, NULL, NULL, 1, 5, 'Dùng rất thích, sẽ giới thiệu cho bạn bè. Giá tiền quá hợp lý.', 'Lê Thị B', 'approved', '2025-12-08 03:12:53', '2025-12-08 03:12:53'),
(3, 2, NULL, NULL, 1, 5, 'Chất lượng tuyệt vời, giao hàng nhanh, nhân viên hỗ trợ tận tình.', 'Phạm Công C', 'approved', '2025-12-08 03:12:53', '2025-12-08 03:12:53'),
(4, 2, NULL, NULL, 0, 5, 'Sản phẩm giống mô tả 100%, trải nghiệm rất tốt. Rất hài lòng.', 'Trần Minh D', 'approved', '2025-12-08 03:12:53', '2025-12-08 03:12:53'),
(5, 3, NULL, NULL, 0, 5, 'Đóng gói kỹ, hàng đẹp, dùng vài hôm thấy rất ổn. 5 sao!', 'Hoàng Thu E', 'approved', '2025-12-08 03:12:53', '2025-12-08 03:13:05'),
(6, 3, NULL, NULL, 0, 5, 'Không có gì để chê, sản phẩm xịn, dùng trơn tru.', 'Đỗ Hải F', 'approved', '2025-12-08 03:12:53', '2025-12-08 03:12:53'),
(7, 4, NULL, NULL, 0, 5, 'Sẽ mua lại lần nữa. Quá chất lượng trong tầm giá.', 'Lưu Gia G', 'approved', '2025-12-08 03:12:53', '2025-12-08 03:13:07'),
(8, 4, NULL, NULL, 0, 5, 'Hàng đẹp, shop phục vụ tốt, đóng gói chắc chắn.', 'Vũ Ngọc H', 'approved', '2025-12-08 03:12:53', '2025-12-08 03:12:53'),
(9, 1, NULL, NULL, 0, 5, 'Dùng gần tuần, mọi thứ vẫn rất tốt. Xứng đáng 5 sao.', 'Ngô Thanh I', 'approved', '2025-12-08 03:12:53', '2025-12-08 03:13:09'),
(10, 2, NULL, NULL, 0, 5, 'Sản phẩm tốt hơn mong đợi, giao hàng siêu nhanh.', 'Bùi Hải K', 'approved', '2025-12-08 03:12:53', '2025-12-08 03:12:53');

-- --------------------------------------------------------

--
-- Table structure for table `settings`
--

CREATE TABLE `settings` (
  `id` int(11) NOT NULL,
  `key_name` varchar(100) NOT NULL,
  `key_value` text DEFAULT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `settings`
--

INSERT INTO `settings` (`id`, `key_name`, `key_value`, `description`, `created_at`, `updated_at`) VALUES
(9, 'site_name', 'Hoa Ngọc Anh Floral & Gifts', 'Tên website', '2025-12-07 09:36:44', '2025-12-07 09:36:44'),
(10, 'site_email', 'contact@hoangocanh.com', 'Email liên hệ', '2025-12-07 09:36:44', '2025-12-07 09:36:44'),
(11, 'site_phone', '0392690630', 'Số điện thoại liên hệ', '2025-12-07 09:36:44', '2025-12-07 09:36:44'),
(12, 'free_shipping_threshold', '600000', 'Ngưỡng miễn phí vận chuyển (VNĐ)', '2025-12-07 09:36:44', '2025-12-07 09:36:44'),
(13, 'shipping_fee', '30000', 'Phí vận chuyển mặc định (VNĐ)', '2025-12-07 09:36:44', '2025-12-07 09:36:44'),
(14, 'delivery_time', '90-120 phút', 'Thời gian giao hàng', '2025-12-07 09:36:44', '2025-12-07 09:36:44'),
(15, 'discount_code', 'Uudais', 'Mã giảm giá mặc định', '2025-12-07 09:36:44', '2025-12-07 09:36:44'),
(16, 'discount_percentage', '5', 'Phần trăm giảm giá mặc định', '2025-12-07 09:36:44', '2025-12-07 09:36:44');

-- --------------------------------------------------------

--
-- Table structure for table `sliders`
--

CREATE TABLE `sliders` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `image_url` varchar(255) NOT NULL,
  `link_url` varchar(255) DEFAULT NULL,
  `button_text` varchar(100) DEFAULT NULL,
  `display_order` int(11) DEFAULT 0,
  `status` enum('active','inactive') DEFAULT 'active',
  `start_date` datetime DEFAULT NULL,
  `end_date` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `sliders`
--

INSERT INTO `sliders` (`id`, `title`, `description`, `image_url`, `link_url`, `button_text`, `display_order`, `status`, `start_date`, `end_date`, `created_at`, `updated_at`) VALUES
(4, 'Chào mừng đến với Hoa Ngọc Anh', 'Dịch vụ đặt hoa online chất lượng, giao hàng nhanh trong 90-120 phút', '/assets/images/sliders/1.png', '/shop.php', 'Mua sắm ngay', 1, 'active', NULL, NULL, '2025-12-07 09:36:44', '2025-12-08 15:18:24'),
(5, 'Hoa tươi mỗi ngày', 'Mang đến những sản phẩm hoa tươi làm đẹp cuộc sống', '/assets/images/sliders/2.png', '/category.php?cat=bo-hoa', 'Xem sản phẩm', 2, 'active', NULL, NULL, '2025-12-07 09:36:44', '2025-12-08 15:18:29'),
(6, 'Miễn phí giao hàng', 'Đơn hàng trên 600k được miễn phí giao hàng', '/assets/images/sliders/3.png', '/shop.php', 'Đặt hàng ngay', 3, 'active', NULL, NULL, '2025-12-07 09:36:44', '2025-12-08 15:18:38');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `full_name` varchar(255) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `avatar` varchar(255) DEFAULT NULL,
  `role` enum('customer','admin') DEFAULT 'customer',
  `status` enum('active','inactive','banned') DEFAULT 'active',
  `email_verified` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `last_login_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `email`, `password`, `full_name`, `phone`, `avatar`, `role`, `status`, `email_verified`, `created_at`, `updated_at`, `last_login_at`) VALUES
(11, 'admin@gmail.com', '$2y$10$MkiahhpKWBZOgRariTzd9uXUpSZmZvp/YPN./xS06QFE.xlowgmQ6', 'Administrator', '0392690630', NULL, 'admin', 'active', 1, '2025-12-07 09:36:44', '2025-12-07 09:37:17', '2025-12-07 09:37:17');

-- --------------------------------------------------------

--
-- Table structure for table `wishlists`
--

CREATE TABLE `wishlists` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `addresses`
--
ALTER TABLE `addresses`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_type` (`type`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_display_order` (`display_order`);

--
-- Indexes for table `blog_posts`
--
ALTER TABLE `blog_posts`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`),
  ADD KEY `idx_slug` (`slug`),
  ADD KEY `idx_author` (`author_id`),
  ADD KEY `idx_status` (`status`);
ALTER TABLE `blog_posts` ADD FULLTEXT KEY `idx_search` (`title`,`content`);

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`),
  ADD KEY `idx_slug` (`slug`),
  ADD KEY `idx_parent` (`parent_id`);

--
-- Indexes for table `coupons`
--
ALTER TABLE `coupons`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `code` (`code`),
  ADD KEY `idx_code` (`code`),
  ADD KEY `idx_dates` (`start_date`,`end_date`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `order_code` (`order_code`),
  ADD KEY `idx_orders_status` (`status`),
  ADD KEY `idx_orders_created` (`created_at`);

--
-- Indexes for table `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_order_items_order` (`order_id`);

--
-- Indexes for table `password_resets`
--
ALTER TABLE `password_resets`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_email` (`email`),
  ADD KEY `idx_token` (`token`),
  ADD KEY `idx_expires` (`expires_at`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`),
  ADD UNIQUE KEY `sku` (`sku`),
  ADD KEY `idx_slug` (`slug`),
  ADD KEY `idx_category` (`category_id`),
  ADD KEY `idx_price` (`price`),
  ADD KEY `idx_featured` (`featured`);
ALTER TABLE `products` ADD FULLTEXT KEY `idx_search` (`name`,`description`);

--
-- Indexes for table `product_attributes`
--
ALTER TABLE `product_attributes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_product` (`product_id`);

--
-- Indexes for table `product_images`
--
ALTER TABLE `product_images`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_product` (`product_id`);

--
-- Indexes for table `reviews`
--
ALTER TABLE `reviews`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_id` (`order_id`),
  ADD KEY `idx_product` (`product_id`),
  ADD KEY `idx_user` (`user_id`),
  ADD KEY `idx_status` (`status`);

--
-- Indexes for table `settings`
--
ALTER TABLE `settings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `key_name` (`key_name`),
  ADD KEY `idx_key` (`key_name`);

--
-- Indexes for table `sliders`
--
ALTER TABLE `sliders`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_display_order` (`display_order`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `idx_email` (`email`),
  ADD KEY `idx_role` (`role`);

--
-- Indexes for table `wishlists`
--
ALTER TABLE `wishlists`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_wishlist` (`user_id`,`product_id`),
  ADD KEY `product_id` (`product_id`),
  ADD KEY `idx_user` (`user_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `addresses`
--
ALTER TABLE `addresses`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `blog_posts`
--
ALTER TABLE `blog_posts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT for table `coupons`
--
ALTER TABLE `coupons`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `order_items`
--
ALTER TABLE `order_items`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `password_resets`
--
ALTER TABLE `password_resets`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=56;

--
-- AUTO_INCREMENT for table `product_attributes`
--
ALTER TABLE `product_attributes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=325;

--
-- AUTO_INCREMENT for table `product_images`
--
ALTER TABLE `product_images`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=242;

--
-- AUTO_INCREMENT for table `reviews`
--
ALTER TABLE `reviews`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `settings`
--
ALTER TABLE `settings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `sliders`
--
ALTER TABLE `sliders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `wishlists`
--
ALTER TABLE `wishlists`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `blog_posts`
--
ALTER TABLE `blog_posts`
  ADD CONSTRAINT `blog_posts_ibfk_1` FOREIGN KEY (`author_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `categories`
--
ALTER TABLE `categories`
  ADD CONSTRAINT `categories_ibfk_1` FOREIGN KEY (`parent_id`) REFERENCES `categories` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `order_items`
--
ALTER TABLE `order_items`
  ADD CONSTRAINT `fk_order_items_order` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `products`
--
ALTER TABLE `products`
  ADD CONSTRAINT `products_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `product_attributes`
--
ALTER TABLE `product_attributes`
  ADD CONSTRAINT `product_attributes_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `product_images`
--
ALTER TABLE `product_images`
  ADD CONSTRAINT `product_images_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `reviews`
--
ALTER TABLE `reviews`
  ADD CONSTRAINT `reviews_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `reviews_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `wishlists`
--
ALTER TABLE `wishlists`
  ADD CONSTRAINT `wishlists_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `wishlists_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
