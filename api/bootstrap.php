<?php
declare(strict_types=1);

require dirname(__DIR__) . '/lib/device_auth.php';

header('Content-Type: application/json; charset=utf-8');

$device = authorizedDeviceFromRequest();

echo json_encode([
    'authorized' => $device !== null,
    'device' => $device === null ? null : [
        'id' => $device['id'],
        'name' => $device['name'],
        'created_at' => $device['created_at'],
    ],
    'app_name' => appConfig()['app_name'],
], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
