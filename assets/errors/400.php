<?php
http_response_code(400);
$_errCode      = '400';
$_errCodeColor = 'var(--jj-warning)';
$_errIconHtml  = '<div style="font-size:5rem">&#x26A0;&#xFE0E;</div>';
$_errTitleKey  = '400.title';
$_errDescKey   = '400.desc';
$_errButtons   = [
    ['href' => '/', 'icon' => 'fa-house', 'key' => 'btn.home', 'class' => 'btn-jj-primary'],
];
require __DIR__ . '/_layout.php';
