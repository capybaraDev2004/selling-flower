<?php
require_once '../../config/config.php';
require_once '../../app/Database/Database.php';

header('Content-Type: application/json; charset=utf-8');

function respond($statusCode, $data) {
    http_response_code($statusCode);
    echo json_encode($data, JSON_UNESCAPED_UNICODE);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    respond(405, ['success' => false, 'message' => 'Phương thức không được hỗ trợ']);
}

$payload = json_decode(file_get_contents('php://input'), true);

$fullname = trim($payload['fullname'] ?? '');
$phone = trim($payload['phone'] ?? '');
$email = trim($payload['email'] ?? '');
$address = trim($payload['address'] ?? '');
$note = trim($payload['note'] ?? '');
$paymentMethod = strtoupper(trim($payload['payment_method'] ?? ''));
$items = $payload['items'] ?? [];

if (!$fullname || !$phone || !$email || !$address || !in_array($paymentMethod, ['COD', 'BANKPLUS'])) {
    respond(400, ['success' => false, 'message' => 'Thiếu thông tin bắt buộc']);
}
if (!is_array($items) || count($items) === 0) {
    respond(400, ['success' => false, 'message' => 'Giỏ hàng trống']);
}

function ensureTables($conn) {
    // Đặt tên cột khớp schema hiện có: status, payment_method (COD/BANKPLUS)
    $conn->query("
        CREATE TABLE IF NOT EXISTS orders (
            id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            order_code VARCHAR(32) NOT NULL UNIQUE,
            customer_name VARCHAR(150) NOT NULL,
            customer_phone VARCHAR(20) NOT NULL,
            customer_email VARCHAR(150) NOT NULL,
            shipping_address TEXT NOT NULL,
            note TEXT NULL,
            payment_method ENUM('COD','BANKPLUS') NOT NULL DEFAULT 'COD',
            status ENUM('PENDING','CONFIRMED','SHIPPING','COMPLETE') NOT NULL DEFAULT 'PENDING',
            subtotal DECIMAL(18,2) NOT NULL DEFAULT 0,
            shipping_fee DECIMAL(18,2) NOT NULL DEFAULT 0,
            total DECIMAL(18,2) NOT NULL DEFAULT 0,
            created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            INDEX idx_orders_status (status),
            INDEX idx_orders_created (created_at)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
    ");

    $conn->query("
        CREATE TABLE IF NOT EXISTS order_items (
            id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            order_id BIGINT UNSIGNED NOT NULL,
            product_id BIGINT UNSIGNED NULL,
            product_name VARCHAR(255) NOT NULL,
            product_slug VARCHAR(255) NULL,
            product_image VARCHAR(255) NULL,
            price DECIMAL(18,2) NOT NULL DEFAULT 0,
            quantity INT UNSIGNED NOT NULL DEFAULT 1,
            line_total DECIMAL(18,2) NOT NULL DEFAULT 0,
            created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
            CONSTRAINT fk_order_items_order FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
            INDEX idx_order_items_order (order_id)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
    ");
}

function generateOrderCode() {
    $now = new DateTime('now', new DateTimeZone('Asia/Ho_Chi_Minh'));
    return 'DH' . $now->format('Ymd-His');
}

try {
    $db = Database::getInstance()->getConnection();
    ensureTables($db);
    $db->begin_transaction();

    $orderCode = generateOrderCode();
    $subtotal = 0;
    $shippingFee = 0;
    $sanitizedItems = [];

    foreach ($items as $item) {
        $pid = isset($item['id']) ? (int)$item['id'] : null;
        $qty = max(1, (int)($item['quantity'] ?? 1));
        $price = (float)($item['price'] ?? 0);
        $lineTotal = $price * $qty;
        $subtotal += $lineTotal;

        $sanitizedItems[] = [
            'product_id' => $pid ?: null,
            'product_name' => $item['name'] ?? ('Sản phẩm #' . ($pid ?: '')),
            'product_slug' => $item['slug'] ?? null,
            'product_image' => $item['image'] ?? null,
            'price' => $price,
            'quantity' => $qty,
            'line_total' => $lineTotal
        ];
    }

    $total = $subtotal + $shippingFee;
    $status = 'PENDING'; // luôn chờ xác nhận

    $stmt = $db->prepare("INSERT INTO orders (order_code, customer_name, customer_phone, customer_email, shipping_address, note, payment_method, status, subtotal, shipping_fee, total, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW())");
    $stmt->bind_param(
        "ssssssssddd",
        $orderCode,
        $fullname,
        $phone,
        $email,
        $address,
        $note,
        $paymentMethod,
        $status,
        $subtotal,
        $shippingFee,
        $total
    );
    $stmt->execute();
    $orderId = $db->insert_id;

    $stmtItem = $db->prepare("INSERT INTO order_items (order_id, product_id, product_name, product_slug, product_image, price, quantity, line_total, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW())");
    foreach ($sanitizedItems as $it) {
        $pid = $it['product_id'];
        $pname = $it['product_name'] ?? '';
        $pslug = $it['product_slug'] ?? '';
        $pimage = $it['product_image'] ?? '';
        $pprice = (float)$it['price'];
        $pqty = (int)$it['quantity'];
        $pline = (float)$it['line_total'];

        $stmtItem->bind_param(
            "iisssdid",
            $orderId,
            $pid,
            $pname,
            $pslug,
            $pimage,
            $pprice,
            $pqty,
            $pline
        );
        $stmtItem->execute();
    }

    $db->commit();

    respond(200, [
        'success' => true,
        'order' => [
            'id' => $orderId,
            'code' => $orderCode,
            'status' => $status,
            'payment_method' => $paymentMethod,
            'subtotal' => $subtotal,
            'shipping_fee' => $shippingFee,
            'total' => $total
        ]
    ]);
} catch (Throwable $e) {
    if (isset($db)) {
        $db->rollback();
    }
    error_log('[create_order] ' . $e->getMessage());
    respond(500, ['success' => false, 'message' => 'Không thể tạo đơn hàng', 'error' => $e->getMessage()]);
}

