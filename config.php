<?php
declare(strict_types=1);

return [
    'admin_secret' => getenv('FISCALIZACION_ADMIN_SECRET') ?: 'R3dmp3t0rc1',
    'activation_code_ttl_hours' => 24,
    'device_ttl_days' => 30,
    'app_name' => 'Fiscalizacion Petorca',
    'db' => [
        'host' => 'dev.dcode.cl',
        'port' => 53306,
        'database' => 'fiscalizacion',
        'username' => 'codex',
        'password' => 'kcm64%VI-9',
        'charset' => 'utf8mb4',
    ],
];
