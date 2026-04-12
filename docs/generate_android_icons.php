<?php
declare(strict_types=1);

$root = dirname(__DIR__);
$sourcePath = $root . '/api/DC.png';
$iconsDir = __DIR__;
$androidResDir = $root . '/android/app/src/main/res';

if (!extension_loaded('gd')) {
    fwrite(STDERR, "GD extension is required.\n");
    exit(1);
}

$source = imagecreatefrompng($sourcePath);
if (!$source instanceof GdImage) {
    fwrite(STDERR, "Unable to load source image.\n");
    exit(1);
}

/**
 * @param array{0:int,1:int,2:int} $rgb
 */
function allocateColor(GdImage $image, array $rgb, int $alpha = 0): int
{
    return imagecolorallocatealpha($image, $rgb[0], $rgb[1], $rgb[2], $alpha);
}

function makeCanvas(int $size, ?array $backgroundRgb = null): GdImage
{
    $canvas = imagecreatetruecolor($size, $size);
    imagealphablending($canvas, false);
    imagesavealpha($canvas, true);

    if ($backgroundRgb === null) {
        $transparent = imagecolorallocatealpha($canvas, 0, 0, 0, 127);
        imagefilledrectangle($canvas, 0, 0, $size, $size, $transparent);
    } else {
        $background = allocateColor($canvas, $backgroundRgb);
        imagefilledrectangle($canvas, 0, 0, $size, $size, $background);
    }

    imagealphablending($canvas, true);

    return $canvas;
}

function resizeCentered(GdImage $target, GdImage $source, int $targetSize): void
{
    $canvasSize = imagesx($target);
    $destination = (int)(($canvasSize - $targetSize) / 2);

    imagecopyresampled(
        $target,
        $source,
        $destination,
        $destination,
        0,
        0,
        $targetSize,
        $targetSize,
        imagesx($source),
        imagesy($source)
    );
}

function whiteToTransparent(GdImage $image, int $threshold = 245): void
{
    imagealphablending($image, false);
    imagesavealpha($image, true);

    $width = imagesx($image);
    $height = imagesy($image);
    $transparent = imagecolorallocatealpha($image, 0, 0, 0, 127);

    for ($y = 0; $y < $height; $y++) {
        for ($x = 0; $x < $width; $x++) {
            $rgba = imagecolorat($image, $x, $y);
            $alpha = ($rgba >> 24) & 0x7F;
            $red = ($rgba >> 16) & 0xFF;
            $green = ($rgba >> 8) & 0xFF;
            $blue = $rgba & 0xFF;

            if ($alpha < 127 && $red >= $threshold && $green >= $threshold && $blue >= $threshold) {
                imagesetpixel($image, $x, $y, $transparent);
            }
        }
    }
}

function savePng(GdImage $image, string $path): void
{
    imagepng($image, $path, 9);
}

function generateLegacyLauncherIcon(GdImage $source, string $path, int $size): void
{
    $icon = makeCanvas($size, [255, 255, 255]);
    resizeCentered($icon, $source, (int)round($size * 0.8));
    savePng($icon, $path);
    imagedestroy($icon);
}

function generateAdaptiveForeground(GdImage $source, string $path, int $size): void
{
    $icon = makeCanvas($size, null);
    resizeCentered($icon, $source, (int)round($size * 0.7));
    whiteToTransparent($icon);
    savePng($icon, $path);
    imagedestroy($icon);
}

if (!is_dir($iconsDir)) {
    fwrite(STDERR, "Output directory is not available.\n");
    exit(1);
}

$master1024 = makeCanvas(1024, [255, 255, 255]);
resizeCentered($master1024, $source, 820);
savePng($master1024, $iconsDir . '/android-icon-1024.png');

$web512 = makeCanvas(512, [255, 255, 255]);
resizeCentered($web512, $source, 408);
savePng($web512, $iconsDir . '/icon-512.png');

$web192 = makeCanvas(192, [255, 255, 255]);
resizeCentered($web192, $source, 154);
savePng($web192, $iconsDir . '/icon-192.png');

$foreground = makeCanvas(1024, null);
resizeCentered($foreground, $source, 700);
whiteToTransparent($foreground);
savePng($foreground, $iconsDir . '/android-icon-foreground.png');

$background = makeCanvas(1024, [238, 248, 251]);
savePng($background, $iconsDir . '/android-icon-background.png');

$legacySizes = [
    'mdpi' => 48,
    'hdpi' => 72,
    'xhdpi' => 96,
    'xxhdpi' => 144,
    'xxxhdpi' => 192,
];

$foregroundSizes = [
    'mdpi' => 108,
    'hdpi' => 162,
    'xhdpi' => 216,
    'xxhdpi' => 324,
    'xxxhdpi' => 432,
];

foreach ($legacySizes as $density => $size) {
    $dir = $androidResDir . '/mipmap-' . $density;
    generateLegacyLauncherIcon($source, $dir . '/ic_launcher.png', $size);
    generateLegacyLauncherIcon($source, $dir . '/ic_launcher_round.png', $size);
}

foreach ($foregroundSizes as $density => $size) {
    $dir = $androidResDir . '/mipmap-' . $density;
    generateAdaptiveForeground($source, $dir . '/ic_launcher_foreground.png', $size);
}

imagedestroy($source);
imagedestroy($master1024);
imagedestroy($web512);
imagedestroy($web192);
imagedestroy($foreground);
imagedestroy($background);

fwrite(STDOUT, "Generated Android launcher icons and web icons.\n");
