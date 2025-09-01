<?php
require_once __DIR__ . '/../src/Character.php';
session_start();
ini_set('display_errors', 0);
error_reporting(E_ALL);

if (!isset($_SESSION['godzilla']) || !isset($_SESSION['kong'])) {
    http_response_code(500);
    echo json_encode(['error' => 'Characters not found in session']);
    exit;
}

$godzilla = $_SESSION['godzilla'];
$kong = $_SESSION['kong'];


$healer= $_POST['healer'] ?? 'godzilla';
if ($healer === 'godzilla' && $godzilla->life > 0) {
    $godzilla->heal();
} elseif ($healer === 'kong' && $kong->life > 0) {
    $kong->heal();
}


$_SESSION['godzilla'] = $godzilla;
$_SESSION['kong'] = $kong;


header('Content-Type: application/json');
echo json_encode([
    'godzilla' => [
        'intelligence' => $godzilla->intelligence,
        'stamina' => $godzilla->stamina,
        'life' => $godzilla->life
    ],
    'kong' => [
        'intelligence' => $kong->intelligence,
        'stamina' => $kong->stamina,
        'life' => $kong->life
    ]
]);
exit;
