<?php
declare(strict_types=1);

header('Content-Type: application/json; charset=utf-8');

if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'POST') {
    http_response_code(405);
    echo json_encode([
        'message' => 'Método no permitido.',
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

if (!isset($_FILES['photo']) || !is_array($_FILES['photo'])) {
    http_response_code(422);
    echo json_encode([
        'message' => 'Debes enviar una fotografía.',
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

$file = $_FILES['photo'];

if (($file['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) {
    http_response_code(422);
    echo json_encode([
        'message' => 'No fue posible recibir la fotografía.',
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

$mime = mime_content_type((string)$file['tmp_name']);
$supportedMimes = ['image/jpeg', 'image/png', 'image/webp'];

if (!in_array($mime, $supportedMimes, true)) {
    http_response_code(422);
    echo json_encode([
        'message' => 'Formato de imagen no soportado. Usa JPG, PNG o WEBP.',
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

if (!function_exists('imagecreatetruecolor')) {
    http_response_code(500);
    echo json_encode([
        'message' => 'La extensión GD no está disponible en el servidor.',
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

$tesseractBinary = trim((string)shell_exec('command -v tesseract 2>/dev/null'));
if ($tesseractBinary === '') {
    http_response_code(503);
    echo json_encode([
        'message' => 'El motor OCR aún no está instalado en el servidor.',
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

/**
 * @return resource|\GdImage
 */
function createImageResource(string $path, string $mime)
{
    return match ($mime) {
        'image/jpeg' => imagecreatefromjpeg($path),
        'image/png' => imagecreatefrompng($path),
        'image/webp' => imagecreatefromwebp($path),
        default => false,
    };
}

/**
 * @param resource|\GdImage $source
 * @return resource|\GdImage
 */
function cloneAndResizeImage($source, int $width, int $height, int $targetWidth)
{
    $scale = $targetWidth / max(1, $width);
    $targetHeight = max(1, (int)round($height * $scale));

    $canvas = imagecreatetruecolor($targetWidth, $targetHeight);
    $white = imagecolorallocate($canvas, 255, 255, 255);
    imagefill($canvas, 0, 0, $white);
    imagecopyresampled($canvas, $source, 0, 0, 0, 0, $targetWidth, $targetHeight, $width, $height);
    return $canvas;
}

/**
 * @param resource|\GdImage $source
 * @return resource|\GdImage
 */
function preprocessThreshold($source, int $width, int $height, int $threshold = 150)
{
    $canvas = cloneAndResizeImage($source, $width, $height, min(1800, max(900, $width)));
    imagefilter($canvas, IMG_FILTER_GRAYSCALE);
    imagefilter($canvas, IMG_FILTER_CONTRAST, -35);
    imagefilter($canvas, IMG_FILTER_BRIGHTNESS, 10);

    $targetWidth = imagesx($canvas);
    $targetHeight = imagesy($canvas);
    for ($y = 0; $y < $targetHeight; $y++) {
        for ($x = 0; $x < $targetWidth; $x++) {
            $rgb = imagecolorat($canvas, $x, $y);
            $gray = $rgb & 0xFF;
            $value = $gray > $threshold ? 255 : 0;
            $color = imagecolorallocate($canvas, $value, $value, $value);
            imagesetpixel($canvas, $x, $y, $color);
        }
    }

    return $canvas;
}

/**
 * @param resource|\GdImage $source
 * @return resource|\GdImage
 */
function preprocessGrayscale($source, int $width, int $height)
{
    $canvas = cloneAndResizeImage($source, $width, $height, min(1800, max(900, $width)));
    imagefilter($canvas, IMG_FILTER_GRAYSCALE);
    imagefilter($canvas, IMG_FILTER_CONTRAST, -25);
    imagefilter($canvas, IMG_FILTER_BRIGHTNESS, 5);
    return $canvas;
}

/**
 * @param resource|\GdImage $source
 * @return resource|\GdImage
 */
function cropRegion($source, float $topRatio, float $heightRatio)
{
    $width = imagesx($source);
    $height = imagesy($source);
    $cropY = max(0, (int)round($height * $topRatio));
    $cropHeight = max(1, min($height - $cropY, (int)round($height * $heightRatio)));

    $cropped = imagecreatetruecolor($width, $cropHeight);
    $white = imagecolorallocate($cropped, 255, 255, 255);
    imagefill($cropped, 0, 0, $white);
    imagecopy($cropped, $source, 0, 0, 0, $cropY, $width, $cropHeight);

    return $cropped;
}

/**
 * @param resource|\GdImage $image
 * @return array{ppu:string,raw_text:string,ocr_log:string}
 */
function runTesseractVariant($image, string $tesseractBinary, string $tempDir, string $variantName, int $psm): array
{
    $basePath = $tempDir . '/' . uniqid('ocr_' . $variantName . '_', true);
    $imagePath = $basePath . '.png';
    $outputBase = $basePath . '_result';
    $textPath = $outputBase . '.txt';

    imagepng($image, $imagePath);

    $command = sprintf(
        '%s %s %s --psm %d -c tessedit_char_whitelist=ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789 2>&1',
        escapeshellarg($tesseractBinary),
        escapeshellarg($imagePath),
        escapeshellarg($outputBase),
        $psm
    );

    $commandOutput = shell_exec($command);
    $rawText = is_file($textPath) ? (string)file_get_contents($textPath) : '';
    $ppu = normalizeCandidatePPU(extractCandidatePPU($rawText));

    @unlink($imagePath);
    @unlink($textPath);

    return [
        'ppu' => $ppu,
        'raw_text' => trim($rawText),
        'ocr_log' => trim((string)$commandOutput),
    ];
}

function extractCandidatePPU(string $text): string
{
    $normalized = strtoupper((string)preg_replace('/[^A-Z0-9]/', '', $text));

    if (preg_match('/[A-Z]{4}[0-9]{2}/', $normalized, $matches) === 1) {
        return $matches[0];
    }

    if (preg_match('/[A-Z]{2}[0-9]{4}/', $normalized, $matches) === 1) {
        return $matches[0];
    }

    if (preg_match('/[A-Z]{2}[A-Z0-9]{2}[0-9]{2}/', $normalized, $matches) === 1) {
        return $matches[0];
    }

    return '';
}

function normalizeCandidatePPU(string $candidate): string
{
    $candidate = strtoupper((string)preg_replace('/[^A-Z0-9]/', '', $candidate));

    if (strlen($candidate) !== 6) {
        return $candidate;
    }

    $asLetters = [
        '0' => 'O',
        '1' => 'I',
        '2' => 'Z',
        '3' => 'J',
        '4' => 'A',
        '5' => 'S',
        '6' => 'G',
        '7' => 'T',
        '8' => 'B',
        '9' => 'P',
    ];

    $asDigits = [
        'O' => '0',
        'Q' => '0',
        'D' => '0',
        'I' => '1',
        'L' => '1',
        'Z' => '2',
        'S' => '5',
        'B' => '8',
        'G' => '6',
        'T' => '7',
        'J' => '3',
    ];

    $chars = str_split($candidate);

    for ($i = 0; $i < 4; $i++) {
        if (ctype_digit($chars[$i]) && isset($asLetters[$chars[$i]])) {
            $chars[$i] = $asLetters[$chars[$i]];
        }
    }

    for ($i = 4; $i < 6; $i++) {
        if (ctype_alpha($chars[$i]) && isset($asDigits[$chars[$i]])) {
            $chars[$i] = $asDigits[$chars[$i]];
        }
    }

    return implode('', $chars);
}

/**
 * @param resource|\GdImage $source
 * @return resource|\GdImage
 */
function applyExifOrientationIfNeeded($source, string $path, string $mime)
{
    if ($mime !== 'image/jpeg' || !function_exists('exif_read_data')) {
        return $source;
    }

    $exif = @exif_read_data($path);
    $orientation = (int)($exif['Orientation'] ?? 1);

    return match ($orientation) {
        3 => imagerotate($source, 180, 0),
        6 => imagerotate($source, -90, 0),
        8 => imagerotate($source, 90, 0),
        default => $source,
    };
}

$source = createImageResource((string)$file['tmp_name'], $mime);
if ($source === false) {
    http_response_code(422);
    echo json_encode([
        'message' => 'No fue posible abrir la imagen.',
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

$source = applyExifOrientationIfNeeded($source, (string)$file['tmp_name'], $mime);
$width = imagesx($source);
$height = imagesy($source);

$tempDir = sys_get_temp_dir() . '/fiscalizacion_ocr';
if (!is_dir($tempDir)) {
    mkdir($tempDir, 0775, true);
}

$variants = [];
$variants[] = ['image' => preprocessGrayscale($source, $width, $height), 'name' => 'full_gray', 'psm' => 7];
$variants[] = ['image' => preprocessThreshold($source, $width, $height, 150), 'name' => 'full_bw_150', 'psm' => 7];
$variants[] = ['image' => preprocessThreshold($source, $width, $height, 130), 'name' => 'full_bw_130', 'psm' => 7];

$middleCrop = cropRegion($source, 0.35, 0.30);
$middleWidth = imagesx($middleCrop);
$middleHeight = imagesy($middleCrop);
$variants[] = ['image' => preprocessGrayscale($middleCrop, $middleWidth, $middleHeight), 'name' => 'mid_gray', 'psm' => 7];
$variants[] = ['image' => preprocessThreshold($middleCrop, $middleWidth, $middleHeight, 145), 'name' => 'mid_bw', 'psm' => 7];
$variants[] = ['image' => preprocessThreshold($middleCrop, $middleWidth, $middleHeight, 145), 'name' => 'mid_sparse', 'psm' => 8];

$bestAttempt = [
    'ppu' => '',
    'raw_text' => '',
    'ocr_log' => '',
];

foreach ($variants as $variant) {
    $attempt = runTesseractVariant($variant['image'], $tesseractBinary, $tempDir, $variant['name'], $variant['psm']);
    imagedestroy($variant['image']);

    if ($bestAttempt['raw_text'] === '' && $attempt['raw_text'] !== '') {
        $bestAttempt = $attempt;
    }

    if ($attempt['ppu'] !== '') {
        imagedestroy($middleCrop);
        imagedestroy($source);
        echo json_encode([
            'ppu' => $attempt['ppu'],
            'raw_text' => $attempt['raw_text'],
        ], JSON_UNESCAPED_UNICODE);
        exit;
    }
}

imagedestroy($middleCrop);
imagedestroy($source);

if ($bestAttempt['ppu'] === '') {
    http_response_code(422);
    echo json_encode([
        'message' => $bestAttempt['raw_text'] === ''
            ? 'La foto sí fue recibida, pero el OCR no logró detectar texto útil. Intenta con una imagen más cerca, frontal y con mejor luz.'
            : 'No se pudo reconocer una patente con suficiente claridad.',
        'raw_text' => $bestAttempt['raw_text'],
        'ocr_log' => $bestAttempt['ocr_log'],
    ], JSON_UNESCAPED_UNICODE);
    exit;
}
