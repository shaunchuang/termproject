<!DOCTYPE html>
<html lang="zh-Hant">
<head>
    <meta charset="UTF-8">
    <title>SkillSwap 註冊</title>
    <!-- Bootstrap 5 CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * {
            font-family: 'Inter', sans-serif;
        }
        
        body {
            background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            padding: 20px 0;
        }
        
        .register-container {
            max-width: 450px;
            margin: 0 auto;
        }
        
        .register-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border: none;
            border-radius: 20px;
            box-shadow: 0 25px 50px rgba(0, 0, 0, 0.15);
            overflow: hidden;
        }
        
        .card-header {
            background: linear-gradient(135deg, #11998e, #38ef7d);
            color: white;
            text-align: center;
            padding: 40px 30px 30px;
            border: none;
        }
        
        .brand-icon {
            width: 60px;
            height: 60px;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
            font-size: 24px;
        }
        
        .card-title {
            font-weight: 600;
            font-size: 28px;
            margin: 0;
            letter-spacing: -0.5px;
        }
        
        .card-subtitle {
            color: rgba(255, 255, 255, 0.8);
            font-weight: 300;
            margin-top: 8px;
        }
        
        .card-body {
            padding: 40px 30px;
        }
        
        .form-floating {
            position: relative;
            margin-bottom: 20px;
        }
        
        .form-floating > .form-control {
            height: 56px;
            padding: 16px 20px;
            border: 2px solid #e9ecef;
            border-radius: 12px;
            font-size: 16px;
            transition: all 0.3s ease;
            background: #f8f9fa;
        }
        
        .form-floating > .form-control:focus {
            border-color: #11998e;
            background: white;
            box-shadow: 0 0 0 0.2rem rgba(17, 153, 142, 0.1);
        }
        
        .form-floating > label {
            padding: 16px 20px;
            color: #6c757d;
            font-weight: 400;
        }
        
        .btn-register {
            background: linear-gradient(135deg, #11998e, #38ef7d);
            border: none;
            border-radius: 12px;
            padding: 16px;
            font-weight: 600;
            font-size: 16px;
            letter-spacing: 0.5px;
            text-transform: uppercase;
            width: 100%;
            transition: all 0.3s ease;
            box-shadow: 0 8px 25px rgba(17, 153, 142, 0.3);
        }
        
        .btn-register:hover {
            transform: translateY(-2px);
            box-shadow: 0 12px 35px rgba(17, 153, 142, 0.4);
        }
        
        .divider {
            text-align: center;
            margin: 30px 0;
            position: relative;
            color: #6c757d;
        }
        
        .divider::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 0;
            right: 0;
            height: 1px;
            background: #dee2e6;
        }
        
        .divider span {
            background: white;
            padding: 0 15px;
            font-size: 14px;
        }
        
        .login-link {
            text-align: center;
            padding: 20px 0;
        }
        
        .login-link a {
            color: #11998e;
            text-decoration: none;
            font-weight: 500;
            transition: color 0.3s ease;
        }
        
        .login-link a:hover {
            color: #38ef7d;
        }
        
        .alert {
            border: none;
            border-radius: 12px;
            margin-bottom: 20px;
            padding: 16px 20px;
            font-weight: 500;
        }
        
        .alert-danger {
            background: linear-gradient(135deg, #ff6b6b, #ee5a52);
            color: white;
            box-shadow: 0 8px 25px rgba(255, 107, 107, 0.3);
        }
        
        .alert-success {
            background: linear-gradient(135deg, #11998e, #38ef7d);
            color: white;
            box-shadow: 0 8px 25px rgba(17, 153, 142, 0.3);
        }
        
        .password-requirements {
            background: #f8f9fa;
            border-radius: 12px;
            padding: 15px;
            margin: 20px 0;
            font-size: 14px;
            color: #6c757d;
        }
        
        .password-requirements h6 {
            color: #495057;
            font-weight: 600;
            margin-bottom: 10px;
        }
        
        .password-requirements ul {
            margin: 0;
            padding-left: 20px;
        }
        
        .password-requirements li {
            margin-bottom: 5px;
        }
        
        @media (max-width: 480px) {
            .register-container {
                margin: 20px;
            }
            
            .card-header {
                padding: 30px 20px 20px;
            }
            
            .card-body {
                padding: 30px 20px;
            }
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="register-container">
            <div class="card register-card">
                <div class="card-header">
                    <div class="brand-icon">
                        <i class="fas fa-user-plus"></i>
                    </div>
                    <h1 class="card-title">加入我們</h1>
                    <p class="card-subtitle">開始您的技能交換之旅</p>
                </div>
                <div class="card-body">
                    <?php if (!empty($error)): ?>
                        <div class="alert alert-danger" role="alert">
                            <i class="fas fa-exclamation-circle me-2"></i>
                            <?= htmlspecialchars($error) ?>
                        </div>
                    <?php endif; ?>
                    <?php if (!empty($success)): ?>
                        <div class="alert alert-success" role="alert">
                            <i class="fas fa-check-circle me-2"></i>
                            <?= htmlspecialchars($success) ?>
                        </div>
                    <?php endif; ?>
                    
                    <form method="post" action="register.php">
                        <div class="form-floating">
                            <input type="text" class="form-control" id="name" name="name" placeholder="您的暱稱" required>
                            <label for="name"><i class="fas fa-user me-2"></i>暱稱</label>
                        </div>
                        <div class="form-floating">
                            <input type="email" class="form-control" id="email" name="email" placeholder="your@email.com" required>
                            <label for="email"><i class="fas fa-envelope me-2"></i>電子郵件</label>
                        </div>
                        <div class="form-floating">
                            <input type="password" class="form-control" id="password" name="password" placeholder="密碼" required>
                            <label for="password"><i class="fas fa-lock me-2"></i>密碼</label>
                        </div>
                        <div class="form-floating">
                            <input type="password" class="form-control" id="password2" name="password2" placeholder="確認密碼" required>
                            <label for="password2"><i class="fas fa-lock me-2"></i>確認密碼</label>
                        </div>
                        
                        <div class="password-requirements">
                            <h6><i class="fas fa-info-circle me-2"></i>密碼要求</h6>
                            <ul>
                                <li>至少 6 個字元</li>
                                <li>建議包含數字和字母</li>
                                <li>兩次密碼輸入需一致</li>
                            </ul>
                        </div>
                        
                        <button type="submit" class="btn btn-success btn-register">
                            <i class="fas fa-user-plus me-2"></i>立即註冊
                        </button>
                    </form>
                    
                    <div class="divider">
                        <span>或</span>
                    </div>
                    
                    <div class="login-link">
                        <a href="login.php">
                            <i class="fas fa-sign-in-alt me-2"></i>已經有帳號？立即登入
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
