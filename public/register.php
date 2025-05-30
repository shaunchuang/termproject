<?php
session_start();
require_once __DIR__ . '/../src/db.php';
require_once __DIR__ . '/../src/auth.php';

$error = '';
$success = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $password2 = $_POST['password2'] ?? '';
    if ($password !== $password2) {
        $error = '兩次密碼輸入不一致';
    } elseif (strlen($password) < 6) {
        $error = '密碼長度至少6碼';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = '電子郵件格式錯誤';
    } elseif (empty($name)) {
        $error = '請輸入暱稱';
    } else {
        $result = auth_register($name, $email, $password);
        if ($result === true) {
            $success = '註冊成功，請登入';
        } else {
            $error = $result;
        }
    }
}
include __DIR__ . '/../templates/register_form.php';
