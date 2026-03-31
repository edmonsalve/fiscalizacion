<?php
declare(strict_types=1);

require dirname(__DIR__) . '/lib/device_auth.php';

header('Content-Type: application/json; charset=utf-8');

if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'POST') {
    http_response_code(405);
    echo json_encode(['message' => 'Método no permitido.'], JSON_UNESCAPED_UNICODE);
    exit;
}

$payload = json_decode(file_get_contents('php://input') ?: '', true);
$activationCode = strtoupper(trim((string)($payload['activation_code'] ?? '')));
$deviceName = trim((string)($payload['device_name'] ?? ''));

if ($activationCode === '' || strlen($activationCode) < 6) {
    http_response_code(422);
    echo json_encode(['message' => 'Debes ingresar un código de activación válido.'], JSON_UNESCAPED_UNICODE);
    exit;
}

try {
    $device = enrollDevice($activationCode, $deviceName, requestUserAgent(), requestIp());
    echo json_encode([
        'message' => 'Equipo activado correctamente.',
        'device' => [
            'id' => $device['id'],
            'name' => $device['name'],
        ],
        'token' => $device['token'],
    ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
} catch (RuntimeException $exception) {
    http_response_code(422);
    echo json_encode([
        'message' => $exception->getMessage(),
    ], JSON_UNESCAPED_UNICODE);
}
