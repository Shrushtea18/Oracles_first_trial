<?php
$active_level = $active_level ?? 1;
$current      = get_level();

$level_nodes = [
    1 => 'I — The Gate',
    2 => 'II — Allegiance',
    3 => 'III — Lineage',
    4 => 'IV — The Fate',
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($page_title ?? "Sanjaya's Oracle"); ?></title>
    <meta name="description" content="Sanjaya's Oracle — The Kurukshetra War Registry. Speak the truth to enter.">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@400;600;700;900&family=Crimson+Pro:ital,wght@0,300;0,400;0,600;1,300;1,400&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">
</head>
<body>

<header class="oracle-header">
    <div class="oracle-title">Sanjaya's Oracle</div>
    <div class="oracle-subtitle">Kurukshetra War Registry &nbsp;·&nbsp; Truth Spoken One Breath at a Time</div>
</header>

<nav class="level-bar" aria-label="Challenge progress">
    <?php foreach ($level_nodes as $num => $label): ?>
        <?php
        $state = '';
        if ($current > $num)         $state = 'done';
        elseif ($active_level === $num) $state = 'active';
        ?>
        <div class="level-node <?php echo $state; ?>" aria-current="<?php echo ($state === 'active') ? 'step' : 'false'; ?>">
            <span class="level-dot"></span>
            <?php echo htmlspecialchars($label); ?>
        </div>
        <?php if ($num < 4): ?>
        <span class="level-sep" aria-hidden="true">›</span>
        <?php endif; ?>
    <?php endforeach; ?>
</nav>
