<?php
// Need to call the POO to create instances Godzilla and Kong
require_once __DIR__ . '/../src/Character.php';
require_once __DIR__ . '/../src/Warrior.php';
require_once __DIR__ . '/../src/Assassin.php';
require_once __DIR__ . '/../src/SpecialAbilityInterface.php';

// this session link both files and transfer data between them
session_start();

// if the sessions var dont exist, create and store in it the instances Godzilla and Kong
if (!isset($_SESSION['char1'])) {
    // Godzilla here is a warrior, so it has an extra var, armor, to adapt its defense
    $_SESSION['char1'] = new Warrior(
        name: 'Godzilla',
        strength: 40,
        intelligence: 60,
        armor: 50,
        srcImg: "/assets/images/godzilla.webp"
    );
}
if (!isset($_SESSION['char2'])) {
    //Kong here is an assassin, so it has an extra var, agility, to adapt its defense and special attack
    $_SESSION['char2'] = new Assassin(
        name: 'King-Kong',
        strength: 60,
        intelligence: 40,
        agility: 50,
        srcImg: "/assets/images/king_kong.webp"
    );
}

// Initialize whose turn is it
if (!isset($_SESSION['currentTurn'])) {
    $_SESSION['currentTurn'] = 'char1';
}

//Create a key:value array to use to create the block of each element
$characters = [
    'char1' => $_SESSION['char1'],
    'char2' => $_SESSION['char2']
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
</head>

<body>
    <h1>Character Battle</h1>

    <!-- Part where we can find the stats and action buttons of the fighters-->
    <div id="fight-zone" class="fight-zone">
        <!-- Create a card for each fighter in $characters array-->
        <?php foreach ($characters as $id => $char): ?>
            <!-- Give key as id, built in func get_class (return Warrior or Assassin) as class, 
             name of the instance by getName as data_Name and same but with ability  -->
            <div class="character-card" id="<?= htmlspecialchars($id) ?>"
                data-class="<?= htmlspecialchars(get_class($char)) ?>" data-name="<?= htmlspecialchars($char->getName()) ?>"
                data-ability="<?= htmlspecialchars($char->getAbilityName()) ?>">
                <img src="<?= htmlspecialchars($char->getSrcImg()); ?>" alt="<?= htmlspecialchars($char->getName()); ?>"
                    width="160">
                <h2><?= htmlspecialchars($char->getName()); ?></h2>
                <p>
                    Health: <span id="<?= $id ?>_health"><?= $char->getHealth(); ?></span> |
                   Intelligence: <span id="<?= $id ?>_intel"><?= $char->getIntelligence(); ?></span> |
                    Stamina: <span id="<?= $id ?>_stamina"><?= $char->getStamina(); ?></span>|
                    
                    Strength: <span id="<?= $id ?>_strength"><?= $char->getStrength(); ?></span> |
                    <br>
                    <?php
                    $reflect = new ReflectionClass($char);
                    $props = $reflect->getProperties(ReflectionProperty::IS_PUBLIC | ReflectionProperty::IS_PROTECTED | ReflectionProperty::IS_PRIVATE);

                    foreach ($props as $prop) {
                        $propName = $prop->getName();

                        // Skip name and srcImg and original stats
                        if (in_array($propName, ['name', 'health', 'strength', 'intelligence', 'stamina', 'srcImg',  'originalStrength', 'originalIntelligence'])) {
                            continue;
                        }

                        // Make it accessible in case it's private/protected
                        $prop->setAccessible(true);
                        $value = $prop->getValue($char);

                        // Convert property name to readable label
                        $label = ucfirst($propName);

                        // Set span with id like char1_health
                        echo "$label: <span id=\"{$id}_$propName\">$value</span> | ";
                    }
                    ?>
                </p>
                <div class="controls" id="<?= $id ?>_controls">
                    <!-- Buttons for each character -->
                    <button class="attack-btn" data-attacker="<?= $id ?>">Attack</button>
                    <button class="heal-btn" data-attacker="<?= $id ?>">Heal</button>
                    <button class="ability-btn" data-attacker="<?= $id ?>"><?= $char->getAbilityName() ?></button>
                    <button class="inspect-btn" data-attacker="<?= $id ?>">Inspect</button>
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