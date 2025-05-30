<?php
session_start();
if (empty($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

require_once __DIR__ . '/../src/db.php';
require_once __DIR__ . '/../src/skill_post.php';
require_once __DIR__ . '/../src/comment.php';

$posts = get_user_posts($_SESSION['user_id'], 20, 0);

$error = '';
$success = '';

// 處理刪除貼文
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_post'])) {
    $post_id = intval($_POST['post_id']);
    $result = delete_post($post_id, $_SESSION['user_id']);
    if ($result === true) {
        $success = '貼文已刪除';
        // 重新獲取資料
        $posts = get_user_posts($_SESSION['user_id'], 20, 0);
    } else {
        $error = $result;
    }
}
?>
<!DOCTYPE html>
<html lang="zh-Hant">
<head>
    <meta charset="UTF-8">
    <title>我的貼文 - SkillSwap</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <style>
        body { background: #f8f9fa; }
        .post-card { transition: transform 0.2s; }
        .post-card:hover { transform: translateY(-2px); }
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
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h1>我的技能服務</h1>
                    <a href="create_post.php" class="btn btn-success">
                        <i class="bi bi-plus-circle"></i> 發布新服務
                    </a>
                </div>

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

                <!-- 貼文列表 -->
                <?php if (empty($posts)): ?>
                    <div class="alert alert-info text-center">
                        <i class="bi bi-info-circle"></i> 您還沒有發布任何技能服務
                        <br><br>
                        <a href="create_post.php" class="btn btn-primary">
                            <i class="bi bi-plus-circle"></i> 立即發布
                        </a>
                    </div>
                <?php else: ?>
                    <div class="row">
                        <?php foreach ($posts as $post): ?>
                            <div class="col-md-6 col-lg-4 mb-4">
                                <div class="card h-100 post-card shadow-sm">
                                    <div class="card-body">
                                        <h5 class="card-title"><?= htmlspecialchars($post['title']) ?></h5>
                                        <p class="card-text"><?= htmlspecialchars(mb_substr($post['description'], 0, 100)) ?>...</p>
                                        
                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                            <span class="badge bg-primary">
                                                <i class="bi bi-coin"></i> <?= $post['point_cost'] ?> 點
                                            </span>
                                            <small class="text-muted">
                                                <i class="bi bi-calendar"></i> <?= date('m-d H:i', strtotime($post['created_at'])) ?>
                                            </small>
                                        </div>

                                        <!-- 統計資訊 -->
                                        <?php
                                        // 獲取此貼文的預約數量
                                        $stmt = $pdo->prepare("SELECT COUNT(*) as booking_count FROM ExchangeBooking WHERE post_id = ?");
                                        $stmt->execute([$post['post_id']]);
                                        $booking_info = $stmt->fetch(PDO::FETCH_ASSOC);
                                        $booking_count = $booking_info['booking_count'] ?? 0;
                                        
                                        // 獲取留言數量
                                        $comment_count = get_comment_count_by_post($post['post_id']);
                                        ?>
                                        <div class="mb-3 d-flex justify-content-between">
                                            <small class="text-muted">
                                                <i class="bi bi-calendar-check"></i> <?= $booking_count ?> 個預約
                                            </small>
                                            <small class="text-muted">
                                                <i class="bi bi-chat-dots"></i> <?= $comment_count ?> 則留言
                                            </small>
                                        </div>
                                    </div>
                                    
                                    <div class="card-footer bg-transparent">
                                        <div class="btn-group w-100" role="group">
                                            <a href="post_detail.php?id=<?= $post['post_id'] ?>" 
                                               class="btn btn-outline-primary btn-sm">
                                                <i class="bi bi-eye"></i> 查看
                                            </a>
                                            <a href="edit_post.php?id=<?= $post['post_id'] ?>" 
                                               class="btn btn-outline-warning btn-sm">
                                                <i class="bi bi-pencil"></i> 編輯
                                            </a>
                                            <button type="button" class="btn btn-outline-danger btn-sm" 
                                                    data-bs-toggle="modal" data-bs-target="#deleteModal<?= $post['post_id'] ?>">
                                                <i class="bi bi-trash"></i> 刪除
                                            </button>
                                        </div>
                                    </div>
                                </div>

                                <!-- 刪除確認模態框 -->
                                <div class="modal fade" id="deleteModal<?= $post['post_id'] ?>" tabindex="-1">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title">確認刪除</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>
                                            <div class="modal-body">
                                                確定要刪除「<?= htmlspecialchars($post['title']) ?>」這個服務嗎？<br>
                                                <small class="text-danger">此操作無法復原</small>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">取消</button>
                                                <form method="POST" action="" class="d-inline">
                                                    <input type="hidden" name="post_id" value="<?= $post['post_id'] ?>">
                                                    <button type="submit" name="delete_post" class="btn btn-danger">確認刪除</button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
