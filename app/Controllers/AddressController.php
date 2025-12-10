<?php
/**
 * Address Controller
 */

class AddressController {
    private $addressModel;
    
    public function __construct() {
        $this->addressModel = new AddressModel();
    }
    
    public function index($type = null) {
        return $this->addressModel->getAll($type);
    }
    
    public function show($id) {
        return $this->addressModel->findById($id);
    }
    
    public function store() {
        $data = [
            'name' => trim($_POST['name'] ?? ''),
            'address' => trim($_POST['address'] ?? ''),
            'main' => isset($_POST['main']) ? intval($_POST['main']) : 0,
            'ward' => trim($_POST['ward'] ?? ''),
            'district' => trim($_POST['district'] ?? ''),
            'city' => trim($_POST['city'] ?? ''),
            'phone' => trim($_POST['phone'] ?? ''),
            'email' => trim($_POST['email'] ?? ''),
            'map_url' => trim($_POST['map_url'] ?? ''),
            'type' => $_POST['type'] ?? 'shop',
            'display_order' => intval($_POST['display_order'] ?? 0),
            'status' => $_POST['status'] ?? 'active',
            'note' => trim($_POST['note'] ?? '')
        ];
        
        if (empty($data['name']) || empty($data['address']) || empty($data['city']) || empty($data['phone']) || empty($data['map_url'])) {
            return ['success' => false, 'message' => 'Vui lòng nhập đầy đủ thông tin bắt buộc'];
        }
        
        if ($this->addressModel->create($data)) {
            return ['success' => true, 'message' => 'Thêm địa chỉ thành công'];
        }
        
        return ['success' => false, 'message' => 'Có lỗi xảy ra'];
    }
    
    public function update($id) {
        $data = [
            'name' => trim($_POST['name'] ?? ''),
            'address' => trim($_POST['address'] ?? ''),
            'main' => isset($_POST['main']) ? intval($_POST['main']) : 0,
            'ward' => trim($_POST['ward'] ?? ''),
            'district' => trim($_POST['district'] ?? ''),
            'city' => trim($_POST['city'] ?? ''),
            'phone' => trim($_POST['phone'] ?? ''),
            'email' => trim($_POST['email'] ?? ''),
            'map_url' => trim($_POST['map_url'] ?? ''),
            'type' => $_POST['type'] ?? 'shop',
            'display_order' => intval($_POST['display_order'] ?? 0),
            'status' => $_POST['status'] ?? 'active',
            'note' => trim($_POST['note'] ?? '')
        ];
        
        if (empty($data['name']) || empty($data['address']) || empty($data['city']) || empty($data['phone']) || empty($data['map_url'])) {
            return ['success' => false, 'message' => 'Vui lòng nhập đầy đủ thông tin bắt buộc'];
        }
        
        if ($this->addressModel->update($id, $data)) {
            return ['success' => true, 'message' => 'Cập nhật địa chỉ thành công'];
        }
        
        return ['success' => false, 'message' => 'Có lỗi xảy ra'];
    }
    
    public function destroy($id) {
        if ($this->addressModel->delete($id)) {
            return ['success' => true, 'message' => 'Xóa địa chỉ thành công'];
        }
        return ['success' => false, 'message' => 'Có lỗi xảy ra'];
    }
}

