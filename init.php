<?php
// ═══════════════════════════════════════════════════════════════════
//  setup/init.php — One-time initialization script
//
//  Run this ONCE via browser or CLI (php setup/init.php) to:
//    1. Create the oracle_ctf database and tables
//    2. Insert sample warriors + archivists
//    3. Generate assets/kali_banner.png with the flag hidden
//       after the PNG IEND chunk (EOF append steganography)
//
//  ⚠  Requires GD extension (php-gd) and the Freetype library.
//  ⚠  Edit DB_* constants in includes/db.php before running.
//  ⚠  Delete or protect this file after running!
// ═══════════════════════════════════════════════════════════════════

// Allow CLI or web browser execution
$is_cli = (php_sapi_name() === 'cli');
$NL     = $is_cli ? "\n" : "<br>\n";
$BOLD   = $is_cli ? "\033[1m" : "<strong>";
$RESET  = $is_cli ? "\033[0m"  : "</strong>";

function log_msg(string $msg, string $type = 'info'): void {
    global $NL, $BOLD, $RESET, $is_cli;
    $icons = ['info' => '  ', 'ok' => '✔ ', 'err' => '✖ ', 'head' => '══'];
    $icon  = $icons[$type] ?? '  ';
    if (!$is_cli) {
        $color = match($type) {
            'ok'   => 'color:#27ae60',
            'err'  => 'color:#e05a4e',
            'head' => 'color:#c9a84c;font-weight:bold',
            default => 'color:#e8dcc8',
        };
        echo "<span style=\"$color\">$icon $msg</span>$NL";
    } else {
        echo "$icon $msg$NL";
    }
}

// ──────────────────────────────────────────────────────────────────
// 0. Bootstrap — load config without requiring DB to exist yet
// ──────────────────────────────────────────────────────────────────
require_once dirname(__DIR__) . '/includes/db.php';

if (!$is_cli) {
    header('Content-Type: text/html; charset=utf-8');
    echo '<!DOCTYPE html><html><head><meta charset="UTF-8">
          <title>Oracle Setup</title>
          <style>body{background:#04030a;font-family:monospace;padding:2rem;font-size:0.95rem;}</style>
          </head><body>';
}

log_msg("Sanjaya's Oracle — Setup Script", 'head');
log_msg("", 'info');

// ──────────────────────────────────────────────────────────────────
// 1. Connect to Database (MySQL or SQLite fallback)
// ──────────────────────────────────────────────────────────────────
$root_conn = null;
if (class_exists('mysqli')) {
    try {
        mysqli_report(MYSQLI_REPORT_OFF);
        $conn = @new mysqli(DB_HOST, DB_USER, DB_PASS);
        if (!$conn->connect_error) {
            $root_conn = $conn;
            log_msg("MySQL connection established.", 'ok');
        }
    } catch (\Throwable $e) {
        $root_conn = null;
    }
}

$archivists = [
    ['sanjaya',  'kurukshetra_chronicles_741'],
    ['vyasa',    'mahabharata_sage_9182'],
    ['dhritrash','blind_king_pass_3355'],
];

$warriors = [
    ['Bhishma',     'Kaurava', 'Shantanu',          'Ganga',
     'Fell on the tenth day, choosing to lie on a bed of arrows until the war\'s end.'],
    ['Drona',       'Kaurava', 'Sage Bharadwaja',   'Unknown',
     'Slain by Dhrishtadyumna on the fifteenth day after laying down his weapons in grief.'],
    ['Karna',       'Kaurava', 'Surya (the Sun God)', 'Kunti',
     'Slain by Arjuna on the seventeenth day while struggling to free his chariot wheel.'],
    ['Abhimanyu',   'Pandava', 'Arjuna',             'Subhadra',
     'Killed inside the Chakravyuha on the thirteenth day, surrounded and overwhelmed.'],
    ['Ghatotkacha', 'Pandava', 'Bhima',              'Hidimbi',
     'Slain by Karna with the Vasavi Shakti on the fourteenth day at nightfall.'],
    ['Shikhandi',   'Pandava', 'Drupada',            'Prishati',
     'Survived the main war; killed by Ashwatthama on the final night in the camp attack.'],
    ['???',         'Pandava', 'Arjuna',             'Ulupi (a Naga princess)',
     'Offered himself to Goddess Kali on the eighth day, then fell in battle the same day.'],
    ['Satyaki',     'Pandava', 'Shini',              'Unknown',
     'One of the few key warriors to survive the entire Kurukshetra war.'],
    ['Kripacharya', 'Kaurava', 'Sage Sharadvana',   'Janapadi',
     'One of the chiranjivis — survived the war due to his immortal nature.'],
];

if ($root_conn) {
    $sql_db = "CREATE DATABASE IF NOT EXISTS `" . DB_NAME . "` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci";
    if (!$root_conn->query($sql_db)) {
        log_msg("Failed to create database: " . $root_conn->error, 'err');
        exit(1);
    }
    log_msg("Database `" . DB_NAME . "` ready.", 'ok');

    $root_conn->select_db(DB_NAME);
    $root_conn->set_charset('utf8mb4');

    $tables = [
        "archivists" => "
            CREATE TABLE IF NOT EXISTS `archivists` (
                `id`       INT AUTO_INCREMENT PRIMARY KEY,
                `username` VARCHAR(60)  NOT NULL,
                `password` VARCHAR(100) NOT NULL
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
        ",
        "kshetra_rakshak" => "
            CREATE TABLE IF NOT EXISTS `kshetra_rakshak` (
                `id`     INT AUTO_INCREMENT PRIMARY KEY,
                `name`   VARCHAR(100) NOT NULL,
                `side`   VARCHAR(50)  NOT NULL,
                `father` VARCHAR(100) DEFAULT NULL,
                `mother` VARCHAR(100) DEFAULT NULL,
                `fate`   TEXT         DEFAULT NULL
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
        ",
    ];

    foreach ($tables as $name => $ddl) {
        if (!$root_conn->query($ddl)) {
            log_msg("Failed to create table `$name`: " . $root_conn->error, 'err');
            exit(1);
        }
    }
    log_msg("MySQL tables ready.", 'ok');

    $root_conn->query("DELETE FROM `archivists`");
    $stmt = $root_conn->prepare("INSERT INTO `archivists` (username, password) VALUES (?, ?)");
    foreach ($archivists as [$u, $p]) {
        $stmt->bind_param('ss', $u, $p);
        $stmt->execute();
    }
    $stmt->close();

    $root_conn->query("DELETE FROM `kshetra_rakshak`");
    $stmt = $root_conn->prepare(
        "INSERT INTO `kshetra_rakshak` (name, side, father, mother, fate) VALUES (?,?,?,?,?)"
    );
    foreach ($warriors as [$n, $s, $f, $m, $fa]) {
        $stmt->bind_param('sssss', $n, $s, $f, $m, $fa);
        $stmt->execute();
    }
    $stmt->close();
    log_msg("Database seeded in MySQL.", 'ok');
    $root_conn->close();
} else {
    log_msg("MySQL not reachable. Initializing SQLite storage fallback...", 'info');
    $sqlite_file = dirname(__DIR__) . '/oracle_ctf.db';
    if (file_exists($sqlite_file)) {
        @unlink($sqlite_file);
    }
    $sdb = new SQLite3($sqlite_file);
    $sdb->exec("CREATE TABLE IF NOT EXISTS archivists (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        username TEXT NOT NULL,
        password TEXT NOT NULL
    )");
    $sdb->exec("CREATE TABLE IF NOT EXISTS kshetra_rakshak (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        name TEXT NOT NULL,
        side TEXT NOT NULL,
        father TEXT DEFAULT NULL,
        mother TEXT DEFAULT NULL,
        fate TEXT DEFAULT NULL
    )");

    $stmt = $sdb->prepare("INSERT INTO archivists (username, password) VALUES (?, ?)");
    foreach ($archivists as [$u, $p]) {
        $stmt->bindValue(1, $u, SQLITE3_TEXT);
        $stmt->bindValue(2, $p, SQLITE3_TEXT);
        $stmt->execute();
    }
    $stmt->close();

    $stmt = $sdb->prepare("INSERT INTO kshetra_rakshak (name, side, father, mother, fate) VALUES (?, ?, ?, ?, ?)");
    foreach ($warriors as [$n, $s, $f, $m, $fa]) {
        $stmt->bindValue(1, $n, SQLITE3_TEXT);
        $stmt->bindValue(2, $s, SQLITE3_TEXT);
        $stmt->bindValue(3, $f, SQLITE3_TEXT);
        $stmt->bindValue(4, $m, SQLITE3_TEXT);
        $stmt->bindValue(5, $fa, SQLITE3_TEXT);
        $stmt->execute();
    }
    $stmt->close();
    $sdb->close();
    log_msg("Database seeded in SQLite (oracle_ctf.db).", 'ok');
}

// ──────────────────────────────────────────────────────────────────
// 6. Generate kali_banner.png with flag hidden after IEND
// ──────────────────────────────────────────────────────────────────
log_msg("", 'info');
log_msg("Generating kali_banner.png …", 'head');

if (!function_exists('imagecreatetruecolor')) {
    log_msg("GD extension not found! Install php-gd, then re-run.", 'err');
    log_msg("Image NOT generated. Cannot serve flag without it.", 'err');
    exit(1);
}

$W = 900;
$H = 420;
$img = imagecreatetruecolor($W, $H);

// ── Colours ─────────────────────────────────────────────────────
$c_bg1     = imagecolorallocate($img,   4,   3,  10);
$c_bg2     = imagecolorallocate($img,  25,  10,  50);
$c_gold    = imagecolorallocate($img, 201, 168,  76);
$c_goldDim = imagecolorallocate($img, 100,  80,  25);
$c_red     = imagecolorallocate($img, 160,  30,  30);
$c_white   = imagecolorallocate($img, 232, 220, 200);
$c_dimText = imagecolorallocate($img, 120, 100,  70);
$c_dark1   = imagecolorallocate($img,  15,  10,  30);
$c_dark2   = imagecolorallocate($img,  35,  20,  60);

// ── Background gradient (top-down bands approximation) ──────────
for ($y = 0; $y < $H; $y++) {
    $ratio = $y / $H;
    $r = (int)(4  + $ratio * 20);
    $g = (int)(3  + $ratio *  5);
    $b = (int)(10 + $ratio * 45);
    $c = imagecolorallocate($img, $r, $g, $b);
    imageline($img, 0, $y, $W, $y, $c);
}

// ── Decorative border frame ──────────────────────────────────────
$pad = 12;
imagerectangle($img, $pad, $pad, $W-$pad, $H-$pad, $c_goldDim);
imagerectangle($img, $pad+4, $pad+4, $W-$pad-4, $H-$pad-4, $c_dark1);

// ── Corner ornaments ─────────────────────────────────────────────
$corners = [[0,0], [$W,0], [0,$H], [$W,$H]];
foreach ($corners as [$cx, $cy]) {
    $ox = ($cx === 0) ? $pad+2 : $W-$pad-2;
    $oy = ($cy === 0) ? $pad+2 : $H-$pad-2;
    imageline($img, $ox, $oy, $ox + ($cx===0?15:-15), $oy, $c_gold);
    imageline($img, $ox, $oy, $ox, $oy + ($cy===0?15:-15), $c_gold);
}

// ── Use built-in GD fonts (no TTF needed) ───────────────────────
// Font sizes: 1=5x3, 2=6x5, 3=7x6, 4=8x8, 5=9x15

// Title banner
$title = "KURUKSHETRA WAR REGISTRY";
$tW    = strlen($title) * imagefontwidth(5);
imagestring($img, 5, ($W - $tW) / 2, 42, $title, $c_gold);

// Horizontal rule under title
imageline($img, 60, 72, $W-60, 72, $c_goldDim);
imageline($img, 60, 74, $W-60, 74, $c_dark2);

// Sub-title
$sub = "SANJAYA'S ORACLE  —  SEALED ARCHIVE DOCUMENT";
$sW  = strlen($sub) * imagefontwidth(3);
imagestring($img, 3, ($W - $sW) / 2, 84, $sub, $c_dimText);

// Centre divider emblem
$emblem = "[ CLASSIFIED ]";
$eW = strlen($emblem) * imagefontwidth(4);
imagestring($img, 4, ($W - $eW) / 2, 150, $emblem, $c_red);

// Record fields
$fields = [
    ['SUBJECT',     'WAR REGISTRY RECORD — IDENTITY SEALED'],
    ['SIDE',        'PANDAVA'],
    ['PARENTAGE',   'SON OF A GREAT ARCHER & A NAGA PRINCESS'],
    ['DATE OF FALL','DAY 8 OF BATTLE  (OFFERING TO GODDESS KALI)'],
    ['STATUS',      'RECORD SEALED — ORACLE CLEARANCE REQUIRED'],
];

$fY = 200;
foreach ($fields as [$label, $value]) {
    imagestring($img, 2, 80, $fY,     $label . ':', $c_goldDim);
    imagestring($img, 2, 250, $fY,    $value,        $c_white);
    $fY += 22;
}

// Bottom rule
imageline($img, 60, $H-60, $W-60, $H-60, $c_goldDim);

// Footer
$footer = "PROPERTY OF THE KURUKSHETRA ROYAL ARCHIVES  —  YEAR OF DHARMA YUDDHA";
$fooW   = strlen($footer) * imagefontwidth(1);
imagestring($img, 1, ($W - $fooW) / 2, $H - 46, $footer, $c_dimText);

// ── Capture PNG bytes ────────────────────────────────────────────
ob_start();
imagepng($img);
$png_bytes = ob_get_clean();
imagedestroy($img);

$flag_payload = "\n-- BEGIN ORACLE MESSAGE --\n"
              . "kctf{73-82-65-86-65-78}\n"
              . "-- END ORACLE MESSAGE --\n";

$final_bytes = $png_bytes . $flag_payload;

// ── Write to assets/ ─────────────────────────────────────────────
$assets_dir = dirname(__DIR__) . '/assets';
if (!is_dir($assets_dir)) {
    mkdir($assets_dir, 0755, true);
}

// Write .htaccess to protect direct HTTP access
$htaccess_content = "# Deny all direct web access\n<IfModule mod_authz_core.c>\n    Require all denied\n</IfModule>\n<IfModule !mod_authz_core.c>\n    Order deny,allow\n    Deny from all\n</IfModule>\n";
file_put_contents($assets_dir . '/.htaccess', $htaccess_content);

$out_path = $assets_dir . '/kali_banner.png';

if (file_put_contents($out_path, $final_bytes) === false) {
    log_msg("Could not write to $out_path — check directory permissions.", 'err');
    exit(1);
}

$kb = round(strlen($final_bytes) / 1024, 1);
log_msg("Image written: $out_path ({$kb} KB)", 'ok');
log_msg("Flag appended after IEND: kctf{73-82-65-86-65-78}", 'ok');
log_msg("Access control written: $assets_dir/.htaccess", 'ok');

// ──────────────────────────────────────────────────────────────────
// 7. Verification reminder
// ──────────────────────────────────────────────────────────────────
log_msg("", 'info');
log_msg("Setup complete! Next steps:", 'head');
log_msg("  1. Configure your web server root → challenge/", 'info');
log_msg("  2. DELETE or restrict access to this file (setup/init.php).", 'info');
log_msg("  3. Verify: strings assets/kali_banner.png | grep KCFT", 'info');
log_msg("  4. Confirm login bypass works on index.php", 'info');
log_msg("  5. Confirm levels 2-4 UNION payloads return expected data", 'info');

if (!$is_cli) {
    echo '</body></html>';
}
