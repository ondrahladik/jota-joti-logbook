<?php
require_once 'assets/inc/config.php';

// Already logged in
if (isLoggedIn()) {
    $ret = $_GET['ret'] ?? '/';
    if (!str_starts_with($ret, '/')) $ret = '/';
    header('Location: ' . $ret); exit;
}

$error = '';
$ret   = $_GET['ret'] ?? '/';
if (!str_starts_with($ret, '/')) $ret = '/';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verifyCsrf();
    $password = $_POST['password'] ?? '';
    $settings = loadSettings();
    $role = tryLogin($password, $settings);
    if ($role !== null) {
        session_regenerate_id(true);
        $_SESSION['jj_role'] = $role;
        setFlash('success', __('login.logged_in_as', ['role' => __('role.' . $role)]));
        $redir = $_POST['ret'] ?? '/';
        if (!str_starts_with($redir, '/')) $redir = '/';
        header('Location: ' . $redir); exit;
    } else {
        $error = __('login.wrong_password');
    }
}

$page_title = __('page.login');
require_once 'assets/inc/header.php';
?>

<div class="d-flex align-items-center justify-content-center" style="min-height:calc(100vh - 130px)">
<div class="w-100" style="max-width:400px;padding:1rem">
    <div class="jj-card">
        <div class="jj-card-header text-center">
            <h4 class="mb-0 text-gold"><i class="fa-solid fa-lock me-2"></i><?= h(__('login.title')) ?></h4>
        </div>
        <div class="p-4">
            <?php if ($error): ?>
            <div class="alert alert-danger jj-alert mb-3">
                <i class="fa-solid fa-circle-xmark me-2"></i><?= h($error) ?>
            </div>
            <?php endif; ?>
            <p class="text-jj-muted small mb-3"><?= h(__('login.role_hint')) ?></p>
            <form method="POST" action="/login">
                <input type="hidden" name="csrf_token" value="<?= h(csrfToken()) ?>">
                <input type="hidden" name="ret" value="<?= h($ret) ?>">
                <div class="mb-3">
                    <label class="form-label" for="password"><?= h(__('login.password')) ?></label>
                    <input type="password" id="password" name="password" class="form-control jj-input" required autofocus>
                </div>
                <button type="submit" class="btn btn-jj-primary w-100">
                    <i class="fa-solid fa-right-to-bracket me-2"></i><?= h(__('login.submit')) ?>
                </button>
            </form>
            <div class="text-center mt-3">
                <a href="/" class="text-jj-muted small"><i class="fa-solid fa-arrow-left me-1"></i><?= h(__('nav.home')) ?></a>
            </div>
        </div>
    </div>
</div>
</div>

<?php require_once 'assets/inc/footer.php'; ?>