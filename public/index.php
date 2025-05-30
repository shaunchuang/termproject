<?php
session_start();
if (empty($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

require_once __DIR__ . '/../src/db.php';
require_once __DIR__ . '/../src/points.php';
require_once __DIR__ . '/../src/skill_post.php';

$user_name = $_SESSION['user_name'] ?? '會員';
$current_points = get_user_points($_SESSION['user_id']);

// 獲取統計資料
$stmt = $pdo->prepare("SELECT COUNT(*) as total_posts FROM SkillPost");
$stmt->execute();
$total_posts = $stmt->fetch(PDO::FETCH_ASSOC)['total_posts'] ?? 0;

$stmt = $pdo->prepare("SELECT COUNT(*) as total_users FROM User");
$stmt->execute();
$total_users = $stmt->fetch(PDO::FETCH_ASSOC)['total_users'] ?? 0;

$stmt = $pdo->prepare("SELECT COUNT(*) as my_posts FROM SkillPost WHERE user_id = ?");
$stmt->execute([$_SESSION['user_id']]);
$my_posts = $stmt->fetch(PDO::FETCH_ASSOC)['my_posts'] ?? 0;

$stmt = $pdo->prepare("SELECT COUNT(*) as my_bookings FROM ExchangeBooking WHERE requester_id = ?");
$stmt->execute([$_SESSION['user_id']]);
$my_bookings = $stmt->fetch(PDO::FETCH_ASSOC)['my_bookings'] ?? 0;

// 獲取最新的技能服務
$latest_posts = get_all_posts('', 6, 0);

?>
<!DOCTYPE html>
<html lang="zh-Hant">
<head>
    <meta charset="UTF-8">
    <title>SkillSwap 首頁</title>
    <!-- Bootstrap 5 CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <style>
        body { background: #f8f9fa; }
        .feature-card { transition: transform 0.2s; }
        .feature-card:hover { transform: translateY(-5px); }
    </style>
</head>
<body>
    <?php include __DIR__ . '/../templates/navbar.php'; ?>

    <div class="container mt-4">
        <!-- 個人統計資料 -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card bg-primary text-white">
                    <div class="card-body">
                        <div class="row text-center">
                            <div class="col-md-3">
                                <h3><?= $current_points ?></h3>
                                <p class="mb-0"><i class="bi bi-coin"></i> 我的點數</p>
                            </div>
                            <div class="col-md-3">
                                <h3><?= $my_posts ?></h3>
                                <p class="mb-0"><i class="bi bi-file-post"></i> 我的貼文</p>
                            </div>
                            <div class="col-md-3">
                                <h3><?= $my_bookings ?></h3>
                                <p class="mb-0"><i class="bi bi-calendar-check"></i> 我的預約</p>
                            </div>
                            <div class="col-md-3">
                                <h3><?= $total_posts ?></h3>
                                <p class="mb-0"><i class="bi bi-graph-up"></i> 總服務數</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="row">
            <div class="col-12">
                <h1 class="text-center mb-4">技能交換平台</h1>
                <p class="text-center text-muted mb-5">分享你的技能，學習新知識，用時間點數創造價值</p>
            </div>
        </div>

        <!-- 功能卡片 -->
        <div class="row g-4">
            <!-- 瀏覽技能服務 -->
            <div class="col-md-6 col-lg-4">
                <div class="card h-100 shadow-sm feature-card">
                    <div class="card-body text-center">
                        <i class="bi bi-search fs-1 text-primary mb-3"></i>
                        <h5 class="card-title">瀏覽技能服務</h5>
                        <p class="card-text">探索社區成員提供的各種技能服務</p>
                        <a href="posts.php" class="btn btn-primary">開始瀏覽</a>
                    </div>
                </div>
            </div>

            <!-- 發布技能服務 -->
            <div class="col-md-6 col-lg-4">
                <div class="card h-100 shadow-sm feature-card">
                    <div class="card-body text-center">
                        <i class="bi bi-plus-circle fs-1 text-success mb-3"></i>
                        <h5 class="card-title">發布技能服務</h5>
                        <p class="card-text">分享你的專長，幫助他人並賺取點數</p>
                        <a href="create_post.php" class="btn btn-success">立即發布</a>
                    </div>
                </div>
            </div>

            <!-- 我的預約 -->
            <div class="col-md-6 col-lg-4">
                <div class="card h-100 shadow-sm feature-card">
                    <div class="card-body text-center">
                        <i class="bi bi-calendar-check fs-1 text-info mb-3"></i>
                        <h5 class="card-title">我的預約</h5>
                        <p class="card-text">管理你的預約記錄和服務安排</p>
                        <a href="bookings.php" class="btn btn-info">查看預約</a>
                    </div>
                </div>
            </div>

            <!-- 點數管理 -->
            <div class="col-md-6 col-lg-4">
                <div class="card h-100 shadow-sm feature-card">
                    <div class="card-body text-center">
                        <i class="bi bi-coin fs-1 text-warning mb-3"></i>
                        <h5 class="card-title">點數管理</h5>
                        <p class="card-text">查看點數餘額和交易記錄</p>
                        <a href="points.php" class="btn btn-warning">點數中心</a>
                    </div>
                </div>
            </div>

            <!-- 我的貼文 -->
            <div class="col-md-6 col-lg-4">
                <div class="card h-100 shadow-sm feature-card">
                    <div class="card-body text-center">
                        <i class="bi bi-file-text fs-1 text-secondary mb-3"></i>
                        <h5 class="card-title">我的貼文</h5>
                        <p class="card-text">管理你發布的技能服務貼文</p>
                        <a href="my_posts.php" class="btn btn-secondary">管理貼文</a>
                    </div>
                </div>
            </div>

            <!-- 個人資料 -->
            <div class="col-md-6 col-lg-4">
                <div class="card h-100 shadow-sm feature-card">
                    <div class="card-body text-center">
                        <i class="bi bi-person-circle fs-1 text-dark mb-3"></i>
                        <h5 class="card-title">個人資料</h5>
                        <p class="card-text">更新你的個人資料和設定</p>
                        <a href="profile.php" class="btn btn-dark">個人設定</a>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- 最新技能服務 -->
        <div class="row mt-5">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h3>最新技能服務</h3>
                    <a href="posts.php" class="btn btn-outline-primary">查看全部</a>
                </div>
                
                <?php if (empty($latest_posts)): ?>
                    <div class="text-center py-5">
                        <i class="bi bi-inbox fs-1 text-muted"></i>
                        <p class="text-muted mt-3">暫無技能服務，<a href="create_post.php">成為第一個發布者</a>！</p>
                    </div>
                <?php else: ?>
                    <div class="row">
                        <?php foreach ($latest_posts as $post): ?>
                            <div class="col-md-6 col-lg-4 mb-4">
                                <div class="card h-100 shadow-sm">
                                    <div class="card-body">
                                        <h6 class="card-title"><?= htmlspecialchars($post['title']) ?></h6>
                                        <p class="card-text text-muted small">
                                            <?= htmlspecialchars(mb_substr($post['description'], 0, 60)) ?>...
                                        </p>
                                        <div class="d-flex justify-content-between align-items-center">
                                            <small class="text-muted">
                                                <i class="bi bi-person"></i> <?= htmlspecialchars($post['author_name']) ?>
                                            </small>
                                            <span class="badge bg-primary">
                                                <i class="bi bi-coin"></i> <?= $post['point_cost'] ?>
                                            </span>
                                        </div>
                                    </div>
                                    <div class="card-footer bg-transparent border-0 pt-0">
                                        <a href="post_detail.php?id=<?= $post['post_id'] ?>" class="btn btn-sm btn-outline-primary w-100">
                                            查看詳情
                                        </a>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>