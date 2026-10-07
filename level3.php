<?php
require_once 'includes/db.php';
oracle_session_start();
require_min_level(3);

$page_title   = "Sanjaya's Oracle — Gate III: Lineage";
$active_level = 3;

$search_results = null;
$search_done    = false;
$alert          = null;
$claimed        = (get_level() >= 4);

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
    } elseif ($action === 'claim' && !$claimed) {
        $answer = strtolower(trim($_POST['answer'] ?? ''));
        if ($answer === 'arjuna') {
            set_level(4);
            $claimed = true;
            $alert = ['type' => 'success',
                      'text' => 'The lineage is confirmed. The Oracle opens the final gate.'];
        } else {
            $alert = ['type' => 'error',
                      'text' => 'The Oracle does not confirm that lineage. Query deeper.'];
        }
    }
}

include 'includes/header.php';
?>

<main class="main-container">
<div class="oracle-card fade-in">

    <div class="card-header">
        <div class="card-title">Gate III — The Warrior's Lineage</div>
        <div class="card-lore">
            "Allegiance alone is not enough. The oracle remembers not just which
            banner a warrior raised, but whose blood he carried. Whose son is he?
            Who bore him into this world?"
        </div>
    </div>

    <div class="card-body">

        <?php if ($alert): ?>
        <div class="alert alert-<?php echo $alert['type']; ?> fade-in">
            <span class="alert-icon"><?php echo $alert['type']==='success' ? '✔' : '✖'; ?></span>
            <?php echo htmlspecialchars($alert['text']); ?>
        </div>
        <?php endif; ?>

        <?php if ($claimed): ?>
        <div class="token-reveal fade-in">
            <div class="token-label">Gate III — Token Issued</div>
            <div class="token-value">AKSHA-3-9b1f57</div>
        </div>
        <p style="color:var(--text-dim); font-style:italic; margin-bottom:1.5rem;">
            The warrior's lineage is confirmed. One last veil remains.
        </p>
        <a href="level4.php" class="btn btn-primary glow" id="proceed-gate4">
            Proceed to Gate IV &rarr;
        </a>
        <?php endif; ?>

        <form method="POST" id="search-form-3" <?php if($claimed) echo 'style="margin-top:2rem;"'; ?>>
            <input type="hidden" name="action" value="search">
            <div class="form-group">
                <label class="form-label" for="search3">Query the War Registry</label>
                <div style="display:flex; gap:0.7rem; flex-wrap:wrap;">
                    <input
                        type="text"
                        class="form-input"
                        id="search3"
                        name="search"
                        placeholder="Search warriors by name…"
                        spellcheck="false"
                        autocomplete="off"
                        style="flex:1; min-width:200px;"
                    >
                    <button type="submit" class="btn btn-secondary" id="btn-search-3">
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
                <table class="results-table" id="results-table-3">
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

        <?php if (!$claimed): ?>
        <hr class="section-divider" style="margin: 2rem 0;">
        <div class="claim-box">
            <div class="claim-title">Declare What You Have Learned</div>
            <div class="claim-lore">
                "The Oracle has spoken the warrior's father and mother. Name the
                father to claim the next seal."
            </div>
            <form method="POST" id="claim-form-3" autocomplete="off">
                <input type="hidden" name="action" value="claim">
                <div class="claim-inline">
                    <input
                        type="text"
                        class="form-input"
                        name="answer"
                        id="answer3"
                        placeholder="Who is the warrior's father?"
                        spellcheck="false"
                        autocomplete="off"
                    >
                    <button type="submit" class="btn btn-primary" id="btn-claim-3">
                        Claim Token
                    </button>
                </div>
            </form>
        </div>
        <?php endif; ?>

    </div>
</div>
</main>

<?php include 'includes/footer.php'; ?>
