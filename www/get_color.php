<?php

function colorPalette($imageFile) {
    $size = @getimagesize($imageFile);

    if ($size === false) {
        user_error("Unable to get image size data");
        return false;
    }

    $img = @imagecreatefromstring(file_get_contents($imageFile));

    if (!$img) {
        user_error("Unable to open image file");
        return false;
    }

    $thisColor = imagecolorat($img, 5, 5);
    $rgb = imagecolorsforindex($img, $thisColor);

    $red = round(round(($rgb['red'] / 0x33)) * 0x33);
    $green = round(round(($rgb['green'] / 0x33)) * 0x33);
    $blue = round(round(($rgb['blue'] / 0x33)) * 0x33);

    $thisRGB = sprintf('%02X%02X%02X', $red, $green, $blue);

    return $thisRGB;
}

if ($_POST) {
    $url = $_POST['url'];
    $color = colorPalette($url, 10, 4);

    echo $color;
}

?>
