<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../config/config.php';

function isLoggedIn() {
    if (empty($_SESSION['user_id'])) {
        return false;
    }

    global $pdo;
    if ($pdo instanceof PDO) {
        $stmt = $pdo->prepare("SELECT id FROM users WHERE id = ?");
        $stmt->execute([$_SESSION['user_id']]);

        if (!$stmt->fetch()) {
            unset($_SESSION['user_id'], $_SESSION['name'], $_SESSION['role']);
            return false;
        }
    }

    return true;
}

function isEditor() {
    return isLoggedIn() && isset($_SESSION['role']) && $_SESSION['role'] === 'editor';
}

function isContributor() {
    return isLoggedIn() && isset($_SESSION['role']) && $_SESSION['role'] === 'contributor';
}

function redirectTo($path) {
    header('Location: ' . BASE_URL . '/' . ltrim($path, '/'));
    exit;
}

function requireLogin() {
    if (!isLoggedIn()) {
        redirectTo('login.php');
    }
}

function requireEditor() {
    requireLogin();
    if (!isEditor()) {
        redirectTo('index.php');
    }
}

function csrfToken() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function csrfField() {
    return '<input type="hidden" name="csrf_token" value="' . htmlspecialchars(csrfToken(), ENT_QUOTES, 'UTF-8') . '">';
}

function verifyCsrfToken() {
    $postedToken = $_POST['csrf_token'] ?? '';
    if (!hash_equals($_SESSION['csrf_token'] ?? '', $postedToken)) {
        http_response_code(403);
        die('Güvenlik doğrulaması başarısız. Lütfen sayfayı yenileyip tekrar deneyin.');
    }
}

function e($value) {
    return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
}
?>
