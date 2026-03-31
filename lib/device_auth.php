<?php
declare(strict_types=1);

/**
 * @return array<string, mixed>
 */
function appConfig(): array
{
    static $config = null;

    if ($config === null) {
        $config = require dirname(__DIR__) . '/config.php';
    }

    return $config;
}

function db(): mysqli
{
    static $mysqli = null;

    if ($mysqli instanceof mysqli) {
        return $mysqli;
    }

    $dbConfig = appConfig()['db'];
    $mysqli = mysqli_init();
    $mysqli->real_connect(
        (string)$dbConfig['host'],
        (string)$dbConfig['username'],
        (string)$dbConfig['password'],
        (string)$dbConfig['database'],
        (int)$dbConfig['port']
    );
    $mysqli->set_charset((string)($dbConfig['charset'] ?? 'utf8mb4'));

    return $mysqli;
}

function generateSecret(int $bytes = 24): string
{
    return rtrim(strtr(base64_encode(random_bytes($bytes)), '+/', '-_'), '=');
}

function nowIso(): string
{
    return gmdate('c');
}

function nowDb(): string
{
    return gmdate('Y-m-d H:i:s');
}

function tokenHash(string $value): string
{
    return hash('sha256', $value);
}

function deviceExpiryDb(): string
{
    $days = (int)(appConfig()['device_ttl_days'] ?? 30);
    return gmdate('Y-m-d H:i:s', time() + ($days * 86400));
}

function activationExpiryDb(): string
{
    $hours = (int)(appConfig()['activation_code_ttl_hours'] ?? 24);
    return gmdate('Y-m-d H:i:s', time() + ($hours * 3600));
}

/**
 * @param mysqli_stmt $stmt
 * @return array<int, array<string, mixed>>
 */
function fetchAllAssoc(mysqli_stmt $stmt): array
{
    $result = $stmt->get_result();
    if (!$result instanceof mysqli_result) {
        return [];
    }

    return $result->fetch_all(MYSQLI_ASSOC);
}

/**
 * @param array<string, mixed> $row
 * @return array<string, mixed>
 */
function mapActivationCodeRow(array $row): array
{
    return [
        'id' => $row['public_id'],
        'plain_code' => $row['plain_code'] ?? null,
        'code_hash' => $row['code_hash'],
        'label' => $row['label'],
        'created_at' => isset($row['created_at']) ? gmdate('c', strtotime((string)$row['created_at'])) : null,
        'expires_at' => isset($row['expires_at']) ? gmdate('c', strtotime((string)$row['expires_at'])) : null,
        'used_at' => !empty($row['used_at']) ? gmdate('c', strtotime((string)$row['used_at'])) : null,
        'used_by_device_id' => $row['used_by_device_public_id'] ?? null,
        'status' => $row['status'],
    ];
}

/**
 * @param array<string, mixed> $row
 * @return array<string, mixed>
 */
function mapDeviceRow(array $row): array
{
    return [
        'id' => $row['public_id'],
        'name' => $row['name'],
        'token_hash' => $row['token_hash'],
        'status' => $row['status'],
        'created_at' => isset($row['created_at']) ? gmdate('c', strtotime((string)$row['created_at'])) : null,
        'expires_at' => isset($row['expires_at']) ? gmdate('c', strtotime((string)$row['expires_at'])) : null,
        'last_seen_at' => !empty($row['last_seen_at']) ? gmdate('c', strtotime((string)$row['last_seen_at'])) : null,
        'last_ip' => $row['last_ip'] ?? null,
        'user_agent' => $row['user_agent'] ?? null,
        'activation_label' => $row['activation_label'] ?? null,
        'activation_code_id' => $row['activation_code_public_id'] ?? null,
    ];
}

/**
 * @return array<string, mixed>
 */
function createActivationCode(string $label = ''): array
{
    $plainCode = strtoupper(substr(bin2hex(random_bytes(4)), 0, 8));
    $publicId = generateSecret(8);
    $safeLabel = trim($label) !== '' ? trim($label) : 'Sin referencia';
    $createdAt = nowDb();
    $expiresAt = activationExpiryDb();

    $stmt = db()->prepare(
        'INSERT INTO activation_codes (public_id, plain_code, code_hash, label, status, created_at, expires_at) VALUES (?, ?, ?, ?, ?, ?, ?)'
    );
    $status = 'active';
    $hash = tokenHash($plainCode);
    $stmt->bind_param('sssssss', $publicId, $plainCode, $hash, $safeLabel, $status, $createdAt, $expiresAt);
    $stmt->execute();
    $stmt->close();

    return [
        'id' => $publicId,
        'plain_code' => $plainCode,
        'code_hash' => $hash,
        'label' => $safeLabel,
        'created_at' => gmdate('c', strtotime($createdAt)),
        'expires_at' => gmdate('c', strtotime($expiresAt)),
        'used_at' => null,
        'used_by_device_id' => null,
        'status' => $status,
    ];
}

/**
 * @return array<string, mixed>|null
 */
function findActivationCode(string $plainCode): ?array
{
    $hash = tokenHash(strtoupper(trim($plainCode)));
    $sql = 'SELECT ac.*, d.public_id AS used_by_device_public_id
            FROM activation_codes ac
            LEFT JOIN devices d ON d.id = ac.used_by_device_id
            WHERE ac.code_hash = ?
            LIMIT 1';
    $stmt = db()->prepare($sql);
    $stmt->bind_param('s', $hash);
    $stmt->execute();
    $rows = fetchAllAssoc($stmt);
    $stmt->close();

    return $rows !== [] ? mapActivationCodeRow($rows[0]) : null;
}

/**
 * @return array<string, mixed>|null
 */
function findActivationCodeById(string $codeId): ?array
{
    $sql = 'SELECT ac.*, d.public_id AS used_by_device_public_id
            FROM activation_codes ac
            LEFT JOIN devices d ON d.id = ac.used_by_device_id
            WHERE ac.public_id = ?
            LIMIT 1';
    $stmt = db()->prepare($sql);
    $stmt->bind_param('s', $codeId);
    $stmt->execute();
    $rows = fetchAllAssoc($stmt);
    $stmt->close();

    return $rows !== [] ? mapActivationCodeRow($rows[0]) : null;
}

function markActivationCodeUsed(string $codeId, string $deviceId): void
{
    $sql = 'UPDATE activation_codes ac
            INNER JOIN devices d ON d.public_id = ?
            SET ac.used_at = ?, ac.used_by_device_id = d.id, ac.status = ?
            WHERE ac.public_id = ?';
    $stmt = db()->prepare($sql);
    $usedAt = nowDb();
    $status = 'used';
    $stmt->bind_param('ssss', $deviceId, $usedAt, $status, $codeId);
    $stmt->execute();
    $stmt->close();
}

function deactivateActivationCode(string $codeId): void
{
    $stmt = db()->prepare('UPDATE activation_codes SET status = ? WHERE public_id = ?');
    $status = 'inactive';
    $stmt->bind_param('ss', $status, $codeId);
    $stmt->execute();
    $stmt->close();
}

/**
 * @return array<string, mixed>
 */
function regenerateActivationCode(string $codeId): array
{
    $code = findActivationCodeById($codeId);
    if ($code === null) {
        throw new RuntimeException('No se encontró la referencia a regenerar.');
    }

    deactivateActivationCode($codeId);
    return createActivationCode((string)$code['label']);
}

/**
 * @return array<string, mixed>
 */
function enrollDevice(string $activationCode, string $deviceName, string $userAgent, string $ipAddress): array
{
    $code = findActivationCode($activationCode);

    if ($code === null) {
        throw new RuntimeException('Código de activación inválido.');
    }

    if (($code['status'] ?? '') !== 'active') {
        throw new RuntimeException('El código de activación ya fue utilizado o fue desactivado.');
    }

    if (strtotime((string)$code['expires_at']) < time()) {
        throw new RuntimeException('El código de activación ya expiró.');
    }

    $token = generateSecret(32);
    $publicId = generateSecret(10);
    $name = trim($deviceName) !== '' ? trim($deviceName) : 'Equipo sin nombre';
    $status = 'active';
    $createdAt = nowDb();
    $expiresAt = deviceExpiryDb();
    $tokenHashValue = tokenHash($token);

    $sql = 'INSERT INTO devices (public_id, name, token_hash, status, created_at, expires_at, last_seen_at, last_ip, user_agent, activation_label, activation_code_id)
            SELECT ?, ?, ?, ?, ?, ?, ?, ?, ?, ac.label, ac.id
            FROM activation_codes ac
            WHERE ac.public_id = ?';
    $stmt = db()->prepare($sql);
    $stmt->bind_param(
        'ssssssssss',
        $publicId,
        $name,
        $tokenHashValue,
        $status,
        $createdAt,
        $expiresAt,
        $createdAt,
        $ipAddress,
        $userAgent,
        $code['id']
    );
    $stmt->execute();
    $stmt->close();

    markActivationCodeUsed((string)$code['id'], $publicId);

    return [
        'id' => $publicId,
        'name' => $name,
        'token_hash' => $tokenHashValue,
        'status' => $status,
        'created_at' => gmdate('c', strtotime($createdAt)),
        'expires_at' => gmdate('c', strtotime($expiresAt)),
        'last_seen_at' => gmdate('c', strtotime($createdAt)),
        'last_ip' => $ipAddress,
        'user_agent' => $userAgent,
        'activation_label' => $code['label'],
        'token' => $token,
    ];
}

/**
 * @return array<string, mixed>|null
 */
function findDeviceByToken(string $token): ?array
{
    $hash = tokenHash($token);
    $sql = 'SELECT d.*, ac.public_id AS activation_code_public_id
            FROM devices d
            LEFT JOIN activation_codes ac ON ac.id = d.activation_code_id
            WHERE d.token_hash = ?
            LIMIT 1';
    $stmt = db()->prepare($sql);
    $stmt->bind_param('s', $hash);
    $stmt->execute();
    $rows = fetchAllAssoc($stmt);
    $stmt->close();

    return $rows !== [] ? mapDeviceRow($rows[0]) : null;
}

function touchDevice(string $token, string $ipAddress, string $userAgent): void
{
    $hash = tokenHash($token);
    $stmt = db()->prepare('UPDATE devices SET last_seen_at = ?, last_ip = ?, user_agent = ? WHERE token_hash = ?');
    $now = nowDb();
    $stmt->bind_param('ssss', $now, $ipAddress, $userAgent, $hash);
    $stmt->execute();
    $stmt->close();
}

function expireDeviceIfNeeded(string $deviceId): void
{
    $stmt = db()->prepare('UPDATE devices SET status = ? WHERE public_id = ? AND status = ? AND expires_at < UTC_TIMESTAMP()');
    $newStatus = 'expired';
    $currentStatus = 'active';
    $stmt->bind_param('sss', $newStatus, $deviceId, $currentStatus);
    $stmt->execute();
    $stmt->close();
}

/**
 * @return array<int, array<string, mixed>>
 */
function listDevices(): array
{
    $sql = 'SELECT d.*, ac.public_id AS activation_code_public_id
            FROM devices d
            LEFT JOIN activation_codes ac ON ac.id = d.activation_code_id
            ORDER BY d.id ASC';
    $result = db()->query($sql);
    $rows = $result instanceof mysqli_result ? $result->fetch_all(MYSQLI_ASSOC) : [];
    return array_map('mapDeviceRow', $rows);
}

/**
 * @return array<int, array<string, mixed>>
 */
function listActivationCodes(): array
{
    $sql = 'SELECT ac.*, d.public_id AS used_by_device_public_id
            FROM activation_codes ac
            LEFT JOIN devices d ON d.id = ac.used_by_device_id
            ORDER BY ac.id ASC';
    $result = db()->query($sql);
    $rows = $result instanceof mysqli_result ? $result->fetch_all(MYSQLI_ASSOC) : [];
    return array_map('mapActivationCodeRow', $rows);
}

function revokeDevice(string $deviceId): void
{
    $stmt = db()->prepare('UPDATE devices SET status = ? WHERE public_id = ?');
    $status = 'revoked';
    $stmt->bind_param('ss', $status, $deviceId);
    $stmt->execute();
    $stmt->close();
}

function renewDevice(string $deviceId): void
{
    $stmt = db()->prepare('UPDATE devices SET status = ?, expires_at = ? WHERE public_id = ?');
    $status = 'active';
    $expiresAt = deviceExpiryDb();
    $stmt->bind_param('sss', $status, $expiresAt, $deviceId);
    $stmt->execute();
    $stmt->close();
}

function requestDeviceToken(): string
{
    $headers = function_exists('getallheaders') ? getallheaders() : [];
    $headers = array_change_key_case(is_array($headers) ? $headers : [], CASE_LOWER);

    return trim((string)($headers['x-device-token'] ?? $_SERVER['HTTP_X_DEVICE_TOKEN'] ?? ''));
}

function requestIp(): string
{
    return (string)($_SERVER['REMOTE_ADDR'] ?? '0.0.0.0');
}

function requestUserAgent(): string
{
    return (string)($_SERVER['HTTP_USER_AGENT'] ?? 'unknown');
}

/**
 * @return array<string, mixed>|null
 */
function authorizedDeviceFromRequest(): ?array
{
    $token = requestDeviceToken();
    if ($token === '') {
        return null;
    }

    $device = findDeviceByToken($token);
    if ($device === null || ($device['status'] ?? '') !== 'active') {
        return null;
    }

    $expiresAt = strtotime((string)($device['expires_at'] ?? ''));
    if ($expiresAt !== false && $expiresAt < time()) {
        expireDeviceIfNeeded((string)$device['id']);
        return null;
    }

    touchDevice($token, requestIp(), requestUserAgent());
    return $device;
}

function requireAuthorizedDeviceJson(): array
{
    $device = authorizedDeviceFromRequest();

    if ($device === null) {
        http_response_code(403);
        echo json_encode([
            'message' => 'Este equipo no está autorizado. Debe activarse antes de consultar.',
            'code' => 'device_not_authorized',
        ], JSON_UNESCAPED_UNICODE);
        exit;
    }

    return $device;
}

function isAdminAuthenticated(): bool
{
    if (session_status() !== PHP_SESSION_ACTIVE) {
        session_start();
    }

    return ($_SESSION['admin_authenticated'] ?? false) === true;
}

function attemptAdminLogin(string $secret): bool
{
    if (session_status() !== PHP_SESSION_ACTIVE) {
        session_start();
    }

    $expected = (string)appConfig()['admin_secret'];
    if ($expected === '' || $expected === 'cambiar-esta-clave-admin') {
        return false;
    }

    if (!hash_equals($expected, $secret)) {
        return false;
    }

    $_SESSION['admin_authenticated'] = true;
    return true;
}

function adminLogout(): void
{
    if (session_status() !== PHP_SESSION_ACTIVE) {
        session_start();
    }

    $_SESSION = [];
    session_destroy();
}
