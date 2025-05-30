<?php
// 資料庫連線設定
$DB_HOST = 'localhost';
$DB_NAME = 'skillswap';
$DB_USER = 'root';
$DB_PASS = '';

// 建立 PDO 連線
try {
    $pdo = new PDO(
        "mysql:host=$DB_HOST;dbname=$DB_NAME;charset=utf8mb4",
        $DB_USER,
        $DB_PASS,
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );
} catch (PDOException $e) {
    die('資料庫連線失敗: ' . $e->getMessage());
}