<?php
declare(strict_types=1);
?>
<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#124f87">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="default">
    <meta name="apple-mobile-web-app-title" content="Fiscalizacion">
    <link rel="manifest" href="/manifest.json">
    <link rel="apple-touch-icon" href="/assets/icons/icon-192.png">
    <title>Fiscalización Vehicular</title>
    <style>
        :root {
            --bg: #e4f2f7;
            --surface: #ffffff;
            --surface-alt: #f1f9fd;
            --primary: #124f87;
            --primary-strong: #071c2a;
            --accent: #1cd3c6;
            --accent-soft: #dffaf7;
            --ok: #168d84;
            --warn: #bc6d3f;
            --warning: #f4a300;
            --warning-strong: #dd9300;
            --text: #071c2a;
            --muted: #5f7891;
            --border: #e3f0f6;
            --border-strong: #c7dce8;
            --shadow: 0 16px 34px rgba(7, 28, 42, 0.14);
            --radius: 26px;
            --radius-sm: 18px;
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
            width: min(100%, 540px);
            margin: 0 auto;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        .page-header {
            position: relative;
            min-height: 250px;
            padding: 28px 22px 44px;
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
            width: 240px;
            height: 240px;
            top: -90px;
            right: -60px;
        }

        .page-header::after {
            width: 180px;
            height: 180px;
            left: -80px;
            bottom: -70px;
        }

        .hero-inner {
            position: relative;
            z-index: 1;
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
        }

        .hero-logo {
            width: min(180px, 52vw);
            height: auto;
            max-height: 162px;
            object-fit: contain;
            filter: drop-shadow(0 12px 18px rgba(7, 28, 42, 0.16));
        }

        .hero-title {
            margin: 14px 0 0;
            color: #ffffff;
            font-size: clamp(1.9rem, 4vw, 2.1rem);
            line-height: 1.06;
            letter-spacing: -0.02em;
        }

        .content-stack {
            width: 94%;
            margin: -30px auto 0;
            position: relative;
            z-index: 2;
            display: grid;
            gap: 18px;
            padding-bottom: 24px;
            flex: 1;
        }

        .card {
            padding: 22px 20px;
            background: var(--surface);
            border: 1px solid rgba(255, 255, 255, 0.72);
            border-radius: var(--radius);
            box-shadow: var(--shadow);
        }

        .card h2 {
            margin: 0 0 8px;
            font-size: 1.05rem;
            color: var(--primary-strong);
            text-transform: uppercase;
            letter-spacing: -0.01em;
        }

        .card-copy,
        .hint,
        .intro-copy {
            margin: 0 0 18px;
            color: var(--muted);
            font-size: 0.94rem;
            line-height: 1.45;
        }

        .device-card {
            display: flex;
            align-items: center;
            gap: 16px;
            padding: 18px 16px;
        }

        .device-brand {
            flex: 0 0 60px;
            width: 60px;
            height: 60px;
            object-fit: contain;
        }

        .device-copy {
            min-width: 0;
        }

        .device-title,
        .device-name {
            display: block;
            line-height: 1.1;
            text-transform: uppercase;
        }

        .device-title {
            color: var(--primary-strong);
            font-size: 0.86rem;
            font-weight: 800;
            letter-spacing: 0.03em;
        }

        .device-name {
            margin-top: 8px;
            font-size: 1.32rem;
            font-weight: 800;
            color: #183656;
            word-break: break-word;
        }

        .field-group + .field-group {
            margin-top: 18px;
        }

        .field-label {
            display: block;
            margin-bottom: 10px;
            font-size: 0.94rem;
            font-weight: 700;
            color: var(--primary-strong);
        }

        .input,
        input[type="text"] {
            width: 100%;
            min-height: 52px;
            padding: 14px 16px;
            border: 1px solid #c7deea;
            border-radius: 14px;
            font-size: 1rem;
            color: var(--text);
            background: #ffffff;
        }

        input[type="text"] {
            font-weight: 700;
        }

        #ppu {
            font-size: 1.25rem;
            letter-spacing: 0.08em;
            text-transform: uppercase;
        }

        #deviceName::placeholder,
        #activationCode::placeholder,
        #ppu::placeholder {
            color: #9b9ea3;
        }

        .input:focus,
        input[type="text"]:focus {
            outline: 3px solid rgba(28, 211, 198, 0.18);
            border-color: var(--accent);
        }

        .actions {
            display: grid;
            gap: 12px;
            margin-top: 18px;
        }

        .result-actions {
            display: none;
            margin-top: 14px;
        }

        .result-actions.show {
            display: grid;
        }

        button {
            border: 1px solid transparent;
            border-radius: 14px;
            min-height: 52px;
            padding: 14px 18px;
            font: inherit;
            font-weight: 700;
            cursor: pointer;
            transition: transform 0.15s ease, box-shadow 0.15s ease, opacity 0.15s ease;
        }

        button:active {
            transform: scale(0.98);
        }

        .primary {
            background: var(--warning);
            border-color: var(--warning);
            color: #fff;
            box-shadow: 0 12px 20px rgba(244, 163, 0, 0.22);
        }

        .secondary {
            background: var(--surface-alt);
            color: #666666;
            border-color: var(--border);
        }

        .primary:hover,
        .primary:focus-visible {
            background: var(--warning-strong);
            border-color: var(--warning-strong);
            box-shadow: 0 14px 24px rgba(244, 163, 0, 0.26);
        }

        .secondary:hover,
        .secondary:focus-visible {
            border-color: var(--border-strong);
            box-shadow: 0 8px 18px rgba(18, 79, 135, 0.08);
        }

        button:disabled {
            cursor: not-allowed;
            opacity: 0.6;
            transform: none;
        }

        .compact-hidden {
            display: none !important;
        }

        .app-hidden {
            display: none !important;
        }

        .status {
            display: none;
            margin-top: 16px;
            padding: 14px 16px;
            border-radius: var(--radius-sm);
            font-size: 0.95rem;
            line-height: 1.4;
        }

        .status-inner {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .status-spinner {
            width: 18px;
            height: 18px;
            border-radius: 999px;
            border: 3px solid rgba(18, 79, 135, 0.18);
            border-top-color: var(--primary);
            animation: spin 0.85s linear infinite;
            flex: 0 0 18px;
        }

        .status-text {
            flex: 1;
        }

        .status.show {
            display: block;
        }

        .status.loading {
            background: #edf9fe;
            color: var(--primary-strong);
        }

        .status.error {
            background: #fff3e9;
            color: #9f6135;
        }

        .status.success {
            background: #e8faf6;
            color: var(--ok);
        }

        .status.error .status-spinner {
            border-color: rgba(159, 97, 53, 0.18);
            border-top-color: #9f6135;
        }

        .results {
            display: grid;
            gap: 18px;
        }

        .summary-card {
            display: none;
            background: rgba(255, 255, 255, 0.94);
            border: 1px solid rgba(255, 255, 255, 0.78);
            border-radius: var(--radius);
            box-shadow: var(--shadow);
            overflow: hidden;
        }

        .summary-card.show {
            display: block;
        }

        .summary-head {
            padding: 18px 18px 12px;
            background: linear-gradient(135deg, rgba(18, 79, 135, 0.12), rgba(28, 211, 198, 0.2));
        }

        .summary-head h3 {
            margin: 0;
            font-size: 1rem;
            color: var(--primary-strong);
            text-transform: uppercase;
        }

        .summary-head p {
            margin: 6px 0 0;
            color: var(--muted);
            font-size: 0.88rem;
        }

        .summary-body {
            display: grid;
            gap: 12px;
            padding: 14px 18px 20px;
        }

        .summary-item {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            padding: 14px 15px;
            background: #fbfeff;
            border: 1px solid var(--border);
            border-radius: 14px;
        }

        .summary-copy strong {
            display: block;
            font-size: 0.95rem;
            color: var(--primary-strong);
        }

        .summary-copy span {
            display: block;
            margin-top: 4px;
            color: var(--muted);
            font-size: 0.82rem;
            line-height: 1.35;
        }

        .traffic {
            width: 16px;
            height: 16px;
            border-radius: 999px;
            flex: 0 0 16px;
            box-shadow: inset 0 0 0 2px rgba(255, 255, 255, 0.65);
        }

        .traffic.green {
            background: #1cd3c6;
        }

        .traffic.yellow {
            background: #f2be63;
        }

        .traffic.red {
            background: #df8a6b;
        }

        .result-card {
            display: none;
            background: rgba(255, 255, 255, 0.94);
            border: 1px solid rgba(255, 255, 255, 0.78);
            border-radius: var(--radius);
            box-shadow: var(--shadow);
            overflow: hidden;
        }

        .result-card.show {
            display: block;
        }

        .result-head {
            padding: 18px 18px 12px;
            background: linear-gradient(135deg, rgba(18, 79, 135, 0.1), rgba(28, 211, 198, 0.18));
        }

        .result-head h3 {
            margin: 0;
            font-size: 1rem;
            color: var(--primary-strong);
            text-transform: uppercase;
        }

        .result-head p {
            margin: 6px 0 0;
            color: var(--muted);
            font-size: 0.88rem;
        }

        .result-body {
            padding: 14px 18px 20px;
        }

        .grid {
            display: grid;
            gap: 12px;
        }

        .item {
            padding: 14px;
            background: #fbfeff;
            border: 1px solid var(--border);
            border-radius: 14px;
        }

        .item span {
            display: block;
            font-size: 0.8rem;
            color: var(--muted);
            margin-bottom: 6px;
        }

        .item strong {
            font-size: 1rem;
            word-break: break-word;
            color: var(--primary-strong);
        }

        .badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 9px 13px;
            border-radius: 999px;
            font-size: 0.84rem;
            font-weight: 700;
            background: #e4fbf8;
            color: var(--ok);
        }

        .badge.error {
            background: #fff2ea;
            color: var(--warn);
        }

        .service-alert {
            margin-top: 12px;
            padding: 14px;
            border: 2px solid #df8a6b;
            border-radius: 14px;
            background: #fff2ea;
            color: #813f25;
            font-size: 0.94rem;
            font-weight: 700;
            line-height: 1.45;
        }

        .service-alert span {
            display: block;
            margin-top: 6px;
            color: #9f6135;
            font-size: 0.86rem;
            font-weight: 600;
        }

        .empty {
            color: var(--muted);
            font-size: 0.92rem;
        }

        @keyframes spin {
            from {
                transform: rotate(0deg);
            }

            to {
                transform: rotate(360deg);
            }
        }

        @media (min-width: 480px) {
            .result-actions {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }

            .grid {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }
        }

        @media (max-width: 420px) {
            .page-header {
                padding-inline: 18px;
            }

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
                font-size: 1.14rem;
            }
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

        .footer-links {
            margin-top: 10px;
            font-size: 0.82rem;
        }

        .footer-links a {
            color: var(--primary);
            font-weight: 700;
            text-decoration: none;
        }

        .footer-links a:hover,
        .footer-links a:focus {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <main class="app-shell">
        <header class="page-header">
            <div class="hero-inner">
                <img class="hero-logo" src="docs/images/logoFisca.png" alt="Logo de la aplicación Fiscalización por PPU">
                <h1 class="hero-title">Fiscalización por PPU</h1>
            </div>
        </header>

        <section class="content-stack">
            <article class="card device-card">
                <img class="device-brand" src="api/logoPET.png" alt="Escudo Municipalidad de Petorca">
                <div class="device-copy">
                    <strong class="device-title" id="deviceStatusTitle">Dispositivo pendiente</strong>
                    <span class="device-name" id="deviceStatusName">Pendiente de activación</span>
                </div>
            </article>

            <section class="card" id="enrollmentCard">
                <h2>Activación del equipo</h2>
                <p class="intro-copy">
                    Este dispositivo debe activarse solo una vez con el código entregado por administración.
                </p>

                <div class="field-group">
                    <label class="field-label" for="deviceName">Nombre del fiscalizador</label>
                    <input class="input" id="deviceName" name="deviceName" type="text" placeholder="INGRESE SU NOMBRE" autocomplete="off">
                </div>

                <div class="field-group">
                    <label class="field-label" for="activationCode">Código de activación</label>
                    <input class="input" id="activationCode" name="activationCode" type="text" maxlength="12" placeholder="EJ: A1B2C3D4" autocomplete="off">
                </div>

                <div class="actions">
                    <button class="primary" id="enrollButton" type="button">Activar equipo</button>
                </div>

                <div class="status" id="enrollmentStatus"></div>
            </section>

            <div id="securedContent" class="app-hidden">
                <section class="card">
                    <h2>Consulta PPU</h2>
                    <p class="hint" id="hintText">Ingrese PPU sin espacios ni guiones.</p>

                    <div class="field-group">
                        <label class="field-label" for="ppu">PPU</label>
                        <input id="ppu" name="ppu" type="text" maxlength="8" placeholder="EJ: KJJK18" autocomplete="off" inputmode="text">
                    </div>

                    <div class="actions" id="primaryActions">
                        <button class="primary" id="searchButton" type="button">Consultar datos</button>
                    </div>

                    <div class="actions result-actions" id="resultActions">
                        <button class="secondary" id="refreshButton" type="button" disabled>Refrescar consulta</button>
                        <button class="secondary" id="newSearchButton" type="button">Nueva consulta</button>
                    </div>

                    <div class="status" id="statusBox"></div>
                </section>

                <section class="results" id="results">
                    <article class="summary-card" id="summaryCard">
                        <div class="summary-head">
                            <h3>Resumen general</h3>
                            <p>Estado rápido de la documentación consultada</p>
                        </div>
                        <div class="summary-body" id="summaryBody"></div>
                    </article>

                    <article class="result-card" id="permisoCard">
                        <div class="result-head">
                            <h3>Permiso de circulación</h3>
                            <p>Datos de pago y vigencia</p>
                        </div>
                        <div class="result-body" id="permisoBody"></div>
                    </article>

                    <article class="result-card" id="soapCard">
                        <div class="result-head">
                            <h3>SOAP</h3>
                            <p>Seguro obligatorio</p>
                        </div>
                        <div class="result-body" id="soapBody"></div>
                    </article>

                    <article class="result-card" id="prtCard">
                        <div class="result-head">
                            <h3>Revisión técnica</h3>
                            <p>Planta revisora y vigencia</p>
                        </div>
                        <div class="result-body" id="prtBody"></div>
                    </article>
                </section>
            </div>
        </section>

        <footer class="app-footer">
            <div class="footer-brand">
                <span>Powered by</span>
                <img src="api/DC.png" alt="Logo dCode">
                <span>dCode</span>
            </div>
            <div class="footer-links">
                <a href="privacy-policy.html" target="_blank" rel="noopener noreferrer">Política de privacidad</a>
            </div>
        </footer>
    </main>

    <script>
        const DEVICE_TOKEN_KEY = 'fiscalizacion_device_token';
        const DEVICE_TOKEN_COOKIE_MAX_AGE = 60 * 60 * 24 * 30;
        const ppuInput = document.getElementById('ppu');
        const searchButton = document.getElementById('searchButton');
        const refreshButton = document.getElementById('refreshButton');
        const newSearchButton = document.getElementById('newSearchButton');
        const enrollmentCard = document.getElementById('enrollmentCard');
        const enrollmentStatus = document.getElementById('enrollmentStatus');
        const enrollButton = document.getElementById('enrollButton');
        const activationCodeInput = document.getElementById('activationCode');
        const deviceNameInput = document.getElementById('deviceName');
        const deviceStatusTitle = document.getElementById('deviceStatusTitle');
        const deviceStatusName = document.getElementById('deviceStatusName');
        const securedContent = document.getElementById('securedContent');
        const statusBox = document.getElementById('statusBox');
        const primaryActions = document.getElementById('primaryActions');
        const resultActions = document.getElementById('resultActions');
        const hintText = document.getElementById('hintText');
        const permisoCard = document.getElementById('permisoCard');
        const soapCard = document.getElementById('soapCard');
        const prtCard = document.getElementById('prtCard');
        const summaryCard = document.getElementById('summaryCard');
        const summaryBody = document.getElementById('summaryBody');
        const permisoBody = document.getElementById('permisoBody');
        const soapBody = document.getElementById('soapBody');
        const prtBody = document.getElementById('prtBody');
        const API_BASE_URL = window.location.origin;

        const apiUrl = (path) => new URL(path, `${API_BASE_URL}/`).toString();

        const fetchJson = async (path, options = {}, timeoutMs = 25000) => {
            const controller = new AbortController();
            const timeoutId = window.setTimeout(() => controller.abort(), timeoutMs);

            try {
                const response = await fetch(apiUrl(path), {
                    ...options,
                    signal: controller.signal
                });
                const data = await response.json();
                return { response, data };
            } catch (error) {
                if (error.name === 'AbortError') {
                    throw new Error('La conexión tardó demasiado. Revisa internet e intenta nuevamente.');
                }

                throw error;
            } finally {
                window.clearTimeout(timeoutId);
            }
        };

        const getCookieValue = (name) => {
            const prefix = `${encodeURIComponent(name)}=`;
            const entry = document.cookie.split('; ').find((item) => item.startsWith(prefix));
            return entry ? decodeURIComponent(entry.slice(prefix.length)) : '';
        };

        const setCookieValue = (name, value, maxAge) => {
            document.cookie = `${encodeURIComponent(name)}=${encodeURIComponent(value)}; path=/; max-age=${maxAge}; samesite=lax`;
        };

        const clearCookieValue = (name) => {
            document.cookie = `${encodeURIComponent(name)}=; path=/; max-age=0; samesite=lax`;
        };

        const readLegacyStoredDeviceToken = () => {
            try {
                const localToken = window.localStorage.getItem(DEVICE_TOKEN_KEY) || '';
                if (localToken !== '') {
                    return localToken;
                }
            } catch (error) {
            }

            return getCookieValue(DEVICE_TOKEN_KEY);
        };

        const writeLegacyStoredDeviceToken = (token) => {
            try {
                window.localStorage.setItem(DEVICE_TOKEN_KEY, token);
            } catch (error) {
            }

            setCookieValue(DEVICE_TOKEN_KEY, token, DEVICE_TOKEN_COOKIE_MAX_AGE);
        };

        const clearLegacyStoredDeviceToken = () => {
            try {
                window.localStorage.removeItem(DEVICE_TOKEN_KEY);
            } catch (error) {
            }

            clearCookieValue(DEVICE_TOKEN_KEY);
        };

        const getNativeDeviceTokenPlugin = () => {
            const capacitor = window.Capacitor;
            if (!capacitor || typeof capacitor.isNativePlatform !== 'function' || !capacitor.isNativePlatform()) {
                return null;
            }

            return capacitor.Plugins?.DeviceToken ?? null;
        };

        const getStoredDeviceToken = async () => {
            const plugin = getNativeDeviceTokenPlugin();

            if (plugin && typeof plugin.getToken === 'function') {
                try {
                    const result = await plugin.getToken();
                    const nativeToken = typeof result?.token === 'string' ? result.token : '';

                    if (nativeToken !== '') {
                        return nativeToken;
                    }

                    const legacyToken = readLegacyStoredDeviceToken();
                    if (legacyToken !== '' && typeof plugin.setToken === 'function') {
                        await plugin.setToken({ token: legacyToken });
                        clearLegacyStoredDeviceToken();
                        return legacyToken;
                    }

                    return '';
                } catch (error) {
                }
            }

            return readLegacyStoredDeviceToken();
        };

        const setStoredDeviceToken = async (token) => {
            const plugin = getNativeDeviceTokenPlugin();

            if (plugin && typeof plugin.setToken === 'function') {
                try {
                    await plugin.setToken({ token });
                    clearLegacyStoredDeviceToken();
                    return;
                } catch (error) {
                }
            }

            writeLegacyStoredDeviceToken(token);
        };

        const clearStoredDeviceToken = async () => {
            const plugin = getNativeDeviceTokenPlugin();

            if (plugin && typeof plugin.clearToken === 'function') {
                try {
                    await plugin.clearToken();
                } catch (error) {
                }
            }

            clearLegacyStoredDeviceToken();
        };

        const formatCurrency = (value) => {
            const amount = Number(value);
            if (Number.isNaN(amount)) {
                return value ?? '-';
            }

            return new Intl.NumberFormat('es-CL', {
                style: 'currency',
                currency: 'CLP',
                maximumFractionDigits: 0
            }).format(amount);
        };

        const sanitizePPU = (value) => value.toUpperCase().replace(/[^A-Z0-9]/g, '');

        const syncRefreshButton = () => {
            const ppu = sanitizePPU(ppuInput.value);
            refreshButton.disabled = ppu.length < 5;
        };

        const setStatus = (message, type = 'loading') => {
            const spinner = type === 'loading' ? '<span class="status-spinner" aria-hidden="true"></span>' : '';
            statusBox.innerHTML = `
                <div class="status-inner">
                    ${spinner}
                    <span class="status-text">${message}</span>
                </div>
            `;
            statusBox.className = `status show ${type}`;
        };

        const clearStatus = () => {
            statusBox.innerHTML = '';
            statusBox.className = 'status';
        };

        const setEnrollmentStatus = (message, type = 'loading') => {
            const spinner = type === 'loading' ? '<span class="status-spinner" aria-hidden="true"></span>' : '';
            enrollmentStatus.innerHTML = `
                <div class="status-inner">
                    ${spinner}
                    <span class="status-text">${message}</span>
                </div>
            `;
            enrollmentStatus.className = `status show ${type}`;
        };

        const clearEnrollmentStatus = () => {
            enrollmentStatus.innerHTML = '';
            enrollmentStatus.className = 'status';
        };

        const createItems = (items) => {
            if (!items.length) {
                return '<p class="empty">No hay información disponible.</p>';
            }

            return `
                <div class="grid">
                    ${items.map((item) => `
                        <div class="item">
                            <span>${item.label}</span>
                            <strong>${item.value ?? '-'}</strong>
                        </div>
                    `).join('')}
                </div>
            `;
        };

        const escapeHtml = (value) => String(value).replace(/[&<>"']/g, (char) => ({
            '&': '&amp;',
            '<': '&lt;',
            '>': '&gt;',
            '"': '&quot;',
            "'": '&#039;'
        }[char]));

        const createBadge = (ok, text) => `<div class="badge ${ok ? '' : 'error'}">${text}</div>`;

        const isExternalUnavailable = (payload, expectedKey) => {
            if (payload?.external_unavailable === true) {
                return true;
            }

            if (payload?.estado && Number(payload.estado) >= 500) {
                return true;
            }

            if (expectedKey === 'PRT') {
                return payload?.estado === 200 && (!payload.PRT || Array.isArray(payload.PRT));
            }

            return false;
        };

        const getExternalUnavailableMessage = (areaLabel) => (
            `El servicio externo de ${areaLabel} no se encuentra disponible. Esto no significa que el permiso esté mal emitido.`
        );

        const createServiceAlert = (areaLabel, payload) => {
            const detail = typeof payload?.msj_estado === 'string' && payload.msj_estado.trim() !== ''
                ? `<span>Detalle técnico: ${escapeHtml(payload.msj_estado.trim())}</span>`
                : '';

            return `<div class="service-alert">${getExternalUnavailableMessage(areaLabel)}${detail}</div>`;
        };

        const getAreaErrorMessage = (areaLabel, payload, noDataMessage) => {
            if (isExternalUnavailable(payload, areaLabel === 'revisión técnica' ? 'PRT' : '')) {
                return getExternalUnavailableMessage(areaLabel);
            }

            const rawMessage = typeof payload?.msj_estado === 'string'
                ? payload.msj_estado.trim()
                : '';
            const normalized = rawMessage.toLowerCase();

            if (normalized.includes('conectar con el servicio externo')) {
                return `No se pudo conectar con el sistema externo de ${areaLabel}. Intenta nuevamente en unos minutos.`;
            }

            if (normalized.includes('respuesta no válida')) {
                return `El sistema externo de ${areaLabel} respondió con un formato no válido.`;
            }

            if (rawMessage !== '') {
                return rawMessage;
            }

            return noDataMessage;
        };

        const createSummaryItem = (title, tone, detail) => `
            <div class="summary-item">
                <div class="summary-copy">
                    <strong>${title}</strong>
                    <span>${detail}</span>
                </div>
                <span class="traffic ${tone}" aria-hidden="true"></span>
            </div>
        `;

        const getPermisoSummary = (payload) => {
            if (isExternalUnavailable(payload, 'permiso')) {
                return {
                    tone: 'red',
                    detail: getExternalUnavailableMessage('permiso de circulación')
                };
            }

            if (payload.estado !== 200 || !payload.permiso) {
                return {
                    tone: 'red',
                    detail: payload.msj_estado || 'No fue posible consultar el permiso.'
                };
            }

            if (payload.permiso.msg && !payload.permiso.placa) {
                return {
                    tone: 'yellow',
                    detail: payload.permiso.msg
                };
            }

            return {
                tone: 'green',
                detail: `Vigente hasta ${payload.permiso.vencimiento || 'sin fecha informada'}.`
            };
        };

        const getSoapSummary = (payload) => {
            if (isExternalUnavailable(payload, 'soap')) {
                return {
                    tone: 'red',
                    detail: getExternalUnavailableMessage('SOAP')
                };
            }

            if (payload.estado !== 200 || !Array.isArray(payload.soap) || payload.soap.length === 0) {
                return {
                    tone: 'red',
                    detail: payload.msj_estado || 'No fue posible consultar el SOAP.'
                };
            }

            const soap = payload.soap[0];
            return {
                tone: 'green',
                detail: `Cobertura hasta ${soap.FechaTermino || 'sin fecha informada'}.`
            };
        };

        const getPrtSummary = (payload) => {
            if (isExternalUnavailable(payload, 'PRT')) {
                return {
                    tone: 'red',
                    detail: getExternalUnavailableMessage('revisión técnica')
                };
            }

            if (payload.estado !== 200 || !payload.PRT || Array.isArray(payload.PRT)) {
                return {
                    tone: 'red',
                    detail: payload.msj_estado || 'No fue posible consultar la revisión técnica.'
                };
            }

            return {
                tone: payload.PRT.ResultadoPRT === 'Aprobada' ? 'green' : 'yellow',
                detail: `${payload.PRT.ResultadoPRT || 'Sin resultado'} hasta ${payload.PRT.fechaVencPRT || 'sin fecha informada'}.`
            };
        };

        const renderSummary = (permiso, soap, prt) => {
            const permisoSummary = getPermisoSummary(permiso);
            const soapSummary = getSoapSummary(soap);
            const prtSummary = getPrtSummary(prt);

            summaryBody.innerHTML = [
                createSummaryItem('Permiso de circulación', permisoSummary.tone, permisoSummary.detail),
                createSummaryItem('SOAP', soapSummary.tone, soapSummary.detail),
                createSummaryItem('Revisión técnica', prtSummary.tone, prtSummary.detail)
            ].join('');

            summaryCard.classList.add('show');
        };

        const renderPermiso = (payload) => {
            if (payload.estado !== 200 || !payload.permiso) {
                permisoBody.innerHTML = `
                    ${createBadge(false, payload.msj_estado || 'Sin información')}
                    ${isExternalUnavailable(payload, 'permiso')
                        ? createServiceAlert('permiso de circulación', payload)
                        : `<p class="empty">${getAreaErrorMessage('permiso de circulación', payload, 'No hay información disponible del permiso de circulación para esta patente.')}</p>`}
                `;
                permisoCard.classList.add('show');
                return;
            }

            const permiso = payload.permiso;
            if (permiso.msg && !permiso.placa) {
                permisoBody.innerHTML = `
                    ${createBadge(true, payload.msj_estado)}
                    <p class="empty">${permiso.msg}</p>
                `;
                permisoCard.classList.add('show');
                return;
            }

            permisoBody.innerHTML = `
                ${createBadge(true, payload.msj_estado)}
                ${createItems([
                    { label: 'Placa', value: permiso.placa },
                    { label: 'Nombre', value: permiso.nombre ?? '-' },
                    { label: 'Año pago', value: permiso.aaaa_pago },
                    { label: 'Fecha pago', value: permiso.fecha_pago },
                    { label: 'Forma de pago', value: permiso.formaPago },
                    { label: 'Vencimiento', value: permiso.vencimiento },
                    { label: 'Monto a pagar', value: formatCurrency(permiso.apagar) },
                    { label: 'N° formulario', value: permiso.nroformulario }
                ])}
            `;
            permisoCard.classList.add('show');
        };

        const renderSoap = (payload) => {
            if (payload.estado !== 200 || !Array.isArray(payload.soap) || payload.soap.length === 0) {
                soapBody.innerHTML = `
                    ${createBadge(false, payload.msj_estado || 'Sin información')}
                    ${isExternalUnavailable(payload, 'soap')
                        ? createServiceAlert('SOAP', payload)
                        : `<p class="empty">${getAreaErrorMessage('SOAP', payload, 'No hay información disponible del SOAP para esta patente.')}</p>`}
                `;
                soapCard.classList.add('show');
                return;
            }

            const soap = payload.soap[0];
            soapBody.innerHTML = `
                ${createBadge(true, payload.msj_estado)}
                ${createItems([
                    { label: 'Compañía', value: soap.NombreCompania },
                    { label: 'N° póliza', value: soap.NroPoliza },
                    { label: 'Inicio cobertura', value: soap.FechaInicio },
                    { label: 'Término cobertura', value: soap.FechaTermino }
                ])}
            `;
            soapCard.classList.add('show');
        };

        const renderPrt = (payload) => {
            if (payload.estado !== 200 || !payload.PRT) {
                prtBody.innerHTML = `
                    ${createBadge(false, payload.msj_estado || 'Sin información')}
                    ${isExternalUnavailable(payload, 'PRT')
                        ? createServiceAlert('revisión técnica', payload)
                        : `<p class="empty">${getAreaErrorMessage('revisión técnica', payload, 'No hay información disponible de la revisión técnica para esta patente.')}</p>`}
                `;
                prtCard.classList.add('show');
                return;
            }

            const prt = payload.PRT;
            prtBody.innerHTML = `
                ${createBadge(true, payload.msj_estado)}
                ${createItems([
                    { label: 'Placa', value: prt.placa },
                    { label: 'Marca', value: prt.marca ?? prt.Marca },
                    { label: 'Modelo', value: prt.modelo ?? prt.Modelo },
                    { label: 'Número de chasis', value: prt.chasis ?? prt.chassis ?? prt.Chasis ?? prt.Chassis },
                    { label: 'Número de motor', value: prt.motor ?? prt.Motor },
                    { label: 'Resultado', value: prt.ResultadoPRT },
                    { label: 'Fecha revisión', value: prt.FechaPRT },
                    { label: 'Vencimiento', value: prt.fechaVencPRT },
                    { label: 'Comuna PRT', value: prt.ComunaPRT },
                    { label: 'Certificado', value: prt.certificadoPRT }
                ])}
            `;
            prtCard.classList.add('show');
        };

        const resetResults = () => {
            summaryCard.classList.remove('show');
            summaryBody.innerHTML = '';
            permisoCard.classList.remove('show');
            soapCard.classList.remove('show');
            prtCard.classList.remove('show');
            permisoBody.innerHTML = '';
            soapBody.innerHTML = '';
            prtBody.innerHTML = '';
        };

        const setCompactView = (enabled) => {
            hintText.classList.toggle('compact-hidden', enabled);
            primaryActions.classList.toggle('compact-hidden', enabled);
            resultActions.classList.toggle('show', enabled);
        };

        const resetSearchForm = () => {
            resetResults();
            clearStatus();
            ppuInput.value = '';
            setCompactView(false);
            syncRefreshButton();
            ppuInput.focus();
        };

        const setDevicePanel = (authorized, deviceName = '') => {
            if (authorized) {
                deviceStatusTitle.textContent = 'Dispositivo autorizado';
                deviceStatusName.textContent = deviceName !== '' ? deviceName.toUpperCase() : 'Equipo activo';
                return;
            }

            const pendingName = deviceName.trim() !== '' ? deviceName.trim().toUpperCase() : 'Pendiente de activación';
            deviceStatusTitle.textContent = 'Dispositivo pendiente';
            deviceStatusName.textContent = pendingName;
        };

        const setAuthorizedUI = (authorized, deviceName = '') => {
            enrollmentCard.classList.toggle('app-hidden', authorized);
            securedContent.classList.toggle('app-hidden', !authorized);
            setDevicePanel(authorized, deviceName);
        };

        const authHeaders = async () => {
            const token = await getStoredDeviceToken();
            return token !== '' ? { 'X-Device-Token': token } : {};
        };

        const bootstrapAuthorization = async () => {
            const token = await getStoredDeviceToken();
            if (token === '') {
                setAuthorizedUI(false);
                return;
            }

            try {
                const { response, data } = await fetchJson('api/bootstrap.php', {
                    headers: await authHeaders()
                });

                if (!response.ok && data.code === 'device_not_authorized') {
                    await clearStoredDeviceToken();
                    setAuthorizedUI(false);
                    return;
                }

                if (!response.ok) {
                    setAuthorizedUI(true);
                    return;
                }

                if (!data.authorized) {
                    await clearStoredDeviceToken();
                    setAuthorizedUI(false);
                    return;
                }

                setAuthorizedUI(true, data.device?.name || '');
            } catch (error) {
                setAuthorizedUI(true);
            }
        };

        ppuInput.addEventListener('input', () => {
            ppuInput.value = sanitizePPU(ppuInput.value);
            syncRefreshButton();
        });

        ppuInput.addEventListener('keydown', (event) => {
            if (event.key === 'Enter') {
                event.preventDefault();
                searchButton.click();
            }
        });

        activationCodeInput.addEventListener('input', () => {
            activationCodeInput.value = activationCodeInput.value.toUpperCase().replace(/[^A-Z0-9]/g, '');
        });

        deviceNameInput.addEventListener('input', () => {
            if (!enrollmentCard.classList.contains('app-hidden')) {
                setDevicePanel(false, deviceNameInput.value);
            }
        });

        enrollButton.addEventListener('click', async () => {
            const activationCode = activationCodeInput.value.toUpperCase().replace(/[^A-Z0-9]/g, '');
            const deviceName = deviceNameInput.value.trim();

            if (activationCode.length < 6) {
                setEnrollmentStatus('Ingresa un código de activación válido.', 'error');
                return;
            }

            setEnrollmentStatus('Activando equipo...', 'loading');

            try {
                const { response, data } = await fetchJson('api/enroll.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        activation_code: activationCode,
                        device_name: deviceName
                    })
                });

                if (!response.ok) {
                    throw new Error(data.message || 'No fue posible activar este equipo.');
                }

                await setStoredDeviceToken(data.token);
                setEnrollmentStatus('Equipo activado correctamente.', 'success');
                await bootstrapAuthorization();
            } catch (error) {
                setEnrollmentStatus(error.message || 'No fue posible activar este equipo.', 'error');
            }
        });

        const runSearch = async (mode = 'search') => {
            const ppu = sanitizePPU(ppuInput.value);
            ppuInput.value = ppu;
            resetResults();
            setCompactView(false);
            syncRefreshButton();

            if (!ppu || ppu.length < 5) {
                setStatus('Ingresa una PPU válida para consultar.', 'error');
                return;
            }

            setStatus(mode === 'refresh' ? `Refrescando datos para la PPU ${ppu}...` : 'Consultando servicios externos...', 'loading');

            try {
                const { response, data } = await fetchJson('api/consulta.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        ...(await authHeaders())
                    },
                    body: JSON.stringify({ ppu })
                }, 35000);

                if (!response.ok) {
                    if (data.code === 'device_not_authorized') {
                        await clearStoredDeviceToken();
                        setAuthorizedUI(false);
                        throw new Error('Este equipo dejó de estar autorizado. Debe activarse nuevamente.');
                    }
                    throw new Error(data.message || 'No fue posible completar la consulta.');
                }

                renderSummary(data.permiso, data.soap, data.prt);
                renderPermiso(data.permiso);
                renderSoap(data.soap);
                renderPrt(data.prt);
                setCompactView(true);
                setStatus(mode === 'refresh' ? `Consulta actualizada para la PPU ${ppu}.` : `Consulta completada para la PPU ${ppu}.`, 'success');
            } catch (error) {
                setStatus(error.message || 'Se produjo un error inesperado.', 'error');
            }
        };

        searchButton.addEventListener('click', async () => {
            await runSearch('search');
        });

        refreshButton.addEventListener('click', async () => {
            await runSearch('refresh');
        });

        newSearchButton.addEventListener('click', () => {
            resetSearchForm();
        });

        if ('serviceWorker' in navigator) {
            window.addEventListener('load', () => {
                navigator.serviceWorker.register('sw.js').catch(() => {});
            });
        }

        syncRefreshButton();
        bootstrapAuthorization();
    </script>
</body>
</html>
