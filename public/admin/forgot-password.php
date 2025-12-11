<?php
require_once '../../config/config.php';
require_once BASE_PATH . '/app/Database/Database.php';
require_once BASE_PATH . '/app/Models/UserModel.php';
require_once BASE_PATH . '/app/Models/AddressModel.php';
require_once BASE_PATH . '/app/Controllers/AuthController.php';
require_once BASE_PATH . '/app/Middleware/AuthMiddleware.php';

AuthMiddleware::guest();

$authController = new AuthController();
$result = $authController->forgotPassword();
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quên mật khẩu - <?php echo APP_NAME; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        body {
            background: linear-gradient(135deg, #ec4899 0%, #f43f5e 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .forgot-card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.2);
            overflow: hidden;
            max-width: 450px;
            width: 100%;
        }
        .forgot-header {
            background: linear-gradient(135deg, #ec4899 0%, #f43f5e 100%);
            color: white;
            padding: 30px;
            text-align: center;
        }
        .forgot-body {
            padding: 40px;
        }
    </style>
</head>
<body>
    <div class="forgot-card">
        <div class="forgot-header">
            <i class="fas fa-key fa-3x mb-3"></i>
            <h3 class="mb-0">Quên mật khẩu</h3>
            <p class="mb-0 mt-2 opacity-75">Nhập số điện thoại và email để xác thực</p>
        </div>
        <div class="forgot-body">
            <?php if ($result): ?>
                <div class="alert alert-<?php echo $result['success'] ? 'success' : 'danger'; ?>">
                    <i class="fas fa-<?php echo $result['success'] ? 'check-circle' : 'exclamation-circle'; ?> me-2"></i>
                    <?php echo htmlspecialchars($result['message']); ?>
                </div>
                <?php if (isset($result['show_reset_form'])): ?>
                    <!-- Hiển thị form đặt mật khẩu mới -->
                    <form method="POST" action="">
                        <input type="hidden" name="verified" value="1">
                        <input type="hidden" name="phone" value="<?php echo htmlspecialchars($_POST['phone'] ?? ''); ?>">
                        <input type="hidden" name="email" value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>">
                        
                        <div class="mb-3">
                            <label class="form-label">
                                <i class="fas fa-lock me-2"></i>Mật khẩu mới
                            </label>
                            <input type="password" name="new_password" class="form-control" required minlength="6" autofocus>
                            <small class="form-text text-muted">Tối thiểu 6 ký tự</small>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">
                                <i class="fas fa-lock me-2"></i>Xác nhận mật khẩu mới
                            </label>
                            <input type="password" name="confirm_new_password" class="form-control" required minlength="6">
                        </div>
                        
                        <button type="submit" class="btn btn-primary w-100 mb-3">
                            <i class="fas fa-check me-2"></i>Đặt lại mật khẩu
                        </button>
                    </form>
                <?php elseif ($result['success'] && isset($result['redirect'])): ?>
                    <script>
                        setTimeout(function() {
                            window.location.href = '<?php echo APP_URL; ?>/admin/login.php?success=reset_success';
                        }, 2000);
                    </script>
                <?php endif; ?>
            <?php endif; ?>
            
            <?php if (!$result || !isset($result['show_reset_form'])): ?>
            <form method="POST" action="">
                <div class="mb-3">
                    <label class="form-label">
                        <i class="fas fa-phone me-2"></i>Số điện thoại
                    </label>
                    <input type="tel" name="phone" class="form-control" required autofocus placeholder="VD: 0123456789" value="<?php echo htmlspecialchars($_POST['phone'] ?? ''); ?>">
                    <small class="form-text text-muted">Số điện thoại từ địa chỉ chính của cửa hàng</small>
                </div>
                
                <div class="mb-3">
                    <label class="form-label">
                        <i class="fas fa-envelope me-2"></i>Email
                    </label>
                    <input type="email" name="email" class="form-control" required placeholder="VD: admin@example.com" value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>">
                    <small class="form-text text-muted">Email từ địa chỉ chính của cửa hàng</small>
                </div>
                
                <button type="submit" class="btn btn-primary w-100 mb-3">
                    <i class="fas fa-check me-2"></i>Xác thực và đặt mật khẩu mới
                </button>
            <?php endif; ?>
                
                <div class="text-center">
                    <a href="<?php echo APP_URL; ?>/admin/login.php" class="text-decoration-none">
                        <i class="fas fa-arrow-left me-1"></i>Quay lại đăng nhập
                    </a>
                </div>
            </form>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

