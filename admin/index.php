<?php
declare(strict_types=1);

require dirname(__DIR__) . '/lib/device_auth.php';

$message = null;
$error = null;

if (($_POST['action'] ?? '') === 'login') {
    if (!attemptAdminLogin((string)($_POST['admin_secret'] ?? ''))) {
        $error = 'Clave administrativa incorrecta o no configurada.';
    } else {
        $message = 'Acceso administrativo habilitado.';
    }
}

if (isAdminAuthenticated()) {
    $action = (string)($_POST['action'] ?? '');

    if ($action === 'create_code') {
        $record = createActivationCode((string)($_POST['label'] ?? ''));
        $message = 'Código generado: ' . $record['plain_code'];
    }

    if ($action === 'deactivate_code') {
        deactivateActivationCode((string)($_POST['code_id'] ?? ''));
        $message = 'Referencia desactivada correctamente.';
    }

    if ($action === 'regenerate_code') {
        $record = regenerateActivationCode((string)($_POST['code_id'] ?? ''));
        $message = 'Nuevo código generado: ' . $record['plain_code'];
    }

    if ($action === 'revoke_device') {
        revokeDevice((string)($_POST['device_id'] ?? ''));
        $message = 'Equipo revocado correctamente.';
    }

    if ($action === 'renew_device') {
        renewDevice((string)($_POST['device_id'] ?? ''));
        $message = 'Equipo renovado por 30 días.';
    }

    if ($action === 'logout') {
        adminLogout();
        header('Location: index.php');
        exit;
    }
}

$devices = isAdminAuthenticated() ? listDevices() : [];
$codes = isAdminAuthenticated()
    ? array_values(array_filter(
        listActivationCodes(),
        static fn (array $code): bool => ($code['status'] ?? '') !== 'inactive'
    ))
    : [];
?>
<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Administración de Equipos</title>
    <style>
        body { font-family: "Segoe UI", sans-serif; margin: 0; background: #eef8fb; color: #17344f; }
        main { width: min(100%, 980px); margin: 0 auto; padding: 24px 16px 40px; }
        .card { background: #fff; border: 1px solid #cfe4ef; border-radius: 18px; padding: 18px; margin-top: 16px; }
        h1, h2 { margin: 0 0 12px; }
        input, button { font: inherit; padding: 12px 14px; border-radius: 12px; border: 1px solid #cfe4ef; }
        input { width: 100%; }
        button { background: #124f87; color: #fff; cursor: pointer; }
        table { width: 100%; border-collapse: collapse; font-size: 0.92rem; }
        th, td { padding: 10px 8px; border-bottom: 1px solid #e5eef3; text-align: left; vertical-align: top; }
        .grid { display: grid; gap: 12px; }
        .msg { padding: 12px 14px; border-radius: 12px; background: #e8faf6; color: #168d84; }
        .err { padding: 12px 14px; border-radius: 12px; background: #fff3e9; color: #9f6135; }
    </style>
</head>
<body>
<main>
    <h1>Administración de Equipos</h1>
    <?php if ($message !== null): ?><p class="msg"><?= htmlspecialchars($message, ENT_QUOTES, 'UTF-8') ?></p><?php endif; ?>
    <?php if ($error !== null): ?><p class="err"><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></p><?php endif; ?>

    <?php if (!isAdminAuthenticated()): ?>
        <section class="card">
            <h2>Ingreso administrativo</h2>
            <form method="post" class="grid">
                <input type="hidden" name="action" value="login">
                <input type="password" name="admin_secret" placeholder="Clave administrativa">
                <button type="submit">Ingresar</button>
            </form>
        </section>
    <?php else: ?>
        <section class="card">
            <h2>Generar código de activación</h2>
            <form method="post" class="grid">
                <input type="hidden" name="action" value="create_code">
                <input type="text" name="label" placeholder="Referencia del equipo o unidad">
                <button type="submit">Generar código</button>
            </form>
        </section>

        <section class="card">
            <h2>Códigos emitidos</h2>
            <table>
                <thead>
                    <tr><th>Referencia</th><th>Código</th><th>Creado</th><th>Expira</th><th>Estado</th><th>Acción</th></tr>
                </thead>
                <tbody>
                <?php foreach (array_reverse($codes) as $code): ?>
                    <tr>
                        <td><?= htmlspecialchars((string)($code['label'] ?? ''), ENT_QUOTES, 'UTF-8') ?></td>
                        <td><strong><?= htmlspecialchars((string)($code['plain_code'] ?? 'No visible'), ENT_QUOTES, 'UTF-8') ?></strong></td>
                        <td><?= htmlspecialchars((string)($code['created_at'] ?? ''), ENT_QUOTES, 'UTF-8') ?></td>
                        <td><?= htmlspecialchars((string)($code['expires_at'] ?? ''), ENT_QUOTES, 'UTF-8') ?></td>
                        <td><?= htmlspecialchars((string)($code['status'] ?? ''), ENT_QUOTES, 'UTF-8') ?></td>
                        <td>
                            <?php if (($code['status'] ?? '') === 'active'): ?>
                                <form method="post" style="margin-bottom:8px;">
                                    <input type="hidden" name="action" value="deactivate_code">
                                    <input type="hidden" name="code_id" value="<?= htmlspecialchars((string)$code['id'], ENT_QUOTES, 'UTF-8') ?>">
                                    <button type="submit">Desactivar</button>
                                </form>
                            <?php endif; ?>
                            <form method="post">
                                <input type="hidden" name="action" value="regenerate_code">
                                <input type="hidden" name="code_id" value="<?= htmlspecialchars((string)$code['id'], ENT_QUOTES, 'UTF-8') ?>">
                                <button type="submit">Generar nuevo</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </section>

        <section class="card">
            <h2>Equipos enrolados</h2>
            <table>
                <thead>
                    <tr><th>Equipo</th><th>Alta</th><th>Vence</th><th>Último uso</th><th>Estado</th><th>Acción</th></tr>
                </thead>
                <tbody>
                <?php foreach (array_reverse($devices) as $device): ?>
                    <tr>
                        <td><?= htmlspecialchars((string)($device['name'] ?? ''), ENT_QUOTES, 'UTF-8') ?></td>
                        <td><?= htmlspecialchars((string)($device['created_at'] ?? ''), ENT_QUOTES, 'UTF-8') ?></td>
                        <td><?= htmlspecialchars((string)($device['expires_at'] ?? ''), ENT_QUOTES, 'UTF-8') ?></td>
                        <td><?= htmlspecialchars((string)($device['last_seen_at'] ?? ''), ENT_QUOTES, 'UTF-8') ?></td>
                        <td><?= htmlspecialchars((string)($device['status'] ?? ''), ENT_QUOTES, 'UTF-8') ?></td>
                        <td>
                            <?php if (($device['status'] ?? '') === 'active'): ?>
                                <form method="post">
                                    <input type="hidden" name="action" value="revoke_device">
                                    <input type="hidden" name="device_id" value="<?= htmlspecialchars((string)$device['id'], ENT_QUOTES, 'UTF-8') ?>">
                                    <button type="submit">Revocar</button>
                                </form>
                            <?php else: ?>
                                <form method="post">
                                    <input type="hidden" name="action" value="renew_device">
                                    <input type="hidden" name="device_id" value="<?= htmlspecialchars((string)$device['id'], ENT_QUOTES, 'UTF-8') ?>">
                                    <button type="submit">Renovar 30 días</button>
                                </form>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </section>

        <section class="card">
            <form method="post">
                <input type="hidden" name="action" value="logout">
                <button type="submit">Cerrar sesión administrativa</button>
            </form>
        </section>
    <?php endif; ?>
</main>
</body>
</html>
