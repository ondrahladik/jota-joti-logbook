<?php
http_response_code(500);
$_errCode     = '';
$_errIconHtml = '<div style="font-size:4rem"><i class="fa-solid fa-triangle-exclamation" style="color:var(--jj-warning)"></i></div>';
$_errTitleKey = 'php.title';
$_errDescKey  = 'php.desc';
$_errExtra    = !empty($error_message)
    ? '<div class="jj-card p-3 mb-4 text-start"><code class="small" style="color:var(--jj-warning);word-break:break-all">'
      . htmlspecialchars($error_message, ENT_QUOTES, 'UTF-8') . '</code></div>'
    : '';
$_errButtons  = [];
require __DIR__ . '/_layout.php';
