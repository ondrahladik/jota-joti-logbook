<?php
require_once 'assets/inc/config.php';
require_once 'assets/inc/db.php';

requireRole('admin');

$settings = loadSettings();
$conn     = tryConnection();  
$logbooks = $conn ? getLogbooks($conn) : [];
$db_down  = ($conn === null);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verifyCsrf();
    $action = $_POST['action'] ?? '';

    if ($action === 'save_config') {
        $newSettings = $settings; 
        $newSettings['db_host']       = trim($_POST['db_host']   ?? '');
        $newSettings['db_user']       = trim($_POST['db_user']   ?? '');
        $newSettings['db_name']       = trim($_POST['db_name']   ?? '');
        $newSettings['callsign']      = strtoupper(trim($_POST['callsign'] ?? 'OK1KKY'));
        $newSettings['app_name']      = trim($_POST['app_name']  ?? 'JOTA-JOTI Logbook');
        $newSettings['default_lang']  = in_array($_POST['default_lang'] ?? '', VALID_LANGS, true) ? $_POST['default_lang'] : 'en';
        $newSettings['default_theme'] = in_array($_POST['default_theme'] ?? '', ['dark','light']) ? $_POST['default_theme'] : 'dark';
        $newSettings['configured']    = true;

        // DB pass only if provided
        if (!empty($_POST['db_pass'])) $newSettings['db_pass'] = $_POST['db_pass'];

        // Passwords only if provided
        $ap = trim($_POST['admin_pass'] ?? '');
        $up = trim($_POST['user_pass']  ?? '');
        if ($ap !== '') $newSettings['admin_pass_hash'] = password_hash($ap, PASSWORD_BCRYPT);
        if ($up !== '') $newSettings['user_pass_hash']  = password_hash($up, PASSWORD_BCRYPT);

        if (saveSettings($newSettings)) {
            setFlash('success', __('config.save_ok'));
        } else {
            setFlash('danger', __('config.save_error'));
        }
        header('Location: /config'); exit;
    }

    if ($action === 'delete_logbook') {
        $year = (int)($_POST['del_year'] ?? 0);
        if ($conn && $year >= 2000 && $year <= 2099 && logbookExists($conn, $year)) {
            if (deleteLogbook($conn, $year)) {
                setFlash('warning', __('logbook.deleted_ok'));
            }
        }
        header('Location: /config'); exit;
    }
}

$page_title = __('config.title');
require_once 'assets/inc/header.php';
?>

<div class="py-3">
    <div class="jj-page-title mb-4">
        <i class="fa-solid fa-gear text-purple"></i>
        <?= h(__('config.title')) ?>
    </div>

    <?php if ($db_down): ?>
    <div class="alert alert-warning d-flex align-items-center gap-2 mb-4" role="alert">
        <i class="fa-solid fa-triangle-exclamation fa-lg"></i>
        <div><?= h(__('config.db_unreachable')) ?></div>
    </div>
    <?php endif; ?>

    <form method="POST" action="/config">
        <input type="hidden" name="csrf_token" value="<?= h(csrfToken()) ?>">
        <input type="hidden" name="action" value="save_config">

        <div class="row g-4 mb-4">
            <!-- DB Settings -->
            <div class="col-md-6">
                <div class="jj-card h-100">
                    <div class="jj-card-header">
                        <h5 class="mb-0 text-purple"><i class="fa-solid fa-database me-2"></i><?= h(__('config.db_settings')) ?></h5>
                    </div>
                    <div class="p-4">
                        <div class="mb-3">
                            <label class="form-label"><?= h(__('config.db_host')) ?></label>
                            <input type="text" name="db_host" class="form-control jj-input" value="<?= h($settings['db_host'] ?? '') ?>" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label"><?= h(__('config.db_user')) ?></label>
                            <input type="text" name="db_user" class="form-control jj-input" value="<?= h($settings['db_user'] ?? '') ?>" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label"><?= h(__('config.db_pass')) ?></label>
                            <input type="password" name="db_pass" class="form-control jj-input" placeholder="<?= h(__('config.password_hint')) ?>">
                        </div>
                        <div class="mb-3">
                            <label class="form-label"><?= h(__('config.db_name')) ?></label>
                            <input type="text" name="db_name" class="form-control jj-input" value="<?= h($settings['db_name'] ?? '') ?>" required>
                        </div>
                    </div>
                </div>
            </div>

            <!-- App Settings -->
            <div class="col-md-6">
                <div class="jj-card h-100">
                    <div class="jj-card-header">
                        <h5 class="mb-0 text-purple"><i class="fa-solid fa-sliders me-2"></i><?= h(__('config.app_settings')) ?></h5>
                    </div>
                    <div class="p-4">
                        <div class="mb-3">
                            <label class="form-label"><?= h(__('config.callsign')) ?></label>
                            <input type="text" name="callsign" class="form-control jj-input" value="<?= h($settings['callsign'] ?? 'OK1KKY') ?>" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label"><?= h(__('config.app_name_label')) ?></label>
                            <input type="text" name="app_name" class="form-control jj-input" value="<?= h($settings['app_name'] ?? 'JOTA-JOTI Logbook') ?>">
                        </div>
                        <div class="mb-3">
                            <label class="form-label"><?= h(__('config.default_lang')) ?></label>
                            <select name="default_lang" class="form-select jj-input">
                                <?php foreach (VALID_LANGS as $l): ?>
                                <option value="<?= $l ?>" <?= ($settings['default_lang'] ?? 'cs') === $l ? 'selected' : '' ?>><?= h(__('lang.' . $l)) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label"><?= h(__('config.default_theme')) ?></label>
                            <select name="default_theme" class="form-select jj-input">
                                <option value="dark"  <?= ($settings['default_theme'] ?? 'dark') === 'dark'  ? 'selected' : '' ?>><?= h(__('theme.dark')) ?></option>
                                <option value="light" <?= ($settings['default_theme'] ?? 'dark') === 'light' ? 'selected' : '' ?>><?= h(__('theme.light')) ?></option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Security -->
        <div class="jj-card mb-4">
            <div class="jj-card-header">
                <h5 class="mb-0 text-purple"><i class="fa-solid fa-lock me-2"></i><?= h(__('config.security')) ?></h5>
            </div>
            <div class="p-4">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label"><?= h(__('config.admin_password')) ?></label>
                        <input type="password" name="admin_pass" class="form-control jj-input" placeholder="<?= h(__('config.password_hint')) ?>">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label"><?= h(__('config.user_password')) ?></label>
                        <input type="password" name="user_pass" class="form-control jj-input" placeholder="<?= h(__('config.password_hint')) ?>">
                    </div>
                </div>
                <div class="mt-2 text-jj-muted small"><i class="fa-solid fa-circle-info me-1"></i><?= h(__('config.password_hint')) ?></div>
            </div>
        </div>

        <div class="text-end mb-4">
            <button type="submit" class="btn btn-jj-primary">
                <i class="fa-solid fa-floppy-disk me-2"></i><?= h(__('action.save')) ?>
            </button>
        </div>
    </form>

    <!-- Danger Zone: Delete logbooks -->
    <?php if (!$db_down): ?>
    <div class="jj-card" style="border-color:rgba(239,68,68,.3)">
        <div class="jj-card-header" style="background:rgba(239,68,68,.06)">
            <h5 class="mb-0 text-danger"><i class="fa-solid fa-triangle-exclamation me-2"></i><?= h(__('config.danger_zone')) ?></h5>
        </div>
        <div class="p-4">
            <h6 class="text-jj-muted"><?= h(__('config.delete_logbook_title')) ?></h6>
            <?php if (empty($logbooks)): ?>
            <p class="text-jj-muted small"><?= h(__('logbook.no_logbooks')) ?></p>
            <?php else: ?>
            <div class="row g-2">
                <?php foreach ($logbooks as $lb): ?>
                <div class="col-auto">
                    <form method="POST" action="/config"
                          onsubmit="return confirm(<?= json_encode(__('config.delete_logbook_confirm', ['year' => $lb['year']])) ?>)">
                        <input type="hidden" name="csrf_token" value="<?= h(csrfToken()) ?>">
                        <input type="hidden" name="action"    value="delete_logbook">
                        <input type="hidden" name="del_year"  value="<?= (int)$lb['year'] ?>">
                        <button type="submit" class="btn btn-sm btn-outline-danger">
                            <i class="fa-solid fa-trash me-1"></i><?= (int)$lb['year'] ?>
                        </button>
                    </form>
                </div>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>
        </div>
    </div>
    <?php endif; ?>
</div>

<?php require_once 'assets/inc/footer.php'; ?>
