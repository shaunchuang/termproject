<?php
session_start();
if (empty($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

require_once __DIR__ . '/../src/db.php';
require_once __DIR__ . '/../src/booking.php';

$tab = $_GET['tab'] ?? 'all';
$bookings = get_user_bookings($_SESSION['user_id'], $tab);

$error = '';
$success = '';

// 處理狀態更新
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_status'])) {
    $booking_id = intval($_POST['booking_id']);
    $new_status = $_POST['new_status'];
    
    $result = update_booking_status($booking_id, $new_status, $_SESSION['user_id']);
    if ($result === true) {
        $success = '預約狀態已更新';
        // 重新獲取資料
        $bookings = get_user_bookings($_SESSION['user_id'], $tab);
    } else {
        $error = $result;
    }
}
?>
<!DOCTYPE html>
<html lang="zh-Hant">
<head>
    <meta charset="UTF-8">
    <title>我的預約 - SkillSwap</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <style>
        body { background: #f8f9fa; }
        .status-badge {
            font-size: 0.8em;
        }
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
                <h1 class="mb-4">我的預約管理</h1>

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

                <!-- 分頁標籤 -->
                <ul class="nav nav-tabs mb-4">
                    <li class="nav-item">
                        <a class="nav-link <?= $tab === 'all' ? 'active' : '' ?>" href="?tab=all">
                            <i class="bi bi-list-ul"></i> 全部預約
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= $tab === 'requested' ? 'active' : '' ?>" href="?tab=requested">
                            <i class="bi bi-calendar-plus"></i> 我的預約
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= $tab === 'received' ? 'active' : '' ?>" href="?tab=received">
                            <i class="bi bi-calendar-event"></i> 接收預約
                        </a>
                    </li>
                </ul>

                <!-- 預約列表 -->
                <?php if (empty($bookings)): ?>
                    <div class="alert alert-info text-center">
                        <i class="bi bi-info-circle"></i> 目前沒有相關的預約記錄
                    </div>
                <?php else: ?>
                    <div class="row">
                        <?php foreach ($bookings as $booking): ?>
                            <div class="col-md-6 col-lg-4 mb-4">
                                <div class="card h-100 shadow-sm">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between align-items-start mb-2">
                                            <h6 class="card-title"><?= htmlspecialchars($booking['title']) ?></h6>
                                            <?php
                                            $badge_class = match($booking['status']) {
                                                '待確認' => 'bg-warning',
                                                '已完成' => 'bg-success',
                                                '已取消' => 'bg-danger',
                                                default => 'bg-secondary'
                                            };
                                            ?>
                                            <span class="badge <?= $badge_class ?> status-badge">
                                                <?= $booking['status'] ?>
                                            </span>
                                        </div>

                                        <p class="card-text small"><?= htmlspecialchars(mb_substr($booking['description'], 0, 80)) ?>...</p>

                                        <div class="mb-2">
                                            <small class="text-muted">
                                                <i class="bi bi-calendar"></i> 
                                                <?= date('Y-m-d H:i', strtotime($booking['scheduled_at'])) ?>
                                            </small>
                                        </div>

                                        <div class="mb-2">
                                            <small class="text-muted">
                                                <i class="bi bi-coin"></i> <?= $booking['point_cost'] ?> 點
                                            </small>
                                        </div>

                                        <?php if (isset($booking['role'])): ?>
                                            <div class="mb-3">
                                                <small class="text-muted">
                                                    <?php if ($booking['role'] === 'provider'): ?>
                                                        <i class="bi bi-person"></i> 預約者：<?= htmlspecialchars($booking['requester_name']) ?>
                                                    <?php else: ?>
                                                        <i class="bi bi-person"></i> 提供者：<?= htmlspecialchars($booking['provider_name']) ?>
                                                    <?php endif; ?>
                                                </small>
                                            </div>
                                        <?php else: ?>
                                            <div class="mb-3">
                                                <small class="text-muted">
                                                    <?php if ($tab === 'received'): ?>
                                                        <i class="bi bi-person"></i> 預約者：<?= htmlspecialchars($booking['requester_name']) ?>
                                                    <?php else: ?>
                                                        <i class="bi bi-person"></i> 提供者：<?= htmlspecialchars($booking['provider_name']) ?>
                                                    <?php endif; ?>
                                                </small>
                                            </div>
                                        <?php endif; ?>

                                        <!-- 操作按鈕 -->
                                        <?php if ($booking['status'] === '待確認'): ?>
                                            <?php if ($tab === 'received' || (isset($booking['role']) && $booking['role'] === 'provider')): ?>
                                                <div class="d-grid gap-1">
                                                    <form method="POST" action="" class="d-inline">
                                                        <input type="hidden" name="booking_id" value="<?= $booking['booking_id'] ?>">
                                                        <input type="hidden" name="new_status" value="已完成">
                                                        <button type="submit" name="update_status" class="btn btn-success btn-sm w-100">
                                                            <i class="bi bi-check-lg"></i> 完成交換
                                                        </button>
                                                    </form>
                                                    <form method="POST" action="" class="d-inline">
                                                        <input type="hidden" name="booking_id" value="<?= $booking['booking_id'] ?>">
                                                        <input type="hidden" name="new_status" value="已取消">
                                                        <button type="submit" name="update_status" class="btn btn-outline-danger btn-sm w-100">
                                                            <i class="bi bi-x-lg"></i> 取消預約
                                                        </button>
                                                    </form>
                                                </div>
                                            <?php else: ?>
                                                <small class="text-muted">等待服務提供者確認</small>
                                            <?php endif; ?>
                                        <?php endif; ?>
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
