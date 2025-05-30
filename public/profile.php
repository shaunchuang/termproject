<?php
session_start();
if (empty($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

require_once __DIR__ . '/../src/db.php';
require_once __DIR__ . '/../src/points.php';

// 獲取用戶資料
$stmt = $pdo->prepare("SELECT * FROM User WHERE user_id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    header('Location: logout.php');
    exit;
}

$current_points = get_user_points($_SESSION['user_id']);

// 獲取統計資料
$stmt = $pdo->prepare("SELECT COUNT(*) as count FROM SkillPost WHERE user_id = ?");
$stmt->execute([$_SESSION['user_id']]);
$post_count = $stmt->fetch(PDO::FETCH_ASSOC)['count'] ?? 0;

$stmt = $pdo->prepare("SELECT COUNT(*) as count FROM ExchangeBooking b 
                      JOIN SkillPost p ON b.post_id = p.post_id 
                      WHERE p.user_id = ?");
$stmt->execute([$_SESSION['user_id']]);
$received_bookings = $stmt->fetch(PDO::FETCH_ASSOC)['count'] ?? 0;

$stmt = $pdo->prepare("SELECT COUNT(*) as count FROM ExchangeBooking WHERE requester_id = ?");
$stmt->execute([$_SESSION['user_id']]);
$made_bookings = $stmt->fetch(PDO::FETCH_ASSOC)['count'] ?? 0;

$error = '';
$success = '';

// 處理個人資料更新
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_profile'])) {
    $new_name = trim($_POST['name'] ?? '');
    $current_password = $_POST['current_password'] ?? '';
    $new_password = $_POST['new_password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    
    if (empty($new_name)) {
        $error = '暱稱不能為空';
    } elseif (!empty($new_password)) {
        // 如果要更改密碼
        if (empty($current_password)) {
            $error = '請輸入目前密碼';
        } elseif (!password_verify($current_password, $user['password_hash'])) {
            $error = '目前密碼錯誤';
        } elseif (strlen($new_password) < 6) {
            $error = '新密碼長度至少6碼';
        } elseif ($new_password !== $confirm_password) {
            $error = '新密碼確認不一致';
        } else {
            // 更新暱稱和密碼
            $new_hash = password_hash($new_password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("UPDATE User SET name = ?, password_hash = ? WHERE user_id = ?");
            try {
                $stmt->execute([$new_name, $new_hash, $_SESSION['user_id']]);
                $success = '個人資料和密碼已更新';
                $_SESSION['user_name'] = $new_name;
                $user['name'] = $new_name;
            } catch (PDOException $e) {
                $error = '更新失敗，請稍後再試';
            }
        }
    } else {
        // 只更新暱稱
        $stmt = $pdo->prepare("UPDATE User SET name = ? WHERE user_id = ?");
        try {
            $stmt->execute([$new_name, $_SESSION['user_id']]);
            $success = '個人資料已更新';
            $_SESSION['user_name'] = $new_name;
            $user['name'] = $new_name;
        } catch (PDOException $e) {
            $error = '更新失敗，請稍後再試';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="zh-Hant">
<head>
    <meta charset="UTF-8">
    <title>個人資料 - SkillSwap</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <style>
        body { background: #f8f9fa; }
        .stat-card { transition: transform 0.2s; }
        .stat-card:hover { transform: translateY(-2px); }
    </style>
</head>
<body>
    <!-- 導航列 -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container">
            <a class="navbar-brand" href="index.php">SkillSwap</a>
            <div class="navbar-nav ms-auto">
                <span class="navbar-text me-3">歡迎，<?= htmlspecialchars($_SESSION['user_name']) ?>！</span>
                <a class="btn btn-outline-light btn-sm" href="logout.php">登出</a>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <div class="row">
            <div class="col-12">
                <h1 class="mb-4">個人資料設定</h1>

                <?php if (!empty($error)): ?>
                    <div class="alert alert-danger">
                        <i class="bi bi-exclamation-triangle"></i> <?= htmlspecialchars($error) ?>
                    </div>
                <?php endif; ?>
                
                <?php if (!empty($success)): ?>
                    <div class="alert alert-success">
                        <i class="bi bi-check-circle"></i> <?= htmlspecialchars($success) ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- 個人統計 -->
        <div class="row mb-4">
            <div class="col-md-3 col-sm-6 mb-3">
                <div class="card text-center stat-card shadow-sm">
                    <div class="card-body">
                        <i class="bi bi-coin text-warning fs-2"></i>
                        <h4 class="mt-2"><?= $current_points ?></h4>
                        <p class="text-muted mb-0">目前點數</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-sm-6 mb-3">
                <div class="card text-center stat-card shadow-sm">
                    <div class="card-body">
                        <i class="bi bi-file-text text-primary fs-2"></i>
                        <h4 class="mt-2"><?= $post_count ?></h4>
                        <p class="text-muted mb-0">發布服務</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-sm-6 mb-3">
                <div class="card text-center stat-card shadow-sm">
                    <div class="card-body">
                        <i class="bi bi-calendar-check text-success fs-2"></i>
                        <h4 class="mt-2"><?= $received_bookings ?></h4>
                        <p class="text-muted mb-0">收到預約</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-sm-6 mb-3">
                <div class="card text-center stat-card shadow-sm">
                    <div class="card-body">
                        <i class="bi bi-calendar-plus text-info fs-2"></i>
                        <h4 class="mt-2"><?= $made_bookings ?></h4>
                        <p class="text-muted mb-0">我的預約</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- 個人資料編輯 -->
            <div class="col-md-8">
                <div class="card shadow">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="bi bi-person-gear"></i> 編輯個人資料
                        </h5>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="">
                            <div class="mb-3">
                                <label for="email" class="form-label">電子郵件</label>
                                <input type="email" class="form-control" id="email" 
                                       value="<?= htmlspecialchars($user['email']) ?>" disabled>
                                <div class="form-text">電子郵件無法修改</div>
                            </div>

                            <div class="mb-3">
                                <label for="name" class="form-label">暱稱</label>
                                <input type="text" class="form-control" id="name" name="name" 
                                       value="<?= htmlspecialchars($user['name']) ?>" required maxlength="100">
                            </div>

                            <hr>
                            <h6 class="mb-3">變更密碼（選填）</h6>

                            <div class="mb-3">
                                <label for="current_password" class="form-label">目前密碼</label>
                                <input type="password" class="form-control" id="current_password" name="current_password">
                                <div class="form-text">如不變更密碼請留空</div>
                            </div>

                            <div class="mb-3">
                                <label for="new_password" class="form-label">新密碼</label>
                                <input type="password" class="form-control" id="new_password" name="new_password" minlength="6">
                                <div class="form-text">至少6碼，如不變更請留空</div>
                            </div>

                            <div class="mb-3">
                                <label for="confirm_password" class="form-label">確認新密碼</label>
                                <input type="password" class="form-control" id="confirm_password" name="confirm_password" minlength="6">
                            </div>

                            <button type="submit" name="update_profile" class="btn btn-primary">
                                <i class="bi bi-check-lg"></i> 更新資料
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- 帳戶資訊 -->
            <div class="col-md-4">
                <div class="card shadow">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="bi bi-info-circle"></i> 帳戶資訊
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <strong>註冊時間：</strong><br>
                            <?= date('Y年m月d日', strtotime($user['created_at'])) ?>
                        </div>
                        <div class="mb-3">
                            <strong>會員ID：</strong><br>
                            #<?= $user['user_id'] ?>
                        </div>
                        <div class="mb-3">
                            <strong>使用天數：</strong><br>
                            <?= ceil((time() - strtotime($user['created_at'])) / 86400) ?> 天
                        </div>
                        
                        <div class="d-grid gap-2">
                            <a href="points.php" class="btn btn-outline-primary">
                                <i class="bi bi-coin"></i> 點數管理
                            </a>
                            <a href="my_posts.php" class="btn btn-outline-success">
                                <i class="bi bi-file-text"></i> 我的貼文
                            </a>
                            <a href="bookings.php" class="btn btn-outline-info">
                                <i class="bi bi-calendar-check"></i> 預約管理
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // 密碼確認驗證
        document.getElementById('confirm_password').addEventListener('input', function() {
            const newPassword = document.getElementById('new_password').value;
            const confirmPassword = this.value;
            
            if (newPassword && confirmPassword && newPassword !== confirmPassword) {
                this.setCustomValidity('密碼確認不一致');
            } else {
                this.setCustomValidity('');
            }
        });
    </script>
</body>
</html>
