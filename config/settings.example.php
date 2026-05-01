<?php
if (!defined('JOTAJOTI_LOADED')) die('Direct access not allowed.');

return [
    // Database
    'db_host'     => 'localhost',
    'db_user'     => 'root',
    'db_pass'     => '',
    'db_name'     => 'logbook',

    // App settings
    'callsign'      => '',
    'app_name'      => 'JOTA-JOTI Logbook',
    'default_lang'  => 'en',
    'default_theme' => 'dark',

    // Passwords (bcrypt hashed)
    // Generate: password_hash('yourpassword', PASSWORD_BCRYPT)
    'user_pass_hash'  => '',  // empty = user role disabled
    'admin_pass_hash' => '',  // empty = admin role disabled

    // Flags
    'configured'  => true,
];
