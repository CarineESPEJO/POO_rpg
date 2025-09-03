<?php
require_once __DIR__ . "/../src/Character.php";
require_once __DIR__ . "/../src/Warrior.php";
require_once __DIR__ . "/../src/Assassin.php";

session_start();

header('Content-Type: application/json');

// ensure characters exist in session
if (!isset($_SESSION['char1']) || !isset($_SESSION['char2'])) {
    // graceful initialization fallback (so direct calls don't 500)
    $_SESSION['char1'] = new Warrior(name: 'Godzilla', strength: 40, intelligence: 60, armor: 50, srcImg: "/assets/images/godzilla.webp");
    $_SESSION['char2'] = new Assassin(name: 'Kong', strength: 60, intelligence: 40, agility: 50, srcImg: "/assets/images/king_kong.webp");
}

$char1 = $_SESSION['char1'];
$char2 = $_SESSION['char2'];

$action = $_POST['action'] ?? '';
$player = $_POST['player'] ?? '';

$message = '';

if ($action === 'reset') {
    // Reset health and stamina to full while preserving class and stats
    $char1->setHealth(100);
    $char1->setStamina(100);
    $char2->setHealth(100);
    $char2->setStamina(100);

    // persist
    $_SESSION['char1'] = $char1;
    $_SESSION['char2'] = $char2;

    echo json_encode([
        'char1' => [
            'name' => $char1->getName(),
            'class' => get_class($char1),
            'strength' => $char1->getStrength(),
            'intelligence' => $char1->getIntelligence(),
            'stamina' => $char1->getStamina(),
            'health' => $char1->getHealth()
        ],
        'char2' => [
            'name' => $char2->getName(),
            'class' => get_class($char2),
            'strength' => $char2->getStrength(),
            'intelligence' => $char2->getIntelligence(),
            'stamina' => $char2->getStamina(),
            'health' => $char2->getHealth()
        ],
        'message' => "New fight started!"
    ]);
    exit;
}

// Validate player param
if (!in_array($player, ['char1', 'char2'], true)) {
    echo json_encode(['error' => 'Invalid player']); exit;
}

// resolve attacker / defender safely
$attacker = ($player === 'char1') ? $char1 : $char2;
$defender = ($player === 'char1') ? $char2 : $char1;

// route action to methods
switch ($action) {
    case 'attack':
        $message = $attacker->attack($defender);
        break;

    case 'heal':
        $message = $attacker->heal();
        break;

    case 'powerstrike':
        if ($attacker instanceof Warrior) {
            $message = $attacker->powerStrike($defender);
        } else {
            $message = "{$attacker->getName()} cannot use Power Strike.";
        }
        break;

    case 'sneakattack':
        if ($attacker instanceof Assassin) {
            $message = $attacker->sneakAttack($defender);
        } else {
            $message = "{$attacker->getName()} cannot use Sneak Attack.";
        }
        break;

    default:
        echo json_encode(['error' => 'Invalid action']); exit;
}

// persist objects back to session
$_SESSION['char1'] = $char1;
$_SESSION['char2'] = $char2;

// build response arrays (plain data)
$response = [
    'char1' => [
        'name' => $char1->getName(),
        'class' => get_class($char1),
        'strength' => $char1->getStrength(),
        'intelligence' => $char1->getIntelligence(),
        'stamina' => $char1->getStamina(),
        'health' => $char1->getHealth()
    ],
    'char2' => [
        'name' => $char2->getName(),
        'class' => get_class($char2),
        'strength' => $char2->getStrength(),
        'intelligence' => $char2->getIntelligence(),
        'stamina' => $char2->getStamina(),
        'health' => $char2->getHealth()
    ],
    'message' => $message
];

echo json_encode($response);
