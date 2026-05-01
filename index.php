<?php
require_once 'assets/inc/config.php';
require_once 'assets/inc/db.php';

$conn     = getConnection();
$logbooks = getLogbooks($conn);
$cur_year = (int)date('Y');

// Aggregate total QSOs
$total_qsos = 0;
foreach ($logbooks as $lb) {
    $t = "log_{$lb['year']}";
    $r = $conn->query("SELECT COUNT(*) c FROM `$t`");
    if ($r) $total_qsos += (int)$r->fetch_assoc()['c'];
}

$page_title = __('page.home');
require_once 'assets/inc/header.php';
?>

<!-- Hero -->
<div class="jj-hero">
    <div class="row align-items-center gy-3">
        <div class="col-md-8">
            <div class="d-flex align-items-center gap-3 mb-3">
                <div>
                    <h1 class="mb-0"><?= h(__('hero.title')) ?></h1>
                    <p class="mb-0 mt-1">
                        <strong class="text-gold"><?= h(APP_CALLSIGN) ?></strong>
                    </p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="jj-stat-card" style="background:rgba(0,0,0,.3);">
                <div class="jj-stat-icon"><i class="fa-solid fa-satellite-dish"></i></div>
                <div class="jj-stat-number"><?= $total_qsos ?></div>
                <div class="jj-stat-label"><?= h(__('hero.total_qso')) ?></div>
            </div>
        </div>
    </div>
</div>

<!-- Quick stat row -->
<div class="row g-3 mb-4">
    <div class="col-6 col-md-3">
        <div class="jj-stat-card">
            <div class="jj-stat-icon"><i class="fa-solid fa-book-open"></i></div>
            <div class="jj-stat-number"><?= count($logbooks) ?></div>
            <div class="jj-stat-label"><?= h(__('stat.logbooks')) ?></div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="jj-stat-card">
            <div class="jj-stat-icon"><i class="fa-solid fa-radio"></i></div>
            <div class="jj-stat-number"><?= $total_qsos ?></div>
            <div class="jj-stat-label"><?= h(__('stat.qsos')) ?></div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="jj-stat-card">
            <div class="jj-stat-icon"><i class="fa-solid fa-calendar-days"></i></div>
            <div class="jj-stat-number"><?= $cur_year ?></div>
            <div class="jj-stat-label"><?= h(__('stat.year')) ?></div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="jj-stat-card">
            <div class="jj-stat-icon"><i class="fa-solid fa-tower-broadcast"></i></div>
            <div class="jj-stat-number" style="font-size:1.5rem"><?= h(APP_CALLSIGN) ?></div>
            <div class="jj-stat-label"><?= h(__('stat.callsign')) ?></div>
        </div>
    </div>
</div>

<div class="row g-3">
    <!-- Logbooks list -->
    <div class="col-lg-8">
        <div class="jj-card h-100">
            <div class="jj-card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0 text-purple"><i class="fa-solid fa-book me-2"></i><?= h(__('logbook.available')) ?></h5>
                <?php if (isAdmin()): ?>
                <a href="/logbook" class="btn btn-sm btn-jj-gold">
                    <i class="fa-solid fa-plus me-1"></i><?= h(__('action.new_logbook')) ?>
                </a>
                <?php endif; ?>
            </div>
            <div class="p-3">
                <?php if (empty($logbooks)): ?>
                <div class="jj-empty-state">
                    <i class="fa-solid fa-book-open fa-5x"></i>
                    <p><?= h(__('logbook.no_logbooks')) ?></p>
                    <?php if (isAdmin()): ?>
                    <a href="/logbook" class="btn btn-jj-gold">
                        <i class="fa-solid fa-plus me-1"></i><?= h(__('logbook.create_first')) ?>
                    </a>
                    <?php endif; ?>
                </div>
                <?php else: ?>
                <div class="table-responsive">
                    <table class="table jj-table">
                        <thead>
                            <tr>
                                <th><?= h(__('table.logbook_year')) ?></th>
                                <th><?= h(__('table.logbook_callsign')) ?></th>
                                <th><?= h(__('table.logbook_qso')) ?></th>
                                <th><?= h(__('table.logbook_created')) ?></th>
                                <th><?= h(__('table.logbook_actions')) ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($logbooks as $lb):
                                $t   = "log_{$lb['year']}";
                                $r   = $conn->query("SELECT COUNT(*) c FROM `$t`");
                                $cnt = $r ? (int)$r->fetch_assoc()['c'] : 0;
                                $isCurrentYear = ((int)$lb['year'] === $cur_year);
                            ?>
                            <tr>
                                <td>
                                    <strong class="text-gold"><?= $lb['year'] ?></strong>
                                    <?php if ($isCurrentYear): ?>
                                        <span class="badge-year ms-2"><?= h(__('logbook.current')) ?></span>
                                    <?php endif; ?>
                                    <span class="ms-2 text-jj-muted small"><?= h($lb['event_name']) ?></span>
                                </td>
                                <td><span class="text-purple"><?= h($lb['callsign'] ?: APP_CALLSIGN) ?></span></td>
                                <td><strong><?= $cnt ?></strong></td>
                                <td class="text-jj-muted"><?= date('d. m. Y', strtotime($lb['created_at'])) ?></td>
                                <td>
                                    <a href="/logbook?year=<?= $lb['year'] ?>" class="btn btn-sm btn-jj-primary">
                                        <i class="fa-solid fa-arrow-right me-1"></i><?= h(__('action.open')) ?>
                                    </a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Sidebar -->
    <div class="col-lg-4">
        <!-- Quick actions -->
        <div class="jj-card mb-3">
            <div class="jj-card-header">
                <h5 class="mb-0 text-purple"><i class="fa-solid fa-bolt me-2"></i><?= h(__('logbook.quick_actions')) ?></h5>
            </div>
            <div class="p-3 d-grid gap-2">
                <?php
                $has_current = false;
                foreach ($logbooks as $lb) {
                    if ((int)$lb['year'] === $cur_year) { $has_current = true; break; }
                }
                ?>
                <?php if ($has_current): ?>
                <a href="/logbook?year=<?= $cur_year ?>" class="btn btn-jj-primary">
                    <i class="fa-solid fa-satellite-dish me-2"></i><?= h(__('action.open')) ?> <?= h(__('nav.logbook')) ?> <?= $cur_year ?>
                </a>
                <?php elseif (isAdmin()): ?>
                <a href="/logbook?action=new" class="btn btn-jj-gold">
                    <i class="fa-solid fa-plus me-2"></i><?= h(__('action.create')) ?> <?= $cur_year ?>
                </a>
                <?php endif; ?>
                <?php if (!empty($logbooks)): ?>
                <a href="/logbook" class="btn btn-jj-outline">
                    <i class="fa-solid fa-list me-2"></i><?= h(__('action.all_logbooks')) ?>
                </a>
                <?php endif; ?>
                <a href="/help" class="btn btn-jj-outline">
                    <i class="fa-solid fa-circle-question me-2"></i><?= h(__('action.help')) ?>
                </a>
            </div>
        </div>

        <!-- About JOTA-JOTI -->
        <div class="jj-card">
            <div class="jj-card-header">
                <h5 class="mb-0 text-gold"><i class="fa-solid fa-circle-info me-2"></i><?= h(__('logbook.about')) ?></h5>
            </div>
            <div class="p-3">
                <p class="small mb-2">
                    <span class="badge-jota me-1">JOTA</span>
                    <strong class="text-purple"><?= h(__('about.jota')) ?></strong><br>
                    <span class="text-jj-muted"><?= h(__('about.jota_desc')) ?></span>
                </p>
                <p class="small mb-3">
                    <span class="badge-joti me-1">JOTI</span>
                    <strong class="text-gold"><?= h(__('about.joti')) ?></strong><br>
                    <span class="text-jj-muted"><?= h(__('about.joti_desc')) ?></span>
                </p>
                <hr style="border-color:var(--jj-border);margin:.75rem 0">
                <p class="small text-jj-muted mb-0">
                    <i class="fa-solid fa-calendar me-1"></i>
                    <?= h(__('about.date_info')) ?>
                </p>
            </div>
        </div>
    </div>
</div>

<?php require_once 'assets/inc/footer.php'; ?>

