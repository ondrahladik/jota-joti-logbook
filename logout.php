<?php
define('JOTAJOTI_LOADED', true);
require_once 'assets/inc/settings.php';
require_once 'assets/inc/auth.php';
require_once 'assets/inc/lang.php';

if (session_status() === PHP_SESSION_NONE) {
    session_set_cookie_params(['lifetime' => 0, 'path' => '/', 'httponly' => true, 'samesite' => 'Lax']);
    session_start();
}

// Capture lang before destroying session
$settings = loadSettings();
processPreOutputActions($settings);
$logoutMsg = __('login.logged_out');

session_regenerate_id(true);
$_SESSION = [];
session_destroy();

session_start();
$_SESSION['flash_toast'] = ['type' => 'info', 'msg' => $logoutMsg];

header('Location: /'); exit;