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
            --bg: #eef8fb;
            --surface: #ffffff;
            --surface-alt: #eefbfa;
            --primary: #124f87;
            --primary-strong: #0d3d68;
            --accent: #1cd3c6;
            --accent-soft: #d9f8f5;
            --ok: #168d84;
            --warn: #bc6d3f;
            --text: #17344f;
            --muted: #5f7891;
            --border: #cfe4ef;
            --shadow: 0 18px 40px rgba(18, 79, 135, 0.14);
            --radius: 22px;
        }

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            min-height: 100vh;
            font-family: "Segoe UI", "Helvetica Neue", sans-serif;
            color: var(--text);
            background:
                radial-gradient(circle at top, rgba(28, 211, 198, 0.26), transparent 34%),
                linear-gradient(180deg, #f4fcfe 0%, #eaf7fb 100%);
        }

        .app {
            width: min(100%, 520px);
            margin: 0 auto;
            padding: 24px 16px 40px;
        }

        .hero {
            background: rgba(255, 255, 255, 0.72);
            backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, 0.7);
            border-radius: 28px;
            padding: 22px 18px;
            box-shadow: var(--shadow);
        }

        .brand-strip {
            display: flex;
            align-items: center;
            justify-content: flex-start;
            gap: 12px;
            margin-bottom: 14px;
            padding: 10px 12px;
            background: rgba(255, 255, 255, 0.72);
            border: 1px solid var(--border);
            border-radius: 18px;
        }

        .brand-group {
            display: flex;
            align-items: center;
            gap: 10px;
            min-width: 0;
        }

        .brand-group img {
            display: block;
            width: auto;
            height: 28px;
            object-fit: contain;
        }

        .brand-group.client img {
            height: 34px;
        }

        .brand-copy {
            min-width: 0;
        }

        .brand-copy strong,
        .brand-copy span {
            display: block;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .brand-copy strong {
            font-size: 0.78rem;
            color: var(--primary-strong);
            letter-spacing: 0.03em;
            text-transform: uppercase;
        }

        .brand-copy span {
            margin-top: 2px;
            font-size: 0.76rem;
            color: var(--muted);
        }

        .footer-brand {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            margin-top: 18px;
            color: var(--muted);
            font-size: 0.78rem;
        }

        .footer-brand img {
            display: block;
            width: auto;
            height: 18px;
            object-fit: contain;
            opacity: 0.9;
        }

        .footer-links {
            margin-top: 14px;
            text-align: center;
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

        .eyebrow {
            display: inline-flex;
            padding: 6px 12px;
            border-radius: 999px;
            background: var(--accent-soft);
            color: var(--primary-strong);
            font-size: 0.78rem;
            font-weight: 700;
            letter-spacing: 0.04em;
            text-transform: uppercase;
        }

        h1 {
            margin: 14px 0 8px;
            font-size: 1.85rem;
            line-height: 1.1;
        }

        .intro {
            margin: 0;
            color: var(--muted);
            font-size: 0.98rem;
            line-height: 1.5;
        }

        .card {
            margin-top: 18px;
            padding: 18px;
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: var(--radius);
            box-shadow: var(--shadow);
        }

        .card h2 {
            margin: 0 0 6px;
            font-size: 1rem;
        }

        .hint {
            margin: 0 0 14px;
            color: var(--muted);
            font-size: 0.92rem;
        }

        .field-label {
            display: block;
            margin-bottom: 8px;
            font-size: 0.92rem;
            font-weight: 700;
        }

        .input,
        input[type="text"] {
            width: 100%;
            padding: 16px 14px;
            border: 1px solid var(--border);
            border-radius: 16px;
            font-size: 1rem;
            color: var(--text);
            background: #fffdfa;
        }

        input[type="text"] {
            font-size: 1.25rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.08em;
        }

        .input:focus,
        input[type="text"]:focus {
            outline: 3px solid rgba(18, 79, 135, 0.18);
            border-color: var(--primary);
        }

        .actions {
            display: grid;
            gap: 10px;
            margin-top: 14px;
        }

        .result-actions {
            display: none;
            margin-top: 14px;
        }

        .result-actions.show {
            display: grid;
        }

        button {
            border: 0;
            border-radius: 16px;
            padding: 14px 16px;
            font: inherit;
            font-weight: 700;
            cursor: pointer;
            transition: transform 0.15s ease, opacity 0.15s ease;
        }

        button:active,
        button:active {
            transform: scale(0.98);
        }

        .primary {
            background: var(--primary);
            color: #fff;
        }

        .secondary {
            background: #e7f5fb;
            color: var(--primary-strong);
            border: 1px solid var(--border);
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

        .enrollment-copy {
            margin: 0 0 14px;
            color: var(--muted);
            line-height: 1.45;
            font-size: 0.94rem;
        }

        .device-badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            margin-top: 10px;
            padding: 8px 12px;
            border-radius: 999px;
            background: #e8faf6;
            color: var(--ok);
            font-size: 0.82rem;
            font-weight: 700;
        }

        .status {
            display: none;
            margin-top: 16px;
            padding: 14px 16px;
            border-radius: 16px;
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
            background: #e7f5fb;
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
            gap: 14px;
            margin-top: 18px;
        }

        .summary-card {
            display: none;
            background: rgba(255, 255, 255, 0.84);
            border: 1px solid var(--border);
            border-radius: var(--radius);
            box-shadow: var(--shadow);
            overflow: hidden;
        }

        .summary-card.show {
            display: block;
        }

        .summary-head {
            padding: 16px 16px 12px;
            background: linear-gradient(135deg, rgba(18, 79, 135, 0.18), rgba(28, 211, 198, 0.24));
        }

        .summary-head h3 {
            margin: 0;
            font-size: 1rem;
        }

        .summary-head p {
            margin: 6px 0 0;
            color: var(--muted);
            font-size: 0.88rem;
        }

        .summary-body {
            display: grid;
            gap: 10px;
            padding: 14px 16px 18px;
        }

        .summary-item {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            padding: 12px 14px;
            background: #fffdfa;
            border: 1px solid var(--border);
            border-radius: 14px;
        }

        .summary-copy strong {
            display: block;
            font-size: 0.95rem;
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
            background: rgba(255, 255, 255, 0.82);
            border: 1px solid var(--border);
            border-radius: var(--radius);
            box-shadow: var(--shadow);
            overflow: hidden;
        }

        .result-card.show {
            display: block;
        }

        .result-head {
            padding: 16px 16px 12px;
            background: linear-gradient(135deg, rgba(18, 79, 135, 0.14), rgba(28, 211, 198, 0.22));
        }

        .result-head h3 {
            margin: 0;
            font-size: 1rem;
        }

        .result-head p {
            margin: 6px 0 0;
            color: var(--muted);
            font-size: 0.88rem;
        }

        .result-body {
            padding: 14px 16px 18px;
        }

        .grid {
            display: grid;
            gap: 10px;
        }

        .item {
            padding: 12px;
            background: #fffdfa;
            border: 1px solid var(--border);
            border-radius: 14px;
        }

        .item span {
            display: block;
            font-size: 0.8rem;
            color: var(--muted);
            margin-bottom: 4px;
        }

        .item strong {
            font-size: 1rem;
            word-break: break-word;
        }

        .badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 8px 12px;
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
            border-radius: 12px;
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
            .grid {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }
        }

        @media (max-width: 359px) {
            .brand-strip {
                align-items: flex-start;
            }
        }
    </style>
</head>
<body>
    <main class="app">
        <section class="hero">
            <div class="brand-strip">
                <div class="brand-group client">
                    <img src="api/logoPET.png" alt="Logo del cliente PET">
                    <div class="brand-copy">
                        <strong>Municipalidad de Petorca</strong>
                        <span>Fiscalización móvil</span>
                    </div>
                </div>
            </div>

            <span class="eyebrow">Consulta Vehicular</span>
            <h1>Fiscalización por PPU</h1>
            <p class="intro">
                Ingresa la patente manualmente para consultar su información.
            </p>
            <div class="device-badge app-hidden" id="deviceBadge"></div>
        </section>

        <section class="card" id="enrollmentCard">
            <h2>Activación del equipo</h2>
            <p class="enrollment-copy">
                Este dispositivo debe activarse una sola vez con un código entregado por administración. Una vez enrolado, la app quedará lista para uso interno sin pedir login en cada consulta.
            </p>

            <label class="field-label" for="deviceName">Nombre del equipo</label>
            <input class="input" id="deviceName" name="deviceName" type="text" placeholder="Ej: Inspección 01 o Móvil 3" autocomplete="off">

            <label class="field-label" for="activationCode" style="margin-top: 14px;">Código de activación</label>
            <input class="input" id="activationCode" name="activationCode" type="text" maxlength="12" placeholder="Ej: A1B2C3D4" autocomplete="off">

            <div class="actions" style="margin-top: 16px;">
                <button class="primary" id="enrollButton" type="button">Activar equipo</button>
            </div>

            <div class="status" id="enrollmentStatus"></div>
        </section>

        <div id="securedContent" class="app-hidden">
        <section class="card">
            <h2>Patente</h2>
            <p class="hint" id="hintText">Escribe la PPU sin espacios ni guiones.</p>

            <label class="field-label" for="ppu">PPU</label>
            <input id="ppu" name="ppu" type="text" maxlength="8" placeholder="Ej: VVDZ60" autocomplete="off" inputmode="text">

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

        <footer class="footer-brand">
            <span>Powered by</span>
            <img src="api/dcode.png" alt="Logo Dcode">
            <span>dCode</span>
        </footer>
        <div class="footer-links">
            <a href="/privacy-policy.html" target="_blank" rel="noopener noreferrer">Politica de privacidad</a>
        </div>
        </div>
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
        const deviceBadge = document.getElementById('deviceBadge');
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

        const setAuthorizedUI = (authorized, deviceName = '') => {
            enrollmentCard.classList.toggle('app-hidden', authorized);
            securedContent.classList.toggle('app-hidden', !authorized);
            deviceBadge.classList.toggle('app-hidden', !authorized);
            deviceBadge.textContent = authorized && deviceName !== '' ? `Equipo autorizado: ${deviceName}` : '';
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
                const response = await fetch('api/bootstrap.php', {
                    headers: await authHeaders()
                });
                const data = await response.json();

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

        enrollButton.addEventListener('click', async () => {
            const activationCode = activationCodeInput.value.toUpperCase().replace(/[^A-Z0-9]/g, '');
            const deviceName = deviceNameInput.value.trim();

            if (activationCode.length < 6) {
                setEnrollmentStatus('Ingresa un código de activación válido.', 'error');
                return;
            }

            setEnrollmentStatus('Activando equipo...', 'loading');

            try {
                const response = await fetch('api/enroll.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        activation_code: activationCode,
                        device_name: deviceName
                    })
                });

                const data = await response.json();
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
                const response = await fetch('api/consulta.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        ...(await authHeaders())
                    },
                    body: JSON.stringify({ ppu })
                });

                const data = await response.json();

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
