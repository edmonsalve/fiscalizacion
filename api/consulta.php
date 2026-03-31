<?php
declare(strict_types=1);

require dirname(__DIR__) . '/lib/device_auth.php';

header('Content-Type: application/json; charset=utf-8');

$isCli = PHP_SAPI === 'cli';
$requestMethod = $isCli ? 'POST' : ($_SERVER['REQUEST_METHOD'] ?? 'GET');

if ($requestMethod !== 'POST') {
    http_response_code(405);
    echo json_encode([
        'message' => 'Método no permitido.',
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

if (!$isCli) {
    requireAuthorizedDeviceJson();
}

$rawInput = $isCli
    ? stream_get_contents(STDIN)
    : file_get_contents('php://input');
$decoded = json_decode($rawInput ?: '', true);
$ppu = strtoupper(preg_replace('/[^A-Z0-9]/', '', (string)($decoded['ppu'] ?? '')));

if ($ppu === '' || strlen($ppu) < 5) {
    http_response_code(422);
    echo json_encode([
        'message' => 'La PPU ingresada no es válida.',
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

/**
 * @return array<string, mixed>
 */
function decodeApiResponse(string|false $result, string $fallbackMessage): array
{
    if ($result === false || $result === '') {
        return [
            'estado' => 500,
            'msj_estado' => $fallbackMessage,
        ];
    }

    $decoded = json_decode($result, true);

    if (!is_array($decoded)) {
        return [
            'estado' => 500,
            'msj_estado' => 'El servicio externo devolvió una respuesta no válida.',
        ];
    }

    return $decoded;
}

/**
 * Ejecuta una petición HTTP JSON sencilla.
 *
 * @return array<string, mixed>
 */
function requestJson(string $url, string $method = 'GET', ?array $body = null): array
{
    $payload = $body !== null
        ? json_encode($body, JSON_UNESCAPED_UNICODE)
        : null;

    if (function_exists('curl_init')) {
        $headers = ['Accept: application/json'];

        if ($payload !== null) {
            $headers[] = 'Content-Type: application/json';
            $headers[] = 'Content-Length: ' . strlen((string)$payload);
        }

        $attempt = static function (bool $verifySsl) use ($url, $method, $payload, $headers): array {
            $ch = curl_init($url);
            curl_setopt_array($ch, [
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_CUSTOMREQUEST => $method,
                CURLOPT_HTTPHEADER => $headers,
                CURLOPT_TIMEOUT => 20,
                CURLOPT_SSL_VERIFYPEER => $verifySsl,
                CURLOPT_SSL_VERIFYHOST => $verifySsl ? 2 : 0,
            ]);

            if ($payload !== null) {
                curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
            }

            $result = curl_exec($ch);
            $errno = curl_errno($ch);
            $error = curl_error($ch);
            curl_close($ch);

            return [
                'result' => $result,
                'errno' => $errno,
                'error' => $error,
            ];
        };

        $secureResponse = $attempt(true);

        if ($secureResponse['errno'] === 0) {
            return decodeApiResponse($secureResponse['result'], 'No fue posible conectar con el servicio externo.');
        }

        $sslErrors = [35, 51, 58, 60, 77, 82, 83, 90, 91];
        if (in_array($secureResponse['errno'], $sslErrors, true)) {
            $insecureResponse = $attempt(false);

            if ($insecureResponse['errno'] === 0) {
                return decodeApiResponse($insecureResponse['result'], 'No fue posible conectar con el servicio externo.');
            }
        }

        return [
            'estado' => 500,
            'msj_estado' => 'No fue posible conectar con el servicio externo: ' . $secureResponse['error'],
        ];
    }

    $headers = ['Accept: application/json'];
    if ($payload !== null) {
        $headers[] = 'Content-Type: application/json';
        $headers[] = 'Content-Length: ' . strlen((string)$payload);
    }

    $attempt = static function (bool $verifySsl) use ($url, $method, $payload, $headers): string|false {
        $context = stream_context_create([
            'http' => [
                'method' => $method,
                'header' => implode("\r\n", $headers),
                'content' => $payload,
                'timeout' => 20,
                'ignore_errors' => true,
            ],
            'ssl' => [
                'verify_peer' => $verifySsl,
                'verify_peer_name' => $verifySsl,
            ],
        ]);

        return @file_get_contents($url, false, $context);
    };

    $result = $attempt(true);
    if ($result !== false) {
        return decodeApiResponse($result, 'No fue posible conectar con el servicio externo.');
    }

    $result = $attempt(false);
    return decodeApiResponse($result, 'No fue posible conectar con el servicio externo.');
}

$response = [
    'ppu' => $ppu,
    'permiso' => requestJson(
        'https://api.dcode.cl/api/ppu/consultaPPU.php',
        'POST',
        ['ppu' => $ppu]
    ),
    'soap' => requestJson(
        'https://api.dcode.cl/api/ppu/consultaSOAP.php?ppu=' . rawurlencode($ppu)
    ),
    'prt' => requestJson(
        'https://api.dcode.cl/api/ppu/consultaPRT.php?ppu=' . rawurlencode($ppu)
    ),
];

echo json_encode($response, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
