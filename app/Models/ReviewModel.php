<?php
/**
 * Review Model
 */

class ReviewModel {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance();
    }
    
    /**
     * Lấy tất cả reviews với filter
     * @param array $filters
     * @return array
     */
    public function getAll($filters = []) {
        $sql = "SELECT r.*, p.name as product_name, p.sku as product_sku 
                FROM reviews r 
                LEFT JOIN products p ON r.product_id = p.id 
                WHERE 1=1";
        $params = [];
        $types = "";
        
        if (!empty($filters['status'])) {
            $sql .= " AND r.status = ?";
            $params[] = $filters['status'];
            $types .= "s";
        }
        
        if (!empty($filters['product_id'])) {
            $sql .= " AND r.product_id = ?";
            $params[] = $filters['product_id'];
            $types .= "i";
        }
        
        if (!empty($filters['search'])) {
            $sql .= " AND (r.customer_name LIKE ? OR r.comment LIKE ? OR p.name LIKE ?)";
            $search = "%{$filters['search']}%";
            $params[] = $search;
            $params[] = $search;
            $params[] = $search;
            $types .= "sss";
        }
        
        $sql .= " ORDER BY r.created_at DESC";
        
        if (!empty($params)) {
            $stmt = $this->db->prepare($sql);
            $stmt->bind_param($types, ...$params);
            $stmt->execute();
            $result = $stmt->get_result();
        } else {
            $result = $this->db->query($sql);
        }
        
        $reviews = [];
        while ($row = $result->fetch_assoc()) {
            $reviews[] = $row;
        }
        return $reviews;
    }
    
    /**
     * Lấy các reviews có main = 1 (hiển thị trên trang chủ)
     * @param int $limit Số lượng reviews cần lấy
     * @return array
     */
    public function getMainReviews($limit = 3) {
        $sql = "SELECT * FROM reviews WHERE main = 1 AND status = 'approved' ORDER BY created_at DESC LIMIT ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("i", $limit);
        $stmt->execute();
        $result = $stmt->get_result();
        
        $reviews = [];
        while ($row = $result->fetch_assoc()) {
            $reviews[] = $row;
        }
        
        return $reviews;
    }
    
    /**
     * Lấy tất cả reviews theo product_id
     * @param int $productId
     * @param string $status
     * @return array
     */
    public function getByProductId($productId, $status = 'approved') {
        $sql = "SELECT * FROM reviews WHERE product_id = ? AND status = ? ORDER BY created_at DESC";
        $stmt = $this->db->prepare($sql);
        if (!$stmt) {
            return [];
        }
        $stmt->bind_param("is", $productId, $status);
        $stmt->execute();
        $result = $stmt->get_result();
        
        $reviews = [];
        while ($row = $result->fetch_assoc()) {
            $reviews[] = $row;
        }
        
        return $reviews;
    }
    
    /**
     * Lấy review theo ID
     * @param int $id
     * @return array|null
     */
    public function getById($id) {
        $sql = "SELECT r.*, p.name as product_name, p.sku as product_sku 
                FROM reviews r 
                LEFT JOIN products p ON r.product_id = p.id 
                WHERE r.id = ?";
        $stmt = $this->db->prepare($sql);
        if (!$stmt) {
            return null;
        }
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        return $result->fetch_assoc();
    }
    
    /**
     * Tạo review mới
     * @param array $data
     * @return int|false ID của review mới hoặc false nếu lỗi
     */
    public function create($data) {
        $sql = "INSERT INTO reviews (product_id, user_id, order_id, rating, comment, customer_name, status, main) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $this->db->prepare($sql);
        if (!$stmt) {
            return false;
        }
        $stmt->bind_param(
            "iiiisssi",
            $data['product_id'],
            $data['user_id'],
            $data['order_id'],
            $data['rating'],
            $data['comment'],
            $data['customer_name'],
            $data['status'],
            $data['main']
        );
        
        if ($stmt->execute()) {
            return $this->db->getLastInsertId();
        }
        
        return false;
    }
    
    /**
     * Cập nhật review
     * @param int $id
     * @param array $data
     * @return bool
     */
    public function update($id, $data) {
        $sql = "UPDATE reviews SET product_id = ?, rating = ?, comment = ?, customer_name = ?, status = ?, main = ? WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        if (!$stmt) {
            return false;
        }
        $stmt->bind_param(
            "iisssii",
            $data['product_id'],
            $data['rating'],
            $data['comment'],
            $data['customer_name'],
            $data['status'],
            $data['main'],
            $id
        );
        
        return $stmt->execute();
    }
    
    /**
     * Xóa review
     * @param int $id
     * @return bool
     */
    public function delete($id) {
        $sql = "DELETE FROM reviews WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        if (!$stmt) {
            return false;
        }
        $stmt->bind_param("i", $id);
        
        return $stmt->execute();
    }

    /**
     * Đếm số review theo product_id (mặc định chỉ lấy review đã duyệt)
     * @param int $productId
     * @param string $status
     * @return int
     */
    public function countByProductId($productId, $status = 'approved') {
        $sql = "SELECT COUNT(*) as total FROM reviews WHERE product_id = ? AND status = ?";
        $stmt = $this->db->prepare($sql);
        if (!$stmt) {
            return 0;
        }
        $stmt->bind_param("is", $productId, $status);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        return intval($result['total'] ?? 0);
    }
    
    /**
     * Tính rating trung bình theo product_id
     * @param int $productId
     * @param string $status
     * @return float
     */
    public function getAverageRating($productId, $status = 'approved') {
        $sql = "SELECT AVG(rating) as avg_rating FROM reviews WHERE product_id = ? AND status = ?";
        $stmt = $this->db->prepare($sql);
        if (!$stmt) {
            return 0.0;
        }
        $stmt->bind_param("is", $productId, $status);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        return floatval($result['avg_rating'] ?? 0.0);
    }
    
    /**
     * Lấy phân bố rating theo product_id (số lượng đánh giá cho mỗi mức sao)
     * @param int $productId
     * @param string $status
     * @return array ['1' => count, '2' => count, ..., '5' => count]
     */
    public function getRatingDistribution($productId, $status = 'approved') {
        $sql = "SELECT rating, COUNT(*) as count FROM reviews WHERE product_id = ? AND status = ? GROUP BY rating ORDER BY rating DESC";
        $stmt = $this->db->prepare($sql);
        if (!$stmt) {
            return ['5' => 0, '4' => 0, '3' => 0, '2' => 0, '1' => 0];
        }
        $stmt->bind_param("is", $productId, $status);
        $stmt->execute();
        $result = $stmt->get_result();
        
        $distribution = ['5' => 0, '4' => 0, '3' => 0, '2' => 0, '1' => 0];
        while ($row = $result->fetch_assoc()) {
            $rating = strval($row['rating']);
            $distribution[$rating] = intval($row['count']);
        }
        
        return $distribution;
    }
}
