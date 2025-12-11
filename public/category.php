<?php
require_once '../config/config.php';
require_once BASE_PATH . '/app/Database/Database.php';
require_once BASE_PATH . '/app/Models/ProductModel.php';
require_once BASE_PATH . '/app/Models/ProductImageModel.php';
require_once BASE_PATH . '/app/Models/CategoryModel.php';
require_once BASE_PATH . '/app/Models/ReviewModel.php';

$category_slug = isset($_GET['cat']) ? trim($_GET['cat']) : '';
$sort = isset($_GET['sort']) ? $_GET['sort'] : 'popular';
$allowedSorts = [
    'popular' => 'Thứ tự theo mức độ phổ biến',
    'rating' => 'Thứ tự theo điểm đánh giá',
    'newest' => 'Mới nhất',
    'price_asc' => 'Thứ tự theo giá: thấp đến cao',
    'price_desc' => 'Thứ tự theo giá: cao xuống thấp'
];
if (!array_key_exists($sort, $allowedSorts)) {
    $sort = 'popular';
}

$productModel = new ProductModel();
$productImageModel = new ProductImageModel();
$categoryModel = new CategoryModel();
$reviewModel = new ReviewModel();

// Helper chuẩn hóa URL ảnh sản phẩm
function normalizeProductImageUrl($url) {
    if (empty($url)) return IMAGES_URL . '/products/default.jpg';
    if (strpos($url, 'http') === 0) return $url;
    if (strpos($url, '/') === 0) return APP_URL . $url;
    // Ưu tiên thư mục uploads/products nếu tồn tại
    $uploadPath = BASE_PATH . '/public/uploads/products/' . $url;
    if (file_exists($uploadPath)) {
        return APP_URL . '/uploads/products/' . $url;
    }
    // Nếu file tồn tại trực tiếp trong public/
    $publicPath = BASE_PATH . '/public/' . $url;
    if (file_exists($publicPath)) {
        return APP_URL . '/' . $url;
    }
    // Fallback: assets/images/products
    return IMAGES_URL . '/products/' . $url;
}

// Lấy danh mục theo slug từ CSDL
$category = $categoryModel->findBySlug($category_slug, 'active');
if (!$category) {
    $category_name = 'Danh mục';
    $page_title = 'Danh mục không tồn tại - ' . APP_NAME;
    $page_description = 'Danh mục bạn tìm không tồn tại.';
    $products = [];
    $subcategories = [];
} else {
    $category_name = $category['name'];
$page_title = $category_name . ' - ' . APP_NAME;
$page_description = 'Xem các sản phẩm ' . $category_name . ' đẹp nhất với giá tốt.';

    // Lấy subcategories từ DB nếu có
    $subcategories = $categoryModel->getByParentId($category['id'], 'active');
    
    // Lấy sản phẩm theo slug danh mục (limit 24)
    $products_raw = $productModel->getByCategorySlug($category_slug, 24);
$products = [];
    foreach ($products_raw as $product) {
        $images = $productImageModel->getByProductId($product['id']);
        $mainImage = !empty($images) ? normalizeProductImageUrl($images[0]['image_url']) : IMAGES_URL . '/products/default.jpg';
        $reviewCount = $reviewModel->countByProductId($product['id'], 'approved');
        $products[] = [
            'id' => $product['id'],
            'name' => $product['name'],
            'slug' => $product['slug'],
            'image' => $mainImage,
            'price' => floatval($product['price']),
            'sale_price' => $product['sale_price'] ? floatval($product['sale_price']) : null,
            'rating' => floatval($product['rating_avg']),
            'reviews' => $reviewCount,
            'sold' => intval($product['sold_count']),
            'created_at' => isset($product['created_at']) ? strtotime($product['created_at']) : $product['id'],
            'category' => $category_name
        ];
    }

    // Sắp xếp sản phẩm theo lựa chọn
    usort($products, function ($a, $b) use ($sort) {
        $priceA = $a['sale_price'] ?? $a['price'];
        $priceB = $b['sale_price'] ?? $b['price'];
        switch ($sort) {
            case 'rating':
                if ($a['rating'] === $b['rating']) {
                    return $b['reviews'] <=> $a['reviews'];
                }
                return $b['rating'] <=> $a['rating'];
            case 'newest':
                return $b['created_at'] <=> $a['created_at'];
            case 'price_asc':
                return $priceA <=> $priceB;
            case 'price_desc':
                return $priceB <=> $priceA;
            case 'popular':
            default:
                if ($a['sold'] === $b['sold']) {
                    return $b['reviews'] <=> $a['reviews'];
                }
                return $b['sold'] <=> $a['sold'];
        }
    });
}

include '../includes/header.php';
?>

<!-- Category Header -->
<div class="bg-gradient-to-r from-rose-500 to-pink-500 text-white py-1 category-breadcrumb-wrapper">
    <div class="container mx-auto px-4">
        <div class="flex flex-wrap items-center justify-between gap-2" style="max-height: 70px !important; overflow: hidden; display: flex; align-items: center;">
            <div class="breadcrumb text-white category-breadcrumb">
                <a href="<?php echo APP_URL; ?>" class="text-white hover:underline">
                    <i class="fas fa-home"></i> Trang chủ
                </a>
                <span class="separator text-white/60">/</span>
                <span class="font-medium"><?php echo $category_name; ?></span>
            </div>
            <form method="get" class="category-sort-form">
                <input type="hidden" name="cat" value="<?php echo htmlspecialchars($category_slug); ?>">
                <select name="sort" class="bg-white text-gray-800 rounded-full px-4 py-1.5 text-sm shadow-sm focus:outline-none focus:ring-2 focus:ring-white/60" onchange="this.form.submit()">
                    <?php foreach ($allowedSorts as $key => $label): ?>
                        <option value="<?php echo $key; ?>" <?php echo $sort === $key ? 'selected' : ''; ?>>
                            <?php echo $label; ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </form>
        </div>
    </div>
</div>

<style>
/* Mobile: Căn giữa dropdown sort */
@media (max-width: 768px) {
    .category-sort-form {
        width: 100%;
        display: flex;
        justify-content: center;
        margin-top: 0.5rem;
        padding-bottom: 15px;
    }
    
    .category-sort-form select {
        width: 100%;
        max-width: 280px;
        margin-top: -15px;
    }
}
</style>

<!-- Products Section -->
<section class="py-12">
    <div class="container mx-auto px-4">
        <!-- Subcategories (if applicable) -->
        <?php if (!empty($subcategories)): ?>
            <div class="mb-8">
                <div class="flex flex-wrap gap-3">
                    <?php foreach ($subcategories as $sub): ?>
                        <a href="?cat=<?php echo $sub['slug']; ?>" 
                           class="px-6 py-2 rounded-full border-2 border-rose-500 text-rose-500 font-medium hover:bg-rose-500 hover:text-white transition">
                            <?php echo htmlspecialchars($sub['name']); ?>
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endif; ?>

        <!-- Products Grid -->
        <div class="product-grid">
            <?php if (empty($products)): ?>
                <div class="col-span-4 text-center text-gray-500">Chưa có sản phẩm trong danh mục này.</div>
            <?php else: ?>
            <?php foreach ($products as $product): ?>
                <?php include '../includes/product-card.php'; ?>
            <?php endforeach; ?>
            <?php endif; ?>
        </div>

        <!-- Load More (placeholder, chưa triển khai) -->
        <?php if (!empty($products)): ?>
        
        <?php endif; ?>
    </div>
</section>

<!-- Category Description (giữ nguyên) -->
<section class="py-12 bg-gray-50">
    <div class="container mx-auto px-4">
        <div class="max-w-4xl mx-auto bg-white rounded-xl shadow-sm p-8">
            <h2 class="text-2xl font-bold mb-4"><?php echo $category_name; ?> - Đẹp và Ý Nghĩa</h2>
            <p class="text-gray-600 leading-relaxed mb-4">
                Tại Hoa Ngọc Anh, chúng tôi cung cấp các mẫu <?php echo strtolower($category_name); ?> đẹp nhất, 
                được thiết kế bởi đội ngũ florist chuyên nghiệp với nhiều năm kinh nghiệm. 
                Mỗi sản phẩm đều được chăm chút tỉ mỉ từng chi tiết, sử dụng hoa tươi nhập khẩu chất lượng cao.
            </p>
            <p class="text-gray-600 leading-relaxed">
                Chúng tôi cam kết giao hoa đúng mẫu, đúng giờ và miễn phí giao hàng cho đơn hàng trên 600.000đ. 
                Đặt hoa ngay hôm nay để nhận ưu đãi giảm giá 5% với mã "Uudai5"!
            </p>
        </div>
    </div>
</section>

<?php include '../includes/footer.php'; ?>
