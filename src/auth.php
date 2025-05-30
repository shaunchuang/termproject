<?php
function auth_login($email, $password) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT * FROM User WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($user && password_verify($password, $user['password_hash'])) {
        return $user;
    }
    return false;
}

function auth_register($name, $email, $password) {
    global $pdo;
    // 檢查 email 是否已存在
    $stmt = $pdo->prepare("SELECT user_id FROM User WHERE email = ?");
    $stmt->execute([$email]);
    if ($stmt->fetch()) {
        return '此電子郵件已註冊';
    }
    $hash = password_hash($password, PASSWORD_DEFAULT);
    $stmt = $pdo->prepare("INSERT INTO User (name, email, password_hash) VALUES (?, ?, ?)");
    try {
        $stmt->execute([$name, $email, $hash]);
        return true;
    } catch (PDOException $e) {
        return '註冊失敗，請稍後再試';
    }
}

function get_user_by_email($email) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT * FROM User WHERE email = ?");
    $stmt->execute([$email]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

function get_user_by_id($user_id) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT * FROM User WHERE user_id = ?");
    $stmt->execute([$user_id]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}