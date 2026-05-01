<?php
require_once __DIR__ . '/config.php';

function getConnection(): mysqli {
    static $conn = null;
    if ($conn === null) {
        $conn = @new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
        if ($conn->connect_error) {
            $db_error = $conn->connect_error;
            require __DIR__ . '/../errors/db-error.php';
            exit;
        }
        $conn->set_charset('utf8mb4');
        ensureLogbooksTable($conn);
    }
    return $conn;
}

function tryConnection(): ?mysqli {
    static $conn = null;
    if ($conn === null) {
        $c = @new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
        if ($c->connect_error) return null;
        $c->set_charset('utf8mb4');
        ensureLogbooksTable($c);
        $conn = $c;
    }
    return $conn;
}

function ensureLogbooksTable(mysqli $conn): void {
    $conn->query("CREATE TABLE IF NOT EXISTS `logbooks` (
        `id`         INT AUTO_INCREMENT PRIMARY KEY,
        `year`       INT UNIQUE NOT NULL,
        `event_name` VARCHAR(255) DEFAULT '',
        `callsign`   VARCHAR(20)  DEFAULT '',
        `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        `notes`      TEXT
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
}

function getLogbooks(mysqli $conn): array {
    $result = $conn->query("SELECT * FROM `logbooks` ORDER BY `year` DESC");
    return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
}

function getLogbookInfo(mysqli $conn, int $year): ?array {
    $stmt = $conn->prepare("SELECT * FROM `logbooks` WHERE `year` = ?");
    $stmt->bind_param("i", $year);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->num_rows > 0 ? $result->fetch_assoc() : null;
}

function logbookExists(mysqli $conn, int $year): bool {
    return getLogbookInfo($conn, $year) !== null;
}

function createLogbook(mysqli $conn, int $year, string $callsign = '', string $notes = ''): bool {
    if (logbookExists($conn, $year)) return false;

    $event_name = "JOTA-JOTI $year";
    $callsign   = $callsign ?: APP_CALLSIGN;

    $stmt = $conn->prepare("INSERT INTO `logbooks` (`year`, `event_name`, `callsign`, `notes`) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("isss", $year, $event_name, $callsign, $notes);
    if (!$stmt->execute()) return false;

    $table = "log_$year";
    $conn->query("CREATE TABLE IF NOT EXISTS `$table` (
        `id`          INT AUTO_INCREMENT PRIMARY KEY,
        `timestamp`   DATETIME     NOT NULL,
        `callsign`    VARCHAR(20)  NOT NULL,
        `band`        VARCHAR(10)  DEFAULT NULL,
        `mode`        VARCHAR(20)  NOT NULL,
        `qth`         VARCHAR(100) DEFAULT NULL,
        `locator`     VARCHAR(20)  DEFAULT NULL,
        `rst_tx`      VARCHAR(10)  DEFAULT NULL,
        `rst_rx`      VARCHAR(10)  DEFAULT NULL,
        `scout_name`  VARCHAR(100) DEFAULT NULL,
        `scout_group` VARCHAR(100) DEFAULT NULL,
        `country`     VARCHAR(100) DEFAULT NULL,
        `note`        TEXT         DEFAULT NULL
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

    return true;
}

function deleteLogbook(mysqli $conn, int $year): bool {
    $stmt = $conn->prepare("DELETE FROM `logbooks` WHERE `year` = ?");
    $stmt->bind_param("i", $year);
    if (!$stmt->execute()) return false;
    $table = "log_$year";
    $conn->query("DROP TABLE IF EXISTS `$table`");
    return true;
}

function getRecords(mysqli $conn, int $year, string $search = ''): array {
    $table = "log_$year";
    if ($search !== '') {
        $like = "%$search%";
        $stmt = $conn->prepare(
            "SELECT * FROM `$table`
             WHERE `callsign` LIKE ? OR `qth` LIKE ?
                OR `scout_name` LIKE ? OR `note` LIKE ? OR `mode` LIKE ?
             ORDER BY `id` DESC"
        );
        $stmt->bind_param("sssss", $like, $like, $like, $like, $like);
    } else {
        $stmt = $conn->prepare("SELECT * FROM `$table` ORDER BY `id` DESC");
    }
    $stmt->execute();
    return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}

function getStats(mysqli $conn, int $year): array {
    $table  = "log_$year";
    $stats  = ['total' => 0, 'by_mode' => [], 'by_band' => []];

    $r = $conn->query("SELECT COUNT(*) c FROM `$table`");
    if ($r) {
        $stats['total'] = (int)$r->fetch_assoc()['c'];
    }

    $r = $conn->query("SELECT `mode`, COUNT(*) cnt FROM `$table` GROUP BY `mode` ORDER BY cnt DESC LIMIT 8");
    if ($r) $stats['by_mode'] = $r->fetch_all(MYSQLI_ASSOC);

    $r = $conn->query("SELECT `band`, COUNT(*) cnt FROM `$table` WHERE `band` IS NOT NULL AND `band`!='' GROUP BY `band` ORDER BY cnt DESC LIMIT 8");
    if ($r) $stats['by_band'] = $r->fetch_all(MYSQLI_ASSOC);

    return $stats;
}

function insertRecord(mysqli $conn, int $year, array $d): bool {
    $table = "log_$year";
    date_default_timezone_set('UTC');
    $ts       = date('Y-m-d H:i:s');
    $callsign = strtoupper(trim($d['callsign']));
    $band     = $d['band']    ?: null;
    $mode     = $d['mode'];
    $qth      = $d['qth']     ?: null;
    $locator  = $d['locator'] ?: null;
    $rst_tx   = $d['rst_tx']  ?: null;
    $rst_rx   = $d['rst_rx']  ?: null;
    $scout_name = $d['scout_name'] ?: null;
    $note     = $d['note']    ?: null;

    $stmt = $conn->prepare(
        "INSERT INTO `$table`
         (`timestamp`,`callsign`,`band`,`mode`,`qth`,`locator`,`rst_tx`,`rst_rx`,`scout_name`,`note`)
         VALUES (?,?,?,?,?,?,?,?,?,?)"
    );
    $stmt->bind_param("ssssssssss", $ts, $callsign, $band, $mode, $qth, $locator, $rst_tx, $rst_rx, $scout_name, $note);
    return $stmt->execute();
}

function updateRecord(mysqli $conn, int $year, int $id, array $d): bool {
    $table    = "log_$year";
    $callsign = strtoupper(trim($d['callsign']));
    $band     = $d['band']    ?: null;
    $mode     = $d['mode'];
    $qth      = $d['qth']     ?: null;
    $locator  = $d['locator'] ?: null;
    $rst_tx   = $d['rst_tx']  ?: null;
    $rst_rx   = $d['rst_rx']  ?: null;
    $scout_name = $d['scout_name'] ?: null;
    $note     = $d['note']    ?: null;

    // Allow updating timestamp when provided
    $ts = null;
    if (!empty($d['qso_time'])) {
        $parsed = DateTime::createFromFormat('Y-m-d\TH:i:s', $d['qso_time'], new DateTimeZone('UTC'));
        if (!$parsed) $parsed = DateTime::createFromFormat('Y-m-d\TH:i', $d['qso_time'], new DateTimeZone('UTC'));
        if ($parsed) $ts = $parsed->format('Y-m-d H:i:s');
    }

    if ($ts !== null) {
        $stmt = $conn->prepare(
            "UPDATE `$table`
             SET `timestamp`=?,`callsign`=?,`band`=?,`mode`=?,`qth`=?,`locator`=?,`rst_tx`=?,`rst_rx`=?,
                 `scout_name`=?,`note`=?
             WHERE `id`=?"
        );
        $stmt->bind_param("ssssssssssi", $ts, $callsign, $band, $mode, $qth, $locator, $rst_tx, $rst_rx, $scout_name, $note, $id);
    } else {
        $stmt = $conn->prepare(
            "UPDATE `$table`
             SET `callsign`=?,`band`=?,`mode`=?,`qth`=?,`locator`=?,`rst_tx`=?,`rst_rx`=?,
                 `scout_name`=?,`note`=?
             WHERE `id`=?"
        );
        $stmt->bind_param("sssssssssi", $callsign, $band, $mode, $qth, $locator, $rst_tx, $rst_rx, $scout_name, $note, $id);
    }
    return $stmt->execute();
}

function deleteRecord(mysqli $conn, int $year, int $id): bool {
    $table = "log_$year";
    $stmt  = $conn->prepare("DELETE FROM `$table` WHERE `id`=?");
    $stmt->bind_param("i", $id);
    return $stmt->execute();
}

function exportADIF(mysqli $conn, int $year): string {
    $table = "log_$year";
    $result = $conn->query("SELECT * FROM `$table` ORDER BY `timestamp` ASC");

    $out  = "<ADIF_VER:5>3.1.0\r\n";
    $out .= "<PROGRAMID:11>JOTAJOTILOG\r\n";
    $out .= "<EOH>\r\n\r\n";

    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $dt   = new DateTime($row['timestamp']);
            $date = $dt->format('Ymd');
            $time = $dt->format('His');
            $f = fn(string $tag, ?string $val) => $val ? "<$tag:" . strlen($val) . ">$val " : '';

            $out .= "<QSO_DATE:" . strlen($date) . ">$date ";
            $out .= "<TIME_ON:" . strlen($time) . ">$time ";
            $out .= "<CALL:" . strlen($row['callsign']) . ">{$row['callsign']} ";
            $out .= $f('BAND',      $row['band']);
            $out .= $f('MODE',      $row['mode']);
            $out .= $f('RST_SENT',  $row['rst_tx']);
            $out .= $f('RST_RCVD',  $row['rst_rx']);
            $out .= $f('QTH',       $row['qth']);
            $out .= $f('GRIDSQUARE',$row['locator']);
            $out .= $f('COUNTRY',   $row['country']);
            $out .= $f('NAME',      $row['scout_name']);
            $out .= $f('COMMENT',   $row['note']);
            $out .= "<EOR>\r\n";
        }
    }
    return $out;
}

function exportXLSX(mysqli $conn, int $year): string {
    $table = "log_$year";
    $result = $conn->query("SELECT * FROM `$table` ORDER BY `timestamp` ASC");

    $rows = [[
        __('qso.id'), __('qso.time_utc'), __('qso.callsign'), __('qso.band'),
        __('qso.mode'), __('qso.qth'), __('qso.locator'), __('qso.rst_tx'), __('qso.rst_rx'),
        __('qso.scout_name'), __('qso.country'), __('qso.note'),
    ]];

    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $rows[] = [
                $row['id'], $row['timestamp'], $row['callsign'], $row['band'] ?? '',
                $row['mode'], $row['qth'] ?? '', $row['locator'] ?? '',
                $row['rst_tx'] ?? '', $row['rst_rx'] ?? '',
                $row['scout_name'] ?? '',
                $row['country'] ?? '', str_replace(["\r","\n"], ' ', $row['note'] ?? ''),
            ];
        }
    }
    return _generateXlsx($rows, "JOTA-JOTI $year");
}

function _generateXlsx(array $rows, string $sheetName = 'Sheet1'): string {
    $esc = fn($v) => htmlspecialchars((string)$v, ENT_XML1, 'UTF-8');

    // Build shared strings
    $strings = [];
    $strIdx  = [];
    foreach ($rows as $row) {
        foreach ($row as $cell) {
            $s = (string)$cell;
            if (!isset($strIdx[$s])) { $strIdx[$s] = count($strings); $strings[] = $s; }
        }
    }

    // Sheet XML
    $sheetData = '';
    foreach ($rows as $ri => $row) {
        $sheetData .= '<row r="' . ($ri + 1) . '">';
        foreach ($row as $ci => $cell) {
            $col = _xlsxCol($ci);
            $ref = $col . ($ri + 1);
            $si  = $strIdx[(string)$cell];
            $s   = ($ri === 0) ? ' s="1"' : '';
            $sheetData .= '<c r="' . $ref . '" t="s"' . $s . '><v>' . $si . '</v></c>';
        }
        $sheetData .= '</row>';
    }

    $ssXml = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
        . '<sst xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main" count="' . count($strings) . '" uniqueCount="' . count($strings) . '">';
    foreach ($strings as $s) { $ssXml .= '<si><t xml:space="preserve">' . $esc($s) . '</t></si>'; }
    $ssXml .= '</sst>';

    $files = [
        '[Content_Types].xml' =>
            '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            . '<Types xmlns="http://schemas.openxmlformats.org/package/2006/content-types">'
            . '<Default Extension="rels" ContentType="application/vnd.openxmlformats-package.relationships+xml"/>'
            . '<Default Extension="xml" ContentType="application/xml"/>'
            . '<Override PartName="/xl/workbook.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet.main+xml"/>'
            . '<Override PartName="/xl/worksheets/sheet1.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.worksheet+xml"/>'
            . '<Override PartName="/xl/sharedStrings.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.sharedStrings+xml"/>'
            . '<Override PartName="/xl/styles.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.styles+xml"/>'
            . '</Types>',
        '_rels/.rels' =>
            '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            . '<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">'
            . '<Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/officeDocument" Target="xl/workbook.xml"/>'
            . '</Relationships>',
        'xl/workbook.xml' =>
            '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            . '<workbook xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main" xmlns:r="http://schemas.openxmlformats.org/officeDocument/2006/relationships">'
            . '<sheets><sheet name="' . $esc($sheetName) . '" sheetId="1" r:id="rId1"/></sheets>'
            . '</workbook>',
        'xl/_rels/workbook.xml.rels' =>
            '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            . '<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">'
            . '<Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/worksheet" Target="worksheets/sheet1.xml"/>'
            . '<Relationship Id="rId2" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/styles" Target="styles.xml"/>'
            . '<Relationship Id="rId3" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/sharedStrings" Target="sharedStrings.xml"/>'
            . '</Relationships>',
        'xl/styles.xml' =>
            '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            . '<styleSheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main">'
            . '<fonts count="2">'
            . '<font><sz val="11"/><name val="Calibri"/><family val="2"/></font>'
            . '<font><b/><sz val="11"/><name val="Calibri"/><family val="2"/></font>'
            . '</fonts>'
            . '<fills count="2"><fill><patternFill patternType="none"/></fill><fill><patternFill patternType="gray125"/></fill></fills>'
            . '<borders count="1"><border><left/><right/><top/><bottom/><diagonal/></border></borders>'
            . '<cellStyleXfs count="1"><xf numFmtId="0" fontId="0" fillId="0" borderId="0"/></cellStyleXfs>'
            . '<cellXfs count="2">'
            . '<xf numFmtId="0" fontId="0" fillId="0" borderId="0" xfId="0"/>'
            . '<xf numFmtId="0" fontId="1" fillId="0" borderId="0" xfId="0" applyFont="1"/>'
            . '</cellXfs>'
            . '<cellStyles count="1"><cellStyle name="Normal" xfId="0" builtinId="0"/></cellStyles>'
            . '</styleSheet>',
        'xl/sharedStrings.xml' => $ssXml,
        'xl/worksheets/sheet1.xml' =>
            '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            . '<worksheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main">'
            . '<sheetData>' . $sheetData . '</sheetData>'
            . '</worksheet>',
    ];

    $tmp = tempnam(sys_get_temp_dir(), 'jj_xlsx_');
    $zip = new ZipArchive();
    $zip->open($tmp, ZipArchive::OVERWRITE);
    foreach ($files as $name => $content) { $zip->addFromString($name, $content); }
    $zip->close();
    $data = file_get_contents($tmp);
    unlink($tmp);
    return $data;
}

function _xlsxCol(int $col): string {
    $name = '';
    for ($n = $col + 1; $n > 0; $n = (int)(($n - 1) / 26)) {
        $name = chr(65 + (($n - 1) % 26)) . $name;
    }
    return $name;
}
