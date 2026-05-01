<?php
$current_page = basename($_SERVER['PHP_SELF'], '.php');
$_req = strtok($_SERVER['REQUEST_URI'], '?');
if ($_req === '/help')    $current_page = 'help';
if ($_req === '/config')  $current_page = 'config';
if ($_req === '/login')   $current_page = 'login';
if ($_req === '/logbook') $current_page = 'logbook';
if ($_req === '/' || $_req === '/index') $current_page = 'index';
$theme = getCurrentTheme();
$lang  = getCurrentLang();
$role  = getCurrentRole();

$title = ($page_title ?? 'JOTA-JOTI Logbook') . ' | JOTA-JOTI';
$allLangs = ['en','de','nl','sv','no','da','fr','es','pt','it','cs','sk','pl','hu','ru','uk','tr','id','ja','ko'];
?><!DOCTYPE html>
<html lang="<?= h($lang) ?>" data-bs-theme="<?= h($theme) ?>" data-theme="<?= h($theme) ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="apple-touch-icon" sizes="180x180" href="/assets/img/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="96x96" href="/assets/img/favicon-96x96.png">
    <link rel="icon" type="image/svg+xml" href="/assets/img/favicon.svg" />
    <link rel="shortcut icon" href="/assets/img/favicon.ico" />
    <link rel="manifest" href="/assets/img/site.webmanifest">
    <title><?= h($title) ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="/assets/css/style.css">
</head>
<body>
<nav class="navbar navbar-expand-lg jj-navbar">
    <div class="container-fluid px-4">
        <a class="navbar-brand d-flex align-items-center gap-2" href="/">
            <span class="jj-logo-icon">&#x269C;&#xFE0F;</span>
            <span class="jj-brand-main">JOTA-JOTI</span>
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="mainNav">
            <ul class="navbar-nav me-auto ms-3">
                <li class="nav-item">
                    <a class="nav-link <?= $current_page === 'index' ? 'active' : '' ?>" href="/">
                        <i class="fa-solid fa-house me-1"></i><?= h(__('nav.home')) ?>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= $current_page === 'logbook' ? 'active' : '' ?>" href="/logbook">
                        <i class="fa-solid fa-satellite-dish me-1"></i><?= h(__('nav.logbook')) ?>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= $current_page === 'help' ? 'active' : '' ?>" href="/help">
                        <i class="fa-solid fa-circle-question me-1"></i><?= h(__('nav.help')) ?>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="https://www.jotajoti.info" target="_blank" rel="noopener">
                        <i class="fa-solid fa-globe me-1"></i><?= h(__('nav.official_site')) ?>
                    </a>
                </li>
                <?php if (isAdmin()): ?>
                <li class="nav-item">
                    <a class="nav-link <?= $current_page === 'config' ? 'active' : '' ?>" href="/config">
                        <i class="fa-solid fa-gear me-1"></i><?= h(__('nav.config')) ?>
                    </a>
                </li>
                <?php endif; ?>
            </ul>

            <div class="d-flex align-items-center gap-2 flex-wrap">
                <!-- UTC Clock -->
                <div class="jj-utc-clock">
                    <i class="fa-regular fa-clock me-1"></i><span id="utcClock">--:--:-- UTC</span>
                </div>

                <!-- Theme toggle -->
                <?php
                $otherTheme = $theme === 'dark' ? 'light' : 'dark';
                $themeIcon  = $theme === 'dark'  ? 'fa-sun'  : 'fa-moon';
                $themeLabel = __($theme === 'dark' ? 'theme.light' : 'theme.dark');
                $themeUrl   = '?set_theme=' . $otherTheme . '&ret=' . urlencode($_SERVER['REQUEST_URI']);
                ?>
                <a href="<?= h($themeUrl) ?>" class="btn btn-sm btn-jj-outline jj-nav-btn" title="<?= h($themeLabel) ?>">
                    <i class="fa-solid <?= $themeIcon ?>"></i>
                </a>

                <!-- Language dropdown -->
                <div class="dropdown">
                    <button class="btn btn-sm btn-jj-outline jj-nav-btn dropdown-toggle" type="button" data-bs-toggle="dropdown">
                        <?= strtoupper(h($lang)) ?>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end jj-lang-menu">
                        <?php foreach ($allLangs as $l): ?>
                        <li>
                            <a class="dropdown-item <?= $l === $lang ? 'active' : '' ?>"
                               href="?set_lang=<?= $l ?>&ret=<?= urlencode($_SERVER['REQUEST_URI']) ?>">
                                <?= h(LANG_NATIVE_NAMES[$l] ?? $l) ?>
                            </a>
                        </li>
                        <?php endforeach; ?>
                    </ul>
                </div>

                <!-- Login / Logout -->
                <?php if (isLoggedIn()): ?>
                <span class="jj-role-badge jj-role-<?= h($role) ?>">
                    <i class="fa-solid fa-user-shield me-1"></i><?= h(__('role.' . $role)) ?>
                </span>
                <a href="/logout" class="btn btn-sm btn-jj-outline jj-nav-btn">
                    <i class="fa-solid fa-right-from-bracket me-1"></i><?= h(__('nav.logout')) ?>
                </a>
                <?php else: ?>
                <span class="jj-role-badge jj-role-guest">
                    <i class="fa-solid fa-eye me-1"></i><?= h(__('role.guest')) ?>
                </span>
                <a href="/login?ret=<?= urlencode($_SERVER['REQUEST_URI']) ?>" class="btn btn-sm btn-jj-primary jj-nav-btn">
                    <i class="fa-solid fa-right-to-bracket me-1"></i><?= h(__('nav.login')) ?>
                </a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</nav>

<!-- Toast container: bottom-right -->
<div id="toastContainer" class="toast-container position-fixed bottom-0 end-0 p-3" style="z-index:2000"></div>

<div class="container-fluid px-4 jj-main-content">
<?php
if (!empty($_SESSION['flash_toast'])) {
    $flash = $_SESSION['flash_toast'];
    unset($_SESSION['flash_toast']);
    $flashJson = json_encode(
        ['type' => $flash['type'], 'msg' => $flash['msg']],
        JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT
    );
    echo "<script>document.addEventListener('DOMContentLoaded',function(){var f=$flashJson;showToast(f.msg,f.type);});</script>\n";
}
?>