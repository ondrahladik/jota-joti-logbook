<?php
if (!defined('JOTAJOTI_LOADED')) define('JOTAJOTI_LOADED', true);

function loadSettings(): array {
    $file = __DIR__ . '/../../config/settings.php';
    if (!file_exists($file)) return [];
    return (array) require $file;
}

function saveSettings(array $settings): bool {
    $dir  = __DIR__ . '/../../config';
    $file = $dir . '/settings.php';
    $tmp  = $dir . '/settings.tmp.' . getmypid() . '.php';

    $content = "<?php\nif (!defined('JOTAJOTI_LOADED')) die('Direct access not allowed.');\n\nreturn " . var_export($settings, true) . ";\n";

    if (file_put_contents($tmp, $content) === false) return false;
    if (!rename($tmp, $file)) {
        @unlink($tmp);
        return false;
    }
    return true;
}

function isConfigured(): bool {
    $file = __DIR__ . '/../../config/settings.php';
    if (!file_exists($file)) return false;
    $cfg = loadSettings();
    return !empty($cfg['configured']) && !empty($cfg['db_host']);
}
