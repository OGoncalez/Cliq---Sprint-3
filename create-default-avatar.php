<?php
// Script para criar avatar padrão
$width = 300;
$height = 300;

// Criar imagem
$image = imagecreatetruecolor($width, $height);

// Cores
$purple = imagecolorallocate($image, 75, 0, 88);
$gold = imagecolorallocate($image, 255, 215, 0);

// Fundo roxo
imagefill($image, 0, 0, $purple);

// Desenhar círculo dourado
$centerX = $width / 2;
$centerY = $height / 2;
imagefilledellipse($image, $centerX, $centerY - 30, 100, 100, $gold);

// Desenhar corpo
imagefilledarc($image, $centerX, $centerY + 80, 180, 180, 0, 180, $gold, IMG_ARC_PIE);

// Criar diretório se não existir
if (!file_exists('uploads')) {
    mkdir('uploads', 0755, true);
}

// Salvar imagem
imagejpeg($image, 'uploads/default-avatar.jpg', 90);
imagedestroy($image);

echo "Avatar padrão criado com sucesso!";
?>
