<?php
// 預約交換相關功能

function create_booking($post_id, $requester_id, $scheduled_at) {
    global $pdo;
    
    // 檢查服務是否存在並獲取點數需求
    $stmt = $pdo->prepare("SELECT user_id, point_cost FROM SkillPost WHERE post_id = ?");
    $stmt->execute([$post_id]);
    $post = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$post) {
        return '服務不存在';
    }
    
    if ($post['user_id'] == $requester_id) {
        return '不能預約自己的服務';
    }
    
    // 檢查點數餘額
    require_once __DIR__ . '/points.php';
    $current_points = get_user_points($requester_id);
    if ($current_points < $post['point_cost']) {
        return "點數不足，需要 {$post['point_cost']} 點，目前僅有 {$current_points} 點";
    }
    
    // 檢查是否已經預約過
    $stmt = $pdo->prepare("SELECT booking_id FROM ExchangeBooking 
                          WHERE post_id = ? AND requester_id = ? AND status = '待確認'");
    $stmt->execute([$post_id, $requester_id]);
    if ($stmt->fetch()) {
        return '您已經預約過此服務';
    }
    
    $stmt = $pdo->prepare("INSERT INTO ExchangeBooking (post_id, requester_id, scheduled_at) VALUES (?, ?, ?)");
    try {
        $stmt->execute([$post_id, $requester_id, $scheduled_at]);
        return true;
    } catch (PDOException $e) {
        return '預約失敗，請稍後再試';
    }
}

function get_user_bookings($user_id, $type = 'all') {
    global $pdo;
    
    if ($type === 'requested') {
        // 我預約的服務
        $sql = "SELECT b.*, p.title, p.description, p.point_cost, u.name as provider_name
                FROM ExchangeBooking b
                JOIN SkillPost p ON b.post_id = p.post_id
                JOIN User u ON p.user_id = u.user_id
                WHERE b.requester_id = ?
                ORDER BY b.scheduled_at DESC";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$user_id]);
    } elseif ($type === 'received') {
        // 別人預約我的服務
        $sql = "SELECT b.*, p.title, p.description, p.point_cost, u.name as requester_name
                FROM ExchangeBooking b
                JOIN SkillPost p ON b.post_id = p.post_id
                JOIN User u ON b.requester_id = u.user_id
                WHERE p.user_id = ?
                ORDER BY b.scheduled_at DESC";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$user_id]);
    } else {
        // 所有相關預約
        $sql = "SELECT b.*, p.title, p.description, p.point_cost, 
                       provider.name as provider_name, requester.name as requester_name,
                       CASE WHEN p.user_id = ? THEN 'provider' ELSE 'requester' END as role
                FROM ExchangeBooking b
                JOIN SkillPost p ON b.post_id = p.post_id
                JOIN User provider ON p.user_id = provider.user_id
                JOIN User requester ON b.requester_id = requester.user_id
                WHERE p.user_id = ? OR b.requester_id = ?
                ORDER BY b.scheduled_at DESC";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$user_id, $user_id, $user_id]);
    }
    
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function update_booking_status($booking_id, $new_status, $user_id) {
    global $pdo;
    
    // 檢查權限（只有服務提供者可以更新狀態）
    $stmt = $pdo->prepare("SELECT b.*, p.user_id as provider_id, p.point_cost
                          FROM ExchangeBooking b
                          JOIN SkillPost p ON b.post_id = p.post_id
                          WHERE b.booking_id = ?");
    $stmt->execute([$booking_id]);
    $booking = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$booking) {
        return '預約不存在';
    }
    
    if ($booking['provider_id'] != $user_id) {
        return '無權限修改此預約';
    }
    
    if (!in_array($new_status, ['待確認', '已完成', '已取消'])) {
        return '無效的狀態';
    }
    
    try {
        $pdo->beginTransaction();
        
        // 更新預約狀態
        $stmt = $pdo->prepare("UPDATE ExchangeBooking SET status = ? WHERE booking_id = ?");
        $stmt->execute([$new_status, $booking_id]);
        
        // 如果完成交換，處理點數轉移
        if ($new_status === '已完成') {
            // 扣除預約者點數
            $stmt = $pdo->prepare("INSERT INTO PointTransaction (from_user_id, to_user_id, amount, reason) 
                                  VALUES (?, NULL, ?, ?)");
            $reason_deduct = "技能交換支付 - 預約 #{$booking_id}";
            $stmt->execute([$booking['requester_id'], -$booking['point_cost'], $reason_deduct]);
            
            // 給予服務提供者點數
            $stmt = $pdo->prepare("INSERT INTO PointTransaction (from_user_id, to_user_id, amount, reason) 
                                  VALUES (?, ?, ?, ?)");
            $reason_earn = "技能交換收入 - 預約 #{$booking_id}";
            $stmt->execute([$booking['requester_id'], $booking['provider_id'], $booking['point_cost'], $reason_earn]);
        }
        
        // 如果取消預約，檢查是否需要退點（可依業務邏輯調整）
        if ($new_status === '已取消' && $booking['status'] === '待確認') {
            // 預約取消不扣點，這裡可以加入其他邏輯
        }
        
        $pdo->commit();
        return true;
    } catch (PDOException $e) {
        $pdo->rollBack();
        return '更新失敗，請稍後再試';
    }
}

function get_booking_by_id($booking_id) {
    global $pdo;
    
    $stmt = $pdo->prepare("SELECT b.*, p.title, p.description, p.point_cost, p.user_id as provider_id,
                          provider.name as provider_name, requester.name as requester_name
                          FROM ExchangeBooking b
                          JOIN SkillPost p ON b.post_id = p.post_id
                          JOIN User provider ON p.user_id = provider.user_id
                          JOIN User requester ON b.requester_id = requester.user_id
                          WHERE b.booking_id = ?");
    $stmt->execute([$booking_id]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}
