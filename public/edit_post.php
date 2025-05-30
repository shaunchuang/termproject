<?php
session_start();
if (empty($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

require_once __DIR__ . '/../src/db.php';
require_once __DIR__ . '/../src/skill_post.php';

$post_id = intval($_GET['id'] ?? 0);
if (!$post_id) {
    header('Location: my_posts.php');
    exit;
}

// 獲取貼文資料
$post = get_post_by_id($post_id);
if (!$post || $post['user_id'] != $_SESSION['user_id']) {
    header('Location: my_posts.php');
    exit;
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $point_cost = intval($_POST['point_cost'] ?? 0);
    
    $result = update_post($post_id, $_SESSION['user_id'], $title, $description, $point_cost);
    if ($result === true) {
        $success = '技能服務已更新！';
        // 重新獲取資料
        $post = get_post_by_id($post_id);
    } else {
        $error = $result;
    }
}
?>
<!DOCTYPE html>
<html lang="zh-Hant">
<head>
    <meta charset="UTF-8">
    <title>編輯技能服務 - SkillSwap</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <style>
        body { background: #f8f9fa; }
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
        <div class="row justify-content-center">
            <div class="col-md-8 col-lg-6">
                <div class="card shadow">
                    <div class="card-header">
                        <h3 class="card-title mb-0">
                            <i class="bi bi-pencil"></i> 編輯技能服務
                        </h3>
                    </div>
                    <div class="card-body">
                        <?php if (!empty($error)): ?>
                            <div class="alert alert-danger" role="alert">
                                <i class="bi bi-exclamation-triangle"></i> <?= htmlspecialchars($error) ?>
                            </div>
                        <?php endif; ?>
                        <?php if (!empty($success)): ?>
                            <div class="alert alert-success" role="alert">
                                <i class="bi bi-check-circle"></i> <?= htmlspecialchars($success) ?>
                            </div>
                        <?php endif; ?>

                        <form method="POST" action="">
                            <div class="mb-3">
                                <label for="title" class="form-label">服務標題 *</label>
                                <input type="text" class="form-control" id="title" name="title" 
                                       value="<?= htmlspecialchars($post['title']) ?>" 
                                       placeholder="例：PHP 網站開發教學" required maxlength="100">
                            </div>

                            <div class="mb-3">
                                <label for="description" class="form-label">服務描述 *</label>
                                <textarea class="form-control" id="description" name="description" rows="6" 
                                          placeholder="詳細說明你的技能服務內容、適合對象、預計時間等..." required><?= htmlspecialchars($post['description']) ?></textarea>
                            </div>

                            <div class="mb-3">
                                <label for="point_cost" class="form-label">點數需求 *</label>
                                <div class="input-group">
                                    <input type="number" class="form-control" id="point_cost" name="point_cost" 
                                           value="<?= $post['point_cost'] ?>" 
                                           min="1" max="1000" placeholder="10" required>
                                    <span class="input-group-text">點</span>
                                </div>
                                <div class="form-text">建議根據服務時間和難度設定合理點數</div>
                            </div>

                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-warning btn-lg">
                                    <i class="bi bi-check-lg"></i> 更新服務
                                </button>
                                <a href="my_posts.php" class="btn btn-outline-secondary">
                                    <i class="bi bi-arrow-left"></i> 返回我的貼文
                                </a>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- 貼文資訊 -->
                <div class="card shadow mt-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="bi bi-info-circle"></i> 貼文資訊
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-sm-6">
                                <strong>發布時間：</strong><br>
                                <?= date('Y-m-d H:i', strtotime($post['created_at'])) ?>
                            </div>
                            <div class="col-sm-6">
                                <?php
                                // 獲取預約數量
                                $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM ExchangeBooking WHERE post_id = ?");
                                $stmt->execute([$post_id]);
                                $booking_count = $stmt->fetch(PDO::FETCH_ASSOC)['count'] ?? 0;
                                ?>
                                <strong>預約數量：</strong><br>
                                <?= $booking_count ?> 個預約
                            </div>
                        </div>
                        
                        <div class="mt-3">
                            <a href="post_detail.php?id=<?= $post_id ?>" class="btn btn-outline-primary">
                                <i class="bi bi-eye"></i> 查看完整頁面
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
