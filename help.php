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
                            <p class="small text-jj-muted mb-0">
                                Skautské setkání prostřednictvím <strong class="text-purple">amatérského rádia</strong>.
                                Skauti z celého světa komunikují na krátkých vlnách (HF) i VHF/UHF.
                                Používají se módy SSB, CW, FT8 a další digitální módy.
                            </p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="p-3" style="background:rgba(6,182,212,.08);border:1px solid rgba(6,182,212,.2);border-radius:10px">
                            <h6 class="text-info mb-2"><span class="badge-joti me-2">JOTI</span>Jamboree on the Internet</h6>
                            <p class="small text-jj-muted mb-0">
                                Skautské setkání prostřednictvím <strong class="text-info">internetu</strong>.
                                Spojení probíhá přes platformy jako Zoom, Teams, Discord, IRC, EchoLink,
                                DMR a další digitální sítě.
                            </p>
                        </div>
                    </div>
                </div>
                <p class="mt-3 mb-0 text-jj-muted small">
                    <i class="fa-solid fa-calendar me-1 text-gold"></i>
                    JOTA-JOTI se koná každoroční <strong class="text-gold">3. víkend v říjnu</strong>.
                    Je to největší skautská akce na světě s miliony účastníků.
                </p>
            </div>
        </div>

        <div class="jj-card mb-3" id="logbook">
            <div class="jj-card-header">
                <h5 class="mb-0 text-purple"><i class="fa-solid fa-book me-2"></i>2. <?= h(__('help.section2')) ?></h5>
            </div>
            <div class="p-4">
                <p class="text-jj-muted">
                    Aplikace vytváří pro každý ročník JOTA-JOTI samostatný logbook (databázovou tabulku).
                    Díky tomu jsou záznamy z jednotlivých let odděleny a přehledně organizovány.
                </p>
                <h6 class="text-purple mt-3">Jak vytvořit nový logbook?</h6>
                <ol class="text-jj-muted small">
                    <li>Přejděte na stránku <a href="/logbook" class="text-purple">Logbook</a>.</li>
                    <li>Klikněte na tlačítko <strong class="text-gold"><i class="fa-solid fa-plus me-1"></i>Nový logbook</strong> vpravo nahoře.</li>
                    <li>V dialogovém okně zadejte rok, volací značku a případně poznámku.</li>
                    <li>Klikněte na <strong>Vytvořit logbook</strong>.</li>
                </ol>
                <h6 class="text-purple mt-3">Přepínání mezi roky</h6>
                <p class="text-jj-muted small mb-0">
                    Jednotlivé logbooky jsou dostupné jako záložky v horní části stránky Logbook.
                    Kliknutím na rok přepnete do příslušného logbooku.
                </p>
            </div>
        </div>

        <div class="jj-card mb-3" id="new-qso">
            <div class="jj-card-header">
                <h5 class="mb-0 text-purple"><i class="fa-solid fa-plus-circle me-2"></i>3. <?= h(__('help.section3')) ?></h5>
            </div>
            <div class="p-4">
                <p class="text-jj-muted">Formulář pro přidání nového spojení je umístěn ve skládací kartě v horní části logbooku.</p>
                <ol class="text-jj-muted small">
                    <li>Vyplňte <strong class="text-gold">Značku</strong> a vyberte <strong class="text-gold">Mód</strong> - tato pole jsou povinná.</li>
                    <li>Dle potřeby vyplňte ostatní pole (pásmo, QTH, země, jméno skauta, RST…).</li>
                    <li>Tlačítko <span class="badge-jota">Přidat QSO</span> se aktivuje po vyplnění povinných polí.</li>
                    <li>Klikněte na tlačítko - záznam bude uložen a čas UTC zaznamená se automaticky.</li>
                </ol>
                <div class="p-3 mt-2" style="background:rgba(245,158,11,.08);border:1px solid rgba(245,158,11,.2);border-radius:8px">
                    <i class="fa-solid fa-lightbulb text-gold me-2"></i>
                    <strong class="text-gold">Tip:</strong>
                    <span class="text-jj-muted small">Volací značka se automaticky převede na velká písmena. Čas UTC se zaznamená v momentě odeslání.</span>
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
                            <tr><th>Pole</th><th>Povinné</th><th>Popis</th></tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td><strong class="text-gold">Značka</strong></td>
                                <td><span class="badge bg-danger">Ano</span></td>
                                <td class="text-jj-muted small">Volací značka protistanice nebo přezdívka při JOTI (např. <code>OK1ABC</code>, <code>Scout_Praha</code>).</td>
                            </tr>
                            <tr>
                                <td><strong class="text-gold">Mód</strong></td>
                                <td><span class="badge bg-danger">Ano</span></td>
                                <td class="text-jj-muted small">Způsob spojení. JOTA: SSB, CW, FT8, FM… JOTI: Zoom, Teams, Discord, IRC, EchoLink…</td>
                            </tr>
                            <tr>
                                <td><strong>Pásmo</strong></td>
                                <td><span class="badge" style="background:var(--jj-border)">Ne</span></td>
                                <td class="text-jj-muted small">Frekvenční pásmo pro JOTA (20m, 40m…). U JOTI módu není relevantní.</td>
                            </tr>
                            <tr>
                                <td><strong>Země</strong></td>
                                <td><span class="badge" style="background:var(--jj-border)">Ne</span></td>
                                <td class="text-jj-muted small">Země protistanice (např. CZ, Germany, OK…). Slouží ke statistikám zemí.</td>
                            </tr>
                            <tr>
                                <td><strong>QTH</strong></td>
                                <td><span class="badge" style="background:var(--jj-border)">Ne</span></td>
                                <td class="text-jj-muted small">Lokalita protistanice (město, obec).</td>
                            </tr>
                            <tr>
                                <td><strong>Lokátor</strong></td>
                                <td><span class="badge" style="background:var(--jj-border)">Ne</span></td>
                                <td class="text-jj-muted small">Maidenhead lokátor protistanice (např. <code>JO70DB</code>).</td>
                            </tr>
                            <tr>
                                <td><strong>RST TX / RX</strong></td>
                                <td><span class="badge" style="background:var(--jj-border)">Ne</span></td>
                                <td class="text-jj-muted small">Reporty signálu - TX odeslán protistanici, RX přijat od protistanice. Typicky <code>59</code> (SSB) nebo <code>599</code> (CW).</td>
                            </tr>
                            <tr>
                                <td><strong>Jméno skauta</strong></td>
                                <td><span class="badge" style="background:var(--jj-border)">Ne</span></td>
                                <td class="text-jj-muted small">Jméno skautského operátora na protistanici. Přidává skautský rozměr záznamu.</td>
                            </tr>
                            <tr>
                                <td><strong>Poznámka</strong></td>
                                <td><span class="badge" style="background:var(--jj-border)">Ne</span></td>
                                <td class="text-jj-muted small">Libovolná poznámka ke spojení.</td>
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
                    Klikněte na ikonu <span style="background:rgba(245,158,11,.15);border:1px solid rgba(245,158,11,.3);color:#fcd34d;padding:2px 8px;border-radius:4px"><i class="fa-solid fa-pen-to-square"></i></span>
                    ve sloupci Akce u záznamu, který chcete upravit.
                </p>
                <ul class="text-jj-muted small">
                    <li>Formulář se automaticky vyplní daty z daného záznamu a zvýrazní se zlatým rámečkem.</li>
                    <li>Proveďte požadované změny a klikněte na <strong>Uložit změny</strong>.</li>
                    <li>Čas (UTC) lze při úpravě změnit - pole je dostupné pro editaci.</li>
                    <li>Pro zrušení úpravy klikněte na tlačítko <i class="fa-solid fa-xmark"></i> (vymazat formulář).</li>
                </ul>
            </div>
        </div>

        <div class="jj-card mb-3" id="delete">
            <div class="jj-card-header">
                <h5 class="mb-0 text-purple"><i class="fa-solid fa-trash me-2"></i>6. <?= h(__('help.section6')) ?></h5>
            </div>
            <div class="p-4">
                <p class="text-jj-muted">
                    Klikněte na ikonu <span style="background:rgba(239,68,68,.15);border:1px solid rgba(239,68,68,.3);color:#fca5a5;padding:2px 8px;border-radius:4px"><i class="fa-solid fa-trash"></i></span>
                    u záznamu, který chcete smazat.
                </p>
                <div class="p-3" style="background:rgba(239,68,68,.08);border:1px solid rgba(239,68,68,.2);border-radius:8px">
                    <i class="fa-solid fa-triangle-exclamation text-danger me-2"></i>
                    <span class="text-jj-muted small">Zobrazí se potvrzovací dialog. Po potvrzení je záznam <strong class="text-danger">trvale smazán</strong> a nelze jej obnovit.</span>
                </div>
            </div>
        </div>

        <div class="jj-card mb-3" id="export">
            <div class="jj-card-header">
                <h5 class="mb-0 text-purple"><i class="fa-solid fa-download me-2"></i>7. <?= h(__('help.section7')) ?></h5>
            </div>
            <div class="p-4">
                <p class="text-jj-muted">Data z logbooku lze exportovat ve dvou formátech:</p>
                <div class="row g-3">
                    <div class="col-md-6">
                        <div class="p-3" style="background:rgba(124,58,237,.08);border:1px solid rgba(124,58,237,.2);border-radius:10px">
                            <h6 class="text-purple mb-2"><i class="fa-solid fa-download me-2"></i>ADIF</h6>
                            <p class="small text-jj-muted mb-0">
                                Standardní formát pro amatérské radiostanice (Amateur Data Interchange Format).
                                Kompatibilní s programy jako Log4OM, HRD, WSJTX, LoTW a dalšími.
                            </p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="p-3" style="background:rgba(6,182,212,.08);border:1px solid rgba(6,182,212,.2);border-radius:10px">
                            <h6 class="text-info mb-2"><i class="fa-solid fa-file-excel me-2"></i>Excel</h6>
                            <p class="small text-jj-muted mb-0">
                                Exportuje pravý soubor Excel (.xlsx), kompatibilní s Microsoft Excel i LibreOffice Calc.
                                Diakritika je správně zachována bez nutnosti konverze.
                            </p>
                        </div>
                    </div>
                </div>
                <p class="small text-jj-muted mt-3 mb-0">
                    Tlačítka <strong>ADIF</strong> a <strong>Excel</strong> najdete v pravé části záhlaví tabulky záznamů.
                </p>
            </div>
        </div>

        <div class="jj-card mb-3" id="tips">
            <div class="jj-card-header">
                <h5 class="mb-0 text-gold"><i class="fa-solid fa-lightbulb me-2"></i>8. <?= h(__('help.section8')) ?></h5>
            </div>
            <div class="p-4">
                <ul class="text-jj-muted small" style="line-height:1.9">
                    <li>🔍 <strong>Rychlé hledání:</strong> Pole hledání v záhlaví tabulky filtruje záznamy v reálném čase - bez načítání stránky.</li>
                    <li>🕐 <strong>UTC hodiny</strong> jsou zobrazeny v pravém rohu navigační lišty a automaticky tikají.</li>
                    <li>⚡ <strong>Automatické velká písmena</strong> v poli Značka - nemusíte přepínat klávesnici.</li>
                    <li>📻 <strong>JOTA/JOTI detekce:</strong> Při výběru internetového módu (Zoom, Teams…) se automaticky přizpůsobí popisky polí a RST pole se zeslaví.</li>
                    <li>📋 <strong>Opakující se údaje</strong> (QTH) zůstávají v polích i po odeslání formuláře. Po vyplnění a odeslání však je formulář resetován.</li>
                    <li>🗑️ <strong>Tlačítko × (vymazat)</strong> v formuláři smaže všechna vyplněná pole bez uložení.</li>
                    <li>📅 <strong>Logbook = rok:</strong> Pro každý ročník JOTA-JOTI se vytváří samostatná databázová tabulka. Záznamy jsou tak čistě odděleny.</li>
                </ul>
            </div>
        </div>

        <div class="jj-card mb-3" id="roles">
            <div class="jj-card-header">
                <h5 class="mb-0 text-purple"><i class="fa-solid fa-user-shield me-2"></i>9. Uživatelské role</h5>
            </div>
            <div class="p-4">
                <p class="text-jj-muted">Aplikace rozlišuje tři úrovně přístupu:</p>
                <div class="row g-3">
                    <div class="col-md-4">
                        <div class="p-3" style="background:rgba(100,116,139,.08);border:1px solid rgba(100,116,139,.2);border-radius:10px">
                            <h6 class="mb-2"><i class="fa-solid fa-eye me-2 text-jj-muted"></i>Návštěvník</h6>
                            <p class="small text-jj-muted mb-0">Může zobrazit všechna data a statistiky, ale nemůže přidávat ani upravovat záznamy.</p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="p-3" style="background:rgba(124,58,237,.08);border:1px solid rgba(124,58,237,.2);border-radius:10px">
                            <h6 class="text-purple mb-2"><i class="fa-solid fa-user me-2"></i>Uživatel</h6>
                            <p class="small text-jj-muted mb-0">Může přidávat a upravovat QSO v aktuálním roce. Nemá přístup ke konfiguraci ani k logbookům minulých let.</p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="p-3" style="background:rgba(245,158,11,.08);border:1px solid rgba(245,158,11,.2);border-radius:10px">
                            <h6 class="text-gold mb-2"><i class="fa-solid fa-user-gear me-2"></i>Administrátor</h6>
                            <p class="small text-jj-muted mb-0">Plný přístup – zakládání a mazání logbooků, úprava QSO z minulých let i konfigurace aplikace.</p>
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
