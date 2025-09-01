<?php require_once __DIR__ . '/../src/Character.php';
$godzilla = new Character();
$kong = new Character();
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