<?php
require_once __DIR__ . '/../src/Character.php';
require_once __DIR__ . '/../src/Warrior.php';
require_once __DIR__ . '/../src/Assassin.php';
session_start();

// Initialize characters
$godzilla = new Warrior(name: 'Godzilla', strength: 40, intelligence: 60, armor: 50, srcImg: "/assets/images/godzilla.webp");
$kong = new Assassin(name: 'Kong', strength: 60, intelligence: 40, agility: 50, srcImg: "/assets/images/king_kong.webp");

$_SESSION['godzilla'] = $godzilla;
$_SESSION['kong'] = $kong;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Godzilla vs Kong RPG</title>
    <link rel="stylesheet" href="/assets/css/index.css">
    <script src="/assets/js/fighting.js" defer></script>
</head>
<body>
    <h1>Godzilla vs Kong</h1>

    <div id="combat_stats">
        <img src="<?php echo $godzilla->getSrcImg(); ?>" alt="Godzilla">
        <h2>Godzilla Stats</h2>
        <p>
            Health: <span id="godzilla_health"><?php echo $godzilla->getHealth(); ?></span> |
            Strength: <span id="godzilla_strength"><?php echo $godzilla->getStrength(); ?></span> |
            Intelligence: <span id="godzilla_intel"><?php echo $godzilla->getIntelligence(); ?></span> |
            Stamina: <span id="godzilla_stamina"><?php echo $godzilla->getStamina(); ?></span>
        </p>

        <img src="<?php echo $kong->getSrcImg(); ?>" alt="Kong">
        <h2>Kong Stats</h2>
        <p>
            Health: <span id="kong_health"><?php echo $kong->getHealth(); ?></span> |
            Strength: <span id="kong_strength"><?php echo $kong->getStrength(); ?></span> |
            Intelligence: <span id="kong_intel"><?php echo $kong->getIntelligence(); ?></span> |
            Stamina: <span id="kong_stamina"><?php echo $kong->getStamina(); ?></span>
        </p>
    </div>

    <div class="controls">
        <div>
            <button id="godzilla_attack">Attack</button>
            <button id="godzilla_powerstrike">Power Strike</button>
            <button id="godzilla_heal">Heal</button>
        </div>
        <br>
        <div>
            <button id="kong_attack">Attack</button>
            <button id="kong_sneakattack">Sneak Attack</button>
            <button id="kong_heal">Heal</button>
        </div>
    </div>

    <p id="winner_message"></p>
    <p id="action_message"></p>

    <br>
    <button id="new_fight" disabled>New Fight</button>

    <h3>Action History</h3>
    <ul id="action_history" style="max-height:300px; overflow-y:auto;"></ul>
</body>
</html>
