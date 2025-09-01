<?php require_once __DIR__ . '/../src/Character.php';
$godzilla = new Character();
$godzilla->name='Godzilla';
$godzilla->life = 70;
$godzilla->strength = 50;
$godzilla->intellect = 30;
$godzilla->energy = 100;
$kong = new Character();
$kong->name='Godzilla';
$kong->strength = 70;
$kong->intellect = 40;
    ?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>RPG</title>
    <p><?php echo print_r($godzilla);?> </p>
    <p><?php echo print_r($kong); ?> </p>
</head>
<body>


</body>
</html>