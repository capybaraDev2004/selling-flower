<?php
/**
 * Authentication Controller
 * Xử lý đăng nhập và quên mật khẩu
 */

class AuthController {
    private $userModel;
    private $addressModel;
    
    public function __construct() {
        $this->userModel = new UserModel();
        require_once BASE_PATH . '/app/Models/AddressModel.php';
        $this->addressModel = new AddressModel();
    }
    
    public function login() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = trim($_POST['email'] ?? '');
            $password = $_POST['password'] ?? '';
            $remember = isset($_POST['remember']);
            
            if (empty($email) || empty($password)) {
                return ['success' => false, 'message' => 'Vui lòng nhập đầy đủ thông tin'];
            }
            
            $user = $this->userModel->findByEmail($email);
            
            if (!$user) {
                return ['success' => false, 'message' => 'Email hoặc mật khẩu không đúng'];
            }
            
            if ($user['role'] !== 'admin') {
                return ['success' => false, 'message' => 'Bạn không có quyền truy cập'];
            }
            
            if ($user['status'] !== 'active') {
                return ['success' => false, 'message' => 'Tài khoản của bạn đã bị khóa'];
            }
            
            if (!password_verify($password, $user['password'])) {
                return ['success' => false, 'message' => 'Email hoặc mật khẩu không đúng'];
            }
            
            // Đăng nhập thành công
            $_SESSION['admin_id'] = $user['id'];
            $_SESSION['admin_name'] = $user['full_name'];
            $_SESSION['admin_email'] = $user['email'];
            $_SESSION['admin_role'] = $user['role'];
            
            $this->userModel->updateLastLogin($user['id']);
            
            if ($remember) {
                setcookie('admin_remember', $user['id'], time() + (86400 * 30), '/');
            }
            
            return ['success' => true, 'message' => 'Đăng nhập thành công'];
        }
        
        return null;
    }
    
    public function logout() {
        session_destroy();
        if (isset($_COOKIE['admin_remember'])) {
            setcookie('admin_remember', '', time() - 3600, '/');
        }
        header('Location: ' . APP_URL . '/admin/login.php');
        exit;
    }
    
    public function forgotPassword() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Nếu đã xác thực thành công, cho phép đặt mật khẩu mới
            if (isset($_POST['verified']) && $_POST['verified'] == '1') {
                $phone = trim($_POST['phone'] ?? '');
                $email = trim($_POST['email'] ?? '');
                $newPassword = $_POST['new_password'] ?? '';
                $confirmNewPassword = $_POST['confirm_new_password'] ?? '';
                
                if (empty($newPassword) || empty($confirmNewPassword)) {
                    return ['success' => false, 'message' => 'Vui lòng nhập đầy đủ thông tin', 'show_reset_form' => true];
                }
                
                if ($newPassword !== $confirmNewPassword) {
                    return ['success' => false, 'message' => 'Mật khẩu xác nhận không khớp', 'show_reset_form' => true];
                }
                
                if (strlen($newPassword) < 6) {
                    return ['success' => false, 'message' => 'Mật khẩu phải có ít nhất 6 ký tự', 'show_reset_form' => true];
                }
                
                // Xác thực lại số điện thoại và email để đảm bảo an toàn
                $mainAddress = $this->addressModel->getMain();
                if (!$mainAddress || $mainAddress['phone'] !== $phone || $mainAddress['email'] !== $email) {
                    return ['success' => false, 'message' => 'Thông tin xác thực không hợp lệ'];
                }
                
                // Cập nhật mật khẩu mới
                $this->userModel->updatePassword($email, $newPassword);
                
                return ['success' => true, 'message' => 'Đặt lại mật khẩu thành công. Vui lòng đăng nhập lại.', 'redirect' => true];
            }
            
            // Bước xác thực: kiểm tra số điện thoại và email
            $phone = trim($_POST['phone'] ?? '');
            $email = trim($_POST['email'] ?? '');
            
            if (empty($phone) || empty($email)) {
                return ['success' => false, 'message' => 'Vui lòng nhập đầy đủ số điện thoại và email'];
            }
            
            // Lấy địa chỉ có main = 1
            $mainAddress = $this->addressModel->getMain();
            
            if (!$mainAddress) {
                return ['success' => false, 'message' => 'Không tìm thấy địa chỉ chính. Vui lòng liên hệ quản trị viên.'];
            }
            
            // Kiểm tra số điện thoại có trùng khớp không
            if ($mainAddress['phone'] !== $phone) {
                return ['success' => false, 'message' => 'Số điện thoại không đúng'];
            }
            
            // Kiểm tra email có trùng khớp không
            if ($mainAddress['email'] !== $email) {
                return ['success' => false, 'message' => 'Email không đúng'];
            }
            
            // Kiểm tra user admin có tồn tại không
            $user = $this->userModel->findByEmail($email);
            
            if (!$user || $user['role'] !== 'admin') {
                return ['success' => false, 'message' => 'Không tìm thấy tài khoản admin'];
            }
            
            // Xác thực thành công, hiển thị form đặt mật khẩu mới
            return ['success' => true, 'message' => 'Xác thực thành công. Vui lòng nhập mật khẩu mới.', 'show_reset_form' => true];
        }
        
        return null;
    }
    
    public function resetPassword() {
        $token = $_GET['token'] ?? '';
        
        if (empty($token)) {
            return ['success' => false, 'message' => 'Token không hợp lệ'];
        }
        
        $reset = $this->userModel->findPasswordResetToken($token);
        
        if (!$reset) {
            return ['success' => false, 'message' => 'Token không hợp lệ hoặc đã hết hạn'];
        }
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $password = $_POST['password'] ?? '';
            $confirmPassword = $_POST['confirm_password'] ?? '';
            
            if (empty($password) || empty($confirmPassword)) {
                return ['success' => false, 'message' => 'Vui lòng nhập đầy đủ thông tin'];
            }
            
            if ($password !== $confirmPassword) {
                return ['success' => false, 'message' => 'Mật khẩu xác nhận không khớp'];
            }
            
            if (strlen($password) < 6) {
                return ['success' => false, 'message' => 'Mật khẩu phải có ít nhất 6 ký tự'];
            }
            
            $this->userModel->updatePassword($reset['email'], $password);
            $this->userModel->markTokenAsUsed($token);
            
            return ['success' => true, 'message' => 'Đặt lại mật khẩu thành công. Vui lòng đăng nhập.'];
        }
        
        return ['token' => $token, 'email' => $reset['email']];
    }
}

