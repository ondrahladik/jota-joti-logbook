<?php
http_response_code(403);
$_errCode      = '403';
$_errCodeColor = 'var(--jj-danger)';
$_errIconHtml  = '<div style="font-size:5rem;filter:drop-shadow(0 0 20px #ef4444)">&#x26D4;&#xFE0E;</div>';
$_errTitleKey  = '403.title';
$_errDescKey   = '403.desc';
$_errButtons   = [
    ['href' => '/',      'icon' => 'fa-house',               'key' => 'btn.home',  'class' => 'btn-jj-primary'],
    ['href' => '/login', 'icon' => 'fa-right-to-bracket',    'key' => 'btn.login', 'class' => 'btn-jj-outline'],
];
require __DIR__ . '/_layout.php';
