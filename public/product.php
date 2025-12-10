<?php
require_once '../config/config.php';
require_once BASE_PATH . '/app/Database/Database.php';
require_once BASE_PATH . '/app/Models/ProductModel.php';
require_once BASE_PATH . '/app/Models/ProductImageModel.php';
require_once BASE_PATH . '/app/Models/CategoryModel.php';
require_once BASE_PATH . '/app/Models/ReviewModel.php';
require_once BASE_PATH . '/app/Models/ProductAttributeModel.php';

$product_slug = isset($_GET['slug']) ? trim($_GET['slug']) : '';

$productModel = new ProductModel();
$productImageModel = new ProductImageModel();
$categoryModel = new CategoryModel();
$reviewModel = new ReviewModel();
$productAttributeModel = new ProductAttributeModel();

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

// Lấy sản phẩm theo slug
$productData = $productModel->findDetailBySlug($product_slug, 'active');
if (!$productData) {
    $page_title = 'Sản phẩm không tồn tại - ' . APP_NAME;
    $page_description = 'Sản phẩm bạn tìm không tồn tại.';
    $product = null;
    $related_products = [];
} else {
    // Ảnh sản phẩm
    $imagesRaw = $productImageModel->getByProductId($productData['id']);
    $images = [];
    foreach ($imagesRaw as $img) {
        $images[] = normalizeProductImageUrl($img['image_url']);
    }
    if (empty($images)) {
        $images[] = IMAGES_URL . '/products/default.jpg';
    }
    
    // Lấy đánh giá thực tế từ database
    $reviewsCount = $reviewModel->countByProductId($productData['id'], 'approved');
    $reviewsAverageRating = $reviewModel->getAverageRating($productData['id'], 'approved');
    $reviewsList = $reviewModel->getByProductId($productData['id'], 'approved');
    $ratingDistribution = $reviewModel->getRatingDistribution($productData['id'], 'approved');
    
    // Lấy thuộc tính sản phẩm
    $attributesRaw = $productAttributeModel->getByProductId($productData['id']);
    $details = [];
    foreach ($attributesRaw as $attr) {
        $details[$attr['attribute_name']] = $attr['attribute_value'];
    }
    // Thêm thông tin cơ bản nếu chưa có thuộc tính
    if (empty($details)) {
        $details['Danh mục'] = $productData['category_name'];
        $details['SKU'] = $productData['sku'];
        $details['Tồn kho'] = $productData['stock_quantity'] . ' sản phẩm';
    } else {
        // Đảm bảo có thông tin cơ bản
        if (!isset($details['Danh mục'])) {
            $details['Danh mục'] = $productData['category_name'];
        }
        if (!isset($details['SKU'])) {
            $details['SKU'] = $productData['sku'];
        }
    }
    
    // Sử dụng rating từ database nếu có đánh giá, nếu không thì dùng giá trị mặc định
    $finalRating = $reviewsCount > 0 ? $reviewsAverageRating : floatval($productData['rating_avg']);
    
    $product = [
        'id' => $productData['id'],
        'name' => $productData['name'],
        'slug' => $productData['slug'],
        'description' => $productData['description'],
        'price' => floatval($productData['price']),
        'sale_price' => $productData['sale_price'] ? floatval($productData['sale_price']) : null,
        'rating' => $finalRating,
        'reviews' => $reviewsCount,
        'sold' => intval($productData['sold_count']),
        'stock' => intval($productData['stock_quantity']),
        'sku' => $productData['sku'],
        'category' => $productData['category_name'],
        'category_slug' => $productData['category_slug'] ?? '',
        'images' => $images,
        'details' => $details,
        'reviews_list' => $reviewsList,
        'rating_distribution' => $ratingDistribution
    ];
    
    $page_title = $product['name'] . ' - ' . APP_NAME;
    $page_description = mb_substr(strip_tags($product['description']), 0, 150) . '...';
    
    // Sản phẩm liên quan: cùng danh mục, exclude current, limit 4
    $categorySlugForRelated = $productData['category_slug'] ?? ($productData['category_name'] ? createSlug($productData['category_name']) : '');
    $related_raw = $productModel->getByCategorySlug($categorySlugForRelated, 8);
    $related_products = [];
    foreach ($related_raw as $item) {
        if ($item['id'] == $product['id']) continue;
        $imgs = $productImageModel->getByProductId($item['id']);
        $imgUrl = !empty($imgs) ? normalizeProductImageUrl($imgs[0]['image_url']) : IMAGES_URL . '/products/default.jpg';
        $related_products[] = [
            'id' => $item['id'],
            'name' => $item['name'],
            'slug' => $item['slug'],
            'image' => $imgUrl,
            'price' => floatval($item['price']),
            'sale_price' => $item['sale_price'] ? floatval($item['sale_price']) : null,
            'rating' => floatval($item['rating_avg']),
            'reviews' => intval($item['rating_count']),
        ];
        if (count($related_products) >= 4) break;
    }
}

include '../includes/header.php';
?>

<!-- Breadcrumb -->
<div class="bg-rose-50 py-2">
    <div class="container mx-auto px-4">
        <div class="breadcrumb text-lg text-rose-600 font-semibold">
            <a href="<?php echo APP_URL; ?>" class="hover:text-rose-700">
                <i class="fas fa-home"></i> Trang chủ
            </a>
            <span class="separator mx-2 text-rose-500">/</span>
            <a href="<?php echo APP_URL; ?>/category.php?cat=<?php echo $product['category_slug']; ?>" class="hover:text-rose-700">
                <?php echo $product['category']; ?>
            </a>
            <span class="separator mx-2 text-rose-500">/</span>
            <span class="text-rose-700"><?php echo $product['name']; ?></span>
        </div>
    </div>
</div>

<!-- Product Detail -->
<section class="py-8">
    <div class="container mx-auto px-4">
        <div class="bg-white rounded-xl shadow-sm p-6 md:p-8">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <!-- Product Images -->
                <div>
                    <!-- Main Image -->
                    <div class="mb-4 rounded-xl overflow-hidden border-2 border-gray-200 aspect-square bg-white">
                        <img id="main-image" 
                             src="<?php echo $product['images'][0]; ?>" 
                             alt="<?php echo htmlspecialchars($product['name']); ?>"
                             class="w-full h-full object-cover">
                    </div>
                    
                    <!-- Thumbnail Images -->
                    <div class="grid grid-cols-4 gap-3">
                        <?php foreach ($product['images'] as $index => $image): ?>
                            <div class="rounded-lg overflow-hidden border-2 border-gray-200 cursor-pointer hover:border-rose-500 transition aspect-square bg-white"
                                 onclick="changeMainImage('<?php echo $image; ?>')">
                                <img src="<?php echo $image; ?>" 
                                     alt="Hình <?php echo $index + 1; ?>"
                                     class="w-full h-full object-cover">
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>

                <!-- Product Info -->
                <div>
                    <div class="mb-2">
                        <span class="inline-block bg-rose-100 text-rose-600 px-3 py-1 rounded-full text-sm font-medium">
                            <?php echo $product['category']; ?>
                        </span>
                        <?php if ($product['stock'] > 0): ?>
                            <span class="inline-block bg-green-100 text-green-600 px-3 py-1 rounded-full text-sm font-medium ml-2">
                                <i class="fas fa-check-circle"></i> Còn hàng
                            </span>
                        <?php endif; ?>
                    </div>

                    <h1 class="text-3xl font-bold text-gray-800 mb-3"><?php echo $product['name']; ?></h1>

                    <!-- Rating & Reviews -->
                    <div class="flex items-center gap-4 mb-4">
                        <div class="flex items-center gap-2">
                            <div class="flex text-yellow-400">
                                <?php 
                                $fullStars = floor($product['rating']);
                                $hasHalfStar = ($product['rating'] - $fullStars) >= 0.5;
                                for ($i = 1; $i <= 5; $i++): 
                                    if ($i <= $fullStars):
                                ?>
                                        <i class="fas fa-star"></i>
                                <?php elseif ($i == $fullStars + 1 && $hasHalfStar): ?>
                                    <i class="fas fa-star-half-alt"></i>
                                    <?php else: ?>
                                        <i class="far fa-star"></i>
                                <?php endif; endfor; ?>
                            </div>
                            <span class="font-semibold"><?php echo number_format($product['rating'], 1); ?></span>
                            <span class="text-gray-600">(<?php echo number_format($product['reviews']); ?> đánh giá)</span>
                        </div>
                        <div class="text-gray-600">
                            <i class="fas fa-box mr-1"></i>
                            Đã bán: <strong><?php echo $product['sold']; ?></strong>
                        </div>
                    </div>

                    <?php 
                        $hasSale = isset($product['sale_price']) && $product['sale_price'] !== null && $product['sale_price'] > 0 && $product['sale_price'] < $product['price'];
                        $activePrice = $hasSale ? $product['sale_price'] : $product['price'];
                    ?>
                    <!-- Price -->
                    <div class="bg-gradient-to-r from-rose-50 to-pink-50 p-6 rounded-xl mb-6">
                        <div class="product-price-container">
                            <?php if ($hasSale): ?>
                                <!-- Giá gốc - Dòng trên -->
                                <div class="product-price-original-line mb-2">
                                    <span class="text-xl text-gray-400 line-through">
                                        <?php echo formatPrice($product['price']); ?>
                                    </span>
                                </div>
                                <!-- Giá khuyến mãi - Dòng dưới -->
                                <div class="product-price-sale-line flex items-baseline gap-3">
                                    <span class="text-3xl font-bold text-rose-500">
                                        <?php echo formatPrice($product['sale_price']); ?>
                                    </span>
                                    <span class="bg-rose-500 text-white px-3 py-1 rounded-full text-sm font-bold">
                                        -<?php echo calculateDiscount($product['price'], $product['sale_price']); ?>%
                                    </span>
                                </div>
                                <p class="text-sm text-gray-600 mt-3">
                                    <i class="fas fa-tag mr-1"></i>
                                    Tiết kiệm: <strong class="text-rose-500"><?php echo formatPrice($product['price'] - $product['sale_price']); ?></strong>
                                </p>
                            <?php else: ?>
                                <div class="product-price-sale-line flex items-baseline gap-3">
                                    <span class="text-3xl font-bold text-rose-500">
                                        <?php echo formatPrice($product['price']); ?>
                                    </span>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Description -->
                    <div class="mb-6">
                        <p class="text-gray-700 leading-relaxed"><?php echo $product['description']; ?></p>
                    </div>

                    <!-- Quantity -->
                    <div class="mb-6">
                        <label class="block font-semibold mb-2">Số lượng:</label>
                        <div class="flex items-center gap-3">
                            <div class="flex items-center border-2 border-gray-300 rounded-lg">
                                <button onclick="decreaseQuantity()" class="w-10 h-10 flex items-center justify-center hover:bg-gray-100 transition">
                                    <i class="fas fa-minus"></i>
                                </button>
                                <input type="number" 
                                       id="quantity" 
                                       value="1" 
                                       min="1" 
                                       max="<?php echo $product['stock']; ?>"
                                       class="w-16 text-center border-0 focus:outline-none font-semibold">
                                <button onclick="increaseQuantity()" class="w-10 h-10 flex items-center justify-center hover:bg-gray-100 transition">
                                    <i class="fas fa-plus"></i>
                                </button>
                            </div>
                            <span class="inline-flex items-center gap-2 px-3 py-2 rounded-full bg-green-50 text-green-700 border border-green-100 text-sm font-semibold shadow-sm">
                                <i class="fas fa-box-open"></i>
                                <?php echo $product['stock']; ?> sản phẩm có sẵn
                            </span>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex flex-col items-center gap-4 mb-6">
                        <button onclick="addToCart(<?php echo $product['id']; ?>, document.getElementById('quantity').value, {
                            name: '<?php echo addslashes($product['name']); ?>', 
                            slug: '<?php echo $product['slug']; ?>', 
                            image: '<?php echo $product['images'][0]; ?>', 
                            price: <?php echo $product['price']; ?>, 
                            sale_price: <?php echo $product['sale_price'] ? $product['sale_price'] : 'null'; ?>,
                            rating: <?php echo isset($product['rating']) && $product['rating'] > 0 ? $product['rating'] : 0; ?>,
                            reviews: <?php echo isset($product['reviews']) ? $product['reviews'] : 0; ?>,
                            sold: <?php echo isset($product['sold']) ? $product['sold'] : 0; ?>
                        })" 
                                class="w-full md:w-2/3 bg-gradient-to-r from-rose-500 to-pink-500 text-white py-4 rounded-xl font-bold text-lg hover:shadow-lg transition-all hover:-translate-y-1">
                            <i class="fas fa-shopping-cart mr-2"></i>
                            Thêm vào giỏ
                        </button>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 w-full">
                            <a href="https://zalo.me/0389932688" target="_blank"
                               class="text-center bg-blue-500 text-white py-4 rounded-xl font-bold text-lg hover:shadow-lg transition-all hover:-translate-y-1">
                                <i class="fas fa-bolt mr-2"></i>
                                Đặt nhanh qua Zalo
                            </a>
                            <a href="tel:0389932688"
                               class="text-center bg-green-500 text-white py-4 rounded-xl font-bold text-lg hover:shadow-lg transition-all hover:-translate-y-1">
                                <i class="fas fa-phone-alt mr-2"></i>
                                Gọi đặt nhanh
                            </a>
                        </div>
                    </div>

                    <!-- Product Info Tags -->
                    <div class="mt-6 p-4 bg-blue-50 rounded-lg">
                        <ul class="space-y-2 text-sm text-gray-700">
                            <li><i class="fas fa-truck text-blue-500 mr-2"></i> Giao hàng nhanh trong 90-120 phút</li>
                            <li><i class="fas fa-gift text-blue-500 mr-2"></i> Miễn phí thiệp hoặc decal in</li>
                            <li><i class="fas fa-undo text-blue-500 mr-2"></i> Đổi trả trong 24h nếu hoa không đúng mẫu</li>
                            <li><i class="fas fa-phone text-blue-500 mr-2"></i> Hotline: <?php echo CONTACT_PHONE_HN; ?></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Product Details Tabs -->
<section class="py-8 bg-gray-50">
    <div class="container mx-auto px-4">
        <div class="bg-white rounded-xl shadow-sm overflow-hidden">
            <div class="border-b">
                <div class="flex">
                    <button class="tab-btn active px-6 py-4 font-semibold" onclick="showTab('details')">
                        Thông tin chi tiết
                    </button>
                    <button class="tab-btn px-6 py-4 font-semibold" onclick="showTab('reviews')">
                        Đánh giá (<?php echo $product['reviews']; ?>)
                    </button>
                </div>
            </div>

            <div id="tab-details" class="tab-content p-6">
                <h3 class="text-xl font-bold mb-4">Thông tin sản phẩm</h3>
                <table class="w-full">
                    <?php foreach ($product['details'] as $key => $value): ?>
                        <tr class="border-b">
                            <td class="py-3 font-semibold w-1/3"><?php echo $key; ?></td>
                            <td class="py-3 text-gray-700"><?php echo $value; ?></td>
                        </tr>
                    <?php endforeach; ?>
                </table>
            </div>

            <div id="tab-reviews" class="tab-content p-6 hidden">
                <h3 class="text-xl font-bold mb-6">Đánh giá từ khách hàng</h3>
                
                <!-- Review Summary -->
                <?php if ($product['reviews'] > 0): ?>
                <div class="bg-gray-50 p-6 rounded-lg mb-6">
                    <div class="flex items-center gap-8">
                        <div class="text-center">
                                <div class="text-5xl font-bold text-rose-500 mb-2"><?php echo number_format($product['rating'], 1); ?></div>
                                <div class="flex text-yellow-400 text-2xl mb-2 justify-center">
                                    <?php 
                                    $fullStars = floor($product['rating']);
                                    $hasHalfStar = ($product['rating'] - $fullStars) >= 0.5;
                                    for ($i = 1; $i <= 5; $i++): 
                                        if ($i <= $fullStars):
                                    ?>
                                    <i class="fas fa-star"></i>
                                    <?php elseif ($i == $fullStars + 1 && $hasHalfStar): ?>
                                        <i class="fas fa-star-half-alt"></i>
                                    <?php else: ?>
                                        <i class="far fa-star"></i>
                                    <?php endif; endfor; ?>
                            </div>
                                <div class="text-gray-600"><?php echo number_format($product['reviews']); ?> đánh giá</div>
                        </div>
                        <div class="flex-1">
                                <?php 
                                $totalReviews = $product['reviews'];
                                for ($i = 5; $i >= 1; $i--): 
                                    $count = $product['rating_distribution'][strval($i)] ?? 0;
                                    $percentage = $totalReviews > 0 ? ($count / $totalReviews) * 100 : 0;
                                ?>
                                <div class="flex items-center gap-3 mb-2">
                                    <span class="w-8"><?php echo $i; ?> <i class="fas fa-star text-yellow-400 text-sm"></i></span>
                                    <div class="flex-1 bg-gray-200 h-2 rounded-full">
                                            <div class="bg-yellow-400 h-2 rounded-full" style="width: <?php echo $percentage; ?>%"></div>
                                        </div>
                                        <span class="w-12 text-sm text-gray-600 text-right"><?php echo $count; ?></span>
                                    </div>
                                <?php endfor; ?>
                                </div>
                        </div>
                    </div>
                <?php else: ?>
                    <div class="bg-gray-50 p-6 rounded-lg mb-6 text-center">
                        <p class="text-gray-600">Chưa có đánh giá nào cho sản phẩm này.</p>
                </div>
                <?php endif; ?>

                <!-- Reviews List -->
                <div class="space-y-6">
                    <?php if (!empty($product['reviews_list'])): ?>
                        <?php foreach ($product['reviews_list'] as $review): ?>
                        <div class="border-b pb-6">
                            <div class="flex items-start gap-4">
                                <div class="w-12 h-12 bg-gradient-to-br from-rose-500 to-pink-500 rounded-full flex items-center justify-center text-white font-bold text-lg">
                                        <?php 
                                        $initials = strtoupper(mb_substr($review['customer_name'], 0, 1));
                                        echo htmlspecialchars($initials);
                                        ?>
                                </div>
                                <div class="flex-1">
                                    <div class="flex items-center gap-3 mb-2">
                                            <span class="font-semibold"><?php echo htmlspecialchars($review['customer_name']); ?></span>
                                        <div class="flex text-yellow-400">
                                                <?php for ($j = 1; $j <= 5; $j++): ?>
                                                    <?php if ($j <= $review['rating']): ?>
                                                <i class="fas fa-star text-sm"></i>
                                                    <?php else: ?>
                                                        <i class="far fa-star text-sm"></i>
                                                    <?php endif; ?>
                                            <?php endfor; ?>
                                            </div>
                                        </div>
                                        <?php if (!empty($review['comment'])): ?>
                                            <p class="text-gray-700 mb-2">
                                                <?php echo nl2br(htmlspecialchars($review['comment'])); ?>
                                            </p>
                                        <?php endif; ?>
                                        <span class="text-sm text-gray-500">
                                            <?php echo date('d/m/Y', strtotime($review['created_at'])); ?>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="text-center py-8 text-gray-500">
                            <p>Chưa có đánh giá nào. Hãy là người đầu tiên đánh giá sản phẩm này!</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Related Products -->
<section class="py-12">
    <div class="container mx-auto px-4">
        <h2 class="text-3xl font-bold text-center mb-8">
            <span class="text-gradient">Sản Phẩm Liên Quan</span>
        </h2>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
            <?php foreach ($related_products as $product): ?>
                <?php include '../includes/product-card.php'; ?>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<script>
function changeMainImage(imageSrc) {
    document.getElementById('main-image').src = imageSrc;
}

function decreaseQuantity() {
    const input = document.getElementById('quantity');
    if (input.value > 1) {
        input.value = parseInt(input.value) - 1;
    }
}

function increaseQuantity() {
    const input = document.getElementById('quantity');
    const max = parseInt(input.max);
    if (input.value < max) {
        input.value = parseInt(input.value) + 1;
    }
}

function buyNow(productId) {
    const quantity = document.getElementById('quantity').value;
    addToCart(productId, quantity);
    window.location.href = '<?php echo APP_URL; ?>/cart.php';
}

function showTab(tabName) {
    // Hide all tabs
    document.querySelectorAll('.tab-content').forEach(tab => {
        tab.classList.add('hidden');
    });
    
    // Remove active class from all buttons
    document.querySelectorAll('.tab-btn').forEach(btn => {
        btn.classList.remove('active', 'text-rose-500', 'border-b-2', 'border-rose-500');
    });
    
    // Show selected tab
    document.getElementById('tab-' + tabName).classList.remove('hidden');
    
    // Add active class to clicked button
    event.target.classList.add('active', 'text-rose-500', 'border-b-2', 'border-rose-500');
}
</script>

<?php include '../includes/footer.php'; ?>

