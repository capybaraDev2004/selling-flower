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

<div class="group bg-white rounded-xl shadow-sm hover:shadow-xl transition-all duration-300 overflow-hidden product-card-wrapper">
    <!-- Product Image -->
    <div class="relative bg-gray-50 product-image-container">
        <a href="<?php echo APP_URL; ?>/product.php?slug=<?php echo $product['slug']; ?>" class="block">
            <img src="<?php echo $product['image']; ?>" 
                 alt="<?php echo htmlspecialchars($product['name']); ?>"
                 class="product-image"
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
                <div class="product-rating-stars-score flex items-center mb-1">
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
                <button onclick="buyNowSingle(<?php echo $product['id']; ?>, 1, {
                name: '<?php echo addslashes($product['name']); ?>', 
                slug: '<?php echo $product['slug']; ?>', 
                image: '<?php echo $product['image']; ?>', 
                price: <?php echo $product['price']; ?>, 
                sale_price: <?php echo $product['sale_price'] ? $product['sale_price'] : 'null'; ?>,
                rating: <?php echo isset($product['rating']) && $product['rating'] > 0 ? $product['rating'] : (isset($product['rating_avg']) ? $product['rating_avg'] : 0); ?>,
                reviews: <?php echo isset($product['reviews']) ? $product['reviews'] : (isset($product['reviews_count']) ? $product['reviews_count'] : (isset($product['rating_count']) ? $product['rating_count'] : 0)); ?>,
                sold: <?php echo isset($product['sold']) ? $product['sold'] : (isset($product['purchases']) ? $product['purchases'] : (isset($product['sold_count']) ? $product['sold_count'] : 0)); ?>
                }, '<?php echo APP_URL; ?>')" 
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

/* Đảm bảo tất cả card có cùng chiều cao */
.product-card-wrapper {
    height: 100% !important;
    display: flex !important;
    flex-direction: column !important;
}

.product-image-container {
    flex-shrink: 0 !important;
}

.product-card-info {
    display: flex !important;
    flex-direction: column !important;
    flex: 1 !important;
    min-height: 0 !important;
}

/* Đảm bảo grid items có cùng chiều cao */
.grid > .product-card-wrapper,
[class*="grid"] > .product-card-wrapper {
    height: 100% !important;
}

/* Container ảnh - Cố định chiều cao cho shop/category/homepage */
.product-image-container {
    width: 100%;
    padding: 0;
    position: relative;
    overflow: hidden;
    background-color: #f9fafb;
    aspect-ratio: 1 / 1;
    height: 0;
    padding-bottom: 100%;
}

.product-image-container a {
    display: block;
    width: 100%;
    height: 100%;
    position: absolute;
    top: 0;
    left: 0;
}

/* Ảnh sản phẩm - Cố định kích thước, fill đầy container */
.product-image {
    width: 100%;
    height: 100%;
    display: block;
    object-fit: cover;
    transition: transform 0.5s ease;
    position: absolute;
    top: 0;
    left: 0;
}

/* Hover effect - scale ảnh khi hover */
.group:hover .product-image {
    transform: scale(1.05);
}

/* Product Card Mobile Layout - Mỗi phần 1 dòng */
@media (max-width: 768px) {
    .product-card-info {
        padding: 1rem !important;
    }
    
    /* Tên sản phẩm - Chiều cao tự động, chỉ tăng khi cần */
    .product-name {
        display: block !important;
        overflow: visible !important;
        text-overflow: unset !important;
        white-space: normal !important;
        word-wrap: break-word !important;
        min-height: auto !important;
        height: auto !important;
        max-height: none !important;
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
        display: block !important;
        overflow: visible !important;
        text-overflow: unset !important;
        white-space: normal !important;
        word-wrap: break-word !important;
        min-height: auto !important;
        height: auto !important;
        max-height: none !important;
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

<script>
// Đồng bộ chiều cao tên sản phẩm trong cùng hàng
(function() {
    let isRunning = false;
    
    function syncProductNameHeights(attempt = 0) {
        if (isRunning && attempt === 0) return;
        isRunning = true;
        
        // Tìm tất cả grid container chứa product cards
        const grids = document.querySelectorAll('.product-grid');
        
        if (grids.length === 0) {
            isRunning = false;
            return;
        }
        
        let processedGrids = 0;
        const totalGrids = grids.length;
        
        grids.forEach(grid => {
            // Lấy tất cả product cards trong grid này
            const cards = Array.from(grid.querySelectorAll('.product-card-wrapper'));
            if (cards.length === 0) {
                processedGrids++;
                if (processedGrids === totalGrids) {
                    isRunning = false;
                }
                return;
            }
            
            // Reset height trước để tính lại chính xác
            cards.forEach(card => {
                const nameEl = card.querySelector('.product-name');
                if (nameEl) {
                    nameEl.style.removeProperty('height');
                    nameEl.style.removeProperty('min-height');
                    nameEl.style.removeProperty('max-height');
                }
            });
            
            // Force reflow để đảm bảo layout đã cập nhật
            void grid.offsetHeight;
            
            // Đợi một chút để browser render lại
            requestAnimationFrame(() => {
                requestAnimationFrame(() => {
                    // Tính toán vị trí của các card sau khi reset
                    const gridRect = grid.getBoundingClientRect();
                    const cardData = cards.map(card => {
                        const cardRect = card.getBoundingClientRect();
                        const nameEl = card.querySelector('.product-name');
                        // Tính top position relative to grid
                        const relativeTop = cardRect.top - gridRect.top;
                        
                        return {
                            card: card,
                            top: Math.round(relativeTop),
                            nameEl: nameEl
                        };
                    });
                    
                    // Nhóm cards theo hàng (cùng top position, sai số 3px)
                    const rows = [];
                    cardData.forEach(item => {
                        let foundRow = false;
                        for (let row of rows) {
                            // So sánh top position với sai số nhỏ
                            if (Math.abs(row[0].top - item.top) <= 3) {
                                row.push(item);
                                foundRow = true;
                                break;
                            }
                        }
                        if (!foundRow) {
                            rows.push([item]);
                        }
                    });
                    
                    // Xử lý từng hàng
                    rows.forEach(row => {
                        // Reset lại height để đo chính xác
                        row.forEach(item => {
                            if (item.nameEl) {
                                item.nameEl.style.removeProperty('height');
                                item.nameEl.style.removeProperty('min-height');
                            }
                        });
                        
                        // Force reflow
                        void row[0].card.offsetHeight;
                        
                        // Tính chiều cao tối đa trong hàng
                        let maxHeight = 0;
                        row.forEach(item => {
                            if (item.nameEl) {
                                const height = item.nameEl.scrollHeight;
                                if (height > maxHeight) {
                                    maxHeight = height;
                                }
                            }
                        });
                        
                        // Áp dụng chiều cao tối đa cho TẤT CẢ card trong hàng
                        // Luôn áp dụng để đảm bảo bằng nhau
                        if (maxHeight > 0) {
                            row.forEach(item => {
                                if (item.nameEl) {
                                    // Dùng setProperty với important để override CSS
                                    item.nameEl.style.setProperty('height', maxHeight + 'px', 'important');
                                    item.nameEl.style.setProperty('min-height', maxHeight + 'px', 'important');
                                }
                            });
                        }
                    });
                    
                    processedGrids++;
                    if (processedGrids === totalGrids) {
                        // Chạy lại một lần nữa sau khi đã set height để đảm bảo đồng bộ
                        if (attempt === 0) {
                            setTimeout(() => {
                                syncProductNameHeights(1);
                            }, 150);
                        } else {
                            isRunning = false;
                        }
                    }
                });
            });
        });
    }
    
    // Đợi images load xong
    function waitForImages() {
        const images = document.querySelectorAll('.product-image');
        let loadedCount = 0;
        const totalImages = images.length;
        
        if (totalImages === 0) {
            setTimeout(syncProductNameHeights, 200);
            return;
        }
        
        let allLoaded = false;
        const checkComplete = () => {
            if (allLoaded) return;
            loadedCount++;
            if (loadedCount >= totalImages) {
                allLoaded = true;
                setTimeout(syncProductNameHeights, 200);
            }
        };
        
        images.forEach(img => {
            if (img.complete) {
                checkComplete();
            } else {
                img.addEventListener('load', checkComplete, { once: true });
                img.addEventListener('error', checkComplete, { once: true });
            }
        });
    }
    
    // Chạy khi DOM ready
    function initSync() {
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', function() {
                setTimeout(waitForImages, 100);
            });
        } else {
            setTimeout(waitForImages, 100);
        }
    }
    
    initSync();
    
    // Chạy lại khi resize (để xử lý responsive)
    let resizeTimer;
    window.addEventListener('resize', function() {
        clearTimeout(resizeTimer);
        resizeTimer = setTimeout(function() {
            // Reset tất cả height trước khi tính lại
            const allCards = document.querySelectorAll('.product-card-wrapper');
            allCards.forEach(card => {
                const nameEl = card.querySelector('.product-name');
                if (nameEl) {
                    nameEl.style.removeProperty('height');
                    nameEl.style.removeProperty('min-height');
                }
            });
            setTimeout(syncProductNameHeights, 150);
        }, 300);
    });
    
    // Chạy lại khi có thay đổi nội dung (MutationObserver)
    const observer = new MutationObserver(function(mutations) {
        let shouldSync = false;
        mutations.forEach(function(mutation) {
            if (mutation.type === 'childList' && mutation.addedNodes.length > 0) {
                shouldSync = true;
            }
        });
        if (shouldSync) {
            setTimeout(syncProductNameHeights, 200);
        }
    });
    
    // Quan sát các grid container
    function observeGrids() {
        const grids = document.querySelectorAll('.product-grid');
        grids.forEach(grid => {
            observer.observe(grid, {
                childList: true,
                subtree: true
            });
        });
    }
    
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', observeGrids);
    } else {
        observeGrids();
    }
})();
</script>

