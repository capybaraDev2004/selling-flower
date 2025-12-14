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
    header("Location: ./index.php");
    exit;
}

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
        // Bỏ qua các attribute "Danh mục" và "SKU" - không hiển thị
        if ($attr['attribute_name'] === 'Danh mục' || $attr['attribute_name'] === 'SKU') {
            continue;
        }
        $details[$attr['attribute_name']] = $attr['attribute_value'];
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
<div class="bg-rose-50">
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
                    <div class="mb-4 rounded-xl overflow-hidden border-2 border-gray-200 bg-white flex items-center justify-center cursor-zoom-in" style="min-height: 400px;" onclick="openImageZoom(document.getElementById('main-image').src)">
                        <img id="main-image" 
                             src="<?php echo $product['images'][0]; ?>" 
                             alt="<?php echo htmlspecialchars($product['name']); ?>"
                             class="w-full h-auto object-contain">
                    </div>
                    
                    <!-- Thumnail Images -->
                    <div class="grid grid-cols-4 gap-3">
                        <?php foreach ($product['images'] as $index => $image): ?>
                            <div class="rounded-lg overflow-hidden border-2 border-gray-200 cursor-pointer hover:border-rose-500 transition aspect-square bg-white flex items-center justify-center"
                                 onclick="changeMainImage('<?php echo $image; ?>')">
                                <img src="<?php echo $image; ?>" 
                                     alt="Hình <?php echo $index + 1; ?>"
                                     class="w-full h-full object-contain p-1 cursor-zoom-in"
                                     onclick="event.stopPropagation(); openImageZoom('<?php echo $image; ?>')">
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
                    <div class="flex items-center gap-2 mb-4">
                        <div class="flex items-center">
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
                            <span class="text-gray-600" style="margin-left: 5px;">(<?php echo number_format($product['reviews']); ?> đánh giá)</span>
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
                    <div class="mb-6 product-description">
                        <div class="text-gray-700 leading-relaxed"><?php echo $product['description']; ?></div>
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
                    <div class="flex flex-col items-center gap-4 mb-6 w-full">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3 w-full">
                            <button onclick="buyNow(<?php echo $product['id']; ?>)"
                                class="w-full bg-white text-rose-600 border-2 border-rose-500 py-4 rounded-xl font-bold text-lg hover:bg-rose-50 transition-all hover:-translate-y-1">
                                <i class="fas fa-bolt mr-2"></i>
                                Mua ngay
                            </button>
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
                                    class="w-full bg-gradient-to-r from-rose-500 to-pink-500 text-white py-4 rounded-xl font-bold text-lg hover:shadow-lg transition-all hover:-translate-y-1">
                            <i class="fas fa-shopping-cart mr-2"></i>
                            Thêm vào giỏ
                        </button>
                        </div>
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
                    <button class="tab-btn active px-6 py-4 font-semibold text-rose-500 border-b-2 border-rose-500" onclick="showTab('details', this)">
                        Mô tả
                    </button>
                    <button class="tab-btn px-6 py-4 font-semibold" onclick="showTab('reviews', this)">
                        Đánh giá (<?php echo $product['reviews']; ?>)
                    </button>
                </div>
            </div>

            <div id="tab-details" class="tab-content p-6">
                <?php if (!empty($product['details'])): ?>
                    <div class="space-y-2 text-gray-700 leading-relaxed">
                        <?php foreach ($product['details'] as $value): ?>
                            <?php 
                                $normalized = preg_replace('/(\r?\n){2,}/', "\n", $value);
                                // Giữ lại thẻ nhấn mạnh cơ bản, tránh script
                                $safeHtml = strip_tags($normalized, '<strong><b><em><i><u><br><p><ul><ol><li>');
                            ?>
                            <div class="whitespace-pre-wrap"><?php echo nl2br($safeHtml); ?></div>
                    <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <p class="text-gray-500">Chưa có thông tin sản phẩm.</p>
                <?php endif; ?>
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
// Dữ liệu sản phẩm phục vụ "Mua ngay" tách biệt giỏ hàng
const productPayload = {
    id: <?php echo (int) $product['id']; ?>,
    name: "<?php echo addslashes($product['name']); ?>",
    slug: "<?php echo addslashes($product['slug']); ?>",
    image: "<?php echo addslashes($product['images'][0] ?? (IMAGES_URL . '/products/default.jpg')); ?>",
    price: <?php echo floatval($product['price']); ?>,
    sale_price: <?php echo $product['sale_price'] ? floatval($product['sale_price']) : 'null'; ?>,
    rating: <?php echo isset($product['rating']) ? floatval($product['rating']) : 'null'; ?>,
    reviews: <?php echo isset($product['reviews']) ? intval($product['reviews']) : 'null'; ?>,
    sold: <?php echo isset($product['sold']) ? intval($product['sold']) : 'null'; ?>
};

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
    const quantity = parseInt(document.getElementById('quantity').value) || 1;

    // Lấy / tạo guestId
    let gid = localStorage.getItem('guest_id');
    if (!gid && typeof getOrCreateGuestId === 'function') {
        gid = getOrCreateGuestId();
    }
    if (!gid) {
        const ts = Date.now();
        gid = `guest_${ts}_${Math.random().toString(36).slice(2)}`;
        localStorage.setItem('guest_id', gid);
    }

    // Lưu dữ liệu mua ngay tách biệt giỏ hàng
    const buyNowCartKey = `buy_now_cart_${gid}`;
    const buyNowProductsKey = `buy_now_products_${gid}`;

    const cart = [{ id: productId, quantity }];
    const products = {};
    products[productId] = {
        ...productPayload,
        quantity
    };

    localStorage.setItem(buyNowCartKey, JSON.stringify(cart));
    localStorage.setItem(buyNowProductsKey, JSON.stringify(products));

    // Chuyển sang checkout với mode buy_now, không đụng tới giỏ hàng chính
    window.location.href = '<?php echo APP_URL; ?>/checkout.php?buy_now=1';
}

function showTab(tabName, el) {
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
    const target = el || event.target;
    target.classList.add('active', 'text-rose-500', 'border-b-2', 'border-rose-500');
}

// Image Lightbox với zoom bằng chuột
let currentZoom = 1;
let translateX = 0;
let translateY = 0;

function openImageZoom(imageUrl) {
    currentZoom = 1;
    translateX = 0;
    translateY = 0;
    const img = document.getElementById('zoom-image');
    img.src = imageUrl;
    img.style.transform = 'scale(1) translate(0, 0)';
    document.getElementById('image-zoom-modal').classList.remove('hidden');
    document.body.style.overflow = 'hidden';
}

function closeImageZoom() {
    currentZoom = 1;
    translateX = 0;
    translateY = 0;
    const img = document.getElementById('zoom-image');
    img.style.transform = 'scale(1) translate(0, 0)';
    document.getElementById('image-zoom-modal').classList.add('hidden');
    document.body.style.overflow = '';
}

// Mouse wheel zoom
document.addEventListener('DOMContentLoaded', function() {
    const modal = document.getElementById('image-zoom-modal');
    const img = document.getElementById('zoom-image');
    const container = document.getElementById('zoom-image-container');
    
    // Close modal khi click vào background
    modal.addEventListener('click', function(e) {
        if (e.target === this || e.target === container) {
            closeImageZoom();
        }
    });
    
    // Mouse wheel zoom
    container.addEventListener('wheel', function(e) {
        e.preventDefault();
        const delta = e.deltaY > 0 ? -0.1 : 0.1;
        const oldZoom = currentZoom;
        currentZoom = Math.max(0.5, Math.min(3, currentZoom + delta));
        
        // Reset translate khi zoom về 1
        if (currentZoom <= 1) {
            translateX = 0;
            translateY = 0;
            container.style.cursor = 'default';
            img.style.cursor = 'default';
        } else {
            container.style.cursor = 'grab';
            img.style.cursor = 'grab';
        }
        
        img.style.transform = `scale(${currentZoom}) translate(${translateX / currentZoom}px, ${translateY / currentZoom}px)`;
    }, { passive: false });
    
    // Drag ảnh khi đã zoom (desktop)
    let isDragging = false;
    let startX, startY;
    
    container.addEventListener('mousedown', function(e) {
        if (currentZoom > 1 && e.target === img) {
            isDragging = true;
            container.style.cursor = 'grabbing';
            img.style.cursor = 'grabbing';
            startX = e.clientX - translateX;
            startY = e.clientY - translateY;
        }
    });
    
    document.addEventListener('mouseup', function() {
        isDragging = false;
        if (currentZoom > 1) {
            container.style.cursor = 'grab';
            img.style.cursor = 'grab';
        }
    });
    
    document.addEventListener('mousemove', function(e) {
        if (!isDragging || currentZoom <= 1) return;
        e.preventDefault();
        translateX = e.clientX - startX;
        translateY = e.clientY - startY;
        img.style.transform = `scale(${currentZoom}) translate(${translateX / currentZoom}px, ${translateY / currentZoom}px)`;
    });
    
    // ========== MOBILE TOUCH EVENTS ==========
    let lastTap = 0;
    let touchStartDistance = 0;
    let touchStartZoom = 1;
    let isPinching = false;
    let touchStartX = 0;
    let touchStartY = 0;
    let isTouchDragging = false;
    let tapTimeout = null;
    
    // Helper function để cập nhật transform
    function updateImageTransform() {
        img.style.transform = `scale(${currentZoom}) translate(${translateX / currentZoom}px, ${translateY / currentZoom}px)`;
    }
    
    // Pinch to zoom và drag
    img.addEventListener('touchstart', function(e) {
        if (e.touches.length === 2) {
            isPinching = true;
            e.preventDefault();
            const touch1 = e.touches[0];
            const touch2 = e.touches[1];
            touchStartDistance = Math.hypot(
                touch2.clientX - touch1.clientX,
                touch2.clientY - touch1.clientY
            );
            touchStartZoom = currentZoom;
            // Hủy tap timeout khi bắt đầu pinch
            if (tapTimeout) {
                clearTimeout(tapTimeout);
                tapTimeout = null;
            }
        } else if (e.touches.length === 1 && currentZoom > 1) {
            // Bắt đầu drag khi đã zoom
            isTouchDragging = true;
            const touch = e.touches[0];
            touchStartX = touch.clientX - translateX;
            touchStartY = touch.clientY - translateY;
        }
    }, { passive: false });
    
    img.addEventListener('touchmove', function(e) {
        if (e.touches.length === 2 && isPinching) {
            e.preventDefault();
            const touch1 = e.touches[0];
            const touch2 = e.touches[1];
            const currentDistance = Math.hypot(
                touch2.clientX - touch1.clientX,
                touch2.clientY - touch1.clientY
            );
            
            const scale = currentDistance / touchStartDistance;
            currentZoom = Math.max(0.5, Math.min(3, touchStartZoom * scale));
            
            // Reset translate khi zoom về 1
            if (currentZoom <= 1) {
                translateX = 0;
                translateY = 0;
            }
            
            updateImageTransform();
        } else if (e.touches.length === 1 && isTouchDragging && currentZoom > 1) {
            e.preventDefault();
            const touch = e.touches[0];
            translateX = touch.clientX - touchStartX;
            translateY = touch.clientY - touchStartY;
            updateImageTransform();
        }
    }, { passive: false });
    
    // Single tap và double tap
    img.addEventListener('touchend', function(e) {
        // Reset pinch và drag flags
        if (e.touches.length < 2) {
            isPinching = false;
        }
        if (e.touches.length === 0) {
            isTouchDragging = false;
        }
        
        // Chỉ xử lý tap khi không có pinch/drag
        if (!isPinching && !isTouchDragging && e.changedTouches.length === 1) {
            const currentTime = new Date().getTime();
            const tapLength = currentTime - lastTap;
            
            if (tapLength < 300 && tapLength > 0) {
                // Double tap - zoom in/out
                e.preventDefault();
                if (tapTimeout) {
                    clearTimeout(tapTimeout);
                }
                if (currentZoom > 1) {
                    // Đang zoom thì thu nhỏ về 1
                    currentZoom = 1;
                    translateX = 0;
                    translateY = 0;
                } else {
                    // Chưa zoom thì zoom to
                    currentZoom = 2;
                }
                updateImageTransform();
                lastTap = 0; // Reset để tránh trigger lại
            } else {
                // Single tap - thu nhỏ nếu đang zoom
                lastTap = currentTime;
                if (currentZoom > 1) {
                    e.preventDefault();
                    tapTimeout = setTimeout(function() {
                        currentZoom = 1;
                        translateX = 0;
                        translateY = 0;
                        updateImageTransform();
                    }, 200); // Delay để phân biệt với double tap
                }
            }
        }
    }, { passive: false });
});

// Keyboard navigation - ESC để đóng
document.addEventListener('keydown', function(e) {
    const modal = document.getElementById('image-zoom-modal');
    if (modal && !modal.classList.contains('hidden') && e.key === 'Escape') {
        closeImageZoom();
    }
});
</script>

<!-- Image Zoom Modal -->
<div id="image-zoom-modal" class="hidden fixed inset-0 flex items-center justify-center" style="z-index: 9999; background: rgba(0, 0, 0, 0.1);">
    <!-- Close Button -->
    <button onclick="closeImageZoom()" class="absolute top-4 right-4 bg-white bg-opacity-80 hover:bg-opacity-100 text-gray-700 rounded-full w-10 h-10 flex items-center justify-center transition-all shadow-lg" style="z-index: 10000;">
        <i class="fas fa-times text-lg"></i>
    </button>
    
    <!-- Image Container -->
    <div id="zoom-image-container" class="w-full h-full flex items-center justify-center overflow-hidden" style="cursor: grab;">
        <img id="zoom-image" 
             src="" 
             alt="Product Image"
             onclick="event.stopPropagation()"
             class="object-contain transition-transform duration-200"
             style="cursor: grab; max-width: 90vw; max-height: 90vh; transform-origin: center center; box-shadow: 0 0 900px 90px rgba(128, 128, 128, 0.9);">
    </div>
</div>

<style>
#image-zoom-modal {
    animation: fadeIn 0.2s ease;
}

@keyframes fadeIn {
    from { opacity: 0; }
    to { opacity: 1; }
}

#zoom-image {
    animation: zoomIn 0.2s ease;
}

@keyframes zoomIn {
    from { 
        opacity: 0;
        transform: scale(0.9);
    }
    to { 
        opacity: 1;
        transform: scale(1);
    }
}

/* Overlay nền tối nhẹ - không dùng background-color */
.image-modal-overlay {
    position: absolute;
    inset: 0;
    backdrop-filter: blur(2px);
    -webkit-backdrop-filter: blur(2px);
}

/* Tạo hiệu ứng tối nhẹ bằng pseudo-element */
.image-modal-overlay::before {
    content: '';
    position: absolute;
    inset: 0;
    opacity: 0.15;
    background: radial-gradient(circle at center, transparent 0%, rgba(0, 0, 0, 0.3) 100%);
}

/* Hoặc dùng cách khác - overlay mỏng */
.image-modal-overlay::after {
    content: '';
    position: absolute;
    inset: 0;
    opacity: 0.2;
    background: linear-gradient(to bottom, rgba(0, 0, 0, 0.1), rgba(0, 0, 0, 0.15));
    pointer-events: none;
}
</style>
</script>

<?php include '../includes/footer.php'; ?>

