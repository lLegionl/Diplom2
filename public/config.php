<?php
// Защита от сессионной фиксации
ini_set('session.use_strict_mode', 1);
ini_set('session.cookie_httponly', 1);
ini_set('session.cookie_secure', 0); // Только для HTTPS
ini_set('session.cookie_samesite', 'Strict');

// Настройки приложения
define('URL_ROOT', 'https://jobfinder/public');

// Инициализация сессии
session_start();

// Проверка авторизации
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

// Редирект если не авторизован
function requireLogin() {
    if (!isLoggedIn()) {
        header('Location: ' . URL_ROOT . '/index.php');
        exit();
    }
}

// Получение информации о текущем пользователе
function currentUser() {
    if (isLoggedIn()) {
        global $pdo;
        $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
        $stmt->execute([$_SESSION['user_id']]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    return null;
}

?>