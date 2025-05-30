<!-- 導航列 -->
<nav class="navbar navbar-expand-lg navbar-dark bg-primary">
    <div class="container">
        <a class="navbar-brand" href="index.php">
            <i class="bi bi-arrow-left-right"></i> SkillSwap
        </a>
        
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav me-auto">
                <li class="nav-item">
                    <a class="nav-link" href="index.php">
                        <i class="bi bi-house"></i> 首頁
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="posts.php">
                        <i class="bi bi-search"></i> 瀏覽服務
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="my_posts.php">
                        <i class="bi bi-file-post"></i> 我的貼文
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="bookings.php">
                        <i class="bi bi-calendar-check"></i> 我的預約
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="points.php">
                        <i class="bi bi-coin"></i> 點數管理
                    </a>
                </li>
            </ul>
            
            <div class="navbar-nav">
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                        <i class="bi bi-person-circle"></i> <?= htmlspecialchars($_SESSION['user_name'] ?? '會員') ?>
                    </a>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="profile.php">
                            <i class="bi bi-person"></i> 個人資料
                        </a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item" href="logout.php">
                            <i class="bi bi-box-arrow-right"></i> 登出
                        </a></li>
                    </ul>
                </li>
            </div>
        </div>
    </div>
</nav>
