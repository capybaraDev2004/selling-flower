<?php
/**
 * Product View
 */
?>

<!-- Toast Notification Container -->
<div id="toastContainer" style="position: fixed; top: 20px; right: 20px; z-index: 9999;"></div>

<?php
// Xác định loại thông báo dựa trên action
$actionType = $_GET['action'] ?? '';
$successMessage = '';
$errorMessage = '';

if (isset($_GET['success']) && $_GET['success'] == '1') {
    $message = $_GET['message'] ?? '';
    switch ($actionType) {
        case 'create':
            $successMessage = !empty($message) ? $message : 'Thêm sản phẩm thành công!';
            break;
        case 'edit':
            $successMessage = !empty($message) ? $message : 'Sửa sản phẩm thành công!';
            break;
        case 'delete':
            $successMessage = !empty($message) ? $message : 'Xóa sản phẩm thành công!';
            break;
        default:
            $successMessage = !empty($message) ? $message : 'Thao tác thành công!';
    }
}

if (isset($_GET['error'])) {
    $errorMessage = htmlspecialchars($_GET['error']);
}
?>

<script>
// Hiển thị toast notification
function showToast(message, type = 'success') {
    const container = document.getElementById('toastContainer');
    const toastId = 'toast-' + Date.now();
    
    const icon = type === 'success' 
        ? '<i class="fas fa-check-circle"></i>' 
        : '<i class="fas fa-exclamation-circle"></i>';
    
    const bgColor = type === 'success' ? '#10b981' : '#ef4444';
    const borderColor = type === 'success' ? '#059669' : '#dc2626';
    
    const toast = document.createElement('div');
    toast.id = toastId;
    toast.className = 'toast-notification';
    toast.style.cssText = `
        background: white;
        border-left: 4px solid ${borderColor};
        border-radius: 8px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
        padding: 16px 20px;
        margin-bottom: 12px;
        min-width: 300px;
        max-width: 400px;
        display: flex;
        align-items: center;
        gap: 12px;
        animation: slideInRight 0.3s ease-out;
        position: relative;
    `;
    
    toast.innerHTML = `
        <div style="font-size: 24px; color: ${bgColor}; flex-shrink: 0;">
            ${icon}
        </div>
        <div style="flex: 1;">
            <div style="font-weight: 600; color: #1f2937; margin-bottom: 4px;">
                ${type === 'success' ? 'Thành công' : 'Lỗi'}
            </div>
            <div style="font-size: 14px; color: #6b7280;">
                ${message}
            </div>
    </div>
        <button onclick="closeToast('${toastId}')" style="background: none; border: none; font-size: 18px; color: #9ca3af; cursor: pointer; padding: 0; width: 24px; height: 24px; display: flex; align-items: center; justify-content: center;">
            <i class="fas fa-times"></i>
        </button>
    `;
    
    container.appendChild(toast);
    
    // Tự động đóng sau 5 giây
    setTimeout(() => {
        closeToast(toastId);
    }, 5000);
}

function closeToast(toastId) {
    const toast = document.getElementById(toastId);
    if (toast) {
        toast.style.animation = 'slideOutRight 0.3s ease-out';
        setTimeout(() => {
            toast.remove();
        }, 300);
    }
}

// CSS Animation
const style = document.createElement('style');
style.textContent = `
    @keyframes slideInRight {
        from {
            transform: translateX(400px);
            opacity: 0;
        }
        to {
            transform: translateX(0);
            opacity: 1;
        }
    }
    @keyframes slideOutRight {
        from {
            transform: translateX(0);
            opacity: 1;
        }
        to {
            transform: translateX(400px);
            opacity: 0;
        }
    }
`;
document.head.appendChild(style);

// Hiển thị thông báo khi trang load
<?php if (!empty($successMessage)): ?>
showToast('<?php echo addslashes($successMessage); ?>', 'success');
<?php endif; ?>

<?php if (!empty($errorMessage)): ?>
showToast('<?php echo addslashes($errorMessage); ?>', 'error');
<?php endif; ?>
</script>

<div class="card mb-3">
    <div class="card-body">
        <form method="GET" class="row g-3">
            <div class="col-md-3">
                <input type="text" name="search" class="form-control" placeholder="Tìm kiếm..." value="<?php echo htmlspecialchars($filters['search']); ?>">
            </div>
            <div class="col-md-3">
                <select name="category_id" class="form-control">
                    <option value="">Tất cả danh mục</option>
                    <?php foreach ($categories as $cat): ?>
                        <option value="<?php echo $cat['id']; ?>" <?php echo $filters['category_id'] == $cat['id'] ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($cat['name']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-2">
                <select name="status" class="form-control">
                    <option value="">Tất cả trạng thái</option>
                    <option value="active" <?php echo $filters['status'] == 'active' ? 'selected' : ''; ?>>Hoạt động</option>
                    <option value="inactive" <?php echo $filters['status'] == 'inactive' ? 'selected' : ''; ?>>Tắt</option>
                </select>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-primary w-100">Lọc</button>
            </div>
            <div class="col-md-2">
                <a href="<?php echo APP_URL; ?>/admin/products.php" class="btn btn-secondary w-100">Đặt lại</a>
            </div>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-header" style="display: flex; justify-content: space-between; align-items: flex-start;">
        <div>
            <h5 class="card-title mb-0">Danh sách Sản phẩm</h5>
            <div class="text-muted" style="font-size: 16px;">
                <?php 
                $hasFilter = !empty($filters['search']) || !empty($filters['category_id']) || !empty($filters['status']);
                if ($hasFilter): 
                ?>
                    Đang hiển thị <strong><?php echo number_format($filteredCount); ?></strong> sản phẩm 
                    (Tổng cộng: <strong><?php echo number_format($totalProducts); ?></strong> sản phẩm)
                <?php else: ?>
                    Tổng cộng: <strong><?php echo number_format($totalProducts); ?></strong> sản phẩm
                <?php endif; ?>
            </div>
        </div>
        <button class="btn btn-primary" onclick="showModal('create')" style="font-size: 14px; padding: 8px 16px; flex-shrink: 0;">
            <i class="fas fa-plus"></i> Thêm mới
        </button>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <style>
                .table tbody tr {
                    height: 50px !important;
                    max-height: 50px !important;
                    line-height: 1.0 !important;
                    margin: 0 !important;
                    padding: 0 !important;
                    vertical-align: middle !important;
                }
                .table tbody td {
                    padding: 0 12px !important;
                    font-size: 15px !important;
                    line-height: 1.0 !important;
                    margin: 0 !important;
                    height: 20px !important;
                    max-height: 20px !important;
                    vertical-align: middle !important;
                    overflow: hidden !important;
                    text-overflow: ellipsis !important;
                    white-space: nowrap !important;
                }
                .table tbody td * {
                    line-height: 1.0 !important;
                    margin: 0 !important;
                    vertical-align: middle !important;
                }
                .table tbody td .badge {
                    font-size: 11px !important;
                    padding: 1px 5px !important;
                    line-height: 1.0 !important;
                    height: auto !important;
                    max-height: 18px !important;
                    display: inline-block !important;
                }
                .table tbody td .btn {
                    padding: 1px 6px !important;
                    font-size: 11px !important;
                    line-height: 1.0 !important;
                    height: 18px !important;
                    max-height: 18px !important;
                    min-height: 18px !important;
                }
                .table tbody td .btn i {
                    font-size: 10px !important;
                    line-height: 1.0 !important;
                }
                .table tbody td div {
                    line-height: 1.0 !important;
                    margin: 0 !important;
                    padding: 0 !important;
                }
                .table tbody td span {
                    line-height: 1.0 !important;
                    margin: 0 !important;
                }
                .table thead th {
                    padding: 6px 12px !important;
                    font-size: 14px !important;
                    vertical-align: middle !important;
                }
                .table tbody td:nth-child(8),
                .table tbody td:nth-child(9) {
                    font-size: 16px !important;
                }
                .table tbody td:nth-child(8) .badge {
                    font-size: 14px !important;
                }
                .table tbody td:nth-child(9) .btn {
                    font-size: 13px !important;
                }
                .table tbody td:nth-child(9) .btn i {
                    font-size: 13px !important;
                }
            </style>
            <table class="table" style="font-size: 14px; margin-bottom: 0;">
                <thead>
                    <tr>
                        <th style="padding: 6px 12px; font-size: 14px;">ID</th>
                        <th style="padding: 6px 12px; font-size: 14px;">Tên sản phẩm</th>
                        <th style="padding: 6px 12px; font-size: 14px;">SKU</th>
                        <th style="padding: 6px 12px; font-size: 14px;">Danh mục</th>
                        <th style="padding: 6px 12px; font-size: 14px;">Giá gốc</th>
                        <th style="padding: 6px 12px; font-size: 14px;">Giá khuyến mại</th>
                        <th style="padding: 6px 12px; font-size: 14px;">Tồn kho</th>
                        <th style="padding: 6px 12px; font-size: 14px;">Trạng thái</th>
                        <th style="padding: 6px 12px; font-size: 14px;">Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($products)): ?>
                        <tr>
                            <td colspan="9" class="text-center text-muted" style="padding: 8px 12px;">Chưa có sản phẩm nào</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($products as $item): ?>
                            <tr>
                                <td><?php echo $item['id']; ?></td>
                                <td><?php echo htmlspecialchars($item['name']); ?></td>
                                <td><?php echo htmlspecialchars($item['sku']); ?></td>
                                <td><?php echo htmlspecialchars($item['category_name'] ?? '-'); ?></td>
                                <td><?php echo formatPrice($item['price']); ?></td>
                                <td class="text-danger fw-bold"><?php echo $item['sale_price'] ? formatPrice($item['sale_price']) : '<span class="text-muted">-</span>'; ?></td>
                                <td><?php echo $item['stock_quantity']; ?></td>
                                <td>
                                    <span class="badge badge-<?php echo $item['status'] == 'active' ? 'success' : ($item['status'] == 'out_of_stock' ? 'warning' : 'danger'); ?> font-size-14">
                                        <?php 
                                        echo $item['status'] == 'active' ? 'Hoạt động' : 
                                            ($item['status'] == 'out_of_stock' ? 'Hết hàng' : 'Tắt'); 
                                        ?>
                                    </span>
                                </td>
                                <td>
                                    <button class="btn btn-sm btn-primary" onclick="showModal('edit', <?php echo $item['id']; ?>)">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button class="btn btn-sm btn-danger" onclick="deleteProduct(<?php echo $item['id']; ?>)">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Create/Edit -->
<div class="modal fade" id="productModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalTitle">Thêm Sản phẩm</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="" id="productForm" enctype="multipart/form-data">
                <input type="hidden" name="action" id="formAction" value="create">
                <input type="hidden" name="id" id="formId">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-8">
                            <div class="mb-3">
                                <label class="form-label">Tên sản phẩm <span style="color: #dc3545;">*</span></label>
                                <input type="text" name="name" class="form-control" id="formName" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label">SKU</label>
                                <input type="text" class="form-control" id="formSku" readonly disabled style="background-color: #e9ecef;">
                                <small class="form-text text-muted">SKU sẽ tự động được tạo (SP + ID)</small>
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Mô tả <span style="color: #dc3545;">*</span></label>
                        <textarea name="description" class="form-control" id="formDescription" rows="4" required></textarea>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Danh mục <span style="color: #dc3545;">*</span></label>
                                <select name="category_id" class="form-control" id="formCategoryId" required>
                                    <option value="">Chọn danh mục</option>
                                    <?php foreach ($categories as $cat): ?>
                                        <option value="<?php echo $cat['id']; ?>"><?php echo htmlspecialchars($cat['name']); ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Trạng thái *</label>
                                <select name="status" class="form-control" id="formStatus" required>
                                    <option value="active">Hoạt động</option>
                                    <option value="inactive">Tắt</option>
                                    <option value="out_of_stock">Hết hàng</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label">Giá gốc (VNĐ) <span style="color: #dc3545;">*</span></label>
                                <input type="number" name="price" class="form-control" id="formPrice" min="0" step="1000" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label">Giá khuyến mãi (VNĐ)</label>
                                <input type="number" name="sale_price" class="form-control" id="formSalePrice" min="0" step="1000">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label">Số lượng tồn kho <span style="color: #dc3545;">*</span></label>
                                <input type="number" name="stock_quantity" class="form-control" id="formStockQuantity" min="1" value="1" required>
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <div class="form-check">
                            <input type="checkbox" name="featured" class="form-check-input" id="formFeatured" value="1">
                            <label class="form-check-label" for="formFeatured">
                                Sản phẩm nổi bật
                            </label>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Hình ảnh sản phẩm</label>
                        <div class="border rounded p-3">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Ảnh chính *</label>
                                
                                <!-- Hiển thị ảnh chính hiện có khi edit -->
                                <div id="currentPrimaryImageContainer" class="mb-3" style="display: none;">
                                    <label class="form-label small text-success fw-bold">
                                        <i class="fas fa-image"></i> Ảnh chính hiện tại:
                                    </label>
                                    <div class="border rounded p-2 bg-light">
                                        <img id="currentPrimaryImage" src="" alt="Ảnh chính hiện tại" style="max-width: 100%; max-height: 150px; width: auto; height: auto; border-radius: 5px; display: block; margin: 0 auto; object-fit: contain;" onerror="this.src='data:image/svg+xml,%3Csvg xmlns=\'http://www.w3.org/2000/svg\' width=\'200\' height=\'150\'%3E%3Crect fill=\'%23ddd\' width=\'200\' height=\'150\'/%3E%3Ctext fill=\'%23999\' x=\'50%25\' y=\'50%25\' text-anchor=\'middle\' dy=\'.3em\'%3EẢnh không tải được%3C/text%3E%3C/svg%3E';">
                                        <small class="d-block text-center text-muted mt-2">Ảnh này sẽ được thay thế nếu bạn chọn ảnh mới</small>
                                    </div>
                                </div>
                                
                                <div class="mb-2">
                                    <label class="form-label small text-muted">Chọn file từ máy:</label>
                                    <input type="file" name="primary_image_file" class="form-control" id="formPrimaryImageFile" accept="image/*">
                                    <small class="form-text text-muted">Chấp nhận: JPG, PNG, GIF, WEBP (tối đa 5MB)</small>
                                </div>
                                <div id="primaryImagePreview" class="mt-2" style="display: none;">
                                    <label class="form-label small text-info fw-bold">
                                        <i class="fas fa-eye"></i> Xem trước ảnh mới:
                                    </label>
                                    <div class="border rounded p-2 bg-light mt-1">
                                        <img id="previewPrimaryImg" src="" alt="Preview" style="max-width: 100%; max-height: 200px; border-radius: 5px; display: block; margin: 0 auto;">
                                    </div>
                                </div>
                            </div>
                            <hr>
                            <div class="mb-3">
                                <label class="form-label fw-bold">Ảnh bổ sung</label>
                                
                                <!-- Hiển thị ảnh phụ hiện có khi edit -->
                                <div id="currentAdditionalImagesContainer" class="mb-3" style="display: none;">
                                    <label class="form-label small text-success fw-bold">
                                        <i class="fas fa-images"></i> Ảnh phụ hiện có:
                                    </label>
                                    <div id="existingAdditionalImages" class="mt-2 row g-2"></div>
                                </div>
                                
                                <div class="mb-2">
                                    <label class="form-label small text-muted">Chọn nhiều file từ máy (giữ Ctrl/Cmd để chọn nhiều):</label>
                                    <input type="file" name="additional_images[]" class="form-control" id="formAdditionalImages" accept="image/*" multiple>
                                    <small class="form-text text-muted">
                                        <i class="fas fa-info-circle"></i> Có thể chọn nhiều ảnh cùng lúc bằng cách giữ phím <strong>Ctrl</strong> (Windows) hoặc <strong>Cmd</strong> (Mac) khi click chọn. Tối đa 5MB mỗi ảnh. Click vào nút X để xóa ảnh đã chọn.
                                    </small>
                                </div>
                                
                                <!-- Hiển thị ảnh mới đã chọn -->
                                <div id="additionalImagesPreviewContainer" style="display: none;">
                                    <label class="form-label small text-info fw-bold mt-2">
                                        <i class="fas fa-eye"></i> Ảnh mới đã chọn:
                                    </label>
                                <div id="additionalImagesPreview" class="mt-2 row g-2"></div>
                                <div id="additionalImagesCount" class="mt-2 text-muted small" style="display: none;"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Thuộc tính sản phẩm <span style="color: #dc3545;">*</span></label>
                        <small class="d-block text-muted mb-2">Nhập các thuộc tính của sản phẩm (ví dụ: Màu sắc, Kích thước, Chất liệu...)</small>
                        <div id="attributesContainer">
                            <!-- Attributes sẽ được thêm vào đây -->
                        </div>
                        <button type="button" class="btn btn-sm btn-outline-primary mt-2" onclick="addAttributeRow()">
                            <i class="fas fa-plus"></i> Thêm thuộc tính
                        </button>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Tiêu đề SEO <span style="color: #dc3545;">*</span></label>
                                <input type="text" name="meta_title" class="form-control" id="formMetaTitle" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Mô tả SEO <span style="color: #dc3545;">*</span></label>
                                <input type="text" name="meta_description" class="form-control" id="formMetaDescription" required>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                    <button type="submit" class="btn btn-primary">Lưu</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
const productData = <?php echo json_encode($products); ?>;
const currentProduct = <?php echo json_encode($product ?? null); ?>;
const allProductImages = <?php echo json_encode($allProductImages ?? []); ?>;
const allProductAttributes = <?php echo json_encode($allProductAttributes ?? []); ?>;
let productAttributes = <?php echo json_encode($productAttributes ?? []); ?>;

// Biến đếm số lượng attributes để tạo ID unique
let attributeIndex = 0;

// Hàm thêm một dòng thuộc tính mới
function addAttributeRow(attributeName = '', attributeValue = '') {
    const container = document.getElementById('attributesContainer');
    const row = document.createElement('div');
    row.className = 'row mb-2 attribute-row';
    row.setAttribute('data-index', attributeIndex);
    
    row.innerHTML = `
        <div class="col-md-5">
            <input type="text" 
                   name="attributes[${attributeIndex}][attribute_name]" 
                   class="form-control attribute-name" 
                   placeholder="Tên thuộc tính (VD: Màu sắc)" 
                   value="${attributeName}"
                   required>
        </div>
        <div class="col-md-6">
            <input type="text" 
                   name="attributes[${attributeIndex}][attribute_value]" 
                   class="form-control attribute-value" 
                   placeholder="Giá trị (VD: Đỏ)" 
                   value="${attributeValue}"
                   required>
        </div>
        <div class="col-md-1">
            <button type="button" class="btn btn-sm btn-danger w-100" onclick="removeAttributeRow(this)" title="Xóa">
                <i class="fas fa-times"></i>
            </button>
        </div>
    `;
    
    container.appendChild(row);
    attributeIndex++;
}

// Hàm xóa dòng thuộc tính
function removeAttributeRow(button) {
    const row = button.closest('.attribute-row');
    row.remove();
}

// Khởi tạo attributes khi load trang
function initAttributes() {
    const container = document.getElementById('attributesContainer');
    container.innerHTML = '';
    attributeIndex = 0;
    
    // Nếu có attributes từ database (khi edit), load chúng
    if (productAttributes && productAttributes.length > 0) {
        productAttributes.forEach(attr => {
            addAttributeRow(attr.attribute_name || '', attr.attribute_value || '');
        });
    } else {
        // Nếu không có, thêm một dòng trống để người dùng nhập
        addAttributeRow();
    }
}

function showModal(action, id = null) {
    const modal = new bootstrap.Modal(document.getElementById('productModal'));
    const form = document.getElementById('productForm');
    const formAction = document.getElementById('formAction');
    const formId = document.getElementById('formId');
    
    // Mảng lưu các file đã chọn (để có thể xóa bớt)
    window.selectedFiles = [];
    
    if (action === 'create') {
        document.getElementById('modalTitle').textContent = 'Thêm Sản phẩm';
        formAction.value = 'create';
        form.action = '<?php echo APP_URL; ?>/admin/actions/products.php?action=create';
        form.reset();
        formId.value = '';
        // Reset preview
        document.getElementById('primaryImagePreview').style.display = 'none';
        document.getElementById('currentPrimaryImageContainer').style.display = 'none';
        document.getElementById('additionalImagesPreview').innerHTML = '';
        document.getElementById('additionalImagesPreviewContainer').style.display = 'none';
        document.getElementById('existingAdditionalImages').innerHTML = '';
        document.getElementById('currentAdditionalImagesContainer').style.display = 'none';
        document.getElementById('formPrimaryImageFile').value = '';
        document.getElementById('formAdditionalImages').value = '';
        window.selectedFiles = [];
        document.getElementById('additionalImagesCount').style.display = 'none';
        // Reset attributes
        initAttributes();
    } else if (action === 'edit' && id) {
        document.getElementById('modalTitle').textContent = 'Sửa Sản phẩm';
        formAction.value = 'edit';
        form.action = '<?php echo APP_URL; ?>/admin/actions/products.php?action=edit&id=' + id;
        formId.value = id;
        
        // Load attributes từ database cho sản phẩm đang edit
        productAttributes = allProductAttributes[id] || [];
        
        const product = productData.find(p => p.id == id);
        if (product) {
            document.getElementById('formName').value = product.name || '';
            document.getElementById('formSku').value = product.sku || '';
            document.getElementById('formDescription').value = product.description || '';
            document.getElementById('formCategoryId').value = product.category_id || '';
            document.getElementById('formPrice').value = product.price || 0;
            document.getElementById('formSalePrice').value = product.sale_price || '';
            document.getElementById('formStockQuantity').value = product.stock_quantity || 0;
            document.getElementById('formFeatured').checked = product.featured == 1;
            document.getElementById('formStatus').value = product.status || 'active';
            document.getElementById('formMetaTitle').value = product.meta_title || '';
            document.getElementById('formMetaDescription').value = product.meta_description || '';
            
            // Lấy ảnh từ database
            const productImagesData = allProductImages[id] || [];
            
            // Hiển thị ảnh chính hiện có
            const primaryImage = productImagesData.find(img => img.is_primary == 1 || img.is_primary == '1');
            if (primaryImage && primaryImage.image_url) {
                const imageUrl = primaryImage.image_url;
                const imgElement = document.getElementById('currentPrimaryImage');
                imgElement.src = imageUrl;
                imgElement.onload = function() {
                    document.getElementById('currentPrimaryImageContainer').style.display = 'block';
                };
                imgElement.onerror = function() {
                    console.error('Không thể tải ảnh:', imageUrl);
                    // Vẫn hiển thị container để người dùng biết có ảnh nhưng lỗi
                    document.getElementById('currentPrimaryImageContainer').style.display = 'block';
                };
            } else {
                document.getElementById('currentPrimaryImageContainer').style.display = 'none';
            }
            
            // Hiển thị ảnh phụ hiện có
            const additionalImages = productImagesData.filter(img => img.is_primary == 0 || img.is_primary == '0');
            const existingPreview = document.getElementById('existingAdditionalImages');
            const existingContainer = document.getElementById('currentAdditionalImagesContainer');
            existingPreview.innerHTML = '';
            
            if (additionalImages.length > 0) {
                existingContainer.style.display = 'block';
                additionalImages.forEach((img, index) => {
                    const col = document.createElement('div');
                    col.className = 'col-md-3 mb-2 existing-image-item';
                    col.setAttribute('data-image-id', img.id);
                    col.innerHTML = `
                        <div class="position-relative border rounded p-2 bg-light" style="min-height: 140px;">
                            <img src="${img.image_url}" alt="Ảnh hiện có ${index+1}" style="width: 100%; height: 100px; object-fit: cover; border-radius: 5px; display: block;" onerror="this.src='data:image/svg+xml,%3Csvg xmlns=\'http://www.w3.org/2000/svg\' width=\'150\' height=\'100\'%3E%3Crect fill=\'%23ddd\' width=\'150\' height=\'100\'/%3E%3Ctext fill=\'%23999\' x=\'50%25\' y=\'50%25\' text-anchor=\'middle\' dy=\'.3em\' font-size=\'12\'%3EẢnh lỗi%3C/text%3E%3C/svg%3E'; this.onerror=null;">
                            <div class="position-absolute top-0 end-0 bg-success text-white rounded-circle" style="width: 22px; height: 22px; display: flex; align-items: center; justify-content: center; font-size: 10px; font-weight: bold; margin: 3px; box-shadow: 0 2px 4px rgba(0,0,0,0.2); z-index: 10;">
                                ${index + 1}
                            </div>
                            <div class="position-absolute top-0 start-0 bg-danger text-white rounded-circle" style="width: 26px; height: 26px; display: flex; align-items: center; justify-content: center; font-size: 13px; margin: 3px; cursor: pointer; box-shadow: 0 2px 4px rgba(0,0,0,0.2); z-index: 10;" onclick="removeExistingImage(${img.id}, this)" title="Xóa ảnh này">
                                <i class="fas fa-times"></i>
                            </div>
                            <small class="d-block text-center text-success mt-1 fw-bold" style="font-size: 11px;">
                                <i class="fas fa-check-circle"></i> Ảnh hiện có
                            </small>
                        </div>
                    `;
                    existingPreview.appendChild(col);
                });
            } else {
                existingContainer.style.display = 'none';
            }
            
            // Reset danh sách file mới chọn khi edit
            window.selectedFiles = [];
            document.getElementById('additionalImagesPreview').innerHTML = '';
            document.getElementById('additionalImagesCount').style.display = 'none';
            document.getElementById('additionalImagesPreviewContainer').style.display = 'none';
            
            // Load attributes
            initAttributes();
        }
    }
    
    modal.show();
}

// Khởi tạo attributes khi trang load
document.addEventListener('DOMContentLoaded', function() {
    initAttributes();
});

// Preview ảnh chính
document.getElementById('formPrimaryImageFile').addEventListener('change', function(e) {
    const file = e.target.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('previewPrimaryImg').src = e.target.result;
            document.getElementById('primaryImagePreview').style.display = 'block';
        };
        reader.readAsDataURL(file);
    } else {
        document.getElementById('primaryImagePreview').style.display = 'none';
    }
});


// Hàm xóa ảnh hiện có
function removeExistingImage(imageId, element) {
    if (confirm('Bạn có chắc chắn muốn xóa ảnh này?')) {
        // Tạo input hidden để đánh dấu ảnh cần xóa
        const form = document.getElementById('productForm');
        const hiddenInput = document.createElement('input');
        hiddenInput.type = 'hidden';
        hiddenInput.name = 'delete_images[]';
        hiddenInput.value = imageId;
        form.appendChild(hiddenInput);
        
        // Xóa khỏi giao diện
        element.closest('.existing-image-item').remove();
    }
}

// Preview ảnh bổ sung
document.getElementById('formAdditionalImages').addEventListener('change', function(e) {
    const files = e.target.files;
    const preview = document.getElementById('additionalImagesPreview');
    const countDiv = document.getElementById('additionalImagesCount');
    
    // Thêm file mới vào danh sách (không xóa file cũ)
    const newFiles = Array.from(files);
    
    // Lọc bỏ file trùng lặp (theo tên và kích thước)
    newFiles.forEach(file => {
        const isDuplicate = window.selectedFiles.some(f => 
            f.name === file.name && f.size === file.size
        );
        if (!isDuplicate) {
            window.selectedFiles.push(file);
        }
    });
    
            // Hiển thị lại tất cả ảnh đã chọn
            renderSelectedImages();
            
            // Reset input để có thể chọn lại file đã chọn
            this.value = '';
            
            // Hiển thị container nếu có ảnh
            if (window.selectedFiles.length > 0) {
                document.getElementById('additionalImagesPreviewContainer').style.display = 'block';
            }
});

// Hàm render lại tất cả ảnh đã chọn
function renderSelectedImages() {
    const preview = document.getElementById('additionalImagesPreview');
    const countDiv = document.getElementById('additionalImagesCount');
    const container = document.getElementById('additionalImagesPreviewContainer');
    
    preview.innerHTML = '';
    
    if (window.selectedFiles.length === 0) {
        countDiv.style.display = 'none';
        container.style.display = 'none';
        return;
    }
    
    container.style.display = 'block';
    
    // Hiển thị số lượng ảnh đã chọn
    countDiv.textContent = `Đã chọn ${window.selectedFiles.length} ảnh bổ sung (click X để xóa):`;
    countDiv.style.display = 'block';
    
    // Preview từng ảnh
    window.selectedFiles.forEach((file, index) => {
        // Kiểm tra kích thước file
        if (file.size > 5 * 1024 * 1024) {
            return;
        }
        
        const reader = new FileReader();
        reader.onload = function(e) {
            const col = document.createElement('div');
            col.className = 'col-md-3 mb-2 selected-image-item';
            col.setAttribute('data-file-index', index);
            col.innerHTML = `
                <div class="position-relative border rounded p-1">
                    <img src="${e.target.result}" alt="Preview ${index+1}" style="width: 100%; height: 100px; object-fit: cover; border-radius: 5px; cursor: pointer;" onclick="removeSelectedImage(${index})">
                    <div class="position-absolute top-0 end-0 bg-dark text-white rounded-circle" style="width: 20px; height: 20px; display: flex; align-items: center; justify-content: center; font-size: 10px; margin: 2px;">
                        ${index + 1}
                    </div>
                    <div class="position-absolute top-0 start-0 bg-danger text-white rounded-circle" style="width: 24px; height: 24px; display: flex; align-items: center; justify-content: center; font-size: 12px; margin: 2px; cursor: pointer;" onclick="removeSelectedImage(${index})" title="Xóa ảnh này">
                        <i class="fas fa-times"></i>
                    </div>
                </div>
            `;
            preview.appendChild(col);
        };
        reader.readAsDataURL(file);
    });
}

// Hàm xóa ảnh đã chọn
function removeSelectedImage(index) {
    if (confirm('Bạn có muốn xóa ảnh này khỏi danh sách?')) {
        window.selectedFiles.splice(index, 1);
        renderSelectedImages();
        updateFileInput();
    }
}

// Hàm cập nhật lại file input (tạo DataTransfer để giữ lại các file còn lại)
function updateFileInput() {
    const input = document.getElementById('formAdditionalImages');
    const dt = new DataTransfer();
    
    window.selectedFiles.forEach(file => {
        dt.items.add(file);
    });
    
    input.files = dt.files;
}

// Validation form trước khi submit
document.getElementById('productForm').addEventListener('submit', function(e) {
    const formAction = document.getElementById('formAction').value;
    
    // Đảm bảo cập nhật file input cho ảnh phụ trước khi submit
    if (window.selectedFiles && window.selectedFiles.length > 0) {
        updateFileInput();
    }
    
    // Kiểm tra attributes: phải có ít nhất một thuộc tính hợp lệ
    const attributeRows = document.querySelectorAll('.attribute-row');
    let hasValidAttribute = false;
    
    attributeRows.forEach(row => {
        const nameInput = row.querySelector('.attribute-name');
        const valueInput = row.querySelector('.attribute-value');
        if (nameInput.value.trim() && valueInput.value.trim()) {
            hasValidAttribute = true;
        }
    });
    
    if (!hasValidAttribute) {
        e.preventDefault();
        alert('Vui lòng nhập ít nhất một thuộc tính sản phẩm (tên thuộc tính và giá trị).');
        return false;
    }
    
    // Chỉ validate khi tạo mới (không validate khi edit vì có thể giữ nguyên ảnh cũ)
    if (formAction === 'create') {
        const primaryImageFile = document.getElementById('formPrimaryImageFile').files[0];
        
        // Kiểm tra ảnh chính: phải có file
        if (!primaryImageFile) {
            e.preventDefault();
            alert('Vui lòng chọn file ảnh chính cho sản phẩm.');
            return false;
        }
    }
});

// Hàm xóa sản phẩm bằng AJAX
function deleteProduct(productId) {
    if (!confirm('Bạn có chắc chắn muốn xóa sản phẩm này?')) {
        return;
    }
    
    // Gửi request xóa bằng AJAX
    fetch('<?php echo APP_URL; ?>/admin/actions/products.php?action=delete&id=' + productId, {
        method: 'GET',
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Hiển thị thông báo thành công
            showToast(data.message || 'Xóa sản phẩm thành công!', 'success');
            
            // Reload trang sau 2.5 giây để người dùng có thể đọc thông báo
            setTimeout(() => {
                window.location.href = '<?php echo APP_URL; ?>/admin/products.php';
            }, 2500);
        } else {
            // Hiển thị thông báo lỗi
            showToast(data.message || 'Có lỗi xảy ra khi xóa sản phẩm!', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showToast('Có lỗi xảy ra khi xóa sản phẩm. Vui lòng thử lại.', 'error');
    });
}
</script>

