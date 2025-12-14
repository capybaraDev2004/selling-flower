<?php
/**
 * Product Controller
 */

require_once BASE_PATH . '/app/Helpers/ImageUploader.php';
require_once BASE_PATH . '/app/Models/ProductImageModel.php';
require_once BASE_PATH . '/app/Models/ProductAttributeModel.php';

class ProductController {
    private $productModel;
    
    public function __construct() {
        $this->productModel = new ProductModel();
    }
    
    public function index($filters = []) {
        return $this->productModel->getAll($filters);
    }
    
    public function show($id) {
        return $this->productModel->findById($id);
    }
    
    public function store() {
        $name = trim($_POST['name'] ?? '');
        
        $data = [
            'category_id' => intval($_POST['category_id'] ?? 0),
            'name' => $name,
            'slug' => '', // Sẽ được tạo sau
            'description' => trim($_POST['description'] ?? ''),
            'price' => floatval($_POST['price'] ?? 0),
            'sale_price' => !empty($_POST['sale_price']) ? floatval($_POST['sale_price']) : null,
            'stock_quantity' => intval($_POST['stock_quantity'] ?? 0),
            'featured' => isset($_POST['featured']) ? 1 : 0,
            'status' => $_POST['status'] ?? 'active',
            'meta_title' => trim($_POST['meta_title'] ?? ''),
            'meta_description' => trim($_POST['meta_description'] ?? '')
        ];
        
        // Validation các trường bắt buộc
        if (empty($data['name'])) {
            return ['success' => false, 'message' => 'Vui lòng nhập tên sản phẩm'];
        }
        if (empty($data['category_id']) || $data['category_id'] <= 0) {
            return ['success' => false, 'message' => 'Vui lòng chọn danh mục'];
        }
        if (empty($data['description'])) {
            return ['success' => false, 'message' => 'Vui lòng nhập mô tả sản phẩm'];
        }
        if (empty($data['price']) || $data['price'] <= 0) {
            return ['success' => false, 'message' => 'Vui lòng nhập giá gốc hợp lệ'];
        }
        if (!isset($data['stock_quantity']) || $data['stock_quantity'] <= 0) {
            return ['success' => false, 'message' => 'Vui lòng nhập số lượng tồn kho lớn hơn 0'];
        }
        if (empty($data['status'])) {
            return ['success' => false, 'message' => 'Vui lòng chọn trạng thái'];
        }
        if (empty($data['meta_title'])) {
            return ['success' => false, 'message' => 'Vui lòng nhập tiêu đề SEO'];
        }
        if (empty($data['meta_description'])) {
            return ['success' => false, 'message' => 'Vui lòng nhập mô tả SEO'];
        }
        
        // Kiểm tra ảnh chính - chỉ chấp nhận file upload
        $primaryImageFile = $_FILES['primary_image_file'] ?? null;
        
        if (!$primaryImageFile || !isset($primaryImageFile['tmp_name']) || empty($primaryImageFile['tmp_name'])) {
            return ['success' => false, 'message' => 'Vui lòng chọn ảnh chính cho sản phẩm'];
        }
        
        // Tạo slug unique trước khi insert
        $data['slug'] = createUniqueSlug($name, $this->productModel);
        
        // Tạo sản phẩm trước để có SKU (SKU sẽ tự động được tạo)
        try {
            $productId = $this->productModel->create($data);
            
            // create() trả về ID nếu thành công, false nếu thất bại
            if (!$productId || $productId <= 0) {
                return ['success' => false, 'message' => 'Không thể tạo sản phẩm. Vui lòng kiểm tra lại thông tin.'];
            }
            
            // Lấy SKU từ sản phẩm vừa tạo
            $product = $this->productModel->findById($productId);
            if (!$product || empty($product['sku'])) {
                // Xóa sản phẩm nếu không lấy được SKU
                $this->productModel->delete($productId);
                return ['success' => false, 'message' => 'Lỗi khi tạo SKU sản phẩm. Vui lòng thử lại.'];
            }
            
            $sku = $product['sku'];
            $imageModel = new ProductImageModel();
            
            // Upload ảnh chính với tên file = SKU.jpg
            $uploadResult = ImageUploader::uploadWithCustomName($primaryImageFile, 'products', $sku, true);
            if (!$uploadResult['success']) {
                // Xóa sản phẩm nếu upload ảnh thất bại
                $this->productModel->delete($productId);
                return ['success' => false, 'message' => $uploadResult['message'] ?? 'Lỗi khi upload ảnh chính'];
            }
            
            $finalPrimaryImageUrl = $uploadResult['url'];
            
            // Lưu ảnh chính vào database
            $imageResult = $imageModel->create([
                'product_id' => $productId,
                'image_url' => $finalPrimaryImageUrl,
                'is_primary' => 1,
                'display_order' => 0
            ]);
            
            // Kiểm tra lỗi khi thêm ảnh
            if (!$imageResult) {
                // Xóa file ảnh và sản phẩm nếu không thêm được ảnh vào database
                ImageUploader::delete($finalPrimaryImageUrl);
                $this->productModel->delete($productId);
                return ['success' => false, 'message' => 'Lỗi khi thêm ảnh sản phẩm. Vui lòng thử lại.'];
            }
            
            // Xử lý ảnh bổ sung với tên file = SKU_{index}.jpg
            if (isset($_FILES['additional_images']) && is_array($_FILES['additional_images'])) {
                $additionalFiles = $_FILES['additional_images'];
                
                // Kiểm tra xem có file nào được upload không
                if (isset($additionalFiles['name']) && is_array($additionalFiles['name'])) {
                    $fileCount = count($additionalFiles['name']);
                    
                    // Kiểm tra xem có ít nhất một file hợp lệ không
                    $hasValidFile = false;
                    for ($i = 0; $i < $fileCount; $i++) {
                        if (isset($additionalFiles['error'][$i]) && $additionalFiles['error'][$i] === UPLOAD_ERR_OK) {
                            $hasValidFile = true;
                            break;
                        }
                    }
                    
                    if ($hasValidFile) {
                        for ($i = 0; $i < $fileCount; $i++) {
                            if (isset($additionalFiles['error'][$i]) && $additionalFiles['error'][$i] === UPLOAD_ERR_OK) {
                                $file = [
                                    'name' => $additionalFiles['name'][$i],
                                    'type' => $additionalFiles['type'][$i],
                                    'tmp_name' => $additionalFiles['tmp_name'][$i],
                                    'error' => $additionalFiles['error'][$i],
                                    'size' => $additionalFiles['size'][$i]
                                ];
                                
                                // Tên file = SKU_{index+1}.jpg (index bắt đầu từ 0, nhưng số thứ tự từ 1)
                                $customFileName = $sku . '_' . ($i + 1);
                                $uploadResult = ImageUploader::uploadWithCustomName($file, 'products', $customFileName, true);
                                
                                if ($uploadResult['success']) {
                                    $imageModel->create([
                                        'product_id' => $productId,
                                        'image_url' => $uploadResult['url'],
                                        'is_primary' => 0,
                                        'display_order' => $i + 1
                                    ]);
                                }
                            }
                        }
                    }
                }
            }
            
            // Xử lý lưu attributes
            if (isset($_POST['attributes']) && is_array($_POST['attributes'])) {
                $attributeModel = new ProductAttributeModel();
                $attributes = [];
                foreach ($_POST['attributes'] as $attr) {
                    $attrName = trim($attr['attribute_name'] ?? '');
                    // Giữ nguyên xuống dòng (\n) trong attribute_value, chỉ trim khoảng trắng ở đầu và cuối
                    $attrValue = preg_replace('/^[\s]+|[\s]+$/u', '', $attr['attribute_value'] ?? '');
                    if (!empty($attrName) && !empty($attrValue)) {
                        $attributes[] = [
                            'attribute_name' => $attrName,
                            'attribute_value' => $attrValue
                        ];
                    }
                }
                if (!empty($attributes)) {
                    $attributeModel->createMultiple($productId, $attributes);
                }
            }
            
            return ['success' => true, 'message' => 'Thêm sản phẩm thành công'];
        } catch (mysqli_sql_exception $e) {
            // Xử lý lỗi duplicate slug
            if (strpos($e->getMessage(), 'Duplicate entry') !== false && strpos($e->getMessage(), 'slug') !== false) {
                // Thử tạo slug unique lại và insert lại
                $data['slug'] = createUniqueSlug($name, $this->productModel);
                try {
                    $productId = $this->productModel->create($data);
                    
                    if (!$productId || $productId <= 0) {
                        return ['success' => false, 'message' => 'Lỗi khi tạo sản phẩm. Vui lòng thử lại.'];
                    }
                    
                    // Lấy SKU từ sản phẩm vừa tạo
                    $product = $this->productModel->findById($productId);
                    if (!$product || empty($product['sku'])) {
                        $this->productModel->delete($productId);
                        return ['success' => false, 'message' => 'Lỗi khi tạo SKU sản phẩm. Vui lòng thử lại.'];
                    }
                    
                    $sku = $product['sku'];
                    $imageModel = new ProductImageModel();
                    
                    // Upload ảnh chính với tên file = SKU.jpg
                    $uploadResult = ImageUploader::uploadWithCustomName($primaryImageFile, 'products', $sku, true);
                    if (!$uploadResult['success']) {
                        $this->productModel->delete($productId);
                        return ['success' => false, 'message' => $uploadResult['message'] ?? 'Lỗi khi upload ảnh chính'];
                    }
                    
                    $finalPrimaryImageUrl = $uploadResult['url'];
                    
                    // Lưu ảnh chính vào database
                    $imageResult = $imageModel->create([
                        'product_id' => $productId,
                        'image_url' => $finalPrimaryImageUrl,
                        'is_primary' => 1,
                        'display_order' => 0
                    ]);
                    
                    if (!$imageResult) {
                        ImageUploader::delete($finalPrimaryImageUrl);
                        $this->productModel->delete($productId);
                        return ['success' => false, 'message' => 'Lỗi khi thêm ảnh sản phẩm. Vui lòng thử lại.'];
                    }
                    
                    // Xử lý ảnh bổ sung với tên file = SKU_{index}.jpg
                    if (isset($_FILES['additional_images']) && is_array($_FILES['additional_images'])) {
                        $additionalFiles = $_FILES['additional_images'];
                        
                        if (isset($additionalFiles['name']) && is_array($additionalFiles['name'])) {
                            $fileCount = count($additionalFiles['name']);
                            
                            $hasValidFile = false;
                            for ($i = 0; $i < $fileCount; $i++) {
                                if (isset($additionalFiles['error'][$i]) && $additionalFiles['error'][$i] === UPLOAD_ERR_OK) {
                                    $hasValidFile = true;
                                    break;
                                }
                            }
                            
                            if ($hasValidFile) {
                                for ($i = 0; $i < $fileCount; $i++) {
                                    if (isset($additionalFiles['error'][$i]) && $additionalFiles['error'][$i] === UPLOAD_ERR_OK) {
                                        $file = [
                                            'name' => $additionalFiles['name'][$i],
                                            'type' => $additionalFiles['type'][$i],
                                            'tmp_name' => $additionalFiles['tmp_name'][$i],
                                            'error' => $additionalFiles['error'][$i],
                                            'size' => $additionalFiles['size'][$i]
                                        ];
                                        
                                        $customFileName = $sku . '_' . ($i + 1);
                                        $uploadResult = ImageUploader::uploadWithCustomName($file, 'products', $customFileName, true);
                                        
                                        if ($uploadResult['success']) {
                                            $imageModel->create([
                                                'product_id' => $productId,
                                                'image_url' => $uploadResult['url'],
                                                'is_primary' => 0,
                                                'display_order' => $i + 1
                                            ]);
                                        }
                                    }
                                }
                            }
                        }
                    }
                    
                    // Xử lý lưu attributes
                    if (isset($_POST['attributes']) && is_array($_POST['attributes'])) {
                        $attributeModel = new ProductAttributeModel();
                        $attributes = [];
                        foreach ($_POST['attributes'] as $attr) {
                            $attrName = trim($attr['attribute_name'] ?? '');
                            // Giữ nguyên xuống dòng (\n) trong attribute_value, chỉ trim khoảng trắng ở đầu và cuối
                            $attrValue = preg_replace('/^[\s]+|[\s]+$/u', '', $attr['attribute_value'] ?? '');
                            if (!empty($attrName) && !empty($attrValue)) {
                                $attributes[] = [
                                    'attribute_name' => $attrName,
                                    'attribute_value' => $attrValue
                                ];
                            }
                        }
                        if (!empty($attributes)) {
                            $attributeModel->createMultiple($productId, $attributes);
                        }
                    }
                    
                    return ['success' => true, 'message' => 'Thêm sản phẩm thành công'];
                } catch (Exception $e2) {
                    return ['success' => false, 'message' => 'Lỗi khi tạo sản phẩm: ' . $e2->getMessage()];
                }
            }
            return ['success' => false, 'message' => 'Lỗi database: ' . $e->getMessage()];
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Lỗi: ' . $e->getMessage()];
        }
    }
    
    public function update($id) {
        $name = trim($_POST['name'] ?? '');
        
        $data = [
            'category_id' => intval($_POST['category_id'] ?? 0),
            'name' => $name,
            'slug' => createSlug($name),
            'description' => trim($_POST['description'] ?? ''),
            'price' => floatval($_POST['price'] ?? 0),
            'sale_price' => !empty($_POST['sale_price']) ? floatval($_POST['sale_price']) : null,
            'stock_quantity' => intval($_POST['stock_quantity'] ?? 0),
            'featured' => isset($_POST['featured']) ? 1 : 0,
            'status' => $_POST['status'] ?? 'active',
            'meta_title' => trim($_POST['meta_title'] ?? ''),
            'meta_description' => trim($_POST['meta_description'] ?? '')
        ];
        
        // Validation các trường bắt buộc
        if (empty($data['name'])) {
            return ['success' => false, 'message' => 'Vui lòng nhập tên sản phẩm'];
        }
        if (empty($data['category_id']) || $data['category_id'] <= 0) {
            return ['success' => false, 'message' => 'Vui lòng chọn danh mục'];
        }
        if (empty($data['description'])) {
            return ['success' => false, 'message' => 'Vui lòng nhập mô tả sản phẩm'];
        }
        if (empty($data['price']) || $data['price'] <= 0) {
            return ['success' => false, 'message' => 'Vui lòng nhập giá gốc hợp lệ'];
        }
        if (!isset($data['stock_quantity']) || $data['stock_quantity'] <= 0) {
            return ['success' => false, 'message' => 'Vui lòng nhập số lượng tồn kho lớn hơn 0'];
        }
        if (empty($data['status'])) {
            return ['success' => false, 'message' => 'Vui lòng chọn trạng thái'];
        }
        if (empty($data['meta_title'])) {
            return ['success' => false, 'message' => 'Vui lòng nhập tiêu đề SEO'];
        }
        if (empty($data['meta_description'])) {
            return ['success' => false, 'message' => 'Vui lòng nhập mô tả SEO'];
        }
        
        try {
        if ($this->productModel->update($id, $data)) {
            // Lấy SKU từ sản phẩm hiện tại
            $product = $this->productModel->findById($id);
            if (!$product || empty($product['sku'])) {
                return ['success' => false, 'message' => 'Không tìm thấy SKU sản phẩm. Vui lòng thử lại.'];
            }
            
            $sku = $product['sku'];
            $imageModel = new ProductImageModel();
                
                // Xử lý xóa ảnh hiện có (nếu có)
                if (isset($_POST['delete_images']) && is_array($_POST['delete_images'])) {
                    foreach ($_POST['delete_images'] as $imageId) {
                        $imageId = intval($imageId);
                        if ($imageId > 0) {
                            // Lấy thông tin ảnh trước khi xóa
                            $oldImages = $imageModel->getByProductId($id);
                            foreach ($oldImages as $img) {
                                if ($img['id'] == $imageId) {
                                    // Xóa file ảnh trên server
                                    ImageUploader::delete($img['image_url']);
                                    // Xóa record trong database
                                    $imageModel->delete($imageId);
                                    break;
                                }
                            }
                        }
                    }
                }
            
            // Xử lý ảnh chính mới (nếu có file upload)
            $primaryImageFile = $_FILES['primary_image_file'] ?? null;
            
            if ($primaryImageFile && isset($primaryImageFile['tmp_name']) && !empty($primaryImageFile['tmp_name'])) {
                // Lấy tất cả ảnh chính cũ trước khi upload
                $oldImages = $imageModel->getByProductId($id);
                $oldPrimaryImages = [];
                foreach ($oldImages as $img) {
                    if ($img['is_primary'] == 1 || $img['is_primary'] == '1') {
                        $oldPrimaryImages[] = $img;
                    }
                }
                
                // Upload ảnh chính với tên file = SKU.jpg (sẽ ghi đè ảnh cũ nếu có)
                $uploadResult = ImageUploader::uploadWithCustomName($primaryImageFile, 'products', $sku, true);
                
                if ($uploadResult['success']) {
                    // Xóa tất cả ảnh chính cũ khỏi database
                    foreach ($oldPrimaryImages as $oldImg) {
                        // Xóa file ảnh cũ (nếu URL khác với ảnh mới)
                        if ($oldImg['image_url'] !== $uploadResult['url']) {
                            ImageUploader::delete($oldImg['image_url']);
                        }
                        // Xóa record trong database
                        $imageModel->delete($oldImg['id']);
                    }
                    
                    // Thêm ảnh chính mới vào database
                    $imageCreateResult = $imageModel->create([
                        'product_id' => $id,
                        'image_url' => $uploadResult['url'],
                        'is_primary' => 1,
                        'display_order' => 0
                    ]);
                    
                    // Kiểm tra nếu không tạo được record trong database, xóa file đã upload
                    if (!$imageCreateResult) {
                        ImageUploader::delete($uploadResult['url']);
                        return ['success' => false, 'message' => 'Lỗi khi lưu ảnh vào database. Vui lòng thử lại.'];
                    }
                } else {
                    // Nếu upload thất bại, trả về lỗi
                    return ['success' => false, 'message' => $uploadResult['message'] ?? 'Lỗi khi upload ảnh chính'];
                }
            }
            
            // Xử lý ảnh bổ sung mới (nếu có)
            if (isset($_FILES['additional_images']) && is_array($_FILES['additional_images'])) {
                $additionalFiles = $_FILES['additional_images'];
                
                // Kiểm tra xem có file nào được upload không
                if (isset($additionalFiles['name']) && is_array($additionalFiles['name'])) {
                    $fileCount = count($additionalFiles['name']);
                    
                    // Kiểm tra xem có ít nhất một file hợp lệ không
                    $hasValidFile = false;
                    for ($i = 0; $i < $fileCount; $i++) {
                        if (isset($additionalFiles['error'][$i]) && $additionalFiles['error'][$i] === UPLOAD_ERR_OK) {
                            $hasValidFile = true;
                            break;
                        }
                    }
                    
                    if ($hasValidFile) {
                        // Đếm số ảnh phụ hiện có để tiếp tục số thứ tự
                        $existingImages = $imageModel->getByProductId($id);
                        $existingAdditionalCount = 0;
                        foreach ($existingImages as $img) {
                            if ($img['is_primary'] == 0 || $img['is_primary'] == '0') {
                                $existingAdditionalCount++;
                            }
                        }
                        
                        for ($i = 0; $i < $fileCount; $i++) {
                            if (isset($additionalFiles['error'][$i]) && $additionalFiles['error'][$i] === UPLOAD_ERR_OK) {
                                $file = [
                                    'name' => $additionalFiles['name'][$i],
                                    'type' => $additionalFiles['type'][$i],
                                    'tmp_name' => $additionalFiles['tmp_name'][$i],
                                    'error' => $additionalFiles['error'][$i],
                                    'size' => $additionalFiles['size'][$i]
                                ];
                                
                                // Tên file = SKU_{số thứ tự tiếp theo}.jpg
                                $imageNumber = $existingAdditionalCount + $i + 1;
                                $customFileName = $sku . '_' . $imageNumber;
                                $uploadResult = ImageUploader::uploadWithCustomName($file, 'products', $customFileName, true);
                                
                                if ($uploadResult['success']) {
                                    $imageCreateResult = $imageModel->create([
                                        'product_id' => $id,
                                        'image_url' => $uploadResult['url'],
                                        'is_primary' => 0,
                                        'display_order' => $existingAdditionalCount + $i + 1
                                    ]);
                                    
                                    // Nếu không tạo được record, xóa file đã upload
                                    if (!$imageCreateResult) {
                                        ImageUploader::delete($uploadResult['url']);
                                    }
                                }
                            }
                        }
                    }
                }
            }
            
            // Xử lý lưu attributes
            if (isset($_POST['attributes']) && is_array($_POST['attributes'])) {
                $attributeModel = new ProductAttributeModel();
                $attributes = [];
                foreach ($_POST['attributes'] as $attr) {
                    $attrName = trim($attr['attribute_name'] ?? '');
                    // Giữ nguyên xuống dòng (\n) trong attribute_value, chỉ trim khoảng trắng ở đầu và cuối
                    $attrValue = preg_replace('/^[\s]+|[\s]+$/u', '', $attr['attribute_value'] ?? '');
                    if (!empty($attrName) && !empty($attrValue)) {
                        $attributes[] = [
                            'attribute_name' => $attrName,
                            'attribute_value' => $attrValue
                        ];
                    }
                }
                // Luôn cập nhật attributes (có thể là mảng rỗng để xóa hết)
                $attributeModel->createMultiple($id, $attributes);
            }
            
                return ['success' => true, 'message' => 'Sửa sản phẩm thành công'];
            }
            
            return ['success' => false, 'message' => 'Có lỗi xảy ra khi cập nhật sản phẩm'];
        } catch (mysqli_sql_exception $e) {
            // Xử lý lỗi duplicate slug
            if (strpos($e->getMessage(), 'Duplicate entry') !== false && strpos($e->getMessage(), 'slug') !== false) {
                // Thử tạo slug unique lại và update lại
                $data['slug'] = createUniqueSlug($name, $this->productModel, $id);
                try {
                    if ($this->productModel->update($id, $data)) {
                        // Lấy SKU từ sản phẩm hiện tại
                        $product = $this->productModel->findById($id);
                        if (!$product || empty($product['sku'])) {
                            return ['success' => false, 'message' => 'Không tìm thấy SKU sản phẩm. Vui lòng thử lại.'];
                        }
                        
                        $sku = $product['sku'];
                        $imageModel = new ProductImageModel();
                        
                        // Xử lý xóa ảnh hiện có (nếu có)
                        if (isset($_POST['delete_images']) && is_array($_POST['delete_images'])) {
                            foreach ($_POST['delete_images'] as $imageId) {
                                $imageId = intval($imageId);
                                if ($imageId > 0) {
                                    $oldImages = $imageModel->getByProductId($id);
                                    foreach ($oldImages as $img) {
                                        if ($img['id'] == $imageId) {
                                            ImageUploader::delete($img['image_url']);
                                            $imageModel->delete($imageId);
                                            break;
                                        }
                                    }
                                }
                            }
                        }
                        
                        // Xử lý ảnh chính mới (nếu có file upload)
                        $primaryImageFile = $_FILES['primary_image_file'] ?? null;
                        
                        if ($primaryImageFile && isset($primaryImageFile['tmp_name']) && !empty($primaryImageFile['tmp_name'])) {
                            // Lấy tất cả ảnh chính cũ trước khi upload
                            $oldImages = $imageModel->getByProductId($id);
                            $oldPrimaryImages = [];
                            foreach ($oldImages as $img) {
                                if ($img['is_primary'] == 1 || $img['is_primary'] == '1') {
                                    $oldPrimaryImages[] = $img;
                                }
                            }
                            
                            // Upload ảnh chính với tên file = SKU.jpg
                            $uploadResult = ImageUploader::uploadWithCustomName($primaryImageFile, 'products', $sku, true);
                            
                            if ($uploadResult['success']) {
                                // Xóa tất cả ảnh chính cũ khỏi database
                                foreach ($oldPrimaryImages as $oldImg) {
                                    // Xóa file ảnh cũ (nếu URL khác với ảnh mới)
                                    if ($oldImg['image_url'] !== $uploadResult['url']) {
                                        ImageUploader::delete($oldImg['image_url']);
                                    }
                                    // Xóa record trong database
                                    $imageModel->delete($oldImg['id']);
                                }
                                
                                $imageCreateResult = $imageModel->create([
                                    'product_id' => $id,
                                    'image_url' => $uploadResult['url'],
                                    'is_primary' => 1,
                                    'display_order' => 0
                                ]);
                                
                                // Kiểm tra nếu không tạo được record trong database, xóa file đã upload
                                if (!$imageCreateResult) {
                                    ImageUploader::delete($uploadResult['url']);
                                    return ['success' => false, 'message' => 'Lỗi khi lưu ảnh vào database. Vui lòng thử lại.'];
                                }
                            } else {
                                // Nếu upload thất bại, trả về lỗi
                                return ['success' => false, 'message' => $uploadResult['message'] ?? 'Lỗi khi upload ảnh chính'];
                            }
                        }
                        
                        // Xử lý ảnh bổ sung mới (nếu có)
                        if (isset($_FILES['additional_images']) && is_array($_FILES['additional_images'])) {
                            $additionalFiles = $_FILES['additional_images'];
                            
                            if (isset($additionalFiles['name']) && is_array($additionalFiles['name'])) {
                                $fileCount = count($additionalFiles['name']);
                                
                                $hasValidFile = false;
                                for ($i = 0; $i < $fileCount; $i++) {
                                    if (isset($additionalFiles['error'][$i]) && $additionalFiles['error'][$i] === UPLOAD_ERR_OK) {
                                        $hasValidFile = true;
                                        break;
                                    }
                                }
                                
                                if ($hasValidFile) {
                                    // Đếm số ảnh phụ hiện có
                                    $existingImages = $imageModel->getByProductId($id);
                                    $existingAdditionalCount = 0;
                                    foreach ($existingImages as $img) {
                                        if ($img['is_primary'] == 0 || $img['is_primary'] == '0') {
                                            $existingAdditionalCount++;
                                        }
                                    }
                                    
                                    for ($i = 0; $i < $fileCount; $i++) {
                                        if (isset($additionalFiles['error'][$i]) && $additionalFiles['error'][$i] === UPLOAD_ERR_OK) {
                                            $file = [
                                                'name' => $additionalFiles['name'][$i],
                                                'type' => $additionalFiles['type'][$i],
                                                'tmp_name' => $additionalFiles['tmp_name'][$i],
                                                'error' => $additionalFiles['error'][$i],
                                                'size' => $additionalFiles['size'][$i]
                                            ];
                                            
                                            // Tên file = SKU_{số thứ tự tiếp theo}.jpg
                                            $imageNumber = $existingAdditionalCount + $i + 1;
                                            $customFileName = $sku . '_' . $imageNumber;
                                            $uploadResult = ImageUploader::uploadWithCustomName($file, 'products', $customFileName, true);
                                            
                                            if ($uploadResult['success']) {
                                                $imageCreateResult = $imageModel->create([
                                                    'product_id' => $id,
                                                    'image_url' => $uploadResult['url'],
                                                    'is_primary' => 0,
                                                    'display_order' => $existingAdditionalCount + $i + 1
                                                ]);
                                                
                                                // Nếu không tạo được record, xóa file đã upload
                                                if (!$imageCreateResult) {
                                                    ImageUploader::delete($uploadResult['url']);
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                        }
            
            // Xử lý lưu attributes
            if (isset($_POST['attributes']) && is_array($_POST['attributes'])) {
                $attributeModel = new ProductAttributeModel();
                $attributes = [];
                foreach ($_POST['attributes'] as $attr) {
                    $attrName = trim($attr['attribute_name'] ?? '');
                    // Giữ nguyên xuống dòng (\n) trong attribute_value, chỉ trim khoảng trắng ở đầu và cuối
                    $attrValue = preg_replace('/^[\s]+|[\s]+$/u', '', $attr['attribute_value'] ?? '');
                    if (!empty($attrName) && !empty($attrValue)) {
                        $attributes[] = [
                            'attribute_name' => $attrName,
                            'attribute_value' => $attrValue
                        ];
                    }
                }
                // Luôn cập nhật attributes (có thể là mảng rỗng để xóa hết)
                $attributeModel->createMultiple($id, $attributes);
            }
                        
                        return ['success' => true, 'message' => 'Sửa sản phẩm thành công'];
        }
                } catch (Exception $e2) {
                    return ['success' => false, 'message' => 'Lỗi khi cập nhật sản phẩm: ' . $e2->getMessage()];
                }
            }
            return ['success' => false, 'message' => 'Lỗi database: ' . $e->getMessage()];
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Lỗi: ' . $e->getMessage()];
        }
    }
    
    public function destroy($id) {
        try {
        if ($this->productModel->delete($id)) {
            return ['success' => true, 'message' => 'Xóa sản phẩm thành công'];
        }
            return ['success' => false, 'message' => 'Không thể xóa sản phẩm. Vui lòng thử lại.'];
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Lỗi khi xóa sản phẩm: ' . $e->getMessage()];
        }
    }
}

