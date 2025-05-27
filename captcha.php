<?php
session_start();
header("Content-type: image/png");

// Genera dos números aleatorios
$num1 = rand(1, 9);
$num2 = rand(1, 9);
$sum = $num1 + $num2;
$_SESSION['captcha_answer'] = $sum;

// Configuración de la imagen
$width = 100;
$height = 40;
$image = imagecreate($width, $height);

// Colores
$bg_color = imagecolorallocate($image, 255, 255, 255); // Fondo blanco
$text_color = imagecolorallocate($image, 0, 0, 0);       // Texto negro
$noise_color = imagecolorallocate($image, 150, 150, 150);  // Ruido en gris

// Añade ruido: dibuja líneas aleatorias para dificultar la lectura automática
for ($i = 0; $i < 50; $i++) {
    imageline($image, rand(0, $width), rand(0, $height),
              rand(0, $width), rand(0, $height), $noise_color);
}

// Texto del captcha (por ejemplo: "3 + 4 =")
$captcha_text = "$num1 + $num2 = ?";

// Centra el texto (usando imagestring; se puede usar imagettftext para más opciones)
$font = 5; // tamaño de fuente interno
$text_width = imagefontwidth($font) * strlen($captcha_text);
$text_height = imagefontheight($font);
$x = ($width - $text_width) / 2;
$y = ($height - $text_height) / 2;
imagestring($image, $font, $x, $y, $captcha_text, $text_color);

// Genera la imagen
imagepng($image);
imagedestroy($image);
?>
