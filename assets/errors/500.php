<?php
http_response_code(500);
$_errCode      = '500';
$_errCodeColor = 'var(--jj-danger)';
$_errIconHtml  = '<div style="font-size:5rem">&#x1F4A5;</div>';
$_errTitleKey  = '500.title';
$_errDescKey   = '500.desc';
$_errButtons   = [
    ['href' => '/', 'icon' => 'fa-house', 'key' => 'btn.home', 'class' => 'btn-jj-primary'],
];
require __DIR__ . '/_layout.php';
