<?php
require_once '../config/config.php';
require_once BASE_PATH . '/app/Database/Database.php';
require_once BASE_PATH . '/app/Models/SliderModel.php';
require_once BASE_PATH . '/app/Models/ProductModel.php';
require_once BASE_PATH . '/app/Models/ProductImageModel.php';
require_once BASE_PATH . '/app/Models/CategoryModel.php';
require_once BASE_PATH . '/app/Models/ReviewModel.php';

$page_title = APP_NAME . ' - Trang chủ';
$page_description = 'Shop hoa tươi online uy tín, giao hoa nhanh trong 90-120 phút. Chuyên cung cấp hoa sinh nhật, hoa khai trương, hoa tốt nghiệp.';

// Lấy dữ liệu từ database
$sliderModel = new SliderModel();
$productModel = new ProductModel();
$productImageModel = new ProductImageModel();
$categoryModel = new CategoryModel();
$reviewModel = new ReviewModel();

// Lấy sliders
$sliders = $sliderModel->getAll('active');

// Lấy featured products
$featured_products_raw = $productModel->getFeatured(8);
$featured_products = [];
foreach ($featured_products_raw as $product) {
    // Lấy ảnh chính
    $images = $productImageModel->getByProductId($product['id']);
    $mainImage = !empty($images) ? $images[0]['image_url'] : IMAGES_URL . '/products/default.jpg';
    
    // Xử lý URL ảnh (nếu là relative path thì thêm APP_URL)
    if (strpos($mainImage, 'http') !== 0 && strpos($mainImage, '/') === 0) {
        $mainImage = APP_URL . $mainImage;
    } elseif (strpos($mainImage, 'http') !== 0) {
        $mainImage = IMAGES_URL . '/products/' . $mainImage;
    }
    
    $featured_products[] = [
        'id' => $product['id'],
        'name' => $product['name'],
        'slug' => $product['slug'],
        'image' => $mainImage,
        'price' => floatval($product['price']),
        'sale_price' => $product['sale_price'] ? floatval($product['sale_price']) : null,
        'rating' => floatval($product['rating_avg']),
        'reviews' => intval($product['rating_count']),
        'sold' => intval($product['sold_count']),
        'category' => $product['category_name']
    ];
}

// Lấy hoa khai trương
$opening_flowers_raw = $productModel->getByCategorySlug('hoa-khai-truong', 4);
$opening_flowers = [];
foreach ($opening_flowers_raw as $product) {
    // Lấy ảnh chính
    $images = $productImageModel->getByProductId($product['id']);
    $mainImage = !empty($images) ? $images[0]['image_url'] : IMAGES_URL . '/products/default.jpg';
    
    // Xử lý URL ảnh
    if (strpos($mainImage, 'http') !== 0 && strpos($mainImage, '/') === 0) {
        $mainImage = APP_URL . $mainImage;
    } elseif (strpos($mainImage, 'http') !== 0) {
        $mainImage = IMAGES_URL . '/products/' . $mainImage;
    }
    
    $opening_flowers[] = [
        'id' => $product['id'],
        'name' => $product['name'],
        'slug' => $product['slug'],
        'image' => $mainImage,
        'price' => floatval($product['price']),
        'sale_price' => $product['sale_price'] ? floatval($product['sale_price']) : null,
        'rating' => floatval($product['rating_avg']),
        'reviews' => intval($product['rating_count']),
        'sold' => intval($product['sold_count']),
        'category' => $product['category_name']
    ];
}

// Lấy danh mục
$categories_raw = $categoryModel->getAll('active');
$categories = [];
foreach ($categories_raw as $cat) {
    if (!$cat['parent_id']) { // Chỉ lấy danh mục cha
        $categories[] = [
            'name' => $cat['name'],
            'slug' => $cat['slug'],
            'icon' => 'fa-spa' // Mặc định, có thể thêm icon vào database sau
        ];
    }
}

// Lấy sản phẩm theo từng danh mục cha (dynamic, không dùng mẫu)
$category_products = [];
foreach ($categories as $cat) {
    $products_raw = $productModel->getByCategorySlug($cat['slug'], 4);
    $mapped = [];
    foreach ($products_raw as $product) {
        $images = $productImageModel->getByProductId($product['id']);
        $mainImage = !empty($images) ? $images[0]['image_url'] : IMAGES_URL . '/products/default.jpg';
        
        // Chuẩn hóa URL ảnh
        if (strpos($mainImage, 'http') !== 0 && strpos($mainImage, '/') === 0) {
            $mainImage = APP_URL . $mainImage;
        } elseif (strpos($mainImage, 'http') !== 0) {
            $mainImage = IMAGES_URL . '/products/' . $mainImage;
        }
        
        $mapped[] = [
            'id' => $product['id'],
            'name' => $product['name'],
            'slug' => $product['slug'],
            'image' => $mainImage,
            'price' => floatval($product['price']),
            'sale_price' => $product['sale_price'] ? floatval($product['sale_price']) : null,
            'rating' => floatval($product['rating_avg']),
            'reviews' => intval($product['rating_count']),
            'sold' => intval($product['sold_count']),
            'category' => $product['category_name']
        ];
    }
    if (!empty($mapped)) {
        $category_products[$cat['slug']] = $mapped;
    }
}


include '../includes/header.php';
?>

<!-- Hero Slider -->
<section class="hero-slider relative overflow-hidden">
    <div class="slider-container relative w-full mx-auto">
        <?php if (!empty($sliders)): ?>
            <div class="slider-wrapper flex items-center justify-center gap-4 relative" style="height: 100%;">
                <?php foreach ($sliders as $index => $slider): ?>
                    <?php 
                    // Lấy image_url từ database (đã được lưu từ ImageUploader hoặc URL external)
                    $imageUrl = trim($slider['image_url']);
                    
                    // Xử lý URL ảnh slider:
                    // ImageUploader lưu: /assets/images/sliders/filename.jpg (relative path từ root)
                    // Hoặc URL external: https://example.com/image.jpg
                    
                    if (empty($imageUrl)) {
                        // Không có ảnh, dùng ảnh mặc định
                        $imageUrl = IMAGES_URL . '/sliders/default.jpg';
                    } elseif (strpos($imageUrl, 'http://') === 0 || strpos($imageUrl, 'https://') === 0) {
                        // URL external từ website khác (ví dụ: https://example.com/image.jpg)
                        // Giữ nguyên URL - không cần xử lý
                    } elseif (strpos($imageUrl, '/assets/images/') === 0) {
                        // Relative path từ root (ví dụ: /assets/images/sliders/file.jpg)
                        // Thêm APP_URL để có đường dẫn đầy đủ
                        $imageUrl = APP_URL . $imageUrl;
                    } elseif (strpos($imageUrl, '/') === 0) {
                        // Relative path khác từ root, cũng thêm APP_URL
                        $imageUrl = APP_URL . $imageUrl;
                    } else {
                        // Path tương đối (không bắt đầu bằng /), thêm IMAGES_URL
                        $imageUrl = IMAGES_URL . '/sliders/' . $imageUrl;
                    }
                    ?>
                    <div class="slider-slide" 
                         data-index="<?php echo $index; ?>"
                         style="background-image: url('<?php echo htmlspecialchars($imageUrl); ?>'); background-size: contain; background-position: center; background-repeat: no-repeat;">
                    </div>
                <?php endforeach; ?>
            </div>
            
            <!-- Slider Indicators -->
            <div class="absolute bottom-4 left-1/2 transform -translate-x-1/2 flex gap-2 z-30">
                <?php foreach ($sliders as $index => $slider): ?>
                    <button class="slider-indicator w-3 h-3 rounded-full <?php echo $index === 0 ? 'bg-white' : 'bg-white opacity-50'; ?>" 
                            data-slide="<?php echo $index; ?>"></button>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <!-- Fallback nếu không có slider -->
            <div class="hero-slide absolute inset-0" style="background-image: url('<?php echo IMAGES_URL; ?>/hero-bg.jpg'); background-size: cover; background-position: center;">
            </div>
        <?php endif; ?>
    </div>
</section>

<!-- Categories -->
<section class="py-12 bg-gray-50">
    <div class="container mx-auto px-4">
        <h2 class="text-3xl font-bold text-center mb-8">
            <span class="text-gradient">Danh Mục Hoa</span>
        </h2>
        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4">
            <?php
            // Icon mapping
            $iconMap = [
                'hoa-sinh-nhat' => 'fa-birthday-cake',
                'hoa-khai-truong' => 'fa-store',
                'hoa-tot-nghiep' => 'fa-graduation-cap',
                'bo-hoa' => 'fa-spa',
                'hoa-lan-ho-diep' => 'fa-leaf',
                'hoa-chia-buon' => 'fa-heart',
                'hoa-8-3' => 'fa-heart',
            ];
            
            foreach ($categories as $cat):
                $icon = $iconMap[$cat['slug']] ?? 'fa-spa';
            ?>
                <a href="<?php echo APP_URL; ?>/category.php?cat=<?php echo $cat['slug']; ?>" 
                   class="category-card-mini bg-white rounded-xl p-6 text-center hover:shadow-lg transition-all hover:-translate-y-1">
                    <div class="w-16 h-16 bg-gradient-to-br from-rose-500 to-pink-500 rounded-full flex items-center justify-center mx-auto mb-3">
                        <i class="fas <?php echo $icon; ?> text-white text-2xl"></i>
                    </div>
                    <h3 class="font-semibold text-gray-800"><?php echo $cat['name']; ?></h3>
                </a>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- Products by Category (dynamic) -->
<?php if (!empty($category_products)): ?>
<section class="py-16 bg-gray-50">
    <div class="container mx-auto px-4">
        <h2 class="text-3xl font-bold text-center mb-10">
            <span class="text-gradient">Sản phẩm theo danh mục</span>
        </h2>
        
        <?php foreach ($categories as $cat): ?>
            <?php if (!isset($category_products[$cat['slug']]) || empty($category_products[$cat['slug']])) continue; ?>
            <div class="mb-12">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-2xl font-bold text-gray-800">
                        <?php echo htmlspecialchars($cat['name']); ?>
                    </h3>
                    <a href="<?php echo APP_URL; ?>/category.php?cat=<?php echo $cat['slug']; ?>" class="text-rose-500 hover:text-rose-600 font-semibold inline-flex items-center gap-2">
                        Xem tất cả <i class="fas fa-arrow-right"></i>
                    </a>
                </div>
                <div class="product-grid">
                    <?php foreach ($category_products[$cat['slug']] as $product): ?>
                        <?php include '../includes/product-card.php'; ?>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</section>
<?php endif; ?>

<!-- Featured Products -->
<section class="py-16">
    <div class="container mx-auto px-4">
        <div class="text-center mb-10">
            <h2 class="text-3xl font-bold mb-3">
                <span class="text-gradient">Mẫu Hoa Được Ưa Chuộng</span>
            </h2>
            <p class="text-gray-600">Những sản phẩm hoa tươi đẹp nhất dành cho bạn</p>
        </div>

        <div class="product-grid">
            <?php foreach ($featured_products as $product): ?>
                <?php include '../includes/product-card.php'; ?>
            <?php endforeach; ?>
        </div>

        <div class="text-center mt-10">
            <a href="<?php echo APP_URL; ?>/shop.php" class="inline-flex items-center gap-2 bg-gradient-to-r from-rose-500 to-pink-500 text-white px-8 py-3 rounded-full font-semibold hover:shadow-lg transition-all hover:-translate-y-1">
                Xem tất cả sản phẩm
                <i class="fas fa-arrow-right"></i>
            </a>
        </div>
    </div>
</section>

<!-- Opening Flowers -->
<section class="py-16 bg-gradient-to-br from-rose-50 to-pink-50">
    <div class="container mx-auto px-4">
        <div class="text-center mb-10">
            <h2 class="text-3xl font-bold mb-3">
                <span class="text-gradient">Hoa Khai Trương</span>
            </h2>
            <p class="text-gray-600">Tổng hợp các mẫu hoa khai trương đẹp nhất</p>
        </div>

        <div class="product-grid">
            <?php foreach ($opening_flowers as $product): ?>
                <?php include '../includes/product-card.php'; ?>
            <?php endforeach; ?>
        </div>

        <div class="text-center mt-10">
            <a href="<?php echo APP_URL; ?>/category.php?cat=hoa-khai-truong" class="inline-flex items-center gap-2 bg-gradient-to-r from-rose-500 to-pink-500 text-white px-8 py-3 rounded-full font-semibold hover:shadow-lg transition-all hover:-translate-y-1">
                Xem thêm hoa khai trương
                <i class="fas fa-arrow-right"></i>
            </a>
        </div>
    </div>
</section>

<!-- Why Choose Us -->
<section class="py-16">
    <div class="container mx-auto px-4">
        <h2 class="text-3xl font-bold text-center mb-12">
            <span class="text-gradient">Tại Sao Chọn Chúng Tôi?</span>
        </h2>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
            <div class="text-center p-6 rounded-xl bg-white shadow-sm hover:shadow-lg transition">
                <div class="w-20 h-20 bg-gradient-to-br from-rose-500 to-pink-500 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-truck text-white text-3xl"></i>
                </div>
                <h3 class="font-bold text-xl mb-2">Giao Hàng Nhanh</h3>
                <p class="text-gray-600">Trong vòng 90-120 phút</p>
            </div>
            <div class="text-center p-6 rounded-xl bg-white shadow-sm hover:shadow-lg transition">
                <div class="w-20 h-20 bg-gradient-to-br from-rose-500 to-pink-500 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-check-circle text-white text-3xl"></i>
                </div>
                <h3 class="font-bold text-xl mb-2">Hoa Tươi 100%</h3>
                <p class="text-gray-600">Đảm bảo chất lượng hoa tươi</p>
            </div>
            <div class="text-center p-6 rounded-xl bg-white shadow-sm hover:shadow-lg transition">
                <div class="w-20 h-20 bg-gradient-to-br from-rose-500 to-pink-500 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-gift text-white text-3xl"></i>
                </div>
                <h3 class="font-bold text-xl mb-2">Miễn Phí Thiệp</h3>
                <p class="text-gray-600">Thiệp hoặc decal in miễn phí</p>
            </div>
            <div class="text-center p-6 rounded-xl bg-white shadow-sm hover:shadow-lg transition">
                <div class="w-20 h-20 bg-gradient-to-br from-rose-500 to-pink-500 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-tags text-white text-3xl"></i>
                </div>
                <h3 class="font-bold text-xl mb-2">Giảm Giá 5%</h3>
                <p class="text-gray-600">Nhập mã "Uudai5" khi đặt online</p>
            </div>
        </div>
    </div>
</section>

<!-- Customer Reviews -->
<section class="py-16 bg-gray-50">
    <div class="container mx-auto px-4">
        <h2 class="text-3xl font-bold text-center mb-12">
            <span class="text-gradient">Khách Hàng Nói Gì Về Chúng Tôi</span>
        </h2>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <?php
            // Lấy 3 reviews có main = 1 từ database
            $reviews = $reviewModel->getMainReviews(3);
            
            if (!empty($reviews)):
                foreach ($reviews as $review):
                    // Format ngày theo dd/mm/yyyy
                    $reviewDate = date('d/m/Y', strtotime($review['created_at']));
                    // Lấy chữ cái đầu của tên khách hàng
                    $firstLetter = mb_substr($review['customer_name'], 0, 1, 'UTF-8');
            ?>
                <div class="bg-white p-6 rounded-xl shadow-sm hover:shadow-lg transition">
                    <div class="flex items-center mb-4">
                        <div class="w-12 h-12 bg-gradient-to-br from-rose-500 to-pink-500 rounded-full flex items-center justify-center text-white font-bold text-xl">
                            <?php echo strtoupper($firstLetter); ?>
                        </div>
                        <div class="ml-3">
                            <h4 class="font-semibold"><?php echo htmlspecialchars($review['customer_name']); ?></h4>
                            <div class="flex text-yellow-400 text-sm">
                                <?php for ($i = 0; $i < $review['rating']; $i++): ?>
                                    <i class="fas fa-star"></i>
                                <?php endfor; ?>
                            </div>
                        </div>
                    </div>
                    <p class="text-gray-600 mb-2"><?php echo htmlspecialchars($review['comment']); ?></p>
                    <span class="text-sm text-gray-400"><?php echo $reviewDate; ?></span>
                </div>
            <?php 
                endforeach;
            else:
                // Hiển thị thông báo nếu không có reviews
            ?>
                <div class="col-span-3 text-center text-gray-500">
                    <p>Chưa có đánh giá nào.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</section>

<style>
@keyframes fade-in {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.animate-fade-in {
    animation: fade-in 1s ease-out;
}

.category-card-mini:hover {
    transform: translateY(-5px);
}

/* Slider Container */
.hero-slider {
    width: 100vw;
    margin-left: calc(50% - 50vw);
    margin-right: calc(50% - 50vw);
    overflow: hidden;
}

.slider-container {
    margin-top: 20px !important;
    width: 100%;
    max-width: 100% !important;
    margin: 0 auto;
    height: 650px;
    position: relative;
    overflow: hidden;
}

@media (max-width: 1023px) {
    .slider-container {
        height: 520px;
    }
}

@media (max-width: 768px) {
    .slider-container {
        height: 320px;
        max-width: 100%;
    }
    
    .slider-slide {
        background-size: contain !important;
        background-position: center !important;
    }
}

.slider-wrapper {
    display: flex;
    align-items: center;
    justify-content: center;
    height: 100%;
    position: relative;
    overflow: hidden;
    padding: 0;
    gap: 0;
}

.slider-slide {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    opacity: 0;
    transition: opacity 0.8s ease-in-out;
    z-index: 1;
    will-change: opacity;
}

/* Ảnh đang active - hiển thị toàn màn hình */
.slider-slide.active {
    opacity: 1 !important;
    z-index: 10;
}

/* Ẩn tất cả các slide không active */
.slider-slide:not(.active) {
    opacity: 0;
    pointer-events: none;
    z-index: 0;
}

.slider-indicator {
    cursor: pointer;
    transition: opacity 0.3s, transform 0.3s;
}

.slider-indicator:hover {
    transform: scale(1.2);
}

.slider-indicator.active {
    opacity: 1 !important;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const slides = Array.from(document.querySelectorAll('.slider-slide'));
    const indicators = document.querySelectorAll('.slider-indicator');
    let currentSlide = 0;
    const totalSlides = slides.length;
    let autoSlideInterval = null;
    
    // Kiểm tra có slides không
    if (totalSlides === 0) return;
    
    /**
     * Hiển thị slide tại index cụ thể
     * @param {number} index - Index của slide cần hiển thị
     */
    function showSlide(index) {
        // Đảm bảo index hợp lệ
        if (index < 0 || index >= totalSlides) {
            console.warn('Invalid slide index:', index);
            return;
        }
        
        // Xóa class active khỏi tất cả slides
        slides.forEach(slide => {
            slide.classList.remove('active');
        });
        
        // Thêm class active cho slide hiện tại
        slides[index].classList.add('active');
        
        // Cập nhật indicators
        indicators.forEach((indicator, i) => {
            if (i === index) {
                indicator.classList.add('active');
                indicator.style.opacity = '1';
            } else {
                indicator.classList.remove('active');
                indicator.style.opacity = '0.5';
            }
        });
    }
    
    /**
     * Chuyển sang slide tiếp theo
     */
    function nextSlide() {
        if (totalSlides <= 1) return;
        currentSlide = (currentSlide + 1) % totalSlides;
        showSlide(currentSlide);
    }
    
    /**
     * Xử lý click vào indicator
     */
    indicators.forEach((indicator, index) => {
        indicator.addEventListener('click', function() {
            if (index !== currentSlide && index >= 0 && index < totalSlides) {
                currentSlide = index;
                showSlide(currentSlide);
            }
        });
    });
    
    /**
     * Bắt đầu auto slide
     */
    function startAutoSlide() {
        if (autoSlideInterval) {
            clearInterval(autoSlideInterval);
        }
        if (totalSlides > 1) {
            autoSlideInterval = setInterval(nextSlide, 4000);
        }
    }
    
    /**
     * Dừng auto slide
     */
    function stopAutoSlide() {
        if (autoSlideInterval) {
            clearInterval(autoSlideInterval);
            autoSlideInterval = null;
        }
    }
    
    // Dừng auto slide khi hover vào slider
    const sliderContainer = document.querySelector('.slider-container');
    if (sliderContainer) {
        sliderContainer.addEventListener('mouseenter', stopAutoSlide);
        sliderContainer.addEventListener('mouseleave', startAutoSlide);
    }
    
    // Khởi tạo: hiển thị slide đầu tiên
    showSlide(0);
    startAutoSlide();
    
    // Cleanup khi trang bị unload
    window.addEventListener('beforeunload', function() {
        stopAutoSlide();
    });
});
</script>

<?php include '../includes/footer.php'; ?>

