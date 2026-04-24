<?php
declare(strict_types=1);

require dirname(__DIR__) . '/lib/device_auth.php';

$message = null;
$error = null;
$historyFilters = [
    'ppu' => trim((string)($_GET['history_ppu'] ?? '')),
    'device' => trim((string)($_GET['history_device'] ?? '')),
    'date_from' => trim((string)($_GET['history_date_from'] ?? '')),
    'date_to' => trim((string)($_GET['history_date_to'] ?? '')),
];

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
$history = isAdminAuthenticated() ? listFiscalizationHistory($historyFilters) : [];
?>
<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Administración de Equipos</title>
    <style>
        :root {
            --bg: #e4f2f7;
            --surface: #ffffff;
            --surface-alt: #f1f9fd;
            --primary: #124f87;
            --primary-strong: #071c2a;
            --accent: #1cd3c6;
            --warning: #f4a300;
            --warning-strong: #dd9300;
            --text: #071c2a;
            --muted: #5f7891;
            --border: #e3f0f6;
            --border-strong: #c7dce8;
            --ok: #168d84;
            --error: #9f6135;
            --shadow: 0 16px 34px rgba(7, 28, 42, 0.14);
            --radius: 26px;
            --radius-sm: 14px;
        }

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            min-height: 100vh;
            font-family: "Aptos", "Trebuchet MS", "Segoe UI", sans-serif;
            color: var(--text);
            background:
                radial-gradient(circle at top, rgba(255, 255, 255, 0.36), transparent 32%),
                linear-gradient(180deg, #eef8fb 0%, var(--bg) 42%, #d8eaf1 100%);
        }

        .app-shell {
            width: min(100%, 1100px);
            margin: 0 auto;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        .page-header {
            position: relative;
            min-height: 250px;
            padding: 30px 24px 48px;
            background:
                radial-gradient(circle at top, rgba(255, 255, 255, 0.2), transparent 34%),
                linear-gradient(180deg, #27d7cb 0%, var(--accent) 58%, #16c6ba 100%);
            overflow: hidden;
        }

        .page-header::before,
        .page-header::after {
            content: "";
            position: absolute;
            border-radius: 999px;
            background: rgba(255, 255, 255, 0.12);
            pointer-events: none;
        }

        .page-header::before {
            width: 260px;
            height: 260px;
            top: -100px;
            right: -70px;
        }

        .page-header::after {
            width: 180px;
            height: 180px;
            left: -70px;
            bottom: -60px;
        }

        .hero-inner {
            position: relative;
            z-index: 1;
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
            gap: 10px;
        }

        .hero-logo {
            width: min(180px, 34vw);
            max-height: 162px;
            object-fit: contain;
            filter: drop-shadow(0 12px 18px rgba(7, 28, 42, 0.16));
        }

        .hero-title {
            margin: 0;
            color: #ffffff;
            font-size: clamp(2rem, 3.6vw, 2.3rem);
            line-height: 1.05;
            letter-spacing: -0.02em;
        }

        .hero-copy {
            margin: 0;
            color: rgba(255, 255, 255, 0.94);
            font-size: 1rem;
        }

        .content-stack {
            width: 94%;
            margin: -34px auto 0;
            position: relative;
            z-index: 2;
            display: grid;
            gap: 18px;
            padding-bottom: 24px;
            flex: 1;
        }

        .card {
            background: var(--surface);
            border: 1px solid rgba(255, 255, 255, 0.76);
            border-radius: var(--radius);
            padding: 22px 20px;
            box-shadow: var(--shadow);
        }

        .card h2 {
            margin: 0 0 8px;
            color: var(--primary-strong);
            font-size: 1.05rem;
            text-transform: uppercase;
        }

        .section-copy {
            margin: 0 0 16px;
            color: var(--muted);
            line-height: 1.45;
        }

        .device-card {
            display: flex;
            align-items: center;
            gap: 16px;
            padding: 18px 16px;
        }

        .device-brand {
            width: 60px;
            height: 60px;
            object-fit: contain;
            flex: 0 0 60px;
        }

        .device-copy {
            min-width: 0;
        }

        .device-title,
        .device-name {
            display: block;
            text-transform: uppercase;
            line-height: 1.1;
        }

        .device-title {
            color: var(--primary-strong);
            font-size: 0.86rem;
            font-weight: 800;
            letter-spacing: 0.03em;
        }

        .device-name {
            margin-top: 8px;
            font-size: 1.3rem;
            font-weight: 800;
            color: #183656;
            word-break: break-word;
        }

        .grid {
            display: grid;
            gap: 12px;
        }

        .grid.filters {
            margin-bottom: 16px;
        }

        .field-label {
            display: block;
            margin-bottom: 10px;
            color: var(--primary-strong);
            font-size: 0.94rem;
            font-weight: 700;
        }

        input,
        button {
            font: inherit;
        }

        input {
            width: 100%;
            min-height: 50px;
            padding: 12px 14px;
            border-radius: var(--radius-sm);
            border: 1px solid #c7deea;
            background: #ffffff;
            color: var(--text);
        }

        input:focus {
            outline: 3px solid rgba(28, 211, 198, 0.18);
            border-color: var(--accent);
        }

        button {
            min-height: 50px;
            padding: 12px 16px;
            border-radius: var(--radius-sm);
            border: 1px solid var(--warning);
            background: var(--warning);
            color: #ffffff;
            font-weight: 700;
            cursor: pointer;
            transition: transform 0.15s ease, box-shadow 0.15s ease, background 0.15s ease;
            box-shadow: 0 12px 20px rgba(244, 163, 0, 0.2);
        }

        button:hover,
        button:focus-visible {
            background: var(--warning-strong);
            border-color: var(--warning-strong);
            box-shadow: 0 14px 24px rgba(244, 163, 0, 0.26);
        }

        button:active {
            transform: scale(0.98);
        }

        .inline-form {
            margin: 0;
        }

        .inline-form + .inline-form {
            margin-top: 8px;
        }

        .msg,
        .err {
            padding: 12px 14px;
            border-radius: 16px;
            line-height: 1.4;
        }

        .msg {
            background: #e8faf6;
            color: var(--ok);
        }

        .err {
            background: #fff3e9;
            color: var(--error);
        }

        .table-wrap {
            overflow-x: auto;
            border: 1px solid var(--border);
            border-radius: 18px;
            background: #fbfeff;
        }

        table {
            width: 100%;
            min-width: 760px;
            border-collapse: collapse;
            font-size: 0.92rem;
        }

        th,
        td {
            padding: 12px 10px;
            border-bottom: 1px solid var(--border);
            text-align: left;
            vertical-align: top;
        }

        th {
            background: rgba(18, 79, 135, 0.06);
            color: var(--primary-strong);
            font-size: 0.82rem;
            text-transform: uppercase;
            letter-spacing: 0.03em;
        }

        tbody tr:last-child td {
            border-bottom: 0;
        }

        .actions-card {
            display: flex;
            justify-content: flex-end;
        }

        .app-footer {
            padding: 6px 18px 26px;
            text-align: center;
        }

        .footer-brand {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            color: var(--primary);
            font-size: 0.82rem;
        }

        .footer-brand img {
            display: block;
            width: auto;
            height: 22px;
            object-fit: contain;
        }

        @media (min-width: 720px) {
            .grid.two-cols {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }

            .grid.filters {
                grid-template-columns: 1.1fr 1.1fr 0.8fr 0.8fr auto;
                align-items: end;
            }
        }

        @media (max-width: 560px) {
            .content-stack {
                width: calc(100% - 18px);
            }

            .card {
                padding-inline: 16px;
            }

            .device-card {
                align-items: flex-start;
            }

            .device-name {
                font-size: 1.12rem;
            }
        }
    </style>
</head>
<body>
<main class="app-shell">
    <header class="page-header">
        <div class="hero-inner">
            <img class="hero-logo" src="../docs/images/logoFisca.png" alt="Logo de la aplicación Fiscalización por PPU">
            <h1 class="hero-title">Administración de Equipos</h1>
            <p class="hero-copy">Panel interno para activar códigos, administrar dispositivos y revisar historial.</p>
        </div>
    </header>

    <section class="content-stack">
        <article class="card device-card">
            <img class="device-brand" src="../api/logoPET.png" alt="Escudo Municipalidad de Petorca">
            <div class="device-copy">
                <strong class="device-title">Módulo administrativo</strong>
                <span class="device-name">Fiscalización Municipalidad de Petorca</span>
            </div>
        </article>

        <?php if ($message !== null): ?><p class="msg"><?= htmlspecialchars($message, ENT_QUOTES, 'UTF-8') ?></p><?php endif; ?>
        <?php if ($error !== null): ?><p class="err"><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></p><?php endif; ?>

        <?php if (!isAdminAuthenticated()): ?>
            <section class="card">
                <h2>Ingreso administrativo</h2>
                <p class="section-copy">Ingrese la clave administrativa para gestionar códigos de activación y equipos autorizados.</p>
                <form method="post" class="grid">
                    <input type="hidden" name="action" value="login">
                    <div>
                        <label class="field-label" for="admin_secret">Clave administrativa</label>
                        <input id="admin_secret" type="password" name="admin_secret" placeholder="Ingrese la clave administrativa">
                    </div>
                    <button type="submit">Ingresar</button>
                </form>
            </section>
        <?php else: ?>
            <section class="card">
                <h2>Generar código de activación</h2>
                <p class="section-copy">Cree códigos nuevos para habilitar equipos o reasignar unidades de fiscalización.</p>
                <form method="post" class="grid two-cols">
                    <input type="hidden" name="action" value="create_code">
                    <div>
                        <label class="field-label" for="label">Referencia del equipo o unidad</label>
                        <input id="label" type="text" name="label" placeholder="Ej: Móvil 3 o Unidad Centro">
                    </div>
                    <div style="display:grid;align-items:end;">
                        <button type="submit">Generar código</button>
                    </div>
                </form>
            </section>

            <section class="card">
                <h2>Códigos emitidos</h2>
                <div class="table-wrap">
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
                                        <form method="post" class="inline-form">
                                            <input type="hidden" name="action" value="deactivate_code">
                                            <input type="hidden" name="code_id" value="<?= htmlspecialchars((string)$code['id'], ENT_QUOTES, 'UTF-8') ?>">
                                            <button type="submit">Desactivar</button>
                                        </form>
                                    <?php endif; ?>
                                    <form method="post" class="inline-form">
                                        <input type="hidden" name="action" value="regenerate_code">
                                        <input type="hidden" name="code_id" value="<?= htmlspecialchars((string)$code['id'], ENT_QUOTES, 'UTF-8') ?>">
                                        <button type="submit">Generar nuevo</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </section>

            <section class="card">
                <h2>Equipos enrolados</h2>
                <div class="table-wrap">
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
                                        <form method="post" class="inline-form">
                                            <input type="hidden" name="action" value="revoke_device">
                                            <input type="hidden" name="device_id" value="<?= htmlspecialchars((string)$device['id'], ENT_QUOTES, 'UTF-8') ?>">
                                            <button type="submit">Revocar</button>
                                        </form>
                                    <?php else: ?>
                                        <form method="post" class="inline-form">
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
                </div>
            </section>

            <section class="card">
                <h2>Historial de fiscalizaciones</h2>
                <form method="get" class="grid filters">
                    <div>
                        <label class="field-label" for="history_ppu">PPU</label>
                        <input id="history_ppu" type="text" name="history_ppu" placeholder="Filtrar por PPU" value="<?= htmlspecialchars($historyFilters['ppu'], ENT_QUOTES, 'UTF-8') ?>">
                    </div>
                    <div>
                        <label class="field-label" for="history_device">Equipo</label>
                        <input id="history_device" type="text" name="history_device" placeholder="Filtrar por equipo" value="<?= htmlspecialchars($historyFilters['device'], ENT_QUOTES, 'UTF-8') ?>">
                    </div>
                    <div>
                        <label class="field-label" for="history_date_from">Desde</label>
                        <input id="history_date_from" type="date" name="history_date_from" value="<?= htmlspecialchars($historyFilters['date_from'], ENT_QUOTES, 'UTF-8') ?>">
                    </div>
                    <div>
                        <label class="field-label" for="history_date_to">Hasta</label>
                        <input id="history_date_to" type="date" name="history_date_to" value="<?= htmlspecialchars($historyFilters['date_to'], ENT_QUOTES, 'UTF-8') ?>">
                    </div>
                    <div style="display:grid;align-items:end;">
                        <button type="submit">Filtrar historial</button>
                    </div>
                </form>
                <div class="table-wrap">
                    <table>
                        <thead>
                            <tr><th>Fecha y hora</th><th>PPU</th><th>Equipo</th><th>ID equipo</th><th>IP</th></tr>
                        </thead>
                        <tbody>
                        <?php foreach ($history as $item): ?>
                            <tr>
                                <td><?= htmlspecialchars((string)($item['consulted_at'] ?? ''), ENT_QUOTES, 'UTF-8') ?></td>
                                <td><?= htmlspecialchars((string)($item['ppu'] ?? ''), ENT_QUOTES, 'UTF-8') ?></td>
                                <td><?= htmlspecialchars((string)($item['device_name'] ?? ''), ENT_QUOTES, 'UTF-8') ?></td>
                                <td><?= htmlspecialchars((string)($item['device_public_id'] ?? ''), ENT_QUOTES, 'UTF-8') ?></td>
                                <td><?= htmlspecialchars((string)($item['ip_address'] ?? ''), ENT_QUOTES, 'UTF-8') ?></td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </section>

            <section class="card actions-card">
                <form method="post">
                    <input type="hidden" name="action" value="logout">
                    <button type="submit">Cerrar sesión administrativa</button>
                </form>
            </section>
        <?php endif; ?>
    </section>

    <footer class="app-footer">
        <div class="footer-brand">
            <span>Powered by</span>
            <img src="../api/DC.png" alt="Logo dCode">
            <span>dCode</span>
        </div>
    </footer>
</main>
</body>
</html>
