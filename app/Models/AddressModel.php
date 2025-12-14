<?php
/**
 * Address Model
 */

class AddressModel {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance();
    }
    
    public function getAll($type = null) {
        $sql = "SELECT * FROM addresses";
        if ($type) {
            $sql .= " WHERE type = ?";
        }
        $sql .= " ORDER BY display_order ASC, id DESC";
        
        $stmt = $this->db->prepare($sql);
        if ($type) {
            $stmt->bind_param("s", $type);
        }
        $stmt->execute();
        $result = $stmt->get_result();
        
        $addresses = [];
        while ($row = $result->fetch_assoc()) {
            $addresses[] = $row;
        }
        return $addresses;
    }
    
    public function findById($id) {
        $stmt = $this->db->prepare("SELECT * FROM addresses WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }
    
    public function create($data) {
        // Kiểm tra và lấy main từ data, mặc định là 0 nếu không có
        $main = isset($data['main']) ? intval($data['main']) : 0;
        
        $stmt = $this->db->prepare("INSERT INTO addresses (name, address, main, northOffice, southOffice, ward, district, city, phone, email, map_url, latitude, longitude, type, display_order, status, note) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NULL, NULL, ?, ?, ?, ?)");
        // name, address, main, northOffice, southOffice, ward, district, city, phone, email, map_url, type, display_order, status, note
        $stmt->bind_param("ssiiisssssssiss",
            $data['name'],           // s
            $data['address'],        // s
            $main,                   // i (main)
            $data['northOffice'],    // i
            $data['southOffice'],    // i
            $data['ward'],           // s
            $data['district'],       // s
            $data['city'],           // s
            $data['phone'],          // s
            $data['email'],          // s
            $data['map_url'],        // s
            $data['type'],           // s
            $data['display_order'],   // i
            $data['status'],         // s
            $data['note']            // s
        );
        return $stmt->execute();
    }
    
    public function update($id, $data) {
        // Kiểm tra và lấy main từ data, mặc định là 0 nếu không có
        $main = isset($data['main']) ? intval($data['main']) : 0;
        
        $stmt = $this->db->prepare("UPDATE addresses SET name = ?, address = ?, main = ?, northOffice = ?, southOffice = ?, ward = ?, district = ?, city = ?, phone = ?, email = ?, map_url = ?, latitude = NULL, longitude = NULL, type = ?, display_order = ?, status = ?, note = ? WHERE id = ?");
        // name, address, main, northOffice, southOffice, ward, district, city, phone, email, map_url, type, display_order, status, note, id
        $stmt->bind_param("ssiiisssssssissi",
            $data['name'],           // s
            $data['address'],        // s
            $main,                   // i (main)
            $data['northOffice'],    // i
            $data['southOffice'],    // i
            $data['ward'],           // s
            $data['district'],       // s
            $data['city'],           // s
            $data['phone'],          // s
            $data['email'],          // s
            $data['map_url'],        // s
            $data['type'],           // s
            $data['display_order'],  // i
            $data['status'],        // s
            $data['note'],          // s
            $id                      // i
        );
        return $stmt->execute();
    }
    
    public function delete($id) {
        $stmt = $this->db->prepare("DELETE FROM addresses WHERE id = ?");
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }
    
    /**
     * Lấy địa chỉ chính (main = 1)
     */
    public function getMain() {
        $stmt = $this->db->prepare("SELECT * FROM addresses WHERE main = 1 AND status = 'active' LIMIT 1");
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }

    /**
     * Lấy danh sách địa chỉ theo type, ưu tiên status active
     */
    public function getActiveByType($type, $limit = null) {
        $sql = "SELECT * FROM addresses WHERE type = ? AND status = 'active' ORDER BY display_order ASC, id DESC";
        if ($limit !== null) {
            $sql .= " LIMIT ?";
        }

        $stmt = $this->db->prepare($sql);
        if ($limit !== null) {
            $stmt->bind_param("si", $type, $limit);
        } else {
            $stmt->bind_param("s", $type);
        }

        $stmt->execute();
        $result = $stmt->get_result();

        $addresses = [];
        while ($row = $result->fetch_assoc()) {
            $addresses[] = $row;
        }
        return $addresses;
    }

    /**
     * Lấy văn phòng miền Bắc (northOffice = 1)
     */
    public function getNorthOffice() {
        $stmt = $this->db->prepare("SELECT * FROM addresses WHERE northOffice = 1 AND status = 'active' ORDER BY display_order ASC, id DESC LIMIT 1");
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }

    /**
     * Lấy văn phòng miền Nam (southOffice = 1)
     */
    public function getSouthOffice() {
        $stmt = $this->db->prepare("SELECT * FROM addresses WHERE southOffice = 1 AND status = 'active' ORDER BY display_order ASC, id DESC LIMIT 1");
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }

    /**
     * Lấy danh sách shop (type = 'shop')
     */
    public function getShops($limit = 10) {
        $stmt = $this->db->prepare("SELECT * FROM addresses WHERE type = 'shop' AND status = 'active' ORDER BY display_order ASC, id DESC LIMIT ?");
        $stmt->bind_param("i", $limit);
        $stmt->execute();
        $result = $stmt->get_result();

        $addresses = [];
        while ($row = $result->fetch_assoc()) {
            $addresses[] = $row;
        }
        return $addresses;
    }
}

