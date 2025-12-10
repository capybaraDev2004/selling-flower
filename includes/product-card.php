<?php
/**
 * Component hiển thị card sản phẩm
 * 
 * @param array $product - Thông tin sản phẩm
 * - id: ID sản phẩm
 * - name: Tên sản phẩm
 * - slug: Slug sản phẩm
 * - image: URL hình ảnh
 * - price: Giá gốc
 * - sale_price: Giá khuyến mãi (optional)
 * - rating: Đánh giá (1-5) (optional)
 * - reviews: Số lượt đánh giá (optional)
 * - sold: Số lượng đã bán (optional)
 */

$has_sale = isset($product['sale_price']) && $product['sale_price'] < $product['price'];
$discount_percent = $has_sale ? calculateDiscount($product['price'], $product['sale_price']) : 0;
$display_price = $has_sale ? $product['sale_price'] : $product['price'];
?>

<div class="group bg-white rounded-xl shadow-sm hover:shadow-xl transition-all duration-300 overflow-hidden">
    <!-- Product Image -->
    <div class="relative overflow-hidden aspect-square">
        <a href="<?php echo APP_URL; ?>/product.php?slug=<?php echo $product['slug']; ?>">
            <img src="<?php echo $product['image']; ?>" 
                 alt="<?php echo htmlspecialchars($product['name']); ?>"
                 class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500"
                 loading="lazy">
        </a>
        
        <?php if ($has_sale): ?>
            <div class="absolute top-3 left-3 bg-rose-500 text-white px-3 py-1 rounded-full text-sm font-semibold">
                -<?php echo $discount_percent; ?>%
            </div>
        <?php endif; ?>
        
        <!-- Quick Actions -->
        <div class="absolute top-3 right-3 flex flex-col gap-2 opacity-0 group-hover:opacity-100 transition-opacity duration-300">
            <button onclick="addToWishlist(<?php echo $product['id']; ?>)" 
                    class="w-10 h-10 bg-white rounded-full flex items-center justify-center shadow-md hover:bg-rose-500 hover:text-white transition-colors"
                    title="Yêu thích">
                <i class="far fa-heart"></i>
            </button>
            <button onclick="quickView(<?php echo $product['id']; ?>)" 
                    class="w-10 h-10 bg-white rounded-full flex items-center justify-center shadow-md hover:bg-rose-500 hover:text-white transition-colors"
                    title="Xem nhanh">
                <i class="far fa-eye"></i>
            </button>
        </div>
        
        <!-- Add to Cart Button (appears on hover) -->
        <button onclick="addToCart(<?php echo $product['id']; ?>, 1, {
            name: '<?php echo addslashes($product['name']); ?>', 
            slug: '<?php echo $product['slug']; ?>', 
            image: '<?php echo $product['image']; ?>', 
            price: <?php echo $product['price']; ?>, 
            sale_price: <?php echo $product['sale_price'] ? $product['sale_price'] : 'null'; ?>,
            rating: <?php echo isset($product['rating']) && $product['rating'] > 0 ? $product['rating'] : (isset($product['rating_avg']) ? $product['rating_avg'] : 0); ?>,
            reviews: <?php echo isset($product['reviews']) ? $product['reviews'] : (isset($product['reviews_count']) ? $product['reviews_count'] : (isset($product['rating_count']) ? $product['rating_count'] : 0)); ?>,
            sold: <?php echo isset($product['sold']) ? $product['sold'] : (isset($product['purchases']) ? $product['purchases'] : (isset($product['sold_count']) ? $product['sold_count'] : 0)); ?>
        })" 
                class="absolute bottom-0 left-0 right-0 bg-rose-500 text-white py-3 font-semibold translate-y-full group-hover:translate-y-0 transition-transform duration-300 hover:bg-rose-600">
            <i class="fas fa-shopping-cart mr-2"></i>Thêm vào giỏ
        </button>
    </div>

    <!-- Product Info -->
    <div class="p-4 product-card-info">
        <a href="<?php echo APP_URL; ?>/product.php?slug=<?php echo $product['slug']; ?>" 
           class="block">
            <h3 class="font-semibold text-gray-800 mb-2 text-lg product-name hover:text-rose-500 transition-colors">
                <?php echo htmlspecialchars($product['name']); ?>
            </h3>
        </a>

        <!-- Rating & Reviews -->
        <?php if (isset($product['rating']) && $product['rating'] > 0): ?>
            <div class="product-rating mb-2 text-sm">
                <div class="product-rating-stars-score flex items-center gap-2 mb-1">
                    <div class="product-rating-stars flex items-center text-yellow-400">
                    <?php for ($i = 1; $i <= 5; $i++): ?>
                        <?php if ($i <= $product['rating']): ?>
                            <i class="fas fa-star"></i>
                        <?php else: ?>
                            <i class="far fa-star"></i>
                        <?php endif; ?>
                    <?php endfor; ?>
                </div>
                    <span class="product-rating-score text-gray-600">
                    <?php echo number_format($product['rating'], 1); ?>
                </span>
                </div>
                <?php if (isset($product['reviews'])): ?>
                    <div class="product-rating-reviews text-gray-400">
                        (<?php echo $product['reviews']; ?> đánh giá)
                    </div>
                <?php endif; ?>
            </div>
        <?php endif; ?>

        <!-- Sales Count -->
        <?php if (isset($product['sold']) && $product['sold'] > 0): ?>
            <div class="text-sm text-gray-600 mb-2">
                <i class="fas fa-check-circle text-green-500 mr-1"></i>
                Đã bán <?php echo $product['sold']; ?>
            </div>
        <?php endif; ?>

        <!-- Price -->
        <div class="product-price mb-3">
            <div class="product-price-current text-xl font-bold text-rose-500 mb-1">
                <?php echo formatPrice($display_price); ?>
            </div>
            <?php if ($has_sale): ?>
                <div class="product-price-original text-sm text-gray-400 line-through">
                    <?php echo formatPrice($product['price']); ?>
                </div>
            <?php endif; ?>
        </div>

        <!-- Stock Status & Buy Button -->
        <div class="product-stock-buy-wrapper text-sm">
            <div class="product-stock-status">
            <span class="text-green-600 font-medium">
                <i class="fas fa-check-circle mr-1"></i>Còn hàng
            </span>
            </div>
            <div class="product-buy-button">
            <button onclick="addToCart(<?php echo $product['id']; ?>, 1, {
                name: '<?php echo addslashes($product['name']); ?>', 
                slug: '<?php echo $product['slug']; ?>', 
                image: '<?php echo $product['image']; ?>', 
                price: <?php echo $product['price']; ?>, 
                sale_price: <?php echo $product['sale_price'] ? $product['sale_price'] : 'null'; ?>,
                rating: <?php echo isset($product['rating']) && $product['rating'] > 0 ? $product['rating'] : (isset($product['rating_avg']) ? $product['rating_avg'] : 0); ?>,
                reviews: <?php echo isset($product['reviews']) ? $product['reviews'] : (isset($product['reviews_count']) ? $product['reviews_count'] : (isset($product['rating_count']) ? $product['rating_count'] : 0)); ?>,
                sold: <?php echo isset($product['sold']) ? $product['sold'] : (isset($product['purchases']) ? $product['purchases'] : (isset($product['sold_count']) ? $product['sold_count'] : 0)); ?>
            })" 
                        class="product-buy-btn text-white font-medium transition-colors">
                Mua ngay <i class="fas fa-arrow-right ml-1"></i>
            </button>
            </div>
        </div>
    </div>
</div>

<style>
.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

/* Product Card Mobile Layout - Mỗi phần 1 dòng */
@media (max-width: 768px) {
    .product-card-info {
        padding: 1rem !important;
    }
    
    /* Tên sản phẩm - 1 dòng */
    .product-name {
        display: -webkit-box;
        -webkit-line-clamp: 1 !important;
        -webkit-box-orient: vertical;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
        min-height: auto !important;
        line-height: 1.5 !important;
        margin-bottom: 0.5rem !important;
    }
    
    /* Rating - Sao và số điểm cùng dòng, đánh giá dòng riêng */
    .product-rating {
        display: block !important;
        width: 100% !important;
    }
    
    .product-rating-stars-score {
        display: flex !important;
        align-items: center !important;
        width: 100% !important;
        margin-bottom: 0.25rem !important;
    }
    
    .product-rating-stars {
        display: flex !important;
        align-items: center !important;
        margin-bottom: 0 !important;
    }
    
    .product-rating-score {
        display: inline !important;
        margin-bottom: 0 !important;
    }
    
    .product-rating-reviews {
        display: block !important;
        width: 100% !important;
    }
    
    /* Giá - Mỗi giá 1 dòng */
    .product-price {
        display: block !important;
        width: 100% !important;
    }
    
    .product-price-current {
        display: block !important;
        width: 100% !important;
        margin-bottom: 0.25rem !important;
    }
    
    .product-price-original {
        display: block !important;
        width: 100% !important;
    }
    
    /* Còn hàng và Mua ngay - Mỗi cái 1 dòng riêng */
    .product-stock-buy-wrapper {
        display: block !important;
        width: 100% !important;
    }
    
    .product-stock-status {
        display: block !important;
        width: 100% !important;
        margin-bottom: 0.75rem !important;
    }
    
    /* Nút Mua ngay - Căn giữa và có style đẹp */
    .product-buy-button {
        display: block !important;
        width: 100% !important;
        text-align: center !important;
    }
    
    .product-buy-btn {
        display: inline-block !important;
        width: 100% !important;
        background: linear-gradient(135deg, #f43f5e 0%, #ec4899 100%) !important;
        color: white !important;
        padding: 0.75rem 1.5rem !important;
        border-radius: 0.5rem !important;
        border: none !important;
        font-weight: 600 !important;
        text-align: center !important;
        cursor: pointer !important;
        transition: all 0.3s ease !important;
        box-shadow: 0 4px 6px rgba(244, 63, 94, 0.2) !important;
    }
    
    .product-buy-btn:hover {
        transform: translateY(-2px) !important;
        box-shadow: 0 6px 12px rgba(244, 63, 94, 0.3) !important;
        background: linear-gradient(135deg, #e11d48 0%, #db2777 100%) !important;
    }
    
    .product-buy-btn:active {
        transform: translateY(0) !important;
    }
}

/* Desktop - Giữ nguyên layout cũ */
@media (min-width: 769px) {
    .product-name {
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
        min-height: 3rem;
    }
    
    .product-rating {
        display: flex !important;
        align-items: center !important;
        gap: 0.5rem !important;
        flex-direction: row !important;
    }
    
    .product-rating-stars-score {
        display: flex !important;
        align-items: center !important;
        gap: 0.5rem !important;
        margin-bottom: 0 !important;
    }
    
    .product-rating-stars {
        display: flex !important;
        align-items: center !important;
        margin-bottom: 0 !important;
    }
    
    .product-rating-score {
        display: inline !important;
        margin-bottom: 0 !important;
    }
    
    .product-rating-reviews {
        display: inline !important;
    }
    
    .product-price {
        display: flex !important;
        align-items: center !important;
        gap: 0.5rem !important;
        flex-direction: row !important;
    }
    
    .product-price-current {
        display: inline !important;
        margin-bottom: 0 !important;
    }
    
    .product-price-original {
        display: inline !important;
    }
    
    /* Trên desktop, "Còn hàng" và "Mua ngay" cùng dòng */
    .product-stock-buy-wrapper {
        display: flex !important;
        align-items: center !important;
        justify-content: space-between !important;
        flex-direction: row !important;
    }
    
    .product-stock-status {
        display: inline !important;
        margin-bottom: 0 !important;
    }
    
    .product-buy-button {
        display: inline !important;
    }
    
    .product-buy-button button {
        display: inline !important;
        width: auto !important;
        background: transparent !important;
        color: #f43f5e !important;
        padding: 0 !important;
        border-radius: 0 !important;
        border: none !important;
        box-shadow: none !important;
        text-align: left !important;
    }
    
    .product-buy-button button:hover {
        color: #e11d48 !important;
        transform: none !important;
        box-shadow: none !important;
        background: transparent !important;
    }
}
</style>

