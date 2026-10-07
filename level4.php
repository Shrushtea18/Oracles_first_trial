<?php
require_once 'includes/db.php';
oracle_session_start();
require_min_level(4);

$page_title   = "Sanjaya's Oracle — Gate IV: The Fate";
$active_level = 4;

$search_results = null;
$search_done    = false;
$alert          = null;
$named          = warrior_named();

$accepted_names = ['iravan', 'iravat', 'iravaan'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'search') {
        $search = oracle_filter($_POST['search'] ?? '');

        if ($search !== '') {
            $db = get_db();
            $query = "SELECT name, side FROM kshetra_rakshak WHERE name = '$search' LIMIT 20";
            $result = $db->query($query);

            $search_results = [];
            if ($result) {
                while ($row = $result->fetch_row()) {
                    $search_results[] = $row;
                }
            }
            $search_done = true;
        }
    } elseif ($action === 'guess' && !$named) {
        $guess = strtolower(trim($_POST['guess'] ?? ''));

        if (in_array($guess, $accepted_names, true)) {
            mark_warrior_named();
            $named = true;
            $alert = [
                'type' => 'success',
                'text' => 'The warrior is known. The Oracle lifts its final seal. The banner awaits.',
            ];
        } elseif ($guess === 'barbarik' || $guess === 'barbareek') {
            $alert = [
                'type' => 'error',
                'text' => 'That name belongs to another record in the archive. Look again.',
            ];
        } else {
            $alert = [
                'type' => 'error',
                'text' => 'The Oracle does not confirm that name. The evidence points elsewhere.',
            ];
        }
    }
}

include 'includes/header.php';
?>

<main class="main-container">
<div class="oracle-card fade-in">

    <div class="card-header">
        <div class="card-title">Gate IV — The Warrior's Fate</div>
        <div class="card-lore">
            "Side. Parentage. And now — how did he fall? When the Oracle speaks
            a warrior's fate, only one name can attach to it. Speak that name, and
            the final seal is broken."
        </div>
    </div>

    <div class="card-body">

        <?php if ($alert): ?>
        <div class="alert alert-<?php echo $alert['type']; ?> fade-in">
            <span class="alert-icon"><?php echo $alert['type']==='success' ? '✔' : '✖'; ?></span>
            <?php echo htmlspecialchars($alert['text']); ?>
        </div>
        <?php endif; ?>

        <?php if ($named): ?>
        <div class="download-reveal fade-in">
            <span class="big-icon">🪔</span>
            <div class="download-title">The Oracle Speaks</div>
            <div class="download-lore">
                "The warrior is named. The archive is unsealed. The Oracle's truth is revealed."
            </div>
            <div class="token-reveal" style="margin-top:1.5rem;">
                <div class="token-label">Flag</div>
                <div class="token-value" id="flag-value" style="letter-spacing:0.12em;">kctf{73-82-65-86-65-78}</div>
            </div>
        </div>
        <hr class="section-divider" style="margin:2rem 0;">
        <?php endif; ?>

        <form method="POST" id="search-form-4">
            <input type="hidden" name="action" value="search">
            <div class="form-group">
                <label class="form-label" for="search4">Query the War Registry</label>
                <div style="display:flex; gap:0.7rem; flex-wrap:wrap;">
                    <input
                        type="text"
                        class="form-input"
                        id="search4"
                        name="search"
                        placeholder="Search warriors by name…"
                        spellcheck="false"
                        autocomplete="off"
                        style="flex:1; min-width:200px;"
                    >
                    <button type="submit" class="btn btn-secondary" id="btn-search-4">
                        Query Oracle
                    </button>
                </div>
            </div>
        </form>

        <div class="terminal" style="margin-top:0.5rem;">
            <div class="terminal-header">
                <span class="t-dot r"></span>
                <span class="t-dot y"></span>
                <span class="t-dot g"></span>
                <span class="terminal-caption">Registry Query · 2 columns</span>
            </div>
            <div class="terminal-body">
                <div class="query-hint">
                    SELECT <span class="var">name</span>, <span class="var">side</span>
                    FROM kshetra_rakshak<br>
                    &nbsp;WHERE name = '<span class="var">[your_input]</span>'
                    LIMIT 20;
                </div>

                <?php if ($search_done): ?>
                <?php if (!empty($search_results)): ?>
                <table class="results-table" id="results-table-4">
                    <thead>
                        <tr><th>col_1</th><th>col_2</th></tr>
                    </thead>
                    <tbody>
                        <?php foreach ($search_results as $row): ?>
                        <tr>
                            <td><?php echo htmlspecialchars((string)($row[0] ?? '')); ?></td>
                            <td><?php echo htmlspecialchars((string)($row[1] ?? '')); ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                <div class="row-count"><?php echo count($search_results); ?> row(s) returned.</div>
                <?php else: ?>
                <div class="no-results">-- 0 rows returned --</div>
                <?php endif; ?>
                <?php endif; ?>
            </div>
        </div>

        <?php if (!$named): ?>
        <hr class="section-divider" style="margin: 2rem 0;">
        <div class="claim-box">
            <div class="claim-title">⚔ Name the Warrior</div>
            <div class="claim-lore">
                "Side known. Parentage known. Fate known. The identity of the warrior
                is now within reach. Speak his name to break the Oracle's final seal."
            </div>
            <form method="POST" id="guess-form" autocomplete="off">
                <input type="hidden" name="action" value="guess">
                <div class="claim-inline">
                    <input
                        type="text"
                        class="form-input"
                        name="guess"
                        id="warrior-name"
                        placeholder="Who is this warrior?"
                        spellcheck="false"
                        autocomplete="off"
                    >
                    <button type="submit" class="btn btn-primary glow" id="btn-guess">
                        Declare His Name
                    </button>
                </div>
            </form>
        </div>
        <?php endif; ?>

    </div>
</div>
</main>

<?php include 'includes/footer.php'; ?>
