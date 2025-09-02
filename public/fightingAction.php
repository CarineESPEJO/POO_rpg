<?php
require_once __DIR__ . '/../src/Character.php';
require_once __DIR__ . '/../src/Warrior.php';
require_once __DIR__ . '/../src/Assassin.php';
session_start();

function respondWithStats($godzilla, $kong, string $message = '', array $history = []): void {
    header('Content-Type: application/json');
    echo json_encode([
        'godzilla' => [
            'intelligence' => $godzilla->getIntelligence(),
            'stamina' => $godzilla->getStamina(),
            'health' => $godzilla->getHealth()
        ],
        'kong' => [
            'intelligence' => $kong->getIntelligence(),
            'stamina' => $kong->getStamina(),
            'health' => $kong->getHealth()
        ],
        'message' => $message,
        'history' => $history
    ]);
    exit;
}

if (!isset($_SESSION['godzilla']) || !isset($_SESSION['kong'])) {
    http_response_code(500);
    echo json_encode(['error' => 'Characters not found in session']);
    exit;
}

$godzilla = $_SESSION['godzilla'];
$kong = $_SESSION['kong'];
$history = $_SESSION['history'] ?? [];

$action = $_POST['action'] ?? '';
$player = $_POST['player'] ?? 'godzilla';
$message = '';

switch ($action) {
    case 'attack':
        if ($player === 'godzilla') $message = $godzilla->attack($kong);
        else $message = $kong->attack($godzilla);
        break;

    case 'heal':
        if ($player === 'godzilla') $message = $godzilla->heal();
        else $message = $kong->heal();
        break;

    case 'powerstrike':
        if ($player === 'godzilla') $message = $godzilla->powerStrike($kong);
        break;

    case 'sneakattack':
        if ($player === 'kong') $message = $kong->sneakAttack($godzilla);
        break;

    case 'reset':
        $godzilla->setAllStats(100, 40, 60, 100);
        $kong->setAllStats(100, 60, 40, 100);
        $history = [];
        $message = '';
        break;

    default:
        http_response_code(400);
        echo json_encode(['error' => 'Invalid action']);
        exit;
}

// Add new message to history at the top
if ($message) array_unshift($history, $message);

$_SESSION['godzilla'] = $godzilla;
$_SESSION['kong'] = $kong;
$_SESSION['history'] = $history;

respondWithStats($godzilla, $kong, $message, $history);
