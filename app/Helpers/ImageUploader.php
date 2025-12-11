<?php
/**
 * Image Upload Helper
 * Xử lý upload và lưu trữ hình ảnh
 */

class ImageUploader {
    private static $allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'];
    private static $maxSize = 5 * 1024 * 1024; // 5MB
    
    /**
     * Upload file hình ảnh
     * @param array $file $_FILES['field_name']
     * @param string $folder Thư mục lưu trữ (sliders, products, categories)
     * @return array ['success' => bool, 'message' => string, 'url' => string]
     */
    public static function upload($file, $folder = 'uploads') {
        // Kiểm tra file có tồn tại
        if (!isset($file) || $file['error'] === UPLOAD_ERR_NO_FILE) {
            return ['success' => false, 'message' => 'Không có file được chọn'];
        }
        
        // Kiểm tra lỗi upload
        if ($file['error'] !== UPLOAD_ERR_OK) {
            return ['success' => false, 'message' => 'Lỗi upload file: ' . self::getUploadError($file['error'])];
        }
        
        // Kiểm tra kích thước
        if ($file['size'] > self::$maxSize) {
            return ['success' => false, 'message' => 'File quá lớn. Tối đa 5MB'];
        }
        
        // Kiểm tra loại file
        $mimeType = mime_content_type($file['tmp_name']);
        
        // Fallback nếu mime_content_type không hoạt động
        if (!$mimeType && function_exists('finfo_open')) {
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $mimeType = finfo_file($finfo, $file['tmp_name']);
            finfo_close($finfo);
        }
        
        // Kiểm tra extension nếu không có mime type
        if (!$mimeType) {
            $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
            $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
            if (!in_array($extension, $allowedExtensions)) {
                return ['success' => false, 'message' => 'File không hợp lệ. Chỉ chấp nhận: JPG, PNG, GIF, WEBP'];
            }
        } elseif (!in_array($mimeType, self::$allowedTypes)) {
            return ['success' => false, 'message' => 'File không hợp lệ. Chỉ chấp nhận: JPG, PNG, GIF, WEBP'];
        }
        
        // Tạo tên file unique
        $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
        $fileName = uniqid('img_', true) . '_' . time() . '.' . $extension;
        
        // Tạo thư mục nếu chưa tồn tại
        $uploadDir = BASE_PATH . '/public/assets/images/' . $folder;
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }
        
        // Đường dẫn file đầy đủ
        $filePath = $uploadDir . '/' . $fileName;
        
        // Upload file
        if (!move_uploaded_file($file['tmp_name'], $filePath)) {
            return ['success' => false, 'message' => 'Không thể lưu file'];
        }
        
        // Trả về URL
        $url = '/assets/images/' . $folder . '/' . $fileName;
        
        return [
            'success' => true,
            'message' => 'Upload thành công',
            'url' => $url,
            'path' => $filePath
        ];
    }
    
    /**
     * Upload file hình ảnh với tên file tùy chỉnh
     * @param array $file $_FILES['field_name']
     * @param string $folder Thư mục lưu trữ (sliders, products, categories)
     * @param string $customFileName Tên file tùy chỉnh (không có extension, sẽ tự động thêm extension từ file gốc)
     * @param bool $overwrite Nếu true, sẽ ghi đè file cũ nếu tồn tại
     * @return array ['success' => bool, 'message' => string, 'url' => string]
     */
    public static function uploadWithCustomName($file, $folder = 'uploads', $customFileName = '', $overwrite = true) {
        // Kiểm tra file có tồn tại
        if (!isset($file) || $file['error'] === UPLOAD_ERR_NO_FILE) {
            return ['success' => false, 'message' => 'Không có file được chọn'];
        }
        
        // Kiểm tra lỗi upload
        if ($file['error'] !== UPLOAD_ERR_OK) {
            return ['success' => false, 'message' => 'Lỗi upload file: ' . self::getUploadError($file['error'])];
        }
        
        // Kiểm tra kích thước
        if ($file['size'] > self::$maxSize) {
            return ['success' => false, 'message' => 'File quá lớn. Tối đa 5MB'];
        }
        
        // Kiểm tra loại file
        $mimeType = mime_content_type($file['tmp_name']);
        
        // Fallback nếu mime_content_type không hoạt động
        if (!$mimeType && function_exists('finfo_open')) {
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $mimeType = finfo_file($finfo, $file['tmp_name']);
            finfo_close($finfo);
        }
        
        // Kiểm tra extension nếu không có mime type
        if (!$mimeType) {
            $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
            $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
            if (!in_array($extension, $allowedExtensions)) {
                return ['success' => false, 'message' => 'File không hợp lệ. Chỉ chấp nhận: JPG, PNG, GIF, WEBP'];
            }
        } elseif (!in_array($mimeType, self::$allowedTypes)) {
            return ['success' => false, 'message' => 'File không hợp lệ. Chỉ chấp nhận: JPG, PNG, GIF, WEBP'];
        }
        
        // Lấy extension từ file gốc
        $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        if (empty($extension)) {
            // Nếu không có extension, thử đoán từ mime type
            $mimeToExt = [
                'image/jpeg' => 'jpg',
                'image/jpg' => 'jpg',
                'image/png' => 'png',
                'image/gif' => 'gif',
                'image/webp' => 'webp'
            ];
            $extension = $mimeToExt[$mimeType] ?? 'jpg';
        }
        
        // Tạo tên file: nếu có customFileName thì dùng, không thì dùng tên mặc định
        if (!empty($customFileName)) {
            // Loại bỏ extension nếu có trong customFileName
            $customFileName = preg_replace('/\.(jpg|jpeg|png|gif|webp)$/i', '', $customFileName);
            $fileName = $customFileName . '.' . $extension;
        } else {
            // Fallback về tên file unique như method upload() cũ
            $fileName = uniqid('img_', true) . '_' . time() . '.' . $extension;
        }
        
        // Tạo thư mục nếu chưa tồn tại
        $uploadDir = BASE_PATH . '/public/assets/images/' . $folder;
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }
        
        // Đường dẫn file đầy đủ
        $filePath = $uploadDir . '/' . $fileName;
        
        // Nếu file đã tồn tại và không cho phép ghi đè
        if (file_exists($filePath) && !$overwrite) {
            return ['success' => false, 'message' => 'File đã tồn tại'];
        }
        
        // Xóa file cũ nếu tồn tại và cho phép ghi đè
        if (file_exists($filePath) && $overwrite) {
            unlink($filePath);
        }
        
        // Upload file
        if (!move_uploaded_file($file['tmp_name'], $filePath)) {
            return ['success' => false, 'message' => 'Không thể lưu file'];
        }
        
        // Trả về URL
        $url = '/assets/images/' . $folder . '/' . $fileName;
        
        return [
            'success' => true,
            'message' => 'Upload thành công',
            'url' => $url,
            'path' => $filePath
        ];
    }
    
    /**
     * Xóa file hình ảnh
     * @param string $url URL của hình ảnh
     * @return bool
     */
    public static function delete($url) {
        if (empty($url) || strpos($url, 'http') === 0) {
            // URL ngoài, không xóa
            return true;
        }
        
        $filePath = BASE_PATH . '/public' . $url;
        if (file_exists($filePath)) {
            return unlink($filePath);
        }
        return true;
    }
    
    /**
     * Xử lý image_url: có thể là URL hoặc file upload
     * @param string|null $imageUrl URL hình ảnh từ input
     * @param array|null $fileUpload File upload từ $_FILES
     * @param string $folder Thư mục lưu trữ
     * @return string|null URL hình ảnh cuối cùng
     */
    public static function processImage($imageUrl = null, $fileUpload = null, $folder = 'uploads') {
        // Ưu tiên file upload
        if ($fileUpload && isset($fileUpload['tmp_name']) && !empty($fileUpload['tmp_name'])) {
            $uploadResult = self::upload($fileUpload, $folder);
            if ($uploadResult['success']) {
                return $uploadResult['url'];
            }
        }
        
        // Nếu có URL và không phải URL ngoài
        if (!empty($imageUrl)) {
            // Nếu là URL ngoài (http/https), giữ nguyên
            if (strpos($imageUrl, 'http') === 0) {
                return $imageUrl;
            }
            
            // Nếu là đường dẫn tương đối, kiểm tra file tồn tại
            $filePath = BASE_PATH . '/public' . $imageUrl;
            if (file_exists($filePath)) {
                return $imageUrl;
            }
        }
        
        return null;
    }
    
    /**
     * Lấy thông báo lỗi upload
     */
    private static function getUploadError($errorCode) {
        switch ($errorCode) {
            case UPLOAD_ERR_INI_SIZE:
            case UPLOAD_ERR_FORM_SIZE:
                return 'File quá lớn';
            case UPLOAD_ERR_PARTIAL:
                return 'File chỉ upload một phần';
            case UPLOAD_ERR_NO_TMP_DIR:
                return 'Thiếu thư mục tạm';
            case UPLOAD_ERR_CANT_WRITE:
                return 'Không thể ghi file';
            case UPLOAD_ERR_EXTENSION:
                return 'Upload bị chặn bởi extension';
            default:
                return 'Lỗi không xác định';
        }
    }
}

