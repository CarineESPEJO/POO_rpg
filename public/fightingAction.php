<?php
require_once __DIR__ . '/../src/Character.php';
session_start();

function respondWithStats(Character $godzilla, Character $kong): void {
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
        ]
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


$action = $_POST['action'] ?? '';
$player = $_POST['player'] ?? 'godzilla';


switch ($action) {
    case 'attack':
        if ($player === 'godzilla') $godzilla->attack($kong);
        else $kong->attack($godzilla);
        break;

    case 'heal':
        if ($player === 'godzilla' && $godzilla->getHealth() > 0) $godzilla->heal();
        elseif ($player === 'kong' && $kong->getHealth() > 0) $kong->heal();
        break;

    case 'reset':
        $godzilla->setAllStats(health:100, strength:40, intelligence:60, stamina:100);
        $kong->setAllStats(health:100, strength:60, intelligence:40, stamina:100);
        break;

    default:
        http_response_code(400);
        echo json_encode(['error' => 'Invalid action']);
        exit;
}


$_SESSION['godzilla'] = $godzilla;
$_SESSION['kong'] = $kong;

// At the beginning of the file, post to sent to fighting.js
respondWithStats($godzilla, $kong);
