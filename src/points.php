<?php
// 點數管理相關功能

function get_user_points($user_id) {
    global $pdo;
    
    $stmt = $pdo->prepare("SELECT COALESCE(SUM(amount), 0) as total_points 
                          FROM PointTransaction 
                          WHERE to_user_id = ? OR (from_user_id = ? AND to_user_id IS NULL)");
    $stmt->execute([$user_id, $user_id]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    return $result['total_points'] ?? 0;
}

function get_user_transactions($user_id, $limit = 20, $offset = 0) {
    global $pdo;
    
    $stmt = $pdo->prepare("SELECT pt.*, 
                          from_user.name as from_user_name,
                          to_user.name as to_user_name
                          FROM PointTransaction pt
                          LEFT JOIN User from_user ON pt.from_user_id = from_user.user_id
                          LEFT JOIN User to_user ON pt.to_user_id = to_user.user_id
                          WHERE pt.from_user_id = ? OR pt.to_user_id = ?
                          ORDER BY pt.created_at DESC
                          LIMIT ? OFFSET ?");
    $stmt->execute([$user_id, $user_id, $limit, $offset]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function add_points($user_id, $amount, $reason = '系統贈送') {
    global $pdo;
    
    if ($amount <= 0) {
        return '點數必須大於 0';
    }
    
    $stmt = $pdo->prepare("INSERT INTO PointTransaction (from_user_id, to_user_id, amount, reason) 
                          VALUES (?, ?, ?, ?)");
    try {
        $stmt->execute([$user_id, $user_id, $amount, $reason]);
        return true;
    } catch (PDOException $e) {
        return '新增點數失敗';
    }
}

function transfer_points($from_user_id, $to_user_id, $amount, $reason = '點數轉讓') {
    global $pdo;
    
    if ($amount <= 0) {
        return '轉讓點數必須大於 0';
    }
    
    if ($from_user_id == $to_user_id) {
        return '不能轉讓給自己';
    }
    
    // 檢查餘額
    $current_points = get_user_points($from_user_id);
    if ($current_points < $amount) {
        return '點數餘額不足';
    }
    
    try {
        $pdo->beginTransaction();
        
        // 扣除點數
        $stmt = $pdo->prepare("INSERT INTO PointTransaction (from_user_id, to_user_id, amount, reason) 
                              VALUES (?, NULL, ?, ?)");
        $stmt->execute([$from_user_id, -$amount, "轉出給用戶: $reason"]);
        
        // 增加點數
        $stmt = $pdo->prepare("INSERT INTO PointTransaction (from_user_id, to_user_id, amount, reason) 
                              VALUES (?, ?, ?, ?)");
        $stmt->execute([$from_user_id, $to_user_id, $amount, "轉入自: $reason"]);
        
        $pdo->commit();
        return true;
    } catch (PDOException $e) {
        $pdo->rollBack();
        return '轉讓失敗，請稍後再試';
    }
}

function get_user_by_email($email) {
    global $pdo;
    
    $stmt = $pdo->prepare("SELECT user_id, name, email FROM User WHERE email = ?");
    $stmt->execute([$email]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}
