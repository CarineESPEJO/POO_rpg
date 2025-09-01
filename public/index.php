<?php require_once __DIR__ . '/../src/Character.php';
$imgsFile = "/assets/images/";
$godzilla = new Character();
$godzilla->name='Godzilla';
$godzilla->strength = 10;
$godzilla->intelligence = 50;
$godzilla -> srcImg = $imgsFile . "godzilla.webp"; 
$kong = new Character();
$kong->name='Godzilla';
$kong->strength = 20;
$kong->intelligence = 40;
$kong -> srcImg = $imgsFile . "king_kong.webp"; 
    ?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>RPG</title>
    <link rel="stylesheet" href="/assets/css/index.css">
</head>
<body>
<p>
    <img src="<?php echo $godzilla->srcImg ?>">
    <?php echo var_dump($godzilla);?> </p>
    <img src="<?php echo $kong->srcImg ?>">
    <p><?php echo var_dump($kong); ?> </p>
    <p>Godzilla: <br> <?php echo $godzilla->stamina; ?></p>
    <p>Kong: <br> <?php echo $kong->intelligence; ?> <br> <?php echo $kong->stamina; ?> <br> <?php echo $kong->life; ?></p>
    <?php $godzilla->fight($kong); ?>
    <p>Godzilla: <br> <?php echo $godzilla->stamina; ?></p>
    <p>Kong: <br> <?php echo $kong->intelligence; ?> <br> <?php echo $kong->stamina; ?> <br> <?php echo $kong->life; ?></p>
    <?php $kong->heal(); ?>
    <p><?php echo $kong->intelligence; ?></p>
</body>
</html>