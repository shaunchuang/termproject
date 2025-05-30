<?php
session_start();
if (empty($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

require_once __DIR__ . '/../src/db.php';
require_once __DIR__ . '/../src/points.php';
require_once __DIR__ . '/../src/auth.php';

$current_points = get_user_points($_SESSION['user_id']);
$transactions = get_user_transactions($_SESSION['user_id'], 20, 0);

$error = '';
$success = '';

// 處理點數轉讓
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['transfer_points'])) {
    $to_email = trim($_POST['to_email'] ?? '');
    $amount = intval($_POST['amount'] ?? 0);
    $reason = trim($_POST['reason'] ?? '點數轉讓');
    
    if (empty($to_email) || $amount <= 0) {
        $error = '請填寫完整的轉讓資訊';
    } else {
        $to_user = get_user_by_email($to_email);
        if (!$to_user) {
            $error = '找不到此電子郵件的用戶';
        } elseif ($to_user['user_id'] == $_SESSION['user_id']) {
            $error = '不能轉讓給自己';
        } else {
            $result = transfer_points($_SESSION['user_id'], $to_user['user_id'], $amount, $reason);
            if ($result === true) {
                $success = "成功轉讓 $amount 點數給 " . htmlspecialchars($to_user['name']);
                // 重新獲取資料
                $current_points = get_user_points($_SESSION['user_id']);
                $transactions = get_user_transactions($_SESSION['user_id'], 20, 0);
            } else {
                $error = $result;
            }
        }
    }
}

// 處理新手贈送（每個用戶只能領取一次）
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['claim_bonus'])) {
    // 檢查是否已經領取過
    $stmt = $pdo->prepare("SELECT tx_id FROM PointTransaction WHERE to_user_id = ? AND reason LIKE '%新手贈送%'");
    $stmt->execute([$_SESSION['user_id']]);
    if ($stmt->fetch()) {
        $error = '您已經領取過新手贈送點數';
    } else {
        $result = add_points($_SESSION['user_id'], 100, '新手贈送');
        if ($result === true) {
            $success = '恭喜！您獲得了 100 點新手贈送點數';
            $current_points = get_user_points($_SESSION['user_id']);
            $transactions = get_user_transactions($_SESSION['user_id'], 20, 0);
        } else {
            $error = $result;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="zh-Hant">
<head>
    <meta charset="UTF-8">
    <title>點數管理 - SkillSwap</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <style>
        body { background: #f8f9fa; }
        .points-display { font-size: 2rem; font-weight: bold; }
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
                <h1 class="mb-4">點數管理中心</h1>

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

        <div class="row mb-4">
            <!-- 點數餘額 -->
            <div class="col-md-4">
                <div class="card shadow text-center">
                    <div class="card-body">
                        <h5 class="card-title">
                            <i class="bi bi-coin text-warning"></i> 目前點數
                        </h5>
                        <div class="points-display text-primary">
                            <?= $current_points ?>
                        </div>
                        <p class="card-text text-muted">點</p>
                    </div>
                </div>
            </div>

            <!-- 新手贈送 -->
            <div class="col-md-4">
                <div class="card shadow">
                    <div class="card-body text-center">
                        <h5 class="card-title">
                            <i class="bi bi-gift text-success"></i> 新手贈送
                        </h5>
                        <p class="card-text">新會員可免費獲得 100 點數！</p>
                        <form method="POST" action="">
                            <button type="submit" name="claim_bonus" class="btn btn-success">
                                <i class="bi bi-gift"></i> 領取贈送
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- 點數轉讓 -->
            <div class="col-md-4">
                <div class="card shadow">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="bi bi-arrow-left-right"></i> 點數轉讓
                        </h5>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="">
                            <div class="mb-3">
                                <label for="to_email" class="form-label">收款人電子郵件</label>
                                <input type="email" class="form-control" id="to_email" name="to_email" required>
                            </div>
                            <div class="mb-3">
                                <label for="amount" class="form-label">轉讓點數</label>
                                <input type="number" class="form-control" id="amount" name="amount" 
                                       min="1" max="<?= $current_points ?>" required>
                            </div>
                            <div class="mb-3">
                                <label for="reason" class="form-label">轉讓原因</label>
                                <input type="text" class="form-control" id="reason" name="reason" 
                                       value="點數轉讓" maxlength="100">
                            </div>
                            <button type="submit" name="transfer_points" class="btn btn-primary w-100">
                                <i class="bi bi-send"></i> 確認轉讓
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- 交易記錄 -->
        <div class="row">
            <div class="col-12">
                <div class="card shadow">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="bi bi-list-ul"></i> 交易記錄
                        </h5>
                    </div>
                    <div class="card-body">
                        <?php if (empty($transactions)): ?>
                            <div class="alert alert-info text-center">
                                <i class="bi bi-info-circle"></i> 目前沒有交易記錄
                            </div>
                        <?php else: ?>
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>時間</th>
                                            <th>類型</th>
                                            <th>對象</th>
                                            <th>點數變動</th>
                                            <th>原因</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($transactions as $tx): ?>
                                            <tr>
                                                <td><?= date('Y-m-d H:i', strtotime($tx['created_at'])) ?></td>
                                                <td>
                                                    <?php if ($tx['to_user_id'] == $_SESSION['user_id'] && $tx['from_user_id'] != $_SESSION['user_id']): ?>
                                                        <span class="badge bg-success">收入</span>
                                                    <?php elseif ($tx['from_user_id'] == $_SESSION['user_id'] && $tx['to_user_id'] == $_SESSION['user_id']): ?>
                                                        <span class="badge bg-info">系統</span>
                                                    <?php else: ?>
                                                        <span class="badge bg-warning">支出</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <?php if ($tx['to_user_id'] == $_SESSION['user_id'] && $tx['from_user_id'] != $_SESSION['user_id']): ?>
                                                        <?= htmlspecialchars($tx['from_user_name'] ?? '系統') ?>
                                                    <?php elseif ($tx['from_user_id'] == $_SESSION['user_id'] && $tx['to_user_id'] == $_SESSION['user_id']): ?>
                                                        系統
                                                    <?php else: ?>
                                                        <?= htmlspecialchars($tx['to_user_name'] ?? '系統') ?>
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <?php if ($tx['to_user_id'] == $_SESSION['user_id'] && $tx['from_user_id'] != $_SESSION['user_id']): ?>
                                                        <span class="text-success">+<?= $tx['amount'] ?></span>
                                                    <?php elseif ($tx['from_user_id'] == $_SESSION['user_id'] && $tx['to_user_id'] == $_SESSION['user_id']): ?>
                                                        <span class="text-info">+<?= $tx['amount'] ?></span>
                                                    <?php else: ?>
                                                        <span class="text-danger"><?= $tx['amount'] ?></span>
                                                    <?php endif; ?>
                                                </td>
                                                <td><?= htmlspecialchars($tx['reason']) ?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
