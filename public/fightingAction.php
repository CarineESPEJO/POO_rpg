<?php

require_once __DIR__ . '/../src/Character.php';
require_once __DIR__ . '/../src/Warrior.php';
require_once __DIR__ . '/../src/Assassin.php';
require_once __DIR__ . '/../src/SpecialAbilityInterface.php';
require_once __DIR__ . '/../src/Arena.php';

session_start();

header('Content-Type: application/json');

$action = $_POST['action'] ?? null;
$player = $_POST['player'] ?? null;

if (!isset($_SESSION['char1']) || !isset($_SESSION['char2'])) {
    echo json_encode(['error' => 'Characters not initialized.']);
    exit;
}

$char1 = $_SESSION['char1'];
$char2 = $_SESSION['char2'];
$currentTurn = $_SESSION['currentTurn'] ?? 'char1';

$arena = new Arena();
$message = '';
$winner = null;

try {
    switch ($action) {
        case 'attack':
        case 'heal':
        case 'inspect':
        case 'useAbility':
            if ($player !== $currentTurn) {
                $message = "It's not your turn!";
            } else {
                $attacker = $_SESSION[$currentTurn];
                $defender = ($currentTurn === 'char1') ? $_SESSION['char2'] : $_SESSION['char1'];
                $message = $arena->playRound($attacker, $defender, $action);

                // Switch turn if not inspect
                if ($action !== 'inspect') {
                    $_SESSION['currentTurn'] = $currentTurn = ($currentTurn === 'char1') ? 'char2' : 'char1';
                }
            }
            break;

        case 'reset':
            $char1->resetStats();
            $char2->resetStats();
            $_SESSION['currentTurn'] = $currentTurn = 'char1';
            $message = "New fight started!";
            break;

        default:
            throw new InvalidArgumentException("Invalid action: $action");
    }

    $winner = $arena->checkWinner($char1, $char2);

    // Prepare JSON response
    $response = [
        'char1' => [
            'name' => $char1->getName(),
            'health' => $char1->getHealth(),
            'strength' => $char1->getStrength(),
            'intelligence' => $char1->getIntelligence(),
            'stamina' => $char1->getStamina(),
            'abilityName' => $char1 instanceof SpecialAbilityInterface ? $char1->getAbilityName() : ''
        ],
        'char2' => [
            'name' => $char2->getName(),
            'health' => $char2->getHealth(),
            'strength' => $char2->getStrength(),
            'intelligence' => $char2->getIntelligence(),
            'stamina' => $char2->getStamina(),
            'abilityName' => $char2 instanceof SpecialAbilityInterface ? $char2->getAbilityName() : ''
        ],
        'currentTurn' => $currentTurn,
        'message' => $message,
        'winner' => $winner
    ];

    echo json_encode($response);

} catch (Throwable $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
