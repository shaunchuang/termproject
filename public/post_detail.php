<?php
session_start();
if (empty($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

require_once __DIR__ . '/../src/db.php';
require_once __DIR__ . '/../src/skill_post.php';
require_once __DIR__ . '/../src/booking.php';
require_once __DIR__ . '/../src/comment.php';

$post_id = intval($_GET['id'] ?? 0);
if (!$post_id) {
    header('Location: posts.php');
    exit;
}

$post = get_post_by_id($post_id);
if (!$post) {
    header('Location: posts.php');
    exit;
}

$error = '';
$success = '';

// 處理留言
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_comment'])) {
    $content = trim($_POST['content'] ?? '');
    
    if (empty($content)) {
        $error = '留言內容不能為空';
    } else {
        $result = create_comment($post_id, $_SESSION['user_id'], $content);
        if ($result === true) {
            $success = '留言發表成功！';
        } else {
            $error = $result;
        }
    }
}

// 處理留言刪除
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_comment'])) {
    $comment_id = intval($_POST['comment_id'] ?? 0);
    $result = delete_comment($comment_id, $_SESSION['user_id']);
    if ($result === true) {
        $success = '留言刪除成功！';
    } else {
        $error = $result;
    }
}

// 處理預約
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['book_service'])) {
    $scheduled_at = $_POST['scheduled_at'] ?? '';
    
    if (empty($scheduled_at)) {
        $error = '請選擇預約時間';
    } else {
        $result = create_booking($post_id, $_SESSION['user_id'], $scheduled_at);
        if ($result === true) {
            $success = '預約成功！請等待服務提供者確認。';
        } else {
            $error = $result;
        }
    }
}

// 獲取留言
$comments = get_comments_by_post($post_id);
?>
<!DOCTYPE html>
<html lang="zh-Hant">
<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($post['title']) ?> - SkillSwap</title>
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
        <div class="row">
            <div class="col-md-8">
                <div class="card shadow">
                    <div class="card-header">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <h3 class="card-title mb-1"><?= htmlspecialchars($post['title']) ?></h3>
                                <div class="text-muted">
                                    <i class="bi bi-person"></i> <?= htmlspecialchars($post['author_name']) ?>
                                    <span class="ms-3">
                                        <i class="bi bi-calendar"></i> <?= date('Y-m-d H:i', strtotime($post['created_at'])) ?>
                                    </span>
                                </div>
                            </div>
                            <span class="badge bg-primary fs-6">
                                <i class="bi bi-coin"></i> <?= $post['point_cost'] ?> 點
                            </span>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="mb-4">
                            <h5>服務描述</h5>
                            <p class="lead"><?= nl2br(htmlspecialchars($post['description'])) ?></p>
                        </div>

                        <?php if ($post['user_id'] != $_SESSION['user_id']): ?>
                            <!-- 預約表單 -->
                            <div class="border-top pt-4">
                                <h5>預約此服務</h5>
                                
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

                                <form method="POST" action="">
                                    <div class="mb-3">
                                        <label for="scheduled_at" class="form-label">預約時間</label>
                                        <input type="datetime-local" class="form-control" id="scheduled_at" 
                                               name="scheduled_at" required min="<?= date('Y-m-d\TH:i') ?>">
                                    </div>
                                    <button type="submit" name="book_service" class="btn btn-success btn-lg">
                                        <i class="bi bi-calendar-check"></i> 立即預約
                                    </button>
                                </form>
                            </div>
                        <?php else: ?>
                            <div class="alert alert-info">
                                <i class="bi bi-info-circle"></i> 這是您發布的服務
                            </div>
                        <?php endif; ?>
                        
                        <!-- 留言區域 -->
                        <div class="border-top pt-4 mt-4">
                            <h5><i class="bi bi-chat-dots"></i> 留言討論 (<?= count($comments) ?>)</h5>
                            
                            <!-- 新增留言表單 -->
                            <form method="POST" action="" class="mb-4">
                                <div class="mb-3">
                                    <label for="content" class="form-label">發表留言</label>
                                    <textarea class="form-control" id="content" name="content" rows="3" 
                                              placeholder="寫下您的想法或問題..." required></textarea>
                                </div>
                                <button type="submit" name="add_comment" class="btn btn-primary">
                                    <i class="bi bi-send"></i> 發表留言
                                </button>
                            </form>
                            
                            <!-- 留言列表 -->
                            <div class="comments-list">
                                <?php if (empty($comments)): ?>
                                    <div class="text-muted text-center py-4">
                                        <i class="bi bi-chat-square-dots fs-2 d-block mb-2"></i>
                                        還沒有留言，成為第一個留言的人吧！
                                    </div>
                                <?php else: ?>
                                    <?php foreach ($comments as $comment): ?>
                                        <div class="card mb-3">
                                            <div class="card-body">
                                                <div class="d-flex justify-content-between align-items-start mb-2">
                                                    <div>
                                                        <strong><?= htmlspecialchars($comment['user_name']) ?></strong>
                                                        <small class="text-muted ms-2">
                                                            <?= date('Y-m-d H:i', strtotime($comment['created_at'])) ?>
                                                        </small>
                                                    </div>
                                                    <?php if ($comment['user_id'] == $_SESSION['user_id']): ?>
                                                        <form method="POST" action="" style="display: inline;">
                                                            <input type="hidden" name="comment_id" value="<?= $comment['comment_id'] ?>">
                                                            <button type="submit" name="delete_comment" 
                                                                    class="btn btn-outline-danger btn-sm"
                                                                    onclick="return confirm('確定要刪除這則留言嗎？')">
                                                                <i class="bi bi-trash"></i>
                                                            </button>
                                                        </form>
                                                    <?php endif; ?>
                                                </div>
                                                <p class="mb-0"><?= nl2br(htmlspecialchars($comment['content'])) ?></p>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card shadow">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="bi bi-info-circle"></i> 服務資訊
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <strong>服務提供者：</strong><br>
                            <?= htmlspecialchars($post['author_name']) ?>
                        </div>
                        <div class="mb-3">
                            <strong>點數需求：</strong><br>
                            <span class="badge bg-primary fs-6">
                                <i class="bi bi-coin"></i> <?= $post['point_cost'] ?> 點
                            </span>
                        </div>
                        <div class="mb-3">
                            <strong>發布時間：</strong><br>
                            <?= date('Y-m-d H:i', strtotime($post['created_at'])) ?>
                        </div>
                        
                        <div class="d-grid gap-2">
                            <a href="posts.php" class="btn btn-outline-secondary">
                                <i class="bi bi-arrow-left"></i> 返回瀏覽
                            </a>
                            <?php if ($post['user_id'] == $_SESSION['user_id']): ?>
                                <a href="edit_post.php?id=<?= $post['post_id'] ?>" class="btn btn-warning">
                                    <i class="bi bi-pencil"></i> 編輯服務
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
