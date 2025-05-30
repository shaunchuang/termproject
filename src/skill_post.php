<?php
// 技能服務貼文相關功能

function get_all_posts($search = '', $limit = 20, $offset = 0) {
    global $pdo;
    
    $sql = "SELECT p.*, u.name as author_name 
            FROM SkillPost p 
            JOIN User u ON p.user_id = u.user_id 
            WHERE p.title LIKE ? OR p.description LIKE ?
            ORDER BY p.created_at DESC 
            LIMIT ? OFFSET ?";
    
    $searchTerm = '%' . $search . '%';
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$searchTerm, $searchTerm, $limit, $offset]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function get_post_by_id($post_id) {
    global $pdo;
    
    $stmt = $pdo->prepare("SELECT p.*, u.name as author_name 
                          FROM SkillPost p 
                          JOIN User u ON p.user_id = u.user_id 
                          WHERE p.post_id = ?");
    $stmt->execute([$post_id]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

function create_post($user_id, $title, $description, $point_cost) {
    global $pdo;
    
    if (empty($title) || empty($description) || $point_cost < 1) {
        return '請填寫完整資訊，點數需求須大於 0';
    }
    
    $stmt = $pdo->prepare("INSERT INTO SkillPost (user_id, title, description, point_cost) VALUES (?, ?, ?, ?)");
    try {
        $stmt->execute([$user_id, $title, $description, $point_cost]);
        return true;
    } catch (PDOException $e) {
        return '發布失敗，請稍後再試';
    }
}

function update_post($post_id, $user_id, $title, $description, $point_cost) {
    global $pdo;
    
    if (empty($title) || empty($description) || $point_cost < 1) {
        return '請填寫完整資訊，點數需求須大於 0';
    }
    
    $stmt = $pdo->prepare("UPDATE SkillPost SET title = ?, description = ?, point_cost = ? 
                          WHERE post_id = ? AND user_id = ?");
    try {
        $stmt->execute([$title, $description, $point_cost, $post_id, $user_id]);
        return $stmt->rowCount() > 0 ? true : '更新失敗或無權限';
    } catch (PDOException $e) {
        return '更新失敗，請稍後再試';
    }
}

function delete_post($post_id, $user_id) {
    global $pdo;
    
    $stmt = $pdo->prepare("DELETE FROM SkillPost WHERE post_id = ? AND user_id = ?");
    try {
        $stmt->execute([$post_id, $user_id]);
        return $stmt->rowCount() > 0 ? true : '刪除失敗或無權限';
    } catch (PDOException $e) {
        return '刪除失敗，請稍後再試';
    }
}

function get_user_posts($user_id, $limit = 20, $offset = 0) {
    global $pdo;
    
    $stmt = $pdo->prepare("SELECT * FROM SkillPost WHERE user_id = ? ORDER BY created_at DESC LIMIT ? OFFSET ?");
    $stmt->execute([$user_id, $limit, $offset]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
