<?php
require_once 'includes/db.php';
oracle_session_start();

if (get_level() >= 2 && !isset($_GET['retry'])) {
    header('Location: level2.php');
    exit;
}

$page_title    = "Sanjaya's Oracle — Gate I: Authentication";
$active_level  = 1;

$alert   = null;
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action   = $_POST['action'] ?? '';
    $username = oracle_filter($_POST['username'] ?? '');
    $password = oracle_filter($_POST['password'] ?? '');

    if ($username !== '' || $password !== '') {
        $db = get_db();
        $query  = "SELECT * FROM archivists WHERE username = '$username' AND password = '$password'";
        $result = $db->query($query);

        if ($result && $result->num_rows > 0) {
            set_level(2);
            $success = true;
            $alert = [
                'type' => 'success',
                'text' => 'The archive recognizes your seal. The first gate opens.',
            ];
        } else {
            $alert = [
                'type' => 'error',
                'text' => 'The Oracle does not recognize these words. The gate remains sealed.',
            ];
        }
    }
}

include 'includes/header.php';
?>

<main class="main-container">

    <?php if (!empty($_GET['err'])): ?>
    <div class="alert alert-error fade-in" style="margin-bottom:1.2rem;">
        <span class="alert-icon">🔒</span>
        "The archive does not know you yet. Return to the first gate."
    </div>
    <?php endif; ?>

    <div class="oracle-card fade-in">
        <div class="card-header">
            <div class="card-title">Gate I — The Archivist's Seal</div>
            <div class="card-lore">
                "The War Registry has slept for a thousand ages. Only a recognized
                archivist may rouse it and make it speak. Present your seal — or
                find another way through."
            </div>
        </div>

        <div class="card-body">

            <?php if ($alert): ?>
            <div class="alert alert-<?php echo $alert['type']; ?> fade-in">
                <span class="alert-icon"><?php echo $alert['type']==='success' ? '✔' : '✖'; ?></span>
                <?php echo htmlspecialchars($alert['text']); ?>
            </div>
            <?php endif; ?>

            <?php if ($success): ?>

            <div class="token-reveal fade-in">
                <div class="token-label">Gate I — Token Issued</div>
                <div class="token-value">AKSHA-1-7f2c19</div>
            </div>
            <p style="color:var(--text-dim); font-style:italic; margin-bottom:1.5rem;">
                The archivist's seal is accepted. The Oracle now awaits your first
                question.
            </p>
            <a href="level2.php" class="btn btn-primary glow" id="proceed-gate2">
                Proceed to Gate II &rarr;
            </a>

            <?php else: ?>

            <form method="POST" id="login-form" autocomplete="off">
                <div class="form-group">
                    <label class="form-label" for="username">Archivist's Name</label>
                    <input
                        type="text"
                        class="form-input"
                        id="username"
                        name="username"
                        placeholder="Enter your archivist name…"
                        spellcheck="false"
                        autocomplete="off"
                    >
                </div>
                <div class="form-group">
                    <label class="form-label" for="password">Seal Phrase</label>
                    <input
                        type="password"
                        class="form-input"
                        id="password"
                        name="password"
                        placeholder="Speak the seal phrase…"
                        autocomplete="off"
                    >
                </div>
                <button type="submit" class="btn btn-primary glow" id="submit-login">
                    Invoke the Oracle ⚡
                </button>
            </form>

            <div class="terminal mt-3">
                <div class="terminal-header">
                    <span class="t-dot r"></span>
                    <span class="t-dot y"></span>
                    <span class="t-dot g"></span>
                    <span class="terminal-caption">Oracle Query Log</span>
                </div>
                <div class="terminal-body">
                    <div class="query-hint">
                        SELECT * FROM archivists<br>
                        &nbsp;WHERE username&nbsp;= '<span class="var">[your_input]</span>'<br>
                        &nbsp;&nbsp; AND password = '<span class="var">[seal]</span>';
                    </div>
                </div>
            </div>

            <?php endif; ?>

        </div>
    </div>

</main>

<?php include 'includes/footer.php'; ?>
