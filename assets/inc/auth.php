<?php
function getCurrentRole(): string {
    return $_SESSION['jj_role'] ?? 'guest';
}

function isLoggedIn(): bool {
    return isset($_SESSION['jj_role']) && $_SESSION['jj_role'] !== 'guest';
}

function isAdmin(): bool {
    return getCurrentRole() === 'admin';
}

function canEdit(): bool {
    $r = getCurrentRole();
    return $r === 'user' || $r === 'admin';
}

function requireRole(string $role): void {
    $order   = ['guest' => 0, 'user' => 1, 'admin' => 2];
    $current = getCurrentRole();
    if (($order[$current] ?? 0) < ($order[$role] ?? 0)) {
        header('Location: /login.php?ret=' . urlencode($_SERVER['REQUEST_URI']));
        exit;
    }
}

function tryLogin(string $password, array $settings): ?string {
    if (!empty($settings['admin_pass_hash']) && password_verify($password, $settings['admin_pass_hash'])) {
        return 'admin';
    }
    if (!empty($settings['user_pass_hash']) && password_verify($password, $settings['user_pass_hash'])) {
        return 'user';
    }
    return null;
}

function setFlash(string $type, string $msg): void {
    $_SESSION['flash_toast'] = ['type' => $type, 'msg' => $msg];
}

function csrfToken(): string {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function verifyCsrf(): void {
    $token = $_POST['csrf_token'] ?? '';
    if (!hash_equals($_SESSION['csrf_token'] ?? '', $token)) {
        http_response_code(403);
        die('Invalid CSRF token. Go back and try again.');
    }
}
