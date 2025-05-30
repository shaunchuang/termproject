<?php
session_start();
if (empty($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

require_once __DIR__ . '/../src/db.php';
require_once __DIR__ . '/../src/skill_post.php';
require_once __DIR__ . '/../src/comment.php';

$search = $_GET['search'] ?? '';
$page = max(1, intval($_GET['page'] ?? 1));
$limit = 12;
$offset = ($page - 1) * $limit;

$posts = get_all_posts($search, $limit, $offset);
?>
<!DOCTYPE html>
<html lang="zh-Hant">
<head>
    <meta charset="UTF-8">
    <title>瀏覽技能服務 - SkillSwap</title>
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
                    <h1>技能服務瀏覽</h1>
                    <a href="create_post.php" class="btn btn-success">
                        <i class="bi bi-plus-circle"></i> 發布服務
                    </a>
                </div>

                <!-- 搜尋表單 -->
                <div class="card mb-4">
                    <div class="card-body">
                        <form method="GET" action="posts.php">
                            <div class="row">
                                <div class="col-md-10">
                                    <input type="text" class="form-control" name="search" 
                                           placeholder="搜尋技能服務..." value="<?= htmlspecialchars($search) ?>">
                                </div>
                                <div class="col-md-2">
                                    <button type="submit" class="btn btn-primary w-100">
                                        <i class="bi bi-search"></i> 搜尋
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- 技能服務列表 -->
                <div class="row">
                    <?php if (empty($posts)): ?>
                        <div class="col-12">
                            <div class="alert alert-info text-center">
                                <i class="bi bi-info-circle"></i> 目前沒有符合條件的技能服務
                            </div>
                        </div>
                    <?php else: ?>
                        <?php foreach ($posts as $post): ?>
                            <div class="col-md-6 col-lg-4 mb-4">
                                <div class="card h-100 post-card shadow-sm">
                                    <div class="card-body">
                                        <h5 class="card-title"><?= htmlspecialchars($post['title']) ?></h5>
                                        <p class="card-text"><?= htmlspecialchars(mb_substr($post['description'], 0, 100)) ?>...</p>
                                        <div class="d-flex justify-content-between align-items-center">
                                            <small class="text-muted">
                                                <i class="bi bi-person"></i> <?= htmlspecialchars($post['author_name']) ?>
                                            </small>
                                            <span class="badge bg-primary">
                                                <i class="bi bi-coin"></i> <?= $post['point_cost'] ?> 點
                                            </span>
                                        </div>
                                        <div class="mt-2 d-flex justify-content-between">
                                            <small class="text-muted">
                                                <i class="bi bi-calendar"></i> <?= date('Y-m-d H:i', strtotime($post['created_at'])) ?>
                                            </small>
                                            <small class="text-muted">
                                                <i class="bi bi-chat-dots"></i> <?= get_comment_count_by_post($post['post_id']) ?> 則留言
                                            </small>
                                        </div>
                                    </div>
                                    <div class="card-footer bg-transparent">
                                        <a href="post_detail.php?id=<?= $post['post_id'] ?>" class="btn btn-outline-primary w-100">
                                            查看詳情
                                        </a>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>

                <!-- 分頁 -->
                <?php if (count($posts) == $limit): ?>
                    <nav>
                        <ul class="pagination justify-content-center">
                            <?php if ($page > 1): ?>
                                <li class="page-item">
                                    <a class="page-link" href="?page=<?= $page - 1 ?>&search=<?= urlencode($search) ?>">上一頁</a>
                                </li>
                            <?php endif; ?>
                            <li class="page-item active">
                                <span class="page-link"><?= $page ?></span>
                            </li>
                            <li class="page-item">
                                <a class="page-link" href="?page=<?= $page + 1 ?>&search=<?= urlencode($search) ?>">下一頁</a>
                            </li>
                        </ul>
                    </nav>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
