<?php
/**
 * Review Actions Handler
 */

require_once __DIR__ . '/../../../config/config.php';
require_once BASE_PATH . '/app/Database/Database.php';
require_once BASE_PATH . '/app/Middleware/AuthMiddleware.php';
require_once BASE_PATH . '/app/Models/ReviewModel.php';

AuthMiddleware::check();

$reviewModel = new ReviewModel();
$action = $_GET['action'] ?? '';
$id = $_GET['id'] ?? null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($action === 'create') {
        $data = [
            'product_id' => intval($_POST['product_id'] ?? 0),
            'user_id' => null,
            'order_id' => null,
            'rating' => intval($_POST['rating'] ?? 5),
            'comment' => trim($_POST['comment'] ?? ''),
            'customer_name' => trim($_POST['customer_name'] ?? ''),
            'status' => $_POST['status'] ?? 'pending',
            'main' => isset($_POST['main']) ? 1 : 0
        ];
        
        // Validate
        if (empty($data['product_id']) || $data['product_id'] <= 0) {
            header('Location: ' . APP_URL . '/admin/reviews.php?error=' . urlencode('Vui lòng chọn sản phẩm'));
            exit;
        }
        
        if (empty($data['customer_name'])) {
            header('Location: ' . APP_URL . '/admin/reviews.php?error=' . urlencode('Tên khách hàng không được để trống'));
            exit;
        }
        
        if ($data['rating'] < 1 || $data['rating'] > 5) {
            header('Location: ' . APP_URL . '/admin/reviews.php?error=' . urlencode('Đánh giá phải từ 1 đến 5 sao'));
            exit;
        }
        
        $result = $reviewModel->create($data);
        if ($result) {
            header('Location: ' . APP_URL . '/admin/reviews.php?success=1&message=' . urlencode('Thêm đánh giá thành công!'));
        } else {
            header('Location: ' . APP_URL . '/admin/reviews.php?error=' . urlencode('Có lỗi xảy ra khi thêm đánh giá'));
        }
        exit;
    }
    
    if ($action === 'edit' && $id) {
        $data = [
            'product_id' => intval($_POST['product_id'] ?? 0),
            'rating' => intval($_POST['rating'] ?? 5),
            'comment' => trim($_POST['comment'] ?? ''),
            'customer_name' => trim($_POST['customer_name'] ?? ''),
            'status' => $_POST['status'] ?? 'pending',
            'main' => isset($_POST['main']) ? 1 : 0
        ];
        
        // Validate
        if (empty($data['product_id']) || $data['product_id'] <= 0) {
            header('Location: ' . APP_URL . '/admin/reviews.php?action=edit&id=' . $id . '&error=' . urlencode('Vui lòng chọn sản phẩm'));
            exit;
        }
        
        if (empty($data['customer_name'])) {
            header('Location: ' . APP_URL . '/admin/reviews.php?action=edit&id=' . $id . '&error=' . urlencode('Tên khách hàng không được để trống'));
            exit;
        }
        
        if ($data['rating'] < 1 || $data['rating'] > 5) {
            header('Location: ' . APP_URL . '/admin/reviews.php?action=edit&id=' . $id . '&error=' . urlencode('Đánh giá phải từ 1 đến 5 sao'));
            exit;
        }
        
        $result = $reviewModel->update($id, $data);
        if ($result) {
            header('Location: ' . APP_URL . '/admin/reviews.php?success=1&message=' . urlencode('Cập nhật đánh giá thành công!'));
        } else {
            header('Location: ' . APP_URL . '/admin/reviews.php?action=edit&id=' . $id . '&error=' . urlencode('Có lỗi xảy ra khi cập nhật đánh giá'));
        }
        exit;
    }
}

if ($action === 'delete' && $id) {
    $result = $reviewModel->delete($id);
    $isAjax = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
    
    if ($isAjax) {
        header('Content-Type: application/json');
        echo json_encode([
            'success' => $result,
            'message' => $result ? 'Xóa đánh giá thành công!' : 'Có lỗi xảy ra khi xóa đánh giá'
        ]);
        exit;
    }
    
    header('Location: ' . APP_URL . '/admin/reviews.php?success=1&message=' . urlencode('Xóa đánh giá thành công!'));
    exit;
}

header('Location: ' . APP_URL . '/admin/reviews.php');
exit;
