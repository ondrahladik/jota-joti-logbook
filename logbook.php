<?php
require_once 'assets/inc/config.php';
require_once 'assets/inc/db.php';

$conn     = getConnection();
$logbooks = getLogbooks($conn);
$cur_year = (int)date('Y');

// POST handlers
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verifyCsrf();

    // Create new logbook
    if (isset($_POST['create_logbook'])) {
        requireRole('admin');
        $ny  = (int)($_POST['new_year']    ?? $cur_year);
        $ncs = trim($_POST['new_callsign'] ?? APP_CALLSIGN);
        $nnt = trim($_POST['new_notes']    ?? '');
        if ($ny < 2000 || $ny > 2099) {
            setFlash('danger', __('logbook.invalid_year'));
            header("Location: /logbook"); exit;
        }
        if (createLogbook($conn, $ny, $ncs, $nnt)) {
            setFlash('success', __('logbook.created_ok', ['year' => $ny]));
        } else {
            setFlash('warning', __('logbook.already_exists', ['year' => $ny]));
        }
        header("Location: /logbook?year=$ny"); exit;
    }

    // Add / Edit record
    if (isset($_POST['submit'])) {
        requireRole('user');
        $year = (int)($_POST['year'] ?? $cur_year);
        // Non-admins can only edit current year
        if ($year < $cur_year && !isAdmin()) {
            setFlash('danger', __('qso.edit_admin_only'));
            header("Location: /logbook?year=$year"); exit;
        }
        if (logbookExists($conn, $year)) {
            $data = [
                'callsign'   => $_POST['callsign']  ?? '',
                'band'       => $_POST['band']       ?? '',
                'mode'       => $_POST['mode']       ?? '',
                'qth'        => $_POST['qth']        ?? '',
                'locator'    => $_POST['locator']    ?? '',
                'rst_tx'     => $_POST['rst_tx']     ?? '',
                'rst_rx'     => $_POST['rst_rx']     ?? '',
                'scout_name' => $_POST['scout_name'] ?? '',
                'note'       => $_POST['note']       ?? '',
                'qso_time'   => $_POST['qso_time']   ?? '',
            ];
            if (empty($data['band'])) {
                setFlash('danger', __('qso.band_required'));
                header("Location: /logbook?year=$year"); exit;
            }
            if (!empty($_POST['record_id'])) {
                updateRecord($conn, $year, (int)$_POST['record_id'], $data);
                setFlash('success', __('qso.updated_ok'));
            } else {
                insertRecord($conn, $year, $data);
                setFlash('success', __('qso.added_ok'));
            }
        }
        header("Location: /logbook?year=$year"); exit;
    }

    // Delete record
    if (isset($_POST['delete'])) {
        requireRole('user');
        $year = (int)($_POST['year'] ?? $cur_year);
        if ($year < $cur_year && !isAdmin()) {
            setFlash('danger', __('qso.edit_admin_only'));
            header("Location: /logbook?year=$year"); exit;
        }
        $did  = (int)($_POST['delete_id'] ?? 0);
        if ($did > 0 && logbookExists($conn, $year)) {
            deleteRecord($conn, $year, $did);
            setFlash('warning', __('qso.deleted_ok'));
        }
        header("Location: /logbook?year=$year"); exit;
    }
}

// Export (GET)
if (isset($_GET['action']) && $_GET['action'] === 'export') {
    $ey  = (int)($_GET['year']   ?? $cur_year);
    $fmt = $_GET['format'] ?? 'adif';
    if (logbookExists($conn, $ey)) {
        if ($fmt === 'adif') {
            header('Content-Type: text/plain; charset=utf-8');
            header("Content-Disposition: attachment; filename=\"JOTAJOTI_{$ey}.adi\"");
            echo exportADIF($conn, $ey); exit;
        }
        if ($fmt === 'xlsx') {
            $data = exportXLSX($conn, $ey);
            while (ob_get_level()) ob_end_clean();
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header("Content-Disposition: attachment; filename=\"JOTAJOTI_{$ey}.xlsx\"");
            header('Content-Length: ' . strlen($data));
            echo $data; exit;
        }
    }
}

// Reload logbooks after any mutations
$logbooks = getLogbooks($conn);

// Determine active year
$show_new    = isset($_GET['action']) && $_GET['action'] === 'new';
$active_year = isset($_GET['year']) ? (int)$_GET['year'] : null;

if ($active_year === null) {
    $active_year = !empty($logbooks) ? (int)$logbooks[0]['year'] : $cur_year;
}

$logbook_info  = getLogbookInfo($conn, $active_year);
$logbook_exists = $logbook_info !== null;
$records        = $logbook_exists ? getRecords($conn, $active_year) : [];
$stats          = $logbook_exists ? getStats($conn, $active_year)   : null;

// Non-admins can only edit/delete QSOs in the current year
$canEditYear = canEdit() && ($active_year >= $cur_year || isAdmin());

$page_title = __('page.logbook');
$jj_i18n = json_encode([
    'action_add_qso' => __('action.add_qso'),
    'action_save'    => __('action.save'),
], JSON_HEX_APOS | JSON_HEX_QUOT);
require_once 'assets/inc/header.php';
?>
<script>window.JJ_I18N = <?= $jj_i18n ?>;</script>

<!-- Top bar: year tabs + new logbook button -->
<div class="d-flex flex-wrap align-items-center gap-2 py-3">
    <div class="jj-page-title me-2">
        <i class="fa-solid fa-satellite-dish text-purple"></i>
        Logbook
    </div>

    <!-- Year tabs -->
    <div class="d-flex flex-wrap gap-2 me-auto">
        <?php foreach ($logbooks as $lb): ?>
        <a href="/logbook?year=<?= $lb['year'] ?>"
           class="jj-year-tab <?= ((int)$lb['year'] === $active_year) ? 'active' : '' ?>">
            <i class="fa-solid fa-book-open"></i>
            <?= $lb['year'] ?>
        </a>
        <?php endforeach; ?>
        <?php if (empty($logbooks)): ?>
        <span class="text-jj-muted small align-self-center"><?= h(__('logbook.no_logbooks')) ?></span>
        <?php endif; ?>
    </div>

    <!-- New logbook button - admin only -->
    <?php if (isAdmin()): ?>
    <button class="btn btn-jj-gold btn-sm" data-bs-toggle="modal" data-bs-target="#newLogbookModal">
        <i class="fa-solid fa-plus me-1"></i><?= h(__('action.new_logbook')) ?>
    </button>
    <?php endif; ?>
</div>

<?php if (!$logbook_exists): ?>
<!-- Empty state -->
<div class="jj-empty-state">
    <i class="fa-solid fa-book-open fa-5x"></i>
    <p>
        <?php if (empty($logbooks)): ?>
        <?= h(__('logbook.no_logbooks')) ?><br>
        <?php if (isAdmin()): ?>
        <?= h(__('logbook.create_first')) ?>
        <?php endif; ?>
        <?php else: ?>
        <?= h(__('logbook.empty')) ?>
        <?php endif; ?>
    </p>
    <?php if (isAdmin()): ?>
    <button class="btn btn-jj-gold" data-bs-toggle="modal" data-bs-target="#newLogbookModal">
        <i class="fa-solid fa-plus me-1"></i><?= h(__('logbook.create_new')) ?>
        <?php if (!empty($logbooks)): ?> <?= $cur_year ?><?php endif; ?>
    </button>
    <?php endif; ?>
</div>

<?php else: ?>

<!-- Stats row -->
<div class="row g-2 mb-3">
    <div class="col-6 col-md-6">
        <div class="jj-stat-card py-3">
            <div class="jj-stat-icon"><i class="fa-solid fa-radio"></i></div>
            <div class="jj-stat-number"><?= $stats['total'] ?></div>
            <div class="jj-stat-label"><?= h(__('stat.total_qso')) ?></div>
        </div>
    </div>
    <div class="col-6 col-md-6">
        <div class="jj-stat-card py-3">
            <div class="jj-stat-icon"><i class="fa-solid fa-wave-square"></i></div>
            <div class="jj-stat-number" style="font-size:1.1rem">
                <?php
                $modes = array_column($stats['by_mode'], 'mode');
                echo h(implode(' / ', array_slice($modes, 0, 3)) ?: '-');
                ?>
            </div>
            <div class="jj-stat-label"><?= h(__('stat.top_modes')) ?></div>
        </div>
    </div>
</div>

<!-- Entry form - users and admins only -->
<?php if ($canEditYear): ?>
<div id="entryForm" class="jj-entry-form mb-3">
    <div class="jj-card-header d-flex align-items-center">
        <h6 class="mb-0 text-purple">
            <i class="fa-solid fa-plus-circle me-2"></i><?= h(__('action.add_qso')) ?> / <?= h(__('action.edit')) ?>
            <span id="modeTypeBadge" class="badge-jota ms-2" style="display:none">JOTA</span>
        </h6>
    </div>
    <div id="formCollapse">
        <form method="POST" action="/logbook" class="p-3">
            <input type="hidden" name="csrf_token" value="<?= h(csrfToken()) ?>">
            <input type="hidden" name="year"       value="<?= $active_year ?>">
            <input type="hidden" id="record_id"    name="record_id" value="">

            <div class="row g-2">
                <!-- UTC Time -->
                <div class="col-12 col-sm-6 col-md-4 col-xl-2">
                    <label class="form-label"><i class="fa-regular fa-clock me-1"></i><?= h(__('qso.time_utc')) ?></label>
                    <div class="jj-input form-control" id="currentTime" style="font-family:monospace;font-size:.8rem">
                        --. --. ---- --:--:--
                    </div>
                    <input type="datetime-local" id="qso_time" name="qso_time"
                           class="form-control jj-input d-none" step="1"
                           style="font-family:monospace;font-size:.8rem">
                </div>

                <!-- Callsign -->
                <div class="col-12 col-sm-6 col-md-4 col-xl-2">
                    <label class="form-label" for="callsign">
                        <i class="fa-solid fa-tower-broadcast me-1"></i><?= h(__('qso.callsign')) ?> <span class="text-danger">*</span>
                    </label>
                    <input type="text" id="callsign" name="callsign"
                           class="form-control jj-input" placeholder="<?= h(__('placeholder.callsign')) ?>" maxlength="20" autocomplete="off">
                </div>

                <!-- Mode -->
                <div class="col-12 col-sm-6 col-md-4 col-xl-2">
                    <label class="form-label" for="mode">
                        <i class="fa-solid fa-signal me-1"></i><?= h(__('qso.mode')) ?> <span class="text-danger">*</span>
                    </label>
                    <select id="mode" name="mode" class="form-select jj-input">
                        <option value="" disabled selected>- <?= h(__('qso.mode')) ?> -</option>
                        <optgroup label="JOTA (radio)">
                            <option value="SSB">SSB</option>
                            <option value="CW">CW</option>
                            <option value="FT8">FT8</option>
                            <option value="FT4">FT4</option>
                            <option value="JS8">JS8</option>
                            <option value="DIGI">DIGI</option>
                            <option value="AM">AM</option>
                            <option value="FM">FM</option>
                        </optgroup>
                        <optgroup label="JOTI (internet)">
                            <option value="Zoom">Zoom</option>
                            <option value="Teams">Teams</option>
                            <option value="Discord">Discord</option>
                            <option value="IRC">IRC</option>
                            <option value="EchoLink">EchoLink</option>
                            <option value="WIRES-X">WIRES-X</option>
                            <option value="DMR">DMR</option>
                            <option value="C4FM">C4FM</option>
                        </optgroup>
                    </select>
                </div>

                <!-- Band -->
                <div id="bandGroup" class="col-12 col-sm-6 col-md-4 col-xl-1">
                    <label class="form-label" for="band">
                        <i class="fa-solid fa-wave-square me-1"></i><?= h(__('qso.band')) ?> <span class="text-danger">*</span>
                    </label>
                    <select id="band" name="band" class="form-select jj-input" required>
                        <option value="">-</option>
                        <optgroup label="HF">
                            <option value="160m">160m / 1.8 MHz</option>
                            <option value="80m">80m / 3.5 MHz</option>
                            <option value="60m">60m / 5 MHz</option>
                            <option value="40m">40m / 7 MHz</option>
                            <option value="30m">30m / 10 MHz</option>
                            <option value="20m">20m / 14 MHz</option>
                            <option value="17m">17m / 18 MHz</option>
                            <option value="15m">15m / 21 MHz</option>
                            <option value="12m">12m / 24 MHz</option>
                            <option value="10m">10m / 28 MHz</option>
                        </optgroup>
                        <optgroup label="VHF/UHF">
                            <option value="6m">6m / 50 MHz</option>
                            <option value="2m">2m / 144 MHz</option>
                            <option value="70cm">70cm / 432 MHz</option>
                        </optgroup>
                    </select>
                </div>

                <!-- QTH -->
                <div class="col-12 col-sm-6 col-md-4 col-xl-2">
                    <label class="form-label" for="qth">
                        <i class="fa-solid fa-location-dot me-1"></i><?= h(__('qso.qth')) ?> / <?= h(__('qso.country')) ?>
                    </label>
                    <input type="text" id="qth" name="qth"
                           class="form-control jj-input" placeholder="<?= h(__('placeholder.qth')) ?>" maxlength="100">
                </div>

                <!-- Locator -->
                <div class="col-6 col-md-3 col-xl-1">
                    <label class="form-label" for="locator">
                        <i class="fa-solid fa-map-pin me-1"></i>Lok.
                    </label>
                    <input type="text" id="locator" name="locator"
                           class="form-control jj-input" placeholder="<?= h(__('placeholder.locator')) ?>" maxlength="20">
                </div>

                <!-- RST group -->
                <div id="rstGroup" class="col-6 col-md-3 col-xl-2">
                    <div class="row g-1">
                        <div class="col-6">
                            <label class="form-label" for="rst_tx"><?= h(__('qso.rst_tx')) ?></label>
                            <input type="text" id="rst_tx" name="rst_tx"
                                   class="form-control jj-input" placeholder="59" maxlength="10">
                        </div>
                        <div class="col-6">
                            <label class="form-label" for="rst_rx"><?= h(__('qso.rst_rx')) ?></label>
                            <input type="text" id="rst_rx" name="rst_rx"
                                   class="form-control jj-input" placeholder="59" maxlength="10">
                        </div>
                    </div>
                </div>

                <!-- Scout name -->
                <div class="col-12 col-sm-6 col-md-4 col-xl-2">
                    <label class="form-label" for="scout_name">
                        <i class="fa-solid fa-user me-1"></i><?= h(__('qso.scout_name')) ?>
                    </label>
                    <input type="text" id="scout_name" name="scout_name"
                           class="form-control jj-input" placeholder="<?= h(__('placeholder.name')) ?>" maxlength="100">
                </div>

                <!-- Note -->
                <div class="col">
                    <label class="form-label" for="note">
                        <i class="fa-solid fa-comment me-1"></i><?= h(__('qso.note')) ?>
                    </label>
                    <input type="text" id="note" name="note"
                           class="form-control jj-input" placeholder="<?= h(__('placeholder.note')) ?>">
                </div>

                <!-- Actions -->
                <div class="col-auto d-flex align-items-end gap-2">
                    <button type="submit" name="submit" class="btn btn-jj-primary px-4" disabled>
                        <i class="fa-solid fa-check me-1"></i><?= h(__('action.add_qso')) ?>
                    </button>
                    <button type="button" class="btn btn-jj-outline" onclick="clearForm()"
                            data-bs-toggle="tooltip" title="<?= h(__('action.clear_form')) ?>">
                        <i class="fa-solid fa-xmark"></i>
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
<?php endif;  ?>

<!-- Hidden delete form -->
<?php if (canEdit()): ?>
<form id="delete_form" method="POST" action="logbook.php">
    <input type="hidden" name="csrf_token" value="<?= h(csrfToken()) ?>">
    <input type="hidden" name="year"       value="<?= $active_year ?>">
    <input type="hidden" id="delete_id"    name="delete_id" value="">
    <input type="hidden" name="delete"     value="true">
</form>
<?php endif; ?>

<!-- Records table -->
<div class="jj-card">
    <div class="jj-card-header">
        <div class="row align-items-center g-2">
            <div class="col-md-5">
                <div class="d-flex align-items-center gap-2">
                    <h6 class="mb-0 text-purple">
                        <i class="fa-solid fa-list me-2"></i><?php
                        $visibleTpl = htmlspecialchars(__('qso.visible_count'), ENT_QUOTES, 'UTF-8');
                        $parts = explode('%n%', $visibleTpl);
                        echo ($parts[0] ?? '') . '<span id="visibleCount">' . count($records) . '</span>' . ($parts[1] ?? '');
                        ?>
                    </h6>
                </div>
            </div>
            <div class="col-md-4">
                <div class="jj-search">
                    <i class="fa-solid fa-magnifying-glass search-icon"></i>
                    <input type="text" id="searchInput" class="form-control jj-input"
                           placeholder="<?= h(__('qso.search')) ?>" autocomplete="off">
                </div>
            </div>
            <div class="col-md-3 text-md-end d-flex gap-2 justify-content-md-end">
                <?php $no_qso = ($stats['total'] == 0); ?>
                <a href="<?= $no_qso ? '#' : '/logbook?action=export&year='.$active_year.'&format=adif' ?>"
                   class="btn btn-sm btn-jj-outline<?= $no_qso ? ' disabled' : '' ?>"
                   <?= $no_qso ? 'aria-disabled="true" tabindex="-1"' : '' ?>
                   data-bs-toggle="tooltip" title="<?= h(__('action.export_adif')) ?>">
                    <i class="fa-solid fa-download me-1"></i><?= h(__('action.export_adif')) ?>
                </a>
                <a href="<?= $no_qso ? '#' : '/logbook?action=export&year='.$active_year.'&format=xlsx' ?>"
                   class="btn btn-sm btn-jj-outline<?= $no_qso ? ' disabled' : '' ?>"
                   <?= $no_qso ? 'aria-disabled="true" tabindex="-1"' : '' ?>
                   data-bs-toggle="tooltip" title="<?= h(__('action.export_xlsx')) ?>">
                    <i class="fa-solid fa-file-excel me-1"></i><?= h(__('action.export_xlsx')) ?>
                </a>
            </div>
        </div>
    </div>

    <div class="table-responsive">
        <table class="table jj-table mb-0" id="logTable">
            <thead>
                <tr>
                    <th><?= h(__('qso.id')) ?></th>
                    <th><i class="fa-regular fa-clock me-1"></i><?= h(__('qso.time_utc')) ?></th>
                    <th><i class="fa-solid fa-tower-broadcast me-1"></i><?= h(__('qso.callsign')) ?></th>
                    <th><?= h(__('qso.mode')) ?></th>
                    <th><?= h(__('qso.band')) ?></th>
                    <th><i class="fa-solid fa-location-dot me-1"></i><?= h(__('qso.qth')) ?> / <?= h(__('qso.country')) ?></th>
                    <th><?= h(__('qso.locator')) ?></th>
                    <th><?= h(__('qso.rst_tx')) ?></th>
                    <th><?= h(__('qso.rst_rx')) ?></th>
                    <th><i class="fa-solid fa-user me-1"></i><?= h(__('qso.scout_name')) ?></th>
                    <th><?= h(__('qso.note')) ?></th>
                    <?php if ($canEditYear): ?>
                    <th><?= h(__('qso.actions')) ?></th>
                    <?php endif; ?>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($records)): ?>
                <tr>
                    <td colspan="<?= $canEditYear ? 12 : 11 ?>" class="text-center py-4 text-jj-muted">
                        <i class="fa-solid fa-inbox fa-2x mb-2 d-block" style="color:var(--jj-border)"></i>
                        <?= h(__('qso.no_records')) ?>
                    </td>
                </tr>
                <?php else: ?>
                <?php
                $seq    = count($records);
                $isGuest = !canEdit();
                foreach ($records as $row):
                    $ts     = date('d. m. Y H:i:s', strtotime($row['timestamp']));
                    $jd     = $isGuest ? '{}' : json_encode($row, JSON_HEX_APOS | JSON_HEX_QUOT);
                    $isJOTI = in_array($row['mode'], ['Zoom','Teams','Discord','IRC','EchoLink','WIRES-X','DMR','C4FM','Internet','Online']);
                    $modeBadge = $isJOTI ? 'badge-joti' : 'badge-jota';
                ?>
                <tr class="data-row">
                    <td class="text-jj-muted"><?= $seq-- ?></td>
                    <td style="font-family:monospace;font-size:.8rem;white-space:nowrap"><?= $ts ?></td>
                    <td><strong class="text-gold"><?= h($row['callsign']) ?></strong></td>
                    <td><span class="<?= $modeBadge ?>"><?= h($row['mode']) ?></span></td>
                    <td><?= h($row['band'] ?? '-') ?></td>
                    <td><?= h($row['qth'] ?? '') ?></td>
                    <td><?= h($row['locator'] ?? '') ?></td>
                    <td><?= h($row['rst_tx'] ?? '') ?></td>
                    <td><?= h($row['rst_rx'] ?? '') ?></td>
                    <?php if ($isGuest): ?>
                    <td class="text-jj-muted">---</td>
                    <td class="text-jj-muted">---</td>
                    <?php else: ?>
                    <td><?= h($row['scout_name'] ?? '') ?></td>
                    <td class="text-jj-muted" style="max-width:160px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap"
                        title="<?= h($row['note'] ?? '') ?>">
                        <?= h($row['note'] ?? '') ?>
                    </td>
                    <?php endif; ?>
                    <?php if ($canEditYear): ?>
                    <td>
                        <div class="btn-group btn-group-sm" role="group">
                            <button class="btn btn-sm" style="background:rgba(245,158,11,.15);border:1px solid rgba(245,158,11,.3);color:#fcd34d"
                                    onclick='fillForm(<?= $jd ?>)'
                                    data-bs-toggle="tooltip" title="<?= h(__('action.edit')) ?>">
                                <i class="fa-solid fa-pen-to-square"></i>
                            </button>
                            <button class="btn btn-sm" style="background:rgba(239,68,68,.15);border:1px solid rgba(239,68,68,.3);color:#fca5a5"
                                    onclick="confirmDelete(<?= $row['id'] ?>)"
                                    data-bs-toggle="tooltip" title="<?= h(__('action.delete')) ?>">
                                <i class="fa-solid fa-trash"></i>
                            </button>
                        </div>
                    </td>
                    <?php endif; ?>
                </tr>
                <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Mode / Band summary -->
<?php if (!empty($stats['by_mode']) || !empty($stats['by_band'])): ?>
<div class="row g-2 mt-2 mb-1">
    <?php if (!empty($stats['by_mode'])): ?>
    <div class="col-auto">
        <span class="text-jj-muted small me-2"><?= h(__('qso.mode')) ?>:</span>
        <?php foreach ($stats['by_mode'] as $m): ?>
        <span class="badge-jota me-1"><?= h($m['mode']) ?> <strong class="text-gold"><?= $m['cnt'] ?></strong></span>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>
    <?php if (!empty($stats['by_band'])): ?>
    <div class="col-auto">
        <span class="text-jj-muted small me-2"><?= h(__('qso.band')) ?>:</span>
        <?php foreach ($stats['by_band'] as $b): ?>
        <span class="badge-year me-1"><?= h($b['band']) ?> <strong><?= $b['cnt'] ?></strong></span>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>
</div>
<?php endif; ?>

<?php endif; ?>

<!-- Modal: Create new logbook - admin only -->
<?php if (isAdmin()): ?>
<div class="modal fade" id="newLogbookModal" tabindex="-1" aria-labelledby="newLogbookModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="background:var(--jj-card);border:1px solid var(--jj-border);border-radius:12px">
            <div class="modal-header" style="border-bottom:1px solid var(--jj-border)">
                <h5 class="modal-title text-gold" id="newLogbookModalLabel">
                    <i class="fa-solid fa-plus-circle me-2"></i><?= h(__('form.new_logbook_title')) ?> JOTA-JOTI
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="logbook.php">
                <input type="hidden" name="csrf_token" value="<?= h(csrfToken()) ?>">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label text-jj-muted small text-uppercase"><?= h(__('form.new_logbook_year')) ?> <span class="text-danger">*</span></label>
                        <input type="number" name="new_year" class="form-control jj-input"
                               value="<?= $cur_year ?>" min="2000" max="2099" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-jj-muted small text-uppercase"><?= h(__('form.new_logbook_callsign')) ?></label>
                        <input type="text" name="new_callsign" class="form-control jj-input"
                               value="<?= h(APP_CALLSIGN) ?>" maxlength="20" placeholder="OK1KKY">
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-jj-muted small text-uppercase"><?= h(__('form.new_logbook_notes')) ?></label>
                        <textarea name="new_notes" class="form-control jj-input" rows="2"></textarea>
                    </div>
                </div>
                <div class="modal-footer" style="border-top:1px solid var(--jj-border)">
                    <button type="button" class="btn btn-jj-outline" data-bs-dismiss="modal"><?= h(__('action.cancel')) ?></button>
                    <button type="submit" name="create_logbook" class="btn btn-jj-gold">
                        <i class="fa-solid fa-plus me-1"></i><?= h(__('action.create')) ?>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php endif; ?>

<?php
// Auto-open new logbook modal if requested via URL
if ($show_new && isAdmin()): ?>
<script>
document.addEventListener('DOMContentLoaded', () => {
    const m = new bootstrap.Modal(document.getElementById('newLogbookModal'));
    m.show();
});
</script>
<?php endif; ?>

<?php require_once 'assets/inc/footer.php'; ?>
