<?php
if (defined('JOTAJOTI_CONFIG_LOADED')) return;
define('JOTAJOTI_CONFIG_LOADED', true);

// Suppress native PHP error output; our shutdown handler shows a friendly page instead
ini_set('display_errors', '0');
error_reporting(E_ALL);

// Register a shutdown handler to catch fatal errors / parse errors nicely
register_shutdown_function(function() {
    $e = error_get_last();
    if ($e && in_array($e['type'], [E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR], true)) {
        if (!headers_sent()) { http_response_code(500); }
        $error_message = $e['message'] . ' in ' . $e['file'] . ' on line ' . $e['line'];
        include __DIR__ . '/../errors/php-error.php';
    }
});

require_once __DIR__ . '/settings.php';  
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/lang.php';

// Session configuration
if (session_status() === PHP_SESSION_NONE) {
    session_set_cookie_params([
        'lifetime' => 0,
        'path'     => '/',
        'httponly' => true,
        'samesite' => 'Lax',
    ]);
    session_start();
}

$settings = loadSettings();

// Process lang/theme switches
processPreOutputActions($settings);

// Define app constants from settings 
define('APP_CALLSIGN', $settings['callsign']  ?? 'OK1KKY');
define('APP_NAME',     $settings['app_name']  ?? 'JOTA-JOTI Logbook');
define('APP_VERSION',  '2.1.0');

// Redirect to setup if not configured 
$current_script = basename($_SERVER['PHP_SELF']);
$bypass_pages   = ['setup.php', 'login.php', 'logout.php'];
if (!isConfigured() && !in_array($current_script, $bypass_pages, true)) {
    header('Location: /setup'); exit;
}

// Define DB constants 
define('DB_HOST', $settings['db_host'] ?? '');
define('DB_USER', $settings['db_user'] ?? '');
define('DB_PASS', $settings['db_pass'] ?? '');
define('DB_NAME', $settings['db_name'] ?? '');
