<?php
http_response_code(503);
$_errCode      = '';
$_errIconHtml  = '<div style="font-size:4rem;filter:drop-shadow(0 0 20px #ef4444)"><i class="fa-solid fa-database" style="color:var(--jj-danger)"></i></div>';
$_errTitleKey  = 'db.title';
$_errDescKey   = 'db.desc';
$_errButtons   = [
    ['href' => '/config', 'icon' => 'fa-gear', 'key' => 'btn.config', 'class' => 'btn-jj-primary'],
];
require __DIR__ . '/_layout.php';
