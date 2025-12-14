<?php
/**
 * Review View
 */
?>

<!-- Toast Notification Container -->
<div id="toastContainer" style="position: fixed; top: 20px; right: 20px; z-index: 9999;"></div>

<?php
// Xác định loại thông báo
$successMessage = '';
$errorMessage = '';

if (isset($_GET['success']) && $_GET['success'] == '1') {
    $message = $_GET['message'] ?? '';
    $successMessage = !empty($message) ? $message : 'Thao tác thành công!';
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

<?php if (!empty($successMessage)): ?>
showToast('<?php echo addslashes($successMessage); ?>', 'success');
<?php endif; ?>

<?php if (!empty($errorMessage)): ?>
showToast('<?php echo addslashes($errorMessage); ?>', 'error');
<?php endif; ?>
</script>

<!-- Filter Form -->
<div class="card mb-3">
    <div class="card-body">
        <form method="GET" class="row g-3">
            <div class="col-md-4">
                <input type="text" name="search" class="form-control" placeholder="Tìm kiếm theo tên, nội dung..." value="<?php echo htmlspecialchars($filters['search']); ?>">
            </div>
            <div class="col-md-3">
                <select name="status" class="form-control">
                    <option value="">Tất cả trạng thái</option>
                    <option value="pending" <?php echo $filters['status'] == 'pending' ? 'selected' : ''; ?>>Chờ duyệt</option>
                    <option value="approved" <?php echo $filters['status'] == 'approved' ? 'selected' : ''; ?>>Đã duyệt</option>
                    <option value="rejected" <?php echo $filters['status'] == 'rejected' ? 'selected' : ''; ?>>Từ chối</option>
                </select>
            </div>
            <div class="col-md-3">
                <select name="product_id" class="form-control">
                    <option value="">Tất cả sản phẩm</option>
                    <?php foreach ($products as $product): ?>
                        <option value="<?php echo $product['id']; ?>" <?php echo $filters['product_id'] == $product['id'] ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($product['name']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-primary w-100">Lọc</button>
            </div>
        </form>
    </div>
</div>

<!-- Reviews List -->
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="card-title mb-0">Danh sách Đánh giá</h5>
        <button class="btn btn-primary" onclick="showCreateModal()">
            <i class="fas fa-plus"></i> Thêm đánh giá
        </button>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Sản phẩm</th>
                        <th>Khách hàng</th>
                        <th>Đánh giá</th>
                        <th>Nội dung</th>
                        <th>Trạng thái</th>
                        <th>Nổi bật</th>
                        <th>Ngày tạo</th>
                        <th>Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($reviews)): ?>
                        <tr>
                            <td colspan="9" class="text-center text-muted">Chưa có đánh giá nào</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($reviews as $item): ?>
                            <tr>
                                <td><?php echo $item['id']; ?></td>
                                <td>
                                    <?php if (!empty($item['product_name'])): ?>
                                        <strong><?php echo htmlspecialchars($item['product_name']); ?></strong><br>
                                        <small class="text-muted"><?php echo htmlspecialchars($item['product_sku'] ?? ''); ?></small>
                                    <?php else: ?>
                                        <span class="text-muted">-</span>
                                    <?php endif; ?>
                                </td>
                                <td><?php echo htmlspecialchars($item['customer_name']); ?></td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <?php for ($i = 1; $i <= 5; $i++): ?>
                                            <i class="fas fa-star <?php echo $i <= $item['rating'] ? 'text-warning' : 'text-muted'; ?>"></i>
                                        <?php endfor; ?>
                                        <span class="ms-2">(<?php echo $item['rating']; ?>)</span>
                                    </div>
                                </td>
                                <td>
                                    <div style="max-width: 300px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;" title="<?php echo htmlspecialchars($item['comment']); ?>">
                                        <?php echo htmlspecialchars($item['comment'] ?: '-'); ?>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge badge-<?php 
                                        echo $item['status'] == 'approved' ? 'success' : 
                                            ($item['status'] == 'rejected' ? 'danger' : 'warning'); 
                                    ?>">
                                        <?php 
                                            $statuses = [
                                                'pending' => 'Chờ duyệt',
                                                'approved' => 'Đã duyệt',
                                                'rejected' => 'Từ chối'
                                            ];
                                            echo $statuses[$item['status']] ?? $item['status'];
                                        ?>
                                    </span>
                                </td>
                                <td>
                                    <?php if ($item['main'] == 1): ?>
                                        <span class="badge badge-info"><i class="fas fa-star"></i> Nổi bật</span>
                                    <?php else: ?>
                                        <span class="text-muted">-</span>
                                    <?php endif; ?>
                                </td>
                                <td><?php echo date('d/m/Y H:i', strtotime($item['created_at'])); ?></td>
                                <td>
                                    <button class="btn btn-sm btn-primary" onclick="showModal(<?php echo $item['id']; ?>)">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button class="btn btn-sm btn-danger" onclick="deleteReview(<?php echo $item['id']; ?>)">
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

<!-- Modal Create -->
<div class="modal fade" id="createReviewModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Thêm Đánh giá</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="<?php echo APP_URL; ?>/admin/actions/reviews.php?action=create">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Sản phẩm <span style="color:#dc3545;">*</span></label>
                        <select name="product_id" class="form-control" required>
                            <option value="">-- Chọn sản phẩm --</option>
                            <?php foreach ($products as $product): ?>
                                <option value="<?php echo $product['id']; ?>">
                                    <?php echo htmlspecialchars($product['name']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Tên khách hàng <span style="color:#dc3545;">*</span></label>
                        <input type="text" name="customer_name" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Đánh giá (sao) <span style="color:#dc3545;">*</span></label>
                        <select name="rating" class="form-control" required>
                            <?php for ($i = 1; $i <= 5; $i++): ?>
                                <option value="<?php echo $i; ?>" <?php echo $i == 5 ? 'selected' : ''; ?>>
                                    <?php echo $i; ?> sao
                                </option>
                            <?php endfor; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Nội dung đánh giá</label>
                        <textarea name="comment" class="form-control" rows="4"></textarea>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Trạng thái <span style="color:#dc3545;">*</span></label>
                                <select name="status" class="form-control" required>
                                    <option value="pending" selected>Chờ duyệt</option>
                                    <option value="approved">Đã duyệt</option>
                                    <option value="rejected">Từ chối</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Hiển thị nổi bật</label>
                                <div class="form-check form-switch mt-2">
                                    <input class="form-check-input" type="checkbox" name="main" value="1">
                                    <label class="form-check-label">Đánh giá nổi bật (hiển thị trên trang chủ)</label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                    <button type="submit" class="btn btn-primary">Thêm</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Edit -->
<?php if ($review): ?>
<div class="modal fade show" id="reviewModal" tabindex="-1" style="display: block;">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Sửa Đánh giá</h5>
                <a href="<?php echo APP_URL; ?>/admin/reviews.php" class="btn-close"></a>
            </div>
            <form method="POST" action="<?php echo APP_URL; ?>/admin/actions/reviews.php?action=edit&id=<?php echo $review['id']; ?>">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Sản phẩm <span style="color:#dc3545;">*</span></label>
                        <select name="product_id" class="form-control" required>
                            <option value="">-- Chọn sản phẩm --</option>
                            <?php foreach ($products as $product): ?>
                                <option value="<?php echo $product['id']; ?>" <?php echo $review['product_id'] == $product['id'] ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($product['name']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Tên khách hàng <span style="color:#dc3545;">*</span></label>
                        <input type="text" name="customer_name" class="form-control" value="<?php echo htmlspecialchars($review['customer_name']); ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Đánh giá (sao) <span style="color:#dc3545;">*</span></label>
                        <select name="rating" class="form-control" required>
                            <?php for ($i = 1; $i <= 5; $i++): ?>
                                <option value="<?php echo $i; ?>" <?php echo $review['rating'] == $i ? 'selected' : ''; ?>>
                                    <?php echo $i; ?> sao
                                </option>
                            <?php endfor; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Nội dung đánh giá</label>
                        <textarea name="comment" class="form-control" rows="4"><?php echo htmlspecialchars($review['comment']); ?></textarea>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Trạng thái <span style="color:#dc3545;">*</span></label>
                                <select name="status" class="form-control" required>
                                    <option value="pending" <?php echo $review['status'] == 'pending' ? 'selected' : ''; ?>>Chờ duyệt</option>
                                    <option value="approved" <?php echo $review['status'] == 'approved' ? 'selected' : ''; ?>>Đã duyệt</option>
                                    <option value="rejected" <?php echo $review['status'] == 'rejected' ? 'selected' : ''; ?>>Từ chối</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Hiển thị nổi bật</label>
                                <div class="form-check form-switch mt-2">
                                    <input class="form-check-input" type="checkbox" name="main" value="1" <?php echo $review['main'] == 1 ? 'checked' : ''; ?>>
                                    <label class="form-check-label">Đánh giá nổi bật (hiển thị trên trang chủ)</label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <a href="<?php echo APP_URL; ?>/admin/reviews.php" class="btn btn-secondary">Đóng</a>
                    <button type="submit" class="btn btn-primary">Lưu</button>
                </div>
            </form>
        </div>
    </div>
</div>
<div class="modal-backdrop fade show"></div>
<?php endif; ?>

<script>
const reviewData = <?php echo json_encode($reviews); ?>;

function showCreateModal() {
    const modal = new bootstrap.Modal(document.getElementById('createReviewModal'));
    modal.show();
}

function showModal(id) {
    window.location.href = '<?php echo APP_URL; ?>/admin/reviews.php?action=edit&id=' + id;
}

function deleteReview(id) {
    if (!confirm('Bạn có chắc chắn muốn xóa đánh giá này?')) return;
    fetch('<?php echo APP_URL; ?>/admin/actions/reviews.php?action=delete&id=' + id, {
        method: 'GET',
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            showToast(data.message || 'Xóa đánh giá thành công!', 'success');
            setTimeout(() => { window.location.href = '<?php echo APP_URL; ?>/admin/reviews.php'; }, 1500);
        } else {
            showToast(data.message || 'Có lỗi xảy ra khi xóa đánh giá!', 'error');
        }
    })
    .catch(() => {
        showToast('Có lỗi xảy ra khi xóa đánh giá. Vui lòng thử lại.', 'error');
    });
}
</script>
