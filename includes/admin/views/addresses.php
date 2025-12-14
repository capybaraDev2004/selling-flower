<?php
/**
 * Address View
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
            $successMessage = !empty($message) ? $message : 'Thêm địa chỉ thành công!';
            break;
        case 'edit':
            $successMessage = !empty($message) ? $message : 'Sửa địa chỉ thành công!';
            break;
        case 'delete':
            $successMessage = !empty($message) ? $message : 'Xóa địa chỉ thành công!';
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
            <div class="col-md-4">
                <select name="type" class="form-control">
                    <option value="">Tất cả loại</option>
                    <option value="office" <?php echo $type == 'office' ? 'selected' : ''; ?>>Văn phòng</option>
                    <option value="shop" <?php echo $type == 'shop' ? 'selected' : ''; ?>>Shop</option>
                    <option value="warehouse" <?php echo $type == 'warehouse' ? 'selected' : ''; ?>>Kho</option>
                </select>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-primary w-100">Lọc</button>
            </div>
            <div class="col-md-2">
                <a href="<?php echo APP_URL; ?>/admin/addresses.php" class="btn btn-secondary w-100">Reset</a>
            </div>
        </form>
    </div>
</div>

<!-- Modal Create/Edit -->
<div class="modal fade" id="addressModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalTitle">Thêm Địa chỉ</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="" id="addressForm">
                <input type="hidden" name="action" id="formAction" value="create">
                <input type="hidden" name="id" id="formId">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Tên <span style="color:#dc3545;">*</span></label>
                            <input type="text" name="name" class="form-control" id="formName" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Điện thoại <span style="color:#dc3545;">*</span></label>
                            <input type="text" name="phone" class="form-control" id="formPhone" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Địa chỉ <span style="color:#dc3545;">*</span></label>
                        <input type="text" name="address" class="form-control" id="formAddress" required>
                    </div>
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Phường/Xã</label>
                            <input type="text" name="ward" class="form-control" id="formWard">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Quận/Huyện</label>
                            <input type="text" name="district" class="form-control" id="formDistrict">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Thành phố <span style="color:#dc3545;">*</span></label>
                            <input type="text" name="city" class="form-control" id="formCity" required>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Email</label>
                            <input type="email" name="email" class="form-control" id="formEmail">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Google Map URL <span style="color:#dc3545;">*</span></label>
                            <input type="url" name="map_url" class="form-control" id="formMapUrl" placeholder="https://maps.google.com/..." required>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Loại <span style="color:#dc3545;">*</span></label>
                            <select name="type" class="form-control" id="formType" required>
                                <option value="office">Văn phòng</option>
                                <option value="shop">Shop</option>
                                <option value="warehouse">Kho</option>
                            </select>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Vùng miền</label>
                            <select name="region" class="form-control" id="formRegion">
                                <option value="">Chọn vùng</option>
                                <option value="north">Miền Bắc</option>
                                <option value="south">Miền Nam</option>
                            </select>
                            <small class="text-muted">Áp dụng cho văn phòng (miền Bắc/miền Nam)</small>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Thứ tự hiển thị <span style="color:#dc3545;">*</span></label>
                            <input type="number" name="display_order" class="form-control" id="formOrder" value="1" min="0" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Trạng thái <span style="color:#dc3545;">*</span></label>
                            <select name="status" class="form-control" id="formStatus" required>
                                <option value="active">Hoạt động</option>
                                <option value="inactive">Tắt</option>
                            </select>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Ghi chú</label>
                        <textarea name="note" class="form-control" id="formNote" rows="2"></textarea>
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
const addressesData = <?php echo json_encode($addresses); ?>;

function showModal(action, id = null) {
    const modal = new bootstrap.Modal(document.getElementById('addressModal'));
    const form = document.getElementById('addressForm');
    const formAction = document.getElementById('formAction');
    const formId = document.getElementById('formId');
    
    if (action === 'create') {
        document.getElementById('modalTitle').textContent = 'Thêm Địa chỉ';
        formAction.value = 'create';
        form.action = '<?php echo APP_URL; ?>/admin/actions/addresses.php?action=create';
        form.reset();
        formId.value = '';
        document.getElementById('formType').value = 'office';
        document.getElementById('formRegion').value = '';
        document.getElementById('formStatus').value = 'active';
        document.getElementById('formOrder').value = 1;
    } else if (action === 'edit' && id) {
        document.getElementById('modalTitle').textContent = 'Sửa Địa chỉ';
        formAction.value = 'edit';
        form.action = '<?php echo APP_URL; ?>/admin/actions/addresses.php?action=edit&id=' + id;
        formId.value = id;
        
        const address = addressesData.find(a => a.id == id);
        if (address) {
            document.getElementById('formName').value = address.name || '';
            document.getElementById('formPhone').value = address.phone || '';
            document.getElementById('formAddress').value = address.address || '';
            document.getElementById('formWard').value = address.ward || '';
            document.getElementById('formDistrict').value = address.district || '';
            document.getElementById('formCity').value = address.city || '';
            document.getElementById('formEmail').value = address.email || '';
            document.getElementById('formMapUrl').value = address.map_url || '';
            document.getElementById('formType').value = address.type || 'office';
            if (address.northOffice == 1) {
                document.getElementById('formRegion').value = 'north';
            } else if (address.southOffice == 1) {
                document.getElementById('formRegion').value = 'south';
            } else {
                document.getElementById('formRegion').value = '';
            }
            document.getElementById('formOrder').value = address.display_order || 1;
            document.getElementById('formStatus').value = address.status || 'active';
            document.getElementById('formNote').value = address.note || '';
        }
    }
    
    modal.show();
}

// Xóa địa chỉ bằng AJAX để hiện toast trước khi reload
function deleteAddress(id) {
    if (!confirm('Bạn có chắc chắn muốn xóa địa chỉ này?')) return;
    fetch('<?php echo APP_URL; ?>/admin/actions/addresses.php?action=delete&id=' + id, {
        method: 'GET',
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            showToast(data.message || 'Xóa địa chỉ thành công!', 'success');
            setTimeout(() => { window.location.href = '<?php echo APP_URL; ?>/admin/addresses.php'; }, 2500);
        } else {
            showToast(data.message || 'Có lỗi xảy ra khi xóa địa chỉ!', 'error');
        }
    })
    .catch(() => {
        showToast('Có lỗi xảy ra khi xóa địa chỉ. Vui lòng thử lại.', 'error');
    });
}
</script>
<div class="card">
    <div class="card-header">
        <h5 class="card-title mb-0">Danh sách Địa chỉ</h5>
        <button class="btn btn-primary btn-sm" onclick="showModal('create')">
            <i class="fas fa-plus"></i> Thêm mới
        </button>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Tên</th>
                        <th>Địa chỉ</th>
                        <th>Thành phố</th>
                        <th>Điện thoại</th>
                        <th>Loại</th>
                        <th>Trạng thái</th>
                        <th>Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($addresses)): ?>
                        <tr>
                            <td colspan="8" class="text-center text-muted">Chưa có địa chỉ nào</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($addresses as $item): ?>
                            <tr>
                                <td><?php echo $item['id']; ?></td>
                                <td><?php echo htmlspecialchars($item['name']); ?></td>
                                <td><?php echo htmlspecialchars($item['address']); ?></td>
                                <td><?php echo htmlspecialchars($item['city']); ?></td>
                                <td><?php echo htmlspecialchars($item['phone']); ?></td>
                                <td>
                                    <?php 
                                        $types = [
                                            'office' => 'Văn phòng',
                                            'shop' => 'Shop',
                                            'warehouse' => 'Kho'
                                        ];
                                        echo $types[$item['type']] ?? $item['type'];
                                    ?>
                                </td>
                                <td>
                                    <span class="badge badge-<?php echo $item['status'] == 'active' ? 'success' : 'danger'; ?>">
                                        <?php echo $item['status'] == 'active' ? 'Hoạt động' : 'Tắt'; ?>
                                    </span>
                                </td>
                                <td>
                                    <button class="btn btn-sm btn-primary" onclick="showModal('edit', <?php echo $item['id']; ?>)">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <a href="<?php echo APP_URL; ?>/admin/actions/addresses.php?action=delete&id=<?php echo $item['id']; ?>" 
                                       class="btn btn-sm btn-danger" 
                                       onclick="return confirm('Bạn có chắc chắn muốn xóa?')">
                                        <i class="fas fa-trash"></i>
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

