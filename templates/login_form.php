<!DOCTYPE html>
<html lang="zh-Hant">
<head>
    <meta charset="UTF-8">
    <title>SkillSwap 登入</title>
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
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            padding: 20px 0;
        }
        
        .login-container {
            max-width: 400px;
            margin: 0 auto;
        }
        
        .login-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border: none;
            border-radius: 20px;
            box-shadow: 0 25px 50px rgba(0, 0, 0, 0.15);
            overflow: hidden;
        }
        
        .card-header {
            background: linear-gradient(135deg, #667eea, #764ba2);
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
            border-color: #667eea;
            background: white;
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.1);
        }
        
        .form-floating > label {
            padding: 16px 20px;
            color: #6c757d;
            font-weight: 400;
        }
        
        .btn-login {
            background: linear-gradient(135deg, #667eea, #764ba2);
            border: none;
            border-radius: 12px;
            padding: 16px;
            font-weight: 600;
            font-size: 16px;
            letter-spacing: 0.5px;
            text-transform: uppercase;
            width: 100%;
            transition: all 0.3s ease;
            box-shadow: 0 8px 25px rgba(102, 126, 234, 0.3);
        }
        
        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 12px 35px rgba(102, 126, 234, 0.4);
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
        
        .register-link {
            text-align: center;
            padding: 20px 0;
        }
        
        .register-link a {
            color: #667eea;
            text-decoration: none;
            font-weight: 500;
            transition: color 0.3s ease;
        }
        
        .register-link a:hover {
            color: #764ba2;
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
        
        @media (max-width: 480px) {
            .login-container {
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
        <div class="login-container">
            <div class="card login-card">
                <div class="card-header">
                    <div class="brand-icon">
                        <i class="fas fa-exchange-alt"></i>
                    </div>
                    <h1 class="card-title">歡迎回來</h1>
                    <p class="card-subtitle">登入您的 SkillSwap 帳號</p>
                </div>
                <div class="card-body">
                    <?php if (!empty($error)): ?>
                        <div class="alert alert-danger" role="alert">
                            <i class="fas fa-exclamation-circle me-2"></i>
                            <?= htmlspecialchars($error) ?>
                        </div>
                    <?php endif; ?>
                    
                    <form method="post" action="login.php">
                        <div class="form-floating">
                            <input type="email" class="form-control" id="email" name="email" placeholder="your@email.com" required autofocus>
                            <label for="email"><i class="fas fa-envelope me-2"></i>電子郵件</label>
                        </div>
                        <div class="form-floating">
                            <input type="password" class="form-control" id="password" name="password" placeholder="密碼" required>
                            <label for="password"><i class="fas fa-lock me-2"></i>密碼</label>
                        </div>
                        <button type="submit" class="btn btn-primary btn-login">
                            <i class="fas fa-sign-in-alt me-2"></i>登入
                        </button>
                    </form>
                    
                    <div class="divider">
                        <span>或</span>
                    </div>
                    
                    <div class="register-link">
                        <a href="register.php">
                            <i class="fas fa-user-plus me-2"></i>還沒有帳號？立即註冊
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>