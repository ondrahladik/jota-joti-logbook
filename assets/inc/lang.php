<?php
const VALID_LANGS  = ['en','de','nl','sv','no','da','fr','es','pt','it','cs','sk','pl','ru','uk','tr','id','ja','ko','hu'];
const VALID_THEMES = ['dark', 'light'];

const LANG_NATIVE_NAMES = [
    'en' => 'English',
    'de' => 'Deutsch',
    'nl' => 'Nederlands',
    'sv' => 'Svenska',
    'no' => 'Norsk',
    'da' => 'Dansk',
    'fr' => 'Français',
    'es' => 'Español',
    'pt' => 'Português',
    'it' => 'Italiano',
    'cs' => 'Čeština',
    'sk' => 'Slovenčina',
    'pl' => 'Polski',
    'ru' => 'Русский',
    'uk' => 'Українська',
    'tr' => 'Türkçe',
    'id' => 'Bahasa Indonesia',
    'ja' => '日本語',
    'ko' => '한국어',
    'hu' => 'Magyar',
];

function getCurrentLang(): string {
    return $_SESSION['jj_lang'] ?? 'en';
}

function getCurrentTheme(): string {
    return $_SESSION['jj_theme'] ?? 'dark';
}

function getLangStrings(string $lang): array {
    /** @var array<string, array<string, string>> $cache */
    static $cache = [];
    if (!isset($cache[$lang])) {
        $file = __DIR__ . "/../../lang/$lang.php";
        $cache[$lang] = file_exists($file) ? (array)(require $file) : [];
    }
    return $cache[$lang];
}

function __(string $key, array $vars = []): string {
    static $strings = null;
    if ($strings === null) {
        $strings = getLangStrings(getCurrentLang());
    }
    $val = $strings[$key] ?? $key;
    foreach ($vars as $k => $v) {
        $val = str_replace('%' . $k . '%', htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8'), $val);
    }
    return $val;
}

function h(string $s): string {
    return htmlspecialchars($s, ENT_QUOTES, 'UTF-8');
}

// Called BEFORE any output to handle lang/theme changes
function processPreOutputActions(array $settings): void {
    // Set lang
    if (isset($_GET['set_lang'])) {
        $lang = $_GET['set_lang'];
        if (in_array($lang, VALID_LANGS, true)) {
            $_SESSION['jj_lang'] = $lang;
            setcookie('jj_lang', $lang, ['expires' => time() + 365 * 86400, 'path' => '/', 'httponly' => true, 'samesite' => 'Lax']);
        }
        $ret = $_GET['ret'] ?? '/';
        if (!str_starts_with($ret, '/')) $ret = '/';
        header('Location: ' . $ret); exit;
    }

    // Set theme
    if (isset($_GET['set_theme'])) {
        $theme = $_GET['set_theme'];
        if (in_array($theme, VALID_THEMES, true)) {
            $_SESSION['jj_theme'] = $theme;
            setcookie('jj_theme', $theme, ['expires' => time() + 365 * 86400, 'path' => '/', 'httponly' => true, 'samesite' => 'Lax']);
        }
        $ret = $_GET['ret'] ?? '/';
        if (!str_starts_with($ret, '/')) $ret = '/';
        header('Location: ' . $ret); exit;
    }

    // Initialize from cookies if not in session
    if (!isset($_SESSION['jj_lang'])) {
        $cookieLang = $_COOKIE['jj_lang'] ?? null;
        $_SESSION['jj_lang'] = in_array($cookieLang, VALID_LANGS, true)
            ? $cookieLang
            : ($settings['default_lang'] ?? 'en');
    }
    if (!isset($_SESSION['jj_theme'])) {
        $cookieTheme = $_COOKIE['jj_theme'] ?? null;
        $_SESSION['jj_theme'] = in_array($cookieTheme, VALID_THEMES, true)
            ? $cookieTheme
            : ($settings['default_theme'] ?? 'dark');
    }
}
