<?php
define('JOTAJOTI_LOADED', true);
require_once __DIR__ . '/assets/inc/settings.php';

register_shutdown_function(function() {
    $e = error_get_last();
    if ($e && in_array($e['type'], [E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR], true)) {
        if (!headers_sent()) { http_response_code(500); }
        $error_message = $e['message'] . ' in ' . $e['file'] . ' on line ' . $e['line'];
        include __DIR__ . '/assets/errors/php-error.php';
    }
});

if (file_exists(__DIR__ . '/config/installed.lock') && isConfigured()) {
    header('Location: /login'); exit;
}

if (session_status() === PHP_SESSION_NONE) {
    session_set_cookie_params(['lifetime' => 0, 'path' => '/', 'httponly' => true, 'samesite' => 'Lax']);
    session_start();
}

// Language selection
$validLangs = ['en','de','nl','sv','no','da','fr','es','pt','it','cs','sk','pl','hu','ru','uk','tr','id','ja','ko'];
if (isset($_GET['setup_lang']) && in_array($_GET['setup_lang'], $validLangs, true)) {
    $_SESSION['setup_lang'] = $_GET['setup_lang'];
    $step = (int)($_GET['step'] ?? 1);
    header('Location: /setup?step=' . $step); exit;
}
$setupLang = $_SESSION['setup_lang'] ?? 'en';

// Mini translation loader for setup
function setupLangFile(string $lang): array {
    /** @var array<string, array<string, string>> $cache */
    static $cache = [];
    if (!isset($cache[$lang])) {
        $f = __DIR__ . "/lang/$lang.php";
        $cache[$lang] = file_exists($f) ? (array)(require $f) : [];
    }
    return $cache[$lang];
}
function s(string $key, array $vars = []): string {
    global $setupLang;
    $strings = setupLangFile($setupLang);
    $val = $strings[$key] ?? $key;
    foreach ($vars as $k => $v) {
        $val = str_replace('%' . $k . '%', htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8'), $val);
    }
    return htmlspecialchars($val, ENT_QUOTES, 'UTF-8');
}

// Session data helpers
function getSetupData(): array {
    return $_SESSION['setup_data'] ?? [];
}
function saveSetupData(): void {
    $saved = $_SESSION['setup_data'] ?? [];
    $fields = ['callsign', 'app_name', 'default_lang', 'default_theme',
                'db_host', 'db_user', 'db_name', 'db_pass', 'admin_pass', 'user_pass'];
    foreach ($fields as $f) {
        if (isset($_POST[$f])) $saved[$f] = $_POST[$f];
    }
    $_SESSION['setup_data'] = $saved;
}
// Get a form value: POST > session > default
function sv(string $key, string $default = ''): string {
    $saved = $_SESSION['setup_data'] ?? [];
    $val = $_POST[$key] ?? $saved[$key] ?? $default;
    return htmlspecialchars($val, ENT_QUOTES, 'UTF-8');
}

// CSRF
if (empty($_SESSION['setup_csrf'])) {
    $_SESSION['setup_csrf'] = bin2hex(random_bytes(32));
}
$csrf    = $_SESSION['setup_csrf'];
$step    = (int)($_POST['step'] ?? $_GET['step'] ?? 1);
$errors  = [];
$success = false;

// Form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!hash_equals($csrf, $_POST['csrf_token'] ?? '')) {
        $errors[] = s('setup.err.csrf');
    } else {
        $step = (int)($_POST['step'] ?? 1);

        // Language switch via form submit
        if (isset($_POST['_lang_switch']) && in_array($_POST['_lang_switch'], $validLangs, true)) {
            saveSetupData();
            $_SESSION['setup_lang'] = $_POST['_lang_switch'];
            $setupLang = $_POST['_lang_switch'];
            header('Location: /setup?step=' . $step); exit;
        }

        // Back button
        if (($_POST['action'] ?? '') === 'prev') {
            saveSetupData();
            $step = max(1, $step - 1);

        } elseif ($step === 3) {
            $settings = [
                'callsign'        => strtoupper(trim($_POST['callsign']      ?? 'OK1KKY')),
                'app_name'        => trim($_POST['app_name']                 ?? 'JOTA-JOTI Logbook'),
                'default_lang'    => in_array($_POST['default_lang'] ?? '', $validLangs) ? $_POST['default_lang'] : 'en',
                'default_theme'   => in_array($_POST['default_theme'] ?? '', ['dark','light']) ? $_POST['default_theme'] : 'dark',
                'db_host'         => trim($_POST['db_host']                  ?? ''),
                'db_user'         => trim($_POST['db_user']                  ?? ''),
                'db_pass'         => $_POST['db_pass']                       ?? '',
                'db_name'         => trim($_POST['db_name']                  ?? ''),
                'user_pass_hash'  => '',
                'admin_pass_hash' => '',
                'configured'      => true,
            ];
            $ap = trim($_POST['admin_pass'] ?? '');
            $up = trim($_POST['user_pass']  ?? '');
            if ($ap !== '') $settings['admin_pass_hash'] = password_hash($ap, PASSWORD_BCRYPT);
            if ($up !== '') $settings['user_pass_hash']  = password_hash($up, PASSWORD_BCRYPT);

            if (empty($settings['admin_pass_hash'])) {
                $errors[] = s('setup.err.adminpass');
            } else {
                $conn = @new mysqli($settings['db_host'], $settings['db_user'], $settings['db_pass'], $settings['db_name']);
                if ($conn->connect_error) {
                    $errors[] = s('setup.err.dbfail');
                } else {
                    $conn->close();
                    if (saveSettings($settings)) {
                        unset($_SESSION['setup_data'], $_SESSION['setup_lang']);
                        file_put_contents(__DIR__ . '/config/installed.lock', date('Y-m-d H:i:s'));
                        $success = true;
                    } else {
                        $errors[] = s('setup.err.write');
                    }
                }
            }

        } elseif ($step === 2) {
            // Validate DB connection before advancing
            $h = trim($_POST['db_host'] ?? '');
            $u = trim($_POST['db_user'] ?? '');
            $p = $_POST['db_pass'] ?? '';
            $n = trim($_POST['db_name'] ?? '');
            if ($h === '' || $u === '' || $n === '') {
                $errors[] = s('setup.err.dbfields');
            } else {
                $conn = @new mysqli($h, $u, $p, $n);
                if ($conn->connect_error) {
                    $errors[] = s('setup.err.dbfail');
                } else {
                    $conn->close();
                    saveSetupData();
                    $step++;
                }
            }

        } else {
            saveSetupData();
            if (isset($_POST['default_lang']) && in_array($_POST['default_lang'], $validLangs, true)) {
                $_SESSION['setup_lang'] = $_POST['default_lang'];
                $setupLang = $_POST['default_lang'];
            }
            $step++;
        }
    }
}

$setupTheme = 'dark';

$langNativeNames = [
    'en' => 'English',        'de' => 'Deutsch',         'nl' => 'Nederlands',
    'sv' => 'Svenska',        'no' => 'Norsk',            'da' => 'Dansk',
    'fr' => 'Français',       'es' => 'Español',          'pt' => 'Português',
    'it' => 'Italiano',       'cs' => 'Čeština',          'sk' => 'Slovenčina',
    'pl' => 'Polski',         'hu' => 'Magyar',           'ru' => 'Русский',
    'uk' => 'Українська',     'tr' => 'Türkçe',         'id' => 'Bahasa Indonesia', 
    'ja' => '日本語',          'ko' => '한국어',         
];
$langShortNames = [
    'en' => 'EN', 'de' => 'DE', 'nl' => 'NL', 'sv' => 'SV', 'no' => 'NO',
    'da' => 'DA', 'fr' => 'FR', 'es' => 'ES', 'pt' => 'PT', 'it' => 'IT',
    'cs' => 'CS', 'sk' => 'SK', 'pl' => 'PL', 'hu' => 'HU', 'ru' => 'RU', 
    'uk' => 'UK', 'tr' => 'TR', 'id' => 'ID', 'ja' => 'JA', 'ko' => 'KO', 
];
?>
<!DOCTYPE html>
<html lang="<?= htmlspecialchars($setupLang) ?>" data-bs-theme="<?= $setupTheme ?>" data-theme="<?= $setupTheme ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= s('setup.title') ?> - JOTA-JOTI Logbook</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="/assets/css/style.css">
</head>
<body>
<div class="container py-5">
    <div class="jj-setup-card">
        <div class="text-center mb-4">
            <span style="font-size:3rem;filter:drop-shadow(0 0 12px #f59e0b)">&#x269C;&#xFE0F;</span>
            <h1 class="text-gold mt-2">JOTA-JOTI Logbook</h1>
            <p class="text-jj-muted"><?= s('setup.wizard') ?></p>
        </div>

        <?php if ($success): ?>
        <div class="jj-card p-4 text-center">
            <i class="fa-solid fa-circle-check fa-3x text-success mb-3"></i>
            <h3 class="text-gold"><?= s('setup.success.title') ?></h3>
            <p class="text-jj-muted"><?= s('setup.success.text') ?></p>
            <a href="/" class="btn btn-jj-gold mt-2">
                <i class="fa-solid fa-arrow-right me-2"></i><?= s('setup.success.btn') ?>
            </a>
        </div>
        <?php else: ?>

        <form method="POST" action="/setup" id="setupForm">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf) ?>">
            <input type="hidden" name="step"       value="<?= $step ?>">

            <!-- Language switcher bar (pill buttons, ~10 per row) -->
            <div class="mb-3">
                <div class="d-flex flex-wrap gap-1 justify-content-center" id="setupLangBar">
                    <?php foreach ($validLangs as $l): ?>
                    <button type="button"
                        class="btn btn-sm jj-lang-pill <?= $l === $setupLang ? 'active' : '' ?>"
                        data-lang="<?= $l ?>"
                        title="<?= htmlspecialchars($langNativeNames[$l] ?? $l) ?>"
                        style="min-width:3rem;max-width:3.5rem;flex:0 0 auto">
                        <?= strtoupper($l) ?>
                    </button>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Carry forward app values (hidden on steps > 1) -->
            <?php if ($step > 1): ?>
            <input type="hidden" name="callsign"      value="<?= sv('callsign', 'OK1KKY') ?>">
            <input type="hidden" name="app_name"      value="<?= sv('app_name', 'JOTA-JOTI Logbook') ?>">
            <input type="hidden" name="default_lang"  value="<?= sv('default_lang', $setupLang) ?>">
            <input type="hidden" name="default_theme" value="<?= sv('default_theme', 'dark') ?>">
            <?php endif; ?>
            <!-- Carry forward DB values (hidden on step 3) -->
            <?php if ($step > 2): ?>
            <input type="hidden" name="db_host" value="<?= sv('db_host', 'localhost') ?>">
            <input type="hidden" name="db_user" value="<?= sv('db_user', 'root') ?>">
            <input type="hidden" name="db_pass" value="<?= sv('db_pass') ?>">
            <input type="hidden" name="db_name" value="<?= sv('db_name', 'logbook') ?>">
            <?php endif; ?>

            <!-- Step indicator -->
            <div class="jj-step-indicator">
                <div class="jj-step <?= $step === 1 ? 'active' : ($step > 1 ? 'done' : '') ?>">
                    <i class="fa-solid fa-sliders me-1"></i><?= s('setup.step.app') ?>
                </div>
                <div class="jj-step <?= $step === 2 ? 'active' : ($step > 2 ? 'done' : '') ?>">
                    <i class="fa-solid fa-database me-1"></i><?= s('setup.step.db') ?>
                </div>
                <div class="jj-step <?= $step === 3 ? 'active' : '' ?>">
                    <i class="fa-solid fa-lock me-1"></i><?= s('setup.step.security') ?>
                </div>
            </div>

            <?php if (!empty($errors)): ?>
            <div class="alert alert-danger jj-alert mb-3">
                <?php foreach ($errors as $e): ?>
                <div><i class="fa-solid fa-circle-xmark me-2"></i><?= htmlspecialchars($e) ?></div>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>

            <div class="jj-card p-4">

            <?php if ($step === 1): ?>
            <h4 class="text-purple mb-3"><i class="fa-solid fa-sliders me-2"></i><?= s('setup.step1.title') ?></h4>
            <div class="mb-3">
                <label class="form-label text-jj-muted small"><?= s('setup.step1.lang_label') ?></label>
                <select name="default_lang" id="defaultLangSelect" class="form-select jj-input">
                    <?php foreach ($validLangs as $l):
                        $sel = ($l === $setupLang) ? ' selected' : '';
                    ?>
                    <option value="<?= $l ?>"<?= $sel ?>><?= htmlspecialchars($langNativeNames[$l]) ?></option>
                    <?php endforeach; ?>
                </select>
                <div class="form-text text-jj-muted" style="font-size:.75rem"><?= s('setup.step1.lang_hint') ?></div>
            </div>
            <div class="mb-3">
                <label class="form-label text-jj-muted small"><?= s('setup.step1.callsign') ?></label>
                <input type="text" name="callsign" class="form-control jj-input"
                       value="<?= sv('callsign', 'OK1KKY') ?>" maxlength="20" required>
            </div>
            <div class="mb-3">
                <label class="form-label text-jj-muted small"><?= s('setup.step1.appname') ?></label>
                <input type="text" name="app_name" class="form-control jj-input"
                       value="<?= sv('app_name', 'JOTA-JOTI Logbook') ?>">
            </div>
            <div class="mb-4">
                <label class="form-label text-jj-muted small"><?= s('setup.step1.theme') ?></label>
                <select name="default_theme" class="form-select jj-input">
                    <?php
                    $themes = ['dark' => s('theme.dark'), 'light' => s('theme.light')];
                    $curTheme = $_SESSION['setup_data']['default_theme'] ?? 'dark';
                    foreach ($themes as $tv => $tl):
                        $sel = ($tv === $curTheme) ? ' selected' : '';
                    ?>
                    <option value="<?= $tv ?>"<?= $sel ?>><?= $tl ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <button type="submit" class="btn btn-jj-primary w-100">
                <?= s('setup.btn.next') ?> <i class="fa-solid fa-arrow-right ms-1"></i>
            </button>

            <?php elseif ($step === 2): ?>
            <h4 class="text-purple mb-3"><i class="fa-solid fa-database me-2"></i><?= s('setup.step2.title') ?></h4>
            <p class="text-jj-muted small mb-3"><?= s('setup.step2.subtitle') ?></p>
            <div class="mb-3">
                <label class="form-label text-jj-muted small"><?= s('setup.step2.host') ?></label>
                <input type="text" name="db_host" class="form-control jj-input"
                       value="<?= sv('db_host', 'localhost') ?>" required>
            </div>
            <div class="mb-3">
                <label class="form-label text-jj-muted small"><?= s('setup.step2.user') ?></label>
                <input type="text" name="db_user" class="form-control jj-input"
                       value="<?= sv('db_user', 'root') ?>" required>
            </div>
            <div class="mb-3">
                <label class="form-label text-jj-muted small"><?= s('setup.step2.pass') ?></label>
                <input type="password" name="db_pass" id="db_pass" class="form-control jj-input"
                       value="<?= sv('db_pass') ?>" autocomplete="current-password">
            </div>
            <div class="mb-4">
                <label class="form-label text-jj-muted small"><?= s('setup.step2.name') ?></label>
                <input type="text" name="db_name" class="form-control jj-input"
                       value="<?= sv('db_name', 'logbook') ?>" required>
            </div>
            <div class="d-flex gap-2">
                <button type="submit" name="action" value="prev" formnovalidate class="btn btn-jj-outline flex-shrink-0">
                    <i class="fa-solid fa-arrow-left me-1"></i><?= s('setup.btn.back') ?>
                </button>
                <button type="submit" class="btn btn-jj-primary flex-grow-1">
                    <?= s('setup.btn.next') ?> <i class="fa-solid fa-arrow-right ms-1"></i>
                </button>
            </div>

            <?php elseif ($step === 3): ?>
            <h4 class="text-purple mb-3"><i class="fa-solid fa-lock me-2"></i><?= s('setup.step3.title') ?></h4>
            <p class="text-jj-muted small mb-3"><?= s('setup.step3.subtitle') ?></p>
            <div class="mb-3">
                <label class="form-label text-jj-muted small">
                    <?= s('setup.step3.adminpass') ?> <span class="text-danger">*</span>
                </label>
                <input type="password" name="admin_pass" class="form-control jj-input"
                       value="<?= sv('admin_pass') ?>" autocomplete="new-password">
                <div class="form-text text-jj-muted" style="font-size:.75rem"><?= s('setup.step3.adminpass_hint') ?></div>
            </div>
            <div class="mb-4">
                <label class="form-label text-jj-muted small"><?= s('setup.step3.userpass') ?></label>
                <input type="password" name="user_pass" class="form-control jj-input"
                       value="<?= sv('user_pass') ?>" autocomplete="new-password">
                <div class="form-text text-jj-muted" style="font-size:.75rem"><?= s('setup.step3.userpass_hint') ?></div>
            </div>
            <div class="d-flex gap-2">
                <button type="submit" name="action" value="prev" formnovalidate class="btn btn-jj-outline flex-shrink-0">
                    <i class="fa-solid fa-arrow-left me-1"></i><?= s('setup.btn.back') ?>
                </button>
                <button type="submit" class="btn btn-jj-gold flex-grow-1">
                    <i class="fa-solid fa-check me-2"></i><?= s('setup.btn.finish') ?>
                </button>
            </div>
            <?php endif; ?>

            </div><!-- /jj-card -->
        </form>
        <?php endif; ?>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
(function() {
    var langNames = <?= json_encode($langNativeNames, JSON_UNESCAPED_UNICODE) ?>;

    // Pill button lang switcher
    var bar = document.getElementById('setupLangBar');
    if (bar) {
        bar.querySelectorAll('.jj-lang-pill').forEach(function(btn) {
            btn.addEventListener('click', function() {
                var lang = this.dataset.lang;
                var h = document.createElement('input');
                h.type = 'hidden'; h.name = '_lang_switch'; h.value = lang;
                document.getElementById('setupForm').appendChild(h);
                document.getElementById('setupForm').submit();
            });
            btn.addEventListener('mouseenter', function() {
                var nameEl = document.getElementById('setupLangName');
                if (nameEl) nameEl.textContent = langNames[this.dataset.lang] || this.dataset.lang;
            });
            btn.addEventListener('mouseleave', function() {
                var cur = bar.querySelector('.jj-lang-pill.active');
                var nameEl = document.getElementById('setupLangName');
                if (nameEl && cur) nameEl.textContent = langNames[cur.dataset.lang] || cur.dataset.lang;
            });
        });
    }

    var dl = document.getElementById('defaultLangSelect');
    if (dl) {
        dl.addEventListener('change', function() {
            var h = document.createElement('input');
            h.type = 'hidden'; h.name = '_lang_switch'; h.value = this.value;
            document.getElementById('setupForm').appendChild(h);
            document.getElementById('setupForm').submit();
        });
    }
})();
</script>
</body>
</html>
