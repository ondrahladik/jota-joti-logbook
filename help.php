<?php
require_once 'assets/inc/config.php';
$page_title = __('help.title');
require_once 'assets/inc/header.php';
?>

<div class="row g-4 py-3">
    
    <div class="col-lg-3 d-none d-lg-block">
        <div class="jj-card" style="position:sticky;top:80px">
            <div class="jj-card-header">
                <h6 class="mb-0 text-purple"><i class="fa-solid fa-list me-2"></i><?= h(__('help.toc')) ?></h6>
            </div>
            <div class="p-2">
                <nav class="nav flex-column">
                    <a class="nav-link text-jj-muted py-1" href="#what-is-jota-joti" style="font-size:.85rem">1. <?= h(__('help.section1')) ?></a>
                    <a class="nav-link text-jj-muted py-1" href="#logbook" style="font-size:.85rem">2. <?= h(__('help.section2')) ?></a>
                    <a class="nav-link text-jj-muted py-1" href="#new-qso" style="font-size:.85rem">3. <?= h(__('help.section3')) ?></a>
                    <a class="nav-link text-jj-muted py-1" href="#fields" style="font-size:.85rem">4. <?= h(__('help.section4')) ?></a>
                    <a class="nav-link text-jj-muted py-1" href="#edit" style="font-size:.85rem">5. <?= h(__('help.section5')) ?></a>
                    <a class="nav-link text-jj-muted py-1" href="#delete" style="font-size:.85rem">6. <?= h(__('help.section6')) ?></a>
                    <a class="nav-link text-jj-muted py-1" href="#export" style="font-size:.85rem">7. <?= h(__('help.section7')) ?></a>
                    <a class="nav-link text-jj-muted py-1" href="#tips" style="font-size:.85rem">8. <?= h(__('help.section8')) ?></a>
                    <a class="nav-link text-jj-muted py-1" href="#roles" style="font-size:.85rem">9. <?= h(__('help.section9')) ?></a>
                </nav>
            </div>
        </div>
    </div>

    <div class="col-lg-9">

        <div class="jj-hero mb-4" style="padding:1.5rem 2rem">
            <h1 style="font-size:1.6rem"><i class="fa-solid fa-circle-question me-2 text-purple"></i><?= h(__('help.title')) ?></h1>
            <p class="mb-0 text-jj-muted"><?= h(__('help.subtitle', ['callsign' => APP_CALLSIGN])) ?></p>
        </div>

        <div class="jj-card mb-3" id="what-is-jota-joti">
            <div class="jj-card-header">
                <h5 class="mb-0 text-gold"><i class="fa-solid fa-campground me-2"></i>1. <?= h(__('help.section1')) ?></h5>
            </div>
            <div class="p-4">
                <div class="row g-3">
                    <div class="col-md-6">
                        <div class="p-3" style="background:rgba(124,58,237,.08);border:1px solid rgba(124,58,237,.2);border-radius:10px">
                            <h6 class="text-purple mb-2"><span class="badge-jota me-2">JOTA</span>Jamboree on the Air</h6>
                            <p class="small text-jj-muted mb-0"><?= h(__('help.jota.desc')) ?></p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="p-3" style="background:rgba(6,182,212,.08);border:1px solid rgba(6,182,212,.2);border-radius:10px">
                            <h6 class="text-info mb-2"><span class="badge-joti me-2">JOTI</span>Jamboree on the Internet</h6>
                            <p class="small text-jj-muted mb-0"><?= h(__('help.joti.desc')) ?></p>
                        </div>
                    </div>
                </div>
                <p class="mt-3 mb-0 text-jj-muted small">
                    <i class="fa-solid fa-calendar me-1 text-gold"></i>
                    <?= h(__('help.event.date')) ?>
                </p>
            </div>
        </div>

        <div class="jj-card mb-3" id="logbook">
            <div class="jj-card-header">
                <h5 class="mb-0 text-purple"><i class="fa-solid fa-book me-2"></i>2. <?= h(__('help.section2')) ?></h5>
            </div>
            <div class="p-4">
                <p class="text-jj-muted"><?= h(__('help.logbook.desc')) ?></p>
                <h6 class="text-purple mt-3"><?= h(__('help.logbook.how')) ?></h6>
                <ol class="text-jj-muted small">
                    <li><?= h(__('help.logbook.s1')) ?></li>
                    <li><?= h(__('help.logbook.s2')) ?></li>
                    <li><?= h(__('help.logbook.s3')) ?></li>
                    <li><?= h(__('help.logbook.s4')) ?></li>
                </ol>
                <h6 class="text-purple mt-3"><?= h(__('help.logbook.tabs')) ?></h6>
                <p class="text-jj-muted small mb-0"><?= h(__('help.logbook.tabs.desc')) ?></p>
            </div>
        </div>

        <div class="jj-card mb-3" id="new-qso">
            <div class="jj-card-header">
                <h5 class="mb-0 text-purple"><i class="fa-solid fa-plus-circle me-2"></i>3. <?= h(__('help.section3')) ?></h5>
            </div>
            <div class="p-4">
                <p class="text-jj-muted"><?= h(__('help.qso.desc')) ?></p>
                <ol class="text-jj-muted small">
                    <li><?= h(__('help.qso.s1')) ?></li>
                    <li><?= h(__('help.qso.s2')) ?></li>
                    <li><?= h(__('help.qso.s3')) ?></li>
                    <li><?= h(__('help.qso.s4')) ?></li>
                </ol>
                <div class="p-3 mt-2" style="background:rgba(245,158,11,.08);border:1px solid rgba(245,158,11,.2);border-radius:8px">
                    <i class="fa-solid fa-lightbulb text-gold me-2"></i>
                    <strong class="text-gold">Tip:</strong>
                    <span class="text-jj-muted small"><?= h(__('help.qso.tip')) ?></span>
                </div>
            </div>
        </div>

        <div class="jj-card mb-3" id="fields">
            <div class="jj-card-header">
                <h5 class="mb-0 text-purple"><i class="fa-solid fa-table-list me-2"></i>4. <?= h(__('help.section4')) ?></h5>
            </div>
            <div class="p-4">
                <div class="table-responsive">
                    <table class="table jj-table">
                        <thead>
                            <tr>
                                <th><?= h(__('help.fields.col.name')) ?></th>
                                <th><?= h(__('help.fields.col.req')) ?></th>
                                <th><?= h(__('help.fields.col.desc')) ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td><strong class="text-gold"><?= h(__('qso.callsign')) ?></strong></td>
                                <td><span class="badge bg-danger"><?= h(__('help.fields.yes')) ?></span></td>
                                <td class="text-jj-muted small"><?= h(__('help.fields.callsign')) ?></td>
                            </tr>
                            <tr>
                                <td><strong class="text-gold"><?= h(__('qso.mode')) ?></strong></td>
                                <td><span class="badge bg-danger"><?= h(__('help.fields.yes')) ?></span></td>
                                <td class="text-jj-muted small"><?= h(__('help.fields.mode')) ?></td>
                            </tr>
                            <tr>
                                <td><strong><?= h(__('qso.band')) ?></strong></td>
                                <td><span class="badge bg-danger"><?= h(__('help.fields.yes')) ?></span></td>
                                <td class="text-jj-muted small"><?= h(__('help.fields.band')) ?></td>
                            </tr>
                            <tr>
                                <td><strong><?= h(__('qso.qth')) ?></strong></td>
                                <td><span class="badge" style="background:var(--jj-border)"><?= h(__('help.fields.no')) ?></span></td>
                                <td class="text-jj-muted small"><?= h(__('help.fields.qth_country')) ?></td>
                            </tr>
                            <tr>
                                <td><strong><?= h(__('qso.locator')) ?></strong></td>
                                <td><span class="badge" style="background:var(--jj-border)"><?= h(__('help.fields.no')) ?></span></td>
                                <td class="text-jj-muted small"><?= h(__('help.fields.locator')) ?></td>
                            </tr>
                            <tr>
                                <td><strong>RST TX / RX</strong></td>
                                <td><span class="badge" style="background:var(--jj-border)"><?= h(__('help.fields.no')) ?></span></td>
                                <td class="text-jj-muted small"><?= h(__('help.fields.rst')) ?></td>
                            </tr>
                            <tr>
                                <td><strong><?= h(__('qso.scout_name')) ?></strong></td>
                                <td><span class="badge" style="background:var(--jj-border)"><?= h(__('help.fields.no')) ?></span></td>
                                <td class="text-jj-muted small"><?= h(__('help.fields.scout')) ?></td>
                            </tr>
                            <tr>
                                <td><strong><?= h(__('qso.note')) ?></strong></td>
                                <td><span class="badge" style="background:var(--jj-border)"><?= h(__('help.fields.no')) ?></span></td>
                                <td class="text-jj-muted small"><?= h(__('help.fields.note_desc')) ?></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="jj-card mb-3" id="edit">
            <div class="jj-card-header">
                <h5 class="mb-0 text-purple"><i class="fa-solid fa-pen-to-square me-2"></i>5. <?= h(__('help.section5')) ?></h5>
            </div>
            <div class="p-4">
                <p class="text-jj-muted">
                    <?= h(__('help.edit.desc')) ?>
                    <span style="background:rgba(245,158,11,.15);border:1px solid rgba(245,158,11,.3);color:#fcd34d;padding:2px 8px;border-radius:4px"><i class="fa-solid fa-pen-to-square"></i></span>
                </p>
                <ul class="text-jj-muted small">
                    <li><?= h(__('help.edit.i1')) ?></li>
                    <li><?= h(__('help.edit.i2')) ?></li>
                    <li><?= h(__('help.edit.i3')) ?></li>
                    <li><?= h(__('help.edit.i4')) ?></li>
                </ul>
            </div>
        </div>

        <div class="jj-card mb-3" id="delete">
            <div class="jj-card-header">
                <h5 class="mb-0 text-purple"><i class="fa-solid fa-trash me-2"></i>6. <?= h(__('help.section6')) ?></h5>
            </div>
            <div class="p-4">
                <p class="text-jj-muted">
                    <?= h(__('help.delete.desc')) ?>
                    <span style="background:rgba(239,68,68,.15);border:1px solid rgba(239,68,68,.3);color:#fca5a5;padding:2px 8px;border-radius:4px"><i class="fa-solid fa-trash"></i></span>
                </p>
                <div class="p-3" style="background:rgba(239,68,68,.08);border:1px solid rgba(239,68,68,.2);border-radius:8px">
                    <i class="fa-solid fa-triangle-exclamation text-danger me-2"></i>
                    <span class="text-jj-muted small"><?= h(__('help.delete.warning')) ?></span>
                </div>
            </div>
        </div>

        <div class="jj-card mb-3" id="export">
            <div class="jj-card-header">
                <h5 class="mb-0 text-purple"><i class="fa-solid fa-download me-2"></i>7. <?= h(__('help.section7')) ?></h5>
            </div>
            <div class="p-4">
                <p class="text-jj-muted"><?= h(__('help.export.desc')) ?></p>
                <div class="row g-3">
                    <div class="col-md-6">
                        <div class="p-3" style="background:rgba(124,58,237,.08);border:1px solid rgba(124,58,237,.2);border-radius:10px">
                            <h6 class="text-purple mb-2"><i class="fa-solid fa-download me-2"></i>ADIF</h6>
                            <p class="small text-jj-muted mb-0"><?= h(__('help.export.adif')) ?></p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="p-3" style="background:rgba(6,182,212,.08);border:1px solid rgba(6,182,212,.2);border-radius:10px">
                            <h6 class="text-info mb-2"><i class="fa-solid fa-file-excel me-2"></i>Excel</h6>
                            <p class="small text-jj-muted mb-0"><?= h(__('help.export.xlsx')) ?></p>
                        </div>
                    </div>
                </div>
                <p class="small text-jj-muted mt-3 mb-0">
                    <?= h(__('help.export.note')) ?>
                </p>
            </div>
        </div>

        <div class="jj-card mb-3" id="tips">
            <div class="jj-card-header">
                <h5 class="mb-0 text-gold"><i class="fa-solid fa-lightbulb me-2"></i>8. <?= h(__('help.section8')) ?></h5>
            </div>
            <div class="p-4">
                <ul class="text-jj-muted small" style="line-height:1.9">
                    <li>&#x1F50D; <?= h(__('help.tips.search')) ?></li>
                    <li>&#x1F550; <?= h(__('help.tips.utc')) ?></li>
                    <li>&#x26A1; <?= h(__('help.tips.uppercase')) ?></li>
                    <li>&#x1F4FB; <?= h(__('help.tips.joti')) ?></li>
                    <li>&#x1F4CB; <?= h(__('help.tips.repeat')) ?></li>
                    <li>&#x1F5D1; <?= h(__('help.tips.clear')) ?></li>
                    <li>&#x1F4C5; <?= h(__('help.tips.logbook')) ?></li>
                </ul>
            </div>
        </div>

        <div class="jj-card mb-3" id="roles">
            <div class="jj-card-header">
                <h5 class="mb-0 text-purple"><i class="fa-solid fa-user-shield me-2"></i>9. <?= h(__('help.section9')) ?></h5>
            </div>
            <div class="p-4">
                <p class="text-jj-muted"><?= h(__('help.roles.desc')) ?></p>
                <div class="row g-3">
                    <div class="col-md-4">
                        <div class="p-3" style="background:rgba(100,116,139,.08);border:1px solid rgba(100,116,139,.2);border-radius:10px">
                            <h6 class="mb-2"><i class="fa-solid fa-eye me-2 text-jj-muted"></i><?= h(__('help.roles.visitor.title')) ?></h6>
                            <p class="small text-jj-muted mb-0"><?= h(__('help.roles.visitor.desc')) ?></p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="p-3" style="background:rgba(124,58,237,.08);border:1px solid rgba(124,58,237,.2);border-radius:10px">
                            <h6 class="text-purple mb-2"><i class="fa-solid fa-user me-2"></i><?= h(__('help.roles.user.title')) ?></h6>
                            <p class="small text-jj-muted mb-0"><?= h(__('help.roles.user.desc')) ?></p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="p-3" style="background:rgba(245,158,11,.08);border:1px solid rgba(245,158,11,.2);border-radius:10px">
                            <h6 class="text-gold mb-2"><i class="fa-solid fa-user-gear me-2"></i><?= h(__('help.roles.admin.title')) ?></h6>
                            <p class="small text-jj-muted mb-0"><?= h(__('help.roles.admin.desc')) ?></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="text-center py-3">
            <a href="/logbook" class="btn btn-jj-primary me-2">
                <i class="fa-solid fa-satellite-dish me-1"></i><?= h(__('help.goto_logbook')) ?>
            </a>
            <a href="/" class="btn btn-jj-outline">
                <i class="fa-solid fa-house me-1"></i><?= h(__('help.goto_home')) ?>
            </a>
        </div>
    </div>
</div>

<?php require_once 'assets/inc/footer.php'; ?>