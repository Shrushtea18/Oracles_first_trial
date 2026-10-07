<?php
define('DB_HOST', 'localhost');
define('DB_USER', 'oracle_user');
define('DB_PASS', 'oracle_pass123');
define('DB_NAME', 'oracle_ctf');

class OracleSqliteResult {
    private array $rows;
    private int $index = 0;
    public int $num_rows = 0;

    public function __construct(array $rows) {
        $this->rows = $rows;
        $this->num_rows = count($rows);
    }

    public function fetch_row(): ?array {
        if ($this->index < $this->num_rows) {
            return $this->rows[$this->index++];
        }
        return null;
    }
}

class OracleSqliteDB {
    private \SQLite3 $db;

    public function __construct(string $path) {
        $this->db = new \SQLite3($path);
    }

    public function query(string $query) {
        try {
            $result = @$this->db->query($query);
            if (!$result) {
                return false;
            }
            $rows = [];
            while ($row = $result->fetchArray(SQLITE3_NUM)) {
                $rows[] = $row;
            }
            return new OracleSqliteResult($rows);
        } catch (\Throwable $e) {
            return false;
        }
    }
}

function get_db(): object {
    static $conn = null;
    if ($conn !== null) {
        return $conn;
    }

    if (class_exists('mysqli')) {
        try {
            mysqli_report(MYSQLI_REPORT_OFF);
            $mysqli = @new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
            if (!$mysqli->connect_error) {
                $mysqli->set_charset('utf8mb4');
                $conn = $mysqli;
                return $conn;
            }
        } catch (\Throwable $e) {}
    }

    $sqlite_paths = [
        dirname(__DIR__) . '/oracle_ctf.db',
        dirname(__DIR__) . '/assets/oracle_ctf.db',
    ];

    foreach ($sqlite_paths as $path) {
        if (file_exists($path)) {
            $conn = new OracleSqliteDB($path);
            return $conn;
        }
    }

    die('<p style="color:#e05a4e;font-family:monospace;padding:2rem;">
         [Oracle] Database connection failed.<br>
         Please run setup/init.php or configure MySQL in includes/db.php.</p>');
}

function oracle_filter(string $input): string {
    $blocked = ['UNION', 'OR 1=1'];
    return str_replace($blocked, '****', $input);
}

function oracle_session_start(): void {
    if (session_status() === PHP_SESSION_NONE) {
        session_name('ORACLE_SESSION');
        session_start();
    }
}

function get_level(): int {
    oracle_session_start();
    return (int)($_SESSION['oracle_level'] ?? 1);
}

function set_level(int $level): void {
    oracle_session_start();
    if ($level > get_level()) {
        $_SESSION['oracle_level'] = $level;
    }
}

function warrior_named(): bool {
    oracle_session_start();
    return !empty($_SESSION['oracle_warrior_named']);
}

function mark_warrior_named(): void {
    oracle_session_start();
    $_SESSION['oracle_warrior_named'] = true;
    set_level(5);
}

function require_min_level(int $min): void {
    if (get_level() < $min) {
        $back = ($min <= 2) ? 'index.php' : 'level' . ($min - 1) . '.php';
        header("Location: $back?err=locked");
        exit;
    }
}

