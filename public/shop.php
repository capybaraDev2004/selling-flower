<?php
require_once '../config/config.php';
require_once BASE_PATH . '/app/Database/Database.php';
require_once BASE_PATH . '/app/Models/ProductModel.php';
require_once BASE_PATH . '/app/Models/ProductImageModel.php';
require_once BASE_PATH . '/app/Models/CategoryModel.php';

$page_title = 'Cửa Hàng - ' . APP_NAME;
$page_description = 'Xem tất cả các sản phẩm hoa tươi đẹp với giá tốt nhất.';

// Lấy tham số lọc và sắp xếp
$sort = isset($_GET['sort']) ? $_GET['sort'] : 'default';
$page = isset($_GET['page']) ? max(1, (int)($_GET['page'])) : 1;
$limit = PRODUCTS_PER_PAGE;
$offset = ($page - 1) * $limit;
$keyword = isset($_GET['q']) ? trim($_GET['q']) : '';

$productModel = new ProductModel();
$productImageModel = new ProductImageModel();
$categoryModel = new CategoryModel();

// Danh mục cho bộ lọc (chỉ lấy danh mục cha active)
$filter_categories_raw = $categoryModel->getAll('active');
$filter_categories = array_filter($filter_categories_raw, fn($c) => empty($c['parent_id']));

$priceRangeMap = [
    'lt200' => ['label' => 'Dưới 200.000đ', 'min' => null, 'max' => 200000],
    '200_500' => ['label' => '200.000đ - 500.000đ', 'min' => 200000, 'max' => 500000],
    '500_1000' => ['label' => '500.000đ - 1.000.000đ', 'min' => 500000, 'max' => 1000000],
    'gt1000' => ['label' => 'Trên 1.000.000đ', 'min' => 1000000, 'max' => null],
];

$selectedPriceKey = isset($_GET['price']) && isset($priceRangeMap[$_GET['price']]) ? $_GET['price'] : '';
$selectedRating = isset($_GET['rating_min']) ? max(0, (int)$_GET['rating_min']) : 0;
$selectedCategories = isset($_GET['cats']) && is_array($_GET['cats']) ? array_map('intval', $_GET['cats']) : [];

$filters = [];
if ($selectedPriceKey) {
    $range = $priceRangeMap[$selectedPriceKey];
    if ($range['min'] !== null) $filters['price_min'] = $range['min'];
    if ($range['max'] !== null) $filters['price_max'] = $range['max'];
}
if ($selectedRating > 0) {
    $filters['rating_min'] = $selectedRating;
}
if (!empty($selectedCategories)) {
    $filters['category_ids'] = $selectedCategories;
}

$queryParams = ['sort' => $sort];
if ($selectedPriceKey) $queryParams['price'] = $selectedPriceKey;
if ($selectedRating > 0) $queryParams['rating_min'] = $selectedRating;
if (!empty($selectedCategories)) {
    foreach ($selectedCategories as $catId) {
        $queryParams['cats'][] = $catId;
    }
}
if ($keyword !== '') {
    $queryParams['q'] = $keyword;
    $filters['keyword'] = $keyword;
}

// Tổng số sản phẩm và phân trang
$total_products = $productModel->countActiveWithFilters($filters);
$total_pages = $total_products > 0 ? ceil($total_products / $limit) : 1;

// Lấy sản phẩm từ DB
$products_raw = $productModel->getAllPaginatedWithFilters($limit, $offset, $sort, $filters);
$products = [];
foreach ($products_raw as $product) {
    $images = $productImageModel->getByProductId($product['id']);
    $mainImage = !empty($images) ? $images[0]['image_url'] : IMAGES_URL . '/products/default.jpg';
    
    if (strpos($mainImage, 'http') !== 0 && strpos($mainImage, '/') === 0) {
        $mainImage = APP_URL . $mainImage;
    } elseif (strpos($mainImage, 'http') !== 0) {
        $mainImage = IMAGES_URL . '/products/' . $mainImage;
    }
    
    $ratingCount = intval($product['rating_count']);
    $products[] = [
        'id' => $product['id'],
        'name' => $product['name'],
        'slug' => $product['slug'],
        'image' => $mainImage,
        'price' => floatval($product['price']),
        'sale_price' => $product['sale_price'] ? floatval($product['sale_price']) : null,
        'rating' => $ratingCount > 0 ? floatval($product['rating_avg']) : 0,
        'reviews' => $ratingCount,
        'sold' => intval($product['sold_count']),
        'category' => $product['category_name']
    ];
}

include '../includes/header.php';
?>

<!-- Breadcrumb -->
<div class="bg-rose-50 py-2">
    <div class="container mx-auto px-4" style="max-height: 40px !important; overflow: hidden;">
        <div class="breadcrumb text-lg text-rose-600 font-semibold" ">
            <a href="<?php echo APP_URL; ?>" class="hover:text-rose-700">
                <i class="fas fa-home"></i> Trang chủ
            </a>
            <span class="separator mx-2 text-rose-500">/</span>
            <span class="text-rose-700">Cửa hàng</span>
        </div>
    </div>
</div>

<!-- Shop Content -->
<section class="py-8">
    <div class="container mx-auto px-4">
        <div class="flex flex-col lg:flex-row gap-8">
            <!-- Mobile Filter Toggle + Overlay -->
            <button id="filter-toggle" class="mobile-filter-toggle lg:hidden">
                <i class="fas fa-filter"></i>
            </button>
            <div id="filter-overlay" class="filter-overlay lg:hidden"></div>

            <!-- Sidebar Filter -->
            <aside class="lg:w-1/4 filter-panel">
                <form method="get" class="filter-form bg-white rounded-xl shadow-sm p-6 space-y-6">
                    <input type="hidden" name="sort" value="<?php echo htmlspecialchars($sort); ?>">
                    <input type="hidden" name="page" value="1">
                    <?php if ($keyword !== ''): ?><input type="hidden" name="q" value="<?php echo htmlspecialchars($keyword); ?>"><?php endif; ?>
                    <h3 class="text-xl font-bold mb-2 flex items-center">
                        <i class="fas fa-filter text-rose-500 mr-2"></i>
                        Bộ lọc
                    </h3>

                    <!-- Price Filter -->
                    <div>
                        <h4 class="font-semibold mb-3">Khoảng giá</h4>
                        <div class="space-y-2">
                            <?php foreach ($priceRangeMap as $key => $range): ?>
                                <label class="flex items-center cursor-pointer">
                                    <input type="radio" name="price" value="<?php echo $key; ?>" class="w-4 h-4 text-rose-500" <?php echo $selectedPriceKey === $key ? 'checked' : ''; ?>>
                                    <span class="ml-2"><?php echo $range['label']; ?></span>
                                </label>
                            <?php endforeach; ?>
                            <label class="flex items-center cursor-pointer">
                                <input type="radio" name="price" value="" class="w-4 h-4 text-rose-500" <?php echo $selectedPriceKey === '' ? 'checked' : ''; ?>>
                                <span class="ml-2 text-gray-500">Bỏ chọn</span>
                            </label>
                        </div>
                    </div>

                    <!-- Category Filter -->
                    <div>
                        <h4 class="font-semibold mb-3">Danh mục</h4>
                        <div class="space-y-2">
                            <?php foreach ($filter_categories as $cat): ?>
                                <?php $checked = in_array((int)$cat['id'], $selectedCategories); ?>
                                <label class="flex items-center cursor-pointer">
                                    <input type="checkbox" name="cats[]" value="<?php echo $cat['id']; ?>" class="w-4 h-4 text-rose-500 rounded" <?php echo $checked ? 'checked' : ''; ?>>
                                    <span class="ml-2"><?php echo htmlspecialchars($cat['name']); ?></span>
                                </label>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <!-- Rating Filter -->
                    <div>
                        <h4 class="font-semibold mb-3">Đánh giá</h4>
                        <div class="space-y-2">
                            <?php for ($i = 5; $i >= 3; $i--): ?>
                                <label class="flex items-center cursor-pointer">
                                    <input type="radio" name="rating_min" value="<?php echo $i; ?>" class="w-4 h-4 text-rose-500" <?php echo $selectedRating === $i ? 'checked' : ''; ?>>
                                    <span class="ml-2 flex items-center text-yellow-400">
                                        <?php for ($j = 0; $j < $i; $j++): ?>
                                            <i class="fas fa-star"></i>
                                        <?php endfor; ?>
                                        <?php for ($j = $i; $j < 5; $j++): ?>
                                            <i class="far fa-star"></i>
                                        <?php endfor; ?>
                                        <span class="text-gray-600 ml-2">trở lên</span>
                                    </span>
                                </label>
                            <?php endfor; ?>
                            <label class="flex items-center cursor-pointer">
                                <input type="radio" name="rating_min" value="0" class="w-4 h-4 text-rose-500" <?php echo $selectedRating === 0 ? 'checked' : ''; ?>>
                                <span class="ml-2 text-gray-500">Bỏ chọn</span>
                            </label>
                        </div>
                    </div>

                    <button type="submit" class="w-full bg-gradient-to-r from-rose-500 to-pink-500 text-white py-2 rounded-lg font-semibold hover:shadow-lg transition">
                        Áp dụng bộ lọc
                    </button>
                </form>
            </aside>

            <!-- Products Grid -->
            <main class="lg:w-3/4">
                <!-- Toolbar -->
                <div class="bg-white rounded-xl shadow-sm p-4 mb-6 flex flex-col md:flex-row justify-between items-center gap-4">
                    <div class="text-gray-600">
                        Hiển thị <strong><?php echo $offset + 1; ?>-<?php echo min($offset + PRODUCTS_PER_PAGE, $total_products); ?></strong> 
                        trong tổng số <strong><?php echo $total_products; ?></strong> sản phẩm
                    </div>
                    <form method="get" class="flex gap-4 items-center" id="sort-form">
                        <?php if ($keyword !== ''): ?><input type="hidden" name="q" value="<?php echo htmlspecialchars($keyword); ?>"><?php endif; ?>
                        <?php if ($selectedPriceKey): ?><input type="hidden" name="price" value="<?php echo htmlspecialchars($selectedPriceKey); ?>"><?php endif; ?>
                        <?php if ($selectedRating > 0): ?><input type="hidden" name="rating_min" value="<?php echo $selectedRating; ?>"><?php endif; ?>
                        <?php if (!empty($selectedCategories)): ?>
                            <?php foreach ($selectedCategories as $catId): ?>
                                <input type="hidden" name="cats[]" value="<?php echo $catId; ?>">
                            <?php endforeach; ?>
                        <?php endif; ?>
                        <input type="hidden" name="page" value="1">
                        <label class="flex items-center gap-2">
                            <span class="text-gray-600">Sắp xếp:</span>
                            <select name="sort" class="border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:border-rose-500" onchange="document.getElementById('sort-form').submit();">
                                <option value="default" <?php echo $sort == 'default' ? 'selected' : ''; ?>>Mặc định</option>
                                <option value="price-asc" <?php echo $sort == 'price-asc' ? 'selected' : ''; ?>>Giá: Thấp đến Cao</option>
                                <option value="price-desc" <?php echo $sort == 'price-desc' ? 'selected' : ''; ?>>Giá: Cao đến Thấp</option>
                                <option value="newest" <?php echo $sort == 'newest' ? 'selected' : ''; ?>>Mới nhất</option>
                                <option value="popular" <?php echo $sort == 'popular' ? 'selected' : ''; ?>>Phổ biến nhất</option>
                            </select>
                        </label>
                    </form>
                </div>

                <!-- Active Filters -->
                <?php if ($selectedPriceKey || $selectedRating > 0 || !empty($selectedCategories) || $keyword !== ''): ?>
                    <div class="bg-rose-50 border border-rose-200 rounded-xl p-4 mb-6">
                        <div class="flex flex-wrap items-center gap-3">
                            <span class="text-rose-700 font-semibold flex items-center">
                                <i class="fas fa-filter mr-2"></i>Đang tìm với:
                            </span>
                            
                            <?php if ($selectedPriceKey): 
                                $removePriceParams = $queryParams;
                                unset($removePriceParams['price']);
                            ?>
                                <span class="inline-flex items-center gap-2 bg-white border border-rose-300 text-rose-700 px-4 py-2 rounded-full text-sm font-medium">
                                    <i class="fas fa-tag"></i>
                                    <?php echo $priceRangeMap[$selectedPriceKey]['label']; ?>
                                    <a href="?<?php echo http_build_query($removePriceParams); ?>" class="text-rose-500 hover:text-rose-700 ml-1">
                                        <i class="fas fa-times"></i>
                                    </a>
                                </span>
                            <?php endif; ?>

                            <?php if ($keyword !== ''): 
                                $removeKeywordParams = $queryParams;
                                unset($removeKeywordParams['q']);
                            ?>
                                <span class="inline-flex items-center gap-2 bg-white border border-rose-300 text-rose-700 px-4 py-2 rounded-full text-sm font-medium">
                                    <i class="fas fa-search"></i>
                                    Từ khóa: “<?php echo htmlspecialchars($keyword); ?>”
                                    <a href="?<?php echo http_build_query($removeKeywordParams); ?>" class="text-rose-500 hover:text-rose-700 ml-1">
                                        <i class="fas fa-times"></i>
                                    </a>
                                </span>
                            <?php endif; ?>
                            
                            <?php if ($selectedRating > 0): 
                                $removeRatingParams = $queryParams;
                                unset($removeRatingParams['rating_min']);
                            ?>
                                <span class="inline-flex items-center gap-2 bg-white border border-rose-300 text-rose-700 px-4 py-2 rounded-full text-sm font-medium">
                                    <i class="fas fa-star text-yellow-400"></i>
                                    <?php echo $selectedRating; ?> sao trở lên
                                    <a href="?<?php echo http_build_query($removeRatingParams); ?>" class="text-rose-500 hover:text-rose-700 ml-1">
                                        <i class="fas fa-times"></i>
                                    </a>
                                </span>
                            <?php endif; ?>
                            
                            <?php if (!empty($selectedCategories)): ?>
                                <?php 
                                $categoryMap = [];
                                foreach ($filter_categories as $cat) {
                                    $categoryMap[$cat['id']] = $cat['name'];
                                }
                                foreach ($selectedCategories as $catId): 
                                    if (isset($categoryMap[$catId])):
                                ?>
                                    <span class="inline-flex items-center gap-2 bg-white border border-rose-300 text-rose-700 px-4 py-2 rounded-full text-sm font-medium">
                                        <i class="fas fa-folder"></i>
                                        <?php echo htmlspecialchars($categoryMap[$catId]); ?>
                                        <?php 
                                        $newCats = array_values(array_diff($selectedCategories, [$catId]));
                                        $newParams = $queryParams;
                                        if (empty($newCats)) {
                                            unset($newParams['cats']);
                                        } else {
                                            $newParams['cats'] = $newCats;
                                        }
                                        ?>
                                        <a href="?<?php echo http_build_query($newParams); ?>" class="text-rose-500 hover:text-rose-700 ml-1">
                                            <i class="fas fa-times"></i>
                                        </a>
                                    </span>
                                <?php 
                                    endif;
                                endforeach; 
                                ?>
                            <?php endif; ?>
                            
                            <a href="?sort=<?php echo $sort; ?>" class="ml-auto text-rose-600 hover:text-rose-700 font-medium text-sm underline">
                                <i class="fas fa-times-circle mr-1"></i>Xóa tất cả bộ lọc
                            </a>
                        </div>
                    </div>
                <?php endif; ?>

                <!-- Products -->
                <div class="product-grid mb-8">
                    <?php foreach ($products as $product): ?>
                        <?php include '../includes/product-card.php'; ?>
                    <?php endforeach; ?>
                </div>

                <!-- Pagination -->
                <?php if ($total_pages > 1): ?>
                    <div class="pagination">
                        <?php if ($page > 1): ?>
                            <a href="?<?php echo http_build_query(array_merge($queryParams, ['page' => $page - 1])); ?>">
                                <i class="fas fa-chevron-left"></i>
                            </a>
                        <?php endif; ?>

                        <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                            <?php if ($i == $page): ?>
                                <span class="active"><?php echo $i; ?></span>
                            <?php else: ?>
                                <a href="?<?php echo http_build_query(array_merge($queryParams, ['page' => $i])); ?>"><?php echo $i; ?></a>
                            <?php endif; ?>
                        <?php endfor; ?>

                        <?php if ($page < $total_pages): ?>
                            <a href="?<?php echo http_build_query(array_merge($queryParams, ['page' => $page + 1])); ?>">
                                <i class="fas fa-chevron-right"></i>
                            </a>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            </main>
        </div>
    </div>
</section>

<script>
// Mobile filter toggle
document.addEventListener('DOMContentLoaded', function() {
    const toggleBtn = document.getElementById('filter-toggle');
    const panel = document.querySelector('.filter-panel');
    const overlay = document.getElementById('filter-overlay');
    if (!toggleBtn || !panel || !overlay) return;

    function setOpen(open) {
        panel.classList.toggle('open', open);
        overlay.classList.toggle('open', open);
        toggleBtn.classList.toggle('open', open);
        document.body.classList.toggle('no-scroll', open);
    }

    toggleBtn.addEventListener('click', () => {
        const isOpen = panel.classList.contains('open');
        setOpen(!isOpen);
    });

    overlay.addEventListener('click', () => setOpen(false));
});
</script>

<?php include '../includes/footer.php'; ?>

