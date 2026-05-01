<?php
http_response_code(404);
$_errCode      = '404';
$_errCodeColor = 'var(--jj-accent)';
$_errIconHtml  = '<div style="font-size:5rem;filter:drop-shadow(0 0 20px #7c3aed)">&#x269C;&#xFE0E;</div>';
$_errTitleKey  = '404.title';
$_errDescKey   = '404.desc';
$_errButtons   = [
    ['href' => '/',        'icon' => 'fa-house',           'key' => 'btn.home',    'class' => 'btn-jj-primary'],
    ['href' => '/logbook', 'icon' => 'fa-satellite-dish',  'key' => 'btn.logbook', 'class' => 'btn-jj-outline'],
];
require __DIR__ . '/_layout.php';
