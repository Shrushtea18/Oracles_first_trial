<?php
require_once 'includes/db.php';
oracle_session_start();

if (!warrior_named()) {
    header('HTTP/1.1 403 Forbidden');
    header('Content-Type: text/plain');
    echo '"The banner has not yet been earned. The Oracle still waits for you to name the warrior."';
    exit;
}

$file = __DIR__ . '/assets/kali_banner.png';

if (!file_exists($file)) {
    header('HTTP/1.1 404 Not Found');
    header('Content-Type: text/plain');
    echo '[Oracle] kali_banner.png not found. Run setup/init.php to generate it.';
    exit;
}

header('Content-Type: image/png');
header('Content-Disposition: attachment; filename="kali_banner.png"');
header('Content-Length: ' . filesize($file));
header('Cache-Control: no-cache, no-store');
readfile($file);
exit;
