<?php
require_once __DIR__ . '/../src/Character.php';
session_start();

//if (!isset($_SESSION['godzilla'])) {
$godzilla = new Character();
$godzilla->name = 'Godzilla';
$godzilla->strength = 40;
$godzilla->intelligence = 60;
$godzilla->srcImg = "/assets/images/godzilla.webp";
$_SESSION['godzilla'] = $godzilla;
//} else {
//    $godzilla = $_SESSION['godzilla'];
//}

//if (!isset($_SESSION['kong'])) {
$kong = new Character();
$kong->name = 'Kong';
$kong->strength = 60;
$kong->intelligence = 40;
$kong->srcImg = "/assets/images/king_kong.webp";
$_SESSION['kong'] = $kong;
//} else {
//    $kong = $_SESSION['kong'];
//}
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
        <img src="<?php echo $godzilla->srcImg; ?>" alt="Godzilla">
        <h2>Stats</h2>
        <p>Godzilla: <br>
            Life: <span id="godzilla_life"><?php echo $godzilla->life; ?></span> |
            Strength: <span id="godzilla_strength"><?php echo $godzilla->strength; ?></span> |
            Intelligence: <span id="godzilla_intel"><?php echo $godzilla->intelligence; ?></span> |
            Stamina: <span id="godzilla_stamina"><?php echo $godzilla->stamina; ?></span>

        </p>
        <img src="<?php echo $kong->srcImg; ?>" alt="Kong">
        <p>Kong: <br>
            Life: <span id="kong_life"><?php echo $kong->life; ?></span> |
            Strength: <span id="kong_strength"><?php echo $kong->strength; ?></span> |
            Intelligence: <span id="kong_intel"><?php echo $kong->intelligence; ?></span> |
            Stamina: <span id="kong_stamina"><?php echo $kong->stamina; ?></span>

        </p>
    </div>

    <div class="controls">
        <button id="godzilla_fight">Godzilla Attack</button>
        <button id="godzilla_heal">Godzilla Heal</button>
        <br> <br>
        <button id="kong_fight">Kong Attack</button>
        <button id="kong_heal">Kong Heal</button>
        <br> <br> <br> <br>
    </div>
    <p id="winner_message"></p>

<button id="new_fight">New Fight</button>

</body>

</html>