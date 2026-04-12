<?php
declare(strict_types=1);

$projectRoot = dirname(__DIR__);
$appConfig = require $projectRoot . '/config.php';

$publicBaseUrl = rtrim((string)($appConfig['public_base_url'] ?? ''), '/');
$capacitorServerUrl = rtrim((string)(getenv('FISCALIZACION_CAP_SERVER_URL') ?: $publicBaseUrl), '/');

if ($capacitorServerUrl === '') {
    fwrite(STDERR, "No se pudo determinar la URL base para Capacitor.\n");
    exit(1);
}

$config = [
    'appId' => 'cl.petorca.fiscalizacion',
    'appName' => 'Fiscalizacion',
    'webDir' => 'native-shell',
    'server' => [
        'url' => $capacitorServerUrl . '/',
        'cleartext' => false,
    ],
    'android' => [
        'allowMixedContent' => false,
    ],
    'ios' => [
        'contentInset' => 'automatic',
    ],
];

$targetPath = $projectRoot . '/capacitor.config.json';
$encoded = json_encode($config, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);

if ($encoded === false) {
    fwrite(STDERR, "No se pudo serializar la configuracion de Capacitor.\n");
    exit(1);
}

$result = file_put_contents($targetPath, $encoded . PHP_EOL);
if ($result === false) {
    fwrite(STDERR, "No se pudo escribir {$targetPath}.\n");
    exit(1);
}

fwrite(STDOUT, "Capacitor configurado con server.url={$config['server']['url']}\n");
