<?php
declare(strict_types=1);

return [
    'admin_secret' => getenv('FISCALIZACION_ADMIN_SECRET') ?: 'R3dmp3t0rc1',
    'activation_code_ttl_hours' => 24,
    'device_ttl_days' => 30,
    'app_name' => 'Fiscalizacion Petorca',
    'public_base_url' => rtrim(getenv('FISCALIZACION_PUBLIC_BASE_URL') ?: 'https://fiscalizacion.dcode.cl', '/'),
    'external_services' => [
        'ppu_api_base_url' => rtrim(getenv('FISCALIZACION_PPU_API_BASE_URL') ?: 'https://apihv.dcode.cl/api/ppu', '/'),
    ],
    'db' => [
        'host' => 'localhost',
        'port' => 3306,
        'database' => 'fiscalizacion',
        'username' => 'codex',
        'password' => 'kcm64%VI-9',
        'charset' => 'utf8mb4',
    ],
];
