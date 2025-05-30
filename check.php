<?php
// 系統檢查頁面
?>
<!DOCTYPE html>
<html lang="zh-Hant">
<head>
    <meta charset="UTF-8">
    <title>SkillSwap 系統檢查</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
</head>
<body class="bg-light">
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card shadow">
                    <div class="card-header bg-primary text-white">
                        <h4 class="mb-0"><i class="bi bi-gear"></i> SkillSwap 系統檢查</h4>
                    </div>
                    <div class="card-body">
                        
                        <!-- PHP 版本檢查 -->
                        <div class="mb-4">
                            <h5><i class="bi bi-code-slash"></i> PHP 環境</h5>
                            <div class="row">
                                <div class="col-sm-4">PHP 版本：</div>
                                <div class="col-sm-8">
                                    <?php $php_version = phpversion(); ?>
                                    <span class="badge <?= version_compare($php_version, '7.4', '>=') ? 'bg-success' : 'bg-danger' ?>">
                                        <?= $php_version ?>
                                    </span>
                                    <?= version_compare($php_version, '7.4', '>=') ? '✓' : '✗ (需要 7.4+)' ?>
                                </div>
                            </div>
                        </div>

                        <!-- 資料庫連線檢查 -->
                        <div class="mb-4">
                            <h5><i class="bi bi-database"></i> 資料庫連線</h5>
                            <?php
                            try {
                                require_once __DIR__ . '/src/db.php';
                                echo '<div class="alert alert-success"><i class="bi bi-check-circle"></i> 資料庫連線成功</div>';
                                
                                // 檢查資料表
                                $tables = ['User', 'SkillPost', 'ExchangeBooking', 'PointTransaction', 'Comment'];
                                $missing_tables = [];
                                
                                foreach ($tables as $table) {
                                    $stmt = $pdo->query("SHOW TABLES LIKE '$table'");
                                    if (!$stmt->fetch()) {
                                        $missing_tables[] = $table;
                                    }
                                }
                                
                                if (empty($missing_tables)) {
                                    echo '<div class="alert alert-success"><i class="bi bi-check-circle"></i> 所有資料表都已建立</div>';
                                } else {
                                    echo '<div class="alert alert-warning"><i class="bi bi-exclamation-triangle"></i> 缺少資料表：' . implode(', ', $missing_tables) . '</div>';
                                    echo '<p>請執行 <code>sql/schema.sql</code> 來建立資料表。</p>';
                                }
                                
                            } catch (Exception $e) {
                                echo '<div class="alert alert-danger"><i class="bi bi-x-circle"></i> 資料庫連線失敗：' . htmlspecialchars($e->getMessage()) . '</div>';
                                echo '<p>請檢查 <code>src/db.php</code> 中的資料庫設定。</p>';
                            }
                            ?>
                        </div>

                        <!-- 檔案權限檢查 -->
                        <div class="mb-4">
                            <h5><i class="bi bi-folder-check"></i> 檔案檢查</h5>
                            <?php
                            $required_files = [
                                'src/db.php',
                                'src/auth.php', 
                                'src/skill_post.php',
                                'src/booking.php',
                                'src/points.php',
                                'src/comment.php',
                                'public/index.php',
                                'public/login.php',
                                'public/register.php'
                            ];
                            
                            $missing_files = [];
                            foreach ($required_files as $file) {
                                if (!file_exists(__DIR__ . '/' . $file)) {
                                    $missing_files[] = $file;
                                }
                            }
                            
                            if (empty($missing_files)) {
                                echo '<div class="alert alert-success"><i class="bi bi-check-circle"></i> 所有必要檔案都存在</div>';
                            } else {
                                echo '<div class="alert alert-danger"><i class="bi bi-x-circle"></i> 缺少檔案：</div>';
                                echo '<ul>';
                                foreach ($missing_files as $file) {
                                    echo '<li>' . htmlspecialchars($file) . '</li>';
                                }
                                echo '</ul>';
                            }
                            ?>
                        </div>

                        <div class="text-center">
                            <a href="public/index.php" class="btn btn-primary btn-lg">
                                <i class="bi bi-arrow-right"></i> 前往 SkillSwap
                            </a>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
