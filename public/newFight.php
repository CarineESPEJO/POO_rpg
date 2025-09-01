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

$godzilla->life = 100;
$godzilla->strength = 40;
$godzilla->intelligence = 60;
$godzilla->stamina = 100;

$kong->life = 100;
$kong->strength = 60;
$kong->intelligence = 40;
$kong->stamina = 100;



$_SESSION['godzilla'] = $godzilla;
$_SESSION['kong'] = $kong;

//JS
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
