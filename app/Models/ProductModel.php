<?php
/**
 * Product Model
 */

class ProductModel {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance();
    }
    
    public function getAll($filters = []) {
        $sql = "SELECT p.*, c.name as category_name FROM products p LEFT JOIN categories c ON p.category_id = c.id WHERE 1=1";
        $params = [];
        $types = "";
        
        if (!empty($filters['category_id'])) {
            $sql .= " AND p.category_id = ?";
            $params[] = $filters['category_id'];
            $types .= "i";
        }
        
        if (!empty($filters['status'])) {
            $sql .= " AND p.status = ?";
            $params[] = $filters['status'];
            $types .= "s";
        }
        
        if (!empty($filters['search'])) {
            $sql .= " AND (p.name LIKE ? OR p.sku LIKE ?)";
            $search = "%{$filters['search']}%";
            $params[] = $search;
            $params[] = $search;
            $types .= "ss";
        }
        
        $sql .= " ORDER BY p.id ASC";
        
        if (!empty($params)) {
            $stmt = $this->db->prepare($sql);
            $stmt->bind_param($types, ...$params);
            $stmt->execute();
            $result = $stmt->get_result();
        } else {
            $result = $this->db->query($sql);
        }
        
        $products = [];
        while ($row = $result->fetch_assoc()) {
            $products[] = $row;
        }
        return $products;
    }
    
    /**
     * Đếm số lượng sản phẩm với filter
     * @param array $filters
     * @return int
     */
    public function count($filters = []) {
        $sql = "SELECT COUNT(*) as total FROM products p WHERE 1=1";
        $params = [];
        $types = "";
        
        if (!empty($filters['category_id'])) {
            $sql .= " AND p.category_id = ?";
            $params[] = $filters['category_id'];
            $types .= "i";
        }
        
        if (!empty($filters['status'])) {
            $sql .= " AND p.status = ?";
            $params[] = $filters['status'];
            $types .= "s";
        }
        
        if (!empty($filters['search'])) {
            $sql .= " AND (p.name LIKE ? OR p.sku LIKE ?)";
            $search = "%{$filters['search']}%";
            $params[] = $search;
            $params[] = $search;
            $types .= "ss";
        }
        
        if (!empty($params)) {
            $stmt = $this->db->prepare($sql);
            $stmt->bind_param($types, ...$params);
            $stmt->execute();
            $result = $stmt->get_result();
        } else {
            $result = $this->db->query($sql);
        }
        
        $row = $result->fetch_assoc();
        return intval($row['total'] ?? 0);
    }
    
    public function findById($id) {
        $stmt = $this->db->prepare("SELECT p.*, c.name as category_name FROM products p LEFT JOIN categories c ON p.category_id = c.id WHERE p.id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }
    
    public function findSlugExists($slug, $excludeId = null) {
        $sql = "SELECT id FROM products WHERE slug = ?";
        if ($excludeId) {
            $sql .= " AND id != ?";
        }
        $stmt = $this->db->prepare($sql);
        if ($excludeId) {
            $stmt->bind_param("si", $slug, $excludeId);
        } else {
            $stmt->bind_param("s", $slug);
        }
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }
    
    public function create($data) {
            // Tạo SKU tự động: SP + ID (sẽ cập nhật sau khi insert)
            $stmt = $this->db->prepare("INSERT INTO products (category_id, name, slug, sku, description, price, sale_price, stock_quantity, featured, status, meta_title, meta_description) VALUES (?, ?, ?, 'SP_TEMP', ?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("isssddiisss",
                $data['category_id'],
                $data['name'],
                $data['slug'],
                $data['description'],
                $data['price'],
                $data['sale_price'],
                $data['stock_quantity'],
                $data['featured'],
                $data['status'],
                $data['meta_title'],
                $data['meta_description']
            );
            
            if ($stmt->execute()) {
            // Lấy ID từ statement thay vì từ Database instance
            $newId = $stmt->insert_id;
            if (empty($newId)) {
                // Fallback: lấy từ connection
                $newId = $this->db->getConnection()->insert_id;
            }
            if (empty($newId)) {
                // Fallback cuối cùng: query lại bằng slug
                $checkStmt = $this->db->prepare("SELECT id FROM products WHERE slug = ? ORDER BY id DESC LIMIT 1");
                $checkStmt->bind_param("s", $data['slug']);
                $checkStmt->execute();
                $result = $checkStmt->get_result();
                $row = $result->fetch_assoc();
                if ($row) {
                    $newId = $row['id'];
                }
            }
            
            if (!empty($newId) && $newId > 0) {
                // Cập nhật SKU với ID thực tế: SP + ID
                $sku = 'SP' . $newId;
                $updateStmt = $this->db->prepare("UPDATE products SET sku = ? WHERE id = ?");
                $updateStmt->bind_param("si", $sku, $newId);
                $updateStmt->execute();
                return $newId; // Trả về ID thay vì true
            }
            return false;
            }
        return false;
    }
    
    public function update($id, $data) {
            // Không cập nhật SKU, giữ nguyên SKU cũ
            $stmt = $this->db->prepare("UPDATE products SET category_id = ?, name = ?, slug = ?, description = ?, price = ?, sale_price = ?, stock_quantity = ?, featured = ?, status = ?, meta_title = ?, meta_description = ? WHERE id = ?");
            $stmt->bind_param("isssddiisssi",
                $data['category_id'],
                $data['name'],
                $data['slug'],
                $data['description'],
                $data['price'],
                $data['sale_price'],
                $data['stock_quantity'],
                $data['featured'],
                $data['status'],
                $data['meta_title'],
                $data['meta_description'],
                $id
            );
            return $stmt->execute();
    }
    
    public function delete($id) {
        $stmt = $this->db->prepare("DELETE FROM products WHERE id = ?");
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }
    
    public function getFeatured($limit = 8) {
        $sql = "SELECT p.*, c.name as category_name FROM products p LEFT JOIN categories c ON p.category_id = c.id WHERE p.featured = 1 AND p.status = 'active' ORDER BY p.id ASC LIMIT ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("i", $limit);
        $stmt->execute();
        $result = $stmt->get_result();
        
        $products = [];
        while ($row = $result->fetch_assoc()) {
            $products[] = $row;
        }
        return $products;
    }
    
    public function countActive() {
        $stmt = $this->db->prepare("SELECT COUNT(*) as total FROM products WHERE status = 'active'");
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        return intval($result['total'] ?? 0);
    }

    public function countActiveWithFilters($filters = []) {
        $priceExpr = "(CASE WHEN p.sale_price IS NOT NULL AND p.sale_price > 0 THEN p.sale_price ELSE p.price END)";
        $sql = "SELECT COUNT(*) as total
                FROM products p
                LEFT JOIN categories c ON p.category_id = c.id
                WHERE p.status = 'active'";

        $params = [];
        $types = "";

        if (!empty($filters['category_ids'])) {
            $placeholders = implode(',', array_fill(0, count($filters['category_ids']), '?'));
            $sql .= " AND p.category_id IN ($placeholders)";
            foreach ($filters['category_ids'] as $catId) {
                $params[] = $catId;
                $types .= "i";
            }
        }

        if (!empty($filters['keyword'])) {
            $sql .= " AND (p.name LIKE ? OR c.name LIKE ?)";
            $kw = '%' . $filters['keyword'] . '%';
            $params[] = $kw;
            $params[] = $kw;
            $types .= "ss";
        }

        if (isset($filters['price_min'])) {
            $sql .= " AND {$priceExpr} >= ?";
            $params[] = $filters['price_min'];
            $types .= "d";
        }

        if (isset($filters['price_max'])) {
            $sql .= " AND {$priceExpr} <= ?";
            $params[] = $filters['price_max'];
            $types .= "d";
        }

        if (!empty($filters['rating_min'])) {
            $sql .= " AND p.rating_avg >= ?";
            $params[] = $filters['rating_min'];
            $types .= "d";
        }

        if (!empty($params)) {
            $stmt = $this->db->prepare($sql);
            $stmt->bind_param($types, ...$params);
            $stmt->execute();
            $result = $stmt->get_result()->fetch_assoc();
        } else {
            $result = $this->db->query($sql)->fetch_assoc();
        }

        return intval($result['total'] ?? 0);
    }
    
    public function findDetailBySlug($slug, $status = 'active') {
        $sql = "SELECT p.*, c.name as category_name, c.slug as category_slug 
                FROM products p 
                LEFT JOIN categories c ON p.category_id = c.id 
                WHERE p.slug = ?";
        if ($status) {
            $sql .= " AND p.status = ?";
        }
        $sql .= " LIMIT 1";
        
        $stmt = $this->db->prepare($sql);
        if ($status) {
            $stmt->bind_param("ss", $slug, $status);
        } else {
            $stmt->bind_param("s", $slug);
        }
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }
    
    public function getAllPaginated($limit, $offset, $sort = 'default') {
        return $this->getAllPaginatedWithFilters($limit, $offset, $sort, []);
    }

    public function getAllPaginatedWithFilters($limit, $offset, $sort = 'default', $filters = []) {
        $priceExpr = "(CASE WHEN p.sale_price IS NOT NULL AND p.sale_price > 0 THEN p.sale_price ELSE p.price END)";

        switch ($sort) {
            case 'price-asc':
                $orderBy = "{$priceExpr} ASC";
                break;
            case 'price-desc':
                $orderBy = "{$priceExpr} DESC";
                break;
            case 'newest':
                $orderBy = "p.id DESC";
                break;
            case 'popular':
                $orderBy = "p.sold_count DESC, p.rating_count DESC";
                break;
            default:
                $orderBy = "p.id DESC";
        }
        
        $sql = "SELECT p.*, c.name as category_name, {$priceExpr} as effective_price
                FROM products p 
                LEFT JOIN categories c ON p.category_id = c.id 
                WHERE p.status = 'active'";

        $params = [];
        $types = "";

        if (!empty($filters['category_ids'])) {
            $placeholders = implode(',', array_fill(0, count($filters['category_ids']), '?'));
            $sql .= " AND p.category_id IN ($placeholders)";
            foreach ($filters['category_ids'] as $catId) {
                $params[] = $catId;
                $types .= "i";
            }
        }

        if (!empty($filters['keyword'])) {
            $sql .= " AND (p.name LIKE ? OR c.name LIKE ?)";
            $kw = '%' . $filters['keyword'] . '%';
            $params[] = $kw;
            $params[] = $kw;
            $types .= "ss";
        }

        if (isset($filters['price_min'])) {
            $sql .= " AND {$priceExpr} >= ?";
            $params[] = $filters['price_min'];
            $types .= "d";
        }

        if (isset($filters['price_max'])) {
            $sql .= " AND {$priceExpr} <= ?";
            $params[] = $filters['price_max'];
            $types .= "d";
        }

        if (!empty($filters['rating_min'])) {
            $sql .= " AND p.rating_avg >= ?";
            $params[] = $filters['rating_min'];
            $types .= "d";
        }

        $sql .= " ORDER BY {$orderBy} LIMIT ? OFFSET ?";
        $params[] = $limit;
        $params[] = $offset;
        $types .= "ii";

        $stmt = $this->db->prepare($sql);
        $stmt->bind_param($types, ...$params);
        $stmt->execute();
        $result = $stmt->get_result();
        
        $products = [];
        while ($row = $result->fetch_assoc()) {
            $products[] = $row;
        }
        return $products;
    }
    
    public function getByCategorySlug($slug, $limit = 8) {
        /**
         * Lấy sản phẩm theo slug category, bao gồm:
         * - Sản phẩm thuộc category có slug = $slug
         * - Sản phẩm thuộc category con của category này (parent_id = category slug)
         */
        $sql = "
            SELECT p.*, c.name as category_name
            FROM products p
            LEFT JOIN categories c ON p.category_id = c.id
            WHERE p.status = 'active'
              AND (
                    c.slug = ?
                    OR c.parent_id = (SELECT id FROM categories WHERE slug = ? LIMIT 1)
                  )
            ORDER BY p.id ASC
            LIMIT ?
        ";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("ssi", $slug, $slug, $limit);
        $stmt->execute();
        $result = $stmt->get_result();
        
        $products = [];
        while ($row = $result->fetch_assoc()) {
            $products[] = $row;
        }
        return $products;
    }
}

