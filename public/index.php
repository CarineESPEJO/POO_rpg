<?php
require_once __DIR__ . '/../src/Character.php';
require_once __DIR__ . '/../src/Warrior.php';
require_once __DIR__ . '/../src/Assassin.php';

session_start();

// Initialize characters only if not already present in session (so state persists across requests)
if (!isset($_SESSION['char1'])) {
    $_SESSION['char1'] = new Warrior(name: 'Godzilla', strength: 40, intelligence: 60, armor: 50, srcImg: "/assets/images/godzilla.webp");
}
if (!isset($_SESSION['char2'])) {
    $_SESSION['char2'] = new Assassin(name: 'Kong', strength: 60, intelligence: 40, agility: 50, srcImg: "/assets/images/king_kong.webp");
}

$char1 = $_SESSION['char1'];
$char2 = $_SESSION['char2'];

$characters = [
    'char1' => $char1,
    'char2' => $char2
];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>RPG Battle</title>
    <link rel="stylesheet" href="/assets/css/index.css">
    <script src="/assets/js/fighting.js" defer></script>
    <style>
      /* small helper so \n in messages become visible (safe) */
      #action_message, #history { white-space: pre-line; }
      .controls button { margin-right:8px; margin-bottom:6px; }
    </style>
</head>
<body>
    <h1>Character Battle</h1>

    <div id="combat_stats" class="combat_stats">
        <?php foreach ($characters as $id => $char): ?>
            <div class="character-block" id="<?= htmlspecialchars($id) ?>"
                 data-class="<?= htmlspecialchars(get_class($char)) ?>"
                 data-name="<?= htmlspecialchars($char->getName()) ?>">
                <img src="<?= htmlspecialchars($char->getSrcImg()); ?>" alt="<?= htmlspecialchars($char->getName()); ?>" width="160">
                <h2><?= htmlspecialchars($char->getName()); ?> Stats</h2>
                <p>
                    Health: <span id="<?= $id ?>_health"><?= $char->getHealth(); ?></span> |
                    Strength: <span id="<?= $id ?>_strength"><?= $char->getStrength(); ?></span> |
                    Intelligence: <span id="<?= $id ?>_intel"><?= $char->getIntelligence(); ?></span> |
                    Stamina: <span id="<?= $id ?>_stamina"><?= $char->getStamina(); ?></span>
                </p>
                <div class="controls" id="<?= $id ?>_controls">
                    <!-- Buttons will be injected dynamically by fighting.js -->
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <div class="combat_msgs">
        <p id="winner_message"></p>
        <p id="action_message"></p>
    </div>

    <button id="new_fight">New Fight</button>

    <div class="history_container">
        <h2>History</h2>
        <div id="history" class="history"></div>
    </div>
</body>
</html>
