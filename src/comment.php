<?php
// 留言相關功能

function create_comment($post_id, $user_id, $content) {
    global $pdo;
    
    if (empty($content)) {
        return '留言內容不能為空';
    }
    
    $stmt = $pdo->prepare("INSERT INTO Comment (post_id, user_id, content) VALUES (?, ?, ?)");
    try {
        $stmt->execute([$post_id, $user_id, $content]);
        return true;
    } catch (PDOException $e) {
        return '留言失敗，請稍後再試';
    }
}

function get_comments_by_post($post_id) {
    global $pdo;
    
    $stmt = $pdo->prepare("SELECT c.*, u.name as user_name 
                          FROM Comment c 
                          JOIN User u ON c.user_id = u.user_id 
                          WHERE c.post_id = ? 
                          ORDER BY c.created_at ASC");
    $stmt->execute([$post_id]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function delete_comment($comment_id, $user_id) {
    global $pdo;
    
    // 檢查留言是否屬於該用戶
    $stmt = $pdo->prepare("SELECT user_id FROM Comment WHERE comment_id = ?");
    $stmt->execute([$comment_id]);
    $comment = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$comment || $comment['user_id'] != $user_id) {
        return '無權限刪除此留言';
    }
    
    $stmt = $pdo->prepare("DELETE FROM Comment WHERE comment_id = ?");
    try {
        $stmt->execute([$comment_id]);
        return true;
    } catch (PDOException $e) {
        return '刪除失敗，請稍後再試';
    }
}

function update_comment($comment_id, $user_id, $content) {
    global $pdo;
    
    if (empty($content)) {
        return '留言內容不能為空';
    }
    
    // 檢查留言是否屬於該用戶
    $stmt = $pdo->prepare("SELECT user_id FROM Comment WHERE comment_id = ?");
    $stmt->execute([$comment_id]);
    $comment = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$comment || $comment['user_id'] != $user_id) {
        return '無權限編輯此留言';
    }
    
    $stmt = $pdo->prepare("UPDATE Comment SET content = ? WHERE comment_id = ?");
    try {
        $stmt->execute([$content, $comment_id]);
        return true;
    } catch (PDOException $e) {
        return '更新失敗，請稍後再試';
    }
}

function get_comment_count_by_post($post_id) {
    global $pdo;
    
    $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM Comment WHERE post_id = ?");
    $stmt->execute([$post_id]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    return $result['count'] ?? 0;
}
