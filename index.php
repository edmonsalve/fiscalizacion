<?php
declare(strict_types=1);
?>
<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
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
            justify-content: space-between;
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

        input[type="text"] {
            width: 100%;
            padding: 16px 14px;
            border: 1px solid var(--border);
            border-radius: 16px;
            font-size: 1.25rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            color: var(--text);
            background: #fffdfa;
        }

        input[type="text"]:focus {
            outline: 3px solid rgba(18, 79, 135, 0.18);
            border-color: var(--primary);
        }

        .actions,
        .camera-actions {
            display: grid;
            gap: 10px;
            margin-top: 14px;
        }

        button,
        .file-label {
            border: 0;
            border-radius: 16px;
            padding: 14px 16px;
            font: inherit;
            font-weight: 700;
            cursor: pointer;
            transition: transform 0.15s ease, opacity 0.15s ease;
        }

        button:active,
        .file-label:active {
            transform: scale(0.98);
        }

        .primary {
            background: var(--primary);
            color: #fff;
        }

        .secondary,
        .file-label {
            background: var(--surface-alt);
            color: var(--primary-strong);
            border: 1px solid var(--border);
            text-align: center;
        }

        .camera {
            display: none;
            margin-top: 14px;
            border-radius: 20px;
            overflow: hidden;
            background: #eaf8fa;
            border: 1px solid var(--border);
        }

        video,
        canvas,
        img.preview {
            width: 100%;
            display: block;
            background: #dff4f5;
        }

        .camera-note {
            margin-top: 10px;
            color: var(--muted);
            font-size: 0.84rem;
            line-height: 1.4;
        }

        input[type="file"] {
            display: none;
        }

        .status {
            display: none;
            margin-top: 16px;
            padding: 14px 16px;
            border-radius: 16px;
            font-size: 0.95rem;
            line-height: 1.4;
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

        .empty {
            color: var(--muted);
            font-size: 0.92rem;
        }

        @media (min-width: 480px) {
            .actions,
            .camera-actions {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }

            .grid {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }
        }

        @media (max-width: 359px) {
            .brand-strip {
                flex-direction: column;
                align-items: stretch;
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
                <div class="brand-group">
                    <img src="api/dcode.png" alt="Logo Dcode">
                    <div class="brand-copy">
                        <strong>Powered by</strong>
                        <span>dCode</span>
                    </div>
                </div>
            </div>

            <span class="eyebrow">Consulta Vehicular</span>
            <h1>Fiscalización por PPU</h1>
            <p class="intro">
                Ingresa la patente manualmente o toma una foto como apoyo visual.
            </p>
        </section>

        <section class="card">
            <h2>Patente</h2>
            <p class="hint">Escribe la PPU sin espacios ni guiones.</p>

            <label class="field-label" for="ppu">PPU</label>
            <input id="ppu" name="ppu" type="text" maxlength="8" placeholder="Ej: VVDZ60" autocomplete="off" inputmode="text">

            <div class="actions">
                <button class="primary" id="searchButton" type="button">Consultar datos</button>
                <button class="secondary" id="openCameraButton" type="button">Usar cámara</button>
            </div>

            <div class="camera" id="cameraBox">
                <video id="cameraVideo" playsinline autoplay muted></video>
                <canvas id="snapshotCanvas" hidden></canvas>
                <img class="preview" id="imagePreview" alt="Vista previa de la foto" hidden>

                <div class="camera-actions">
                    <button class="secondary" id="captureButton" type="button">Tomar foto</button>
                    <button class="secondary" id="closeCameraButton" type="button">Cerrar cámara</button>
                </div>
            </div>

            <div class="actions">
                <label class="file-label" for="photoInput">Subir foto desde el equipo</label>
                <button class="secondary" id="clearPhotoButton" type="button">Quitar foto</button>
            </div>

            <input id="photoInput" type="file" accept="image/*" capture="environment">
            <p class="camera-note">
                La foto queda como referencia visual en pantalla para apoyar la lectura de la PPU. La consulta se realiza con la patente escrita en el campo.
            </p>

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
    </main>

    <script>
        const ppuInput = document.getElementById('ppu');
        const searchButton = document.getElementById('searchButton');
        const openCameraButton = document.getElementById('openCameraButton');
        const closeCameraButton = document.getElementById('closeCameraButton');
        const captureButton = document.getElementById('captureButton');
        const clearPhotoButton = document.getElementById('clearPhotoButton');
        const photoInput = document.getElementById('photoInput');
        const statusBox = document.getElementById('statusBox');
        const cameraBox = document.getElementById('cameraBox');
        const cameraVideo = document.getElementById('cameraVideo');
        const snapshotCanvas = document.getElementById('snapshotCanvas');
        const imagePreview = document.getElementById('imagePreview');
        const permisoCard = document.getElementById('permisoCard');
        const soapCard = document.getElementById('soapCard');
        const prtCard = document.getElementById('prtCard');
        const summaryCard = document.getElementById('summaryCard');
        const summaryBody = document.getElementById('summaryBody');
        const permisoBody = document.getElementById('permisoBody');
        const soapBody = document.getElementById('soapBody');
        const prtBody = document.getElementById('prtBody');

        let mediaStream = null;

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

        const setStatus = (message, type = 'loading') => {
            statusBox.textContent = message;
            statusBox.className = `status show ${type}`;
        };

        const clearStatus = () => {
            statusBox.textContent = '';
            statusBox.className = 'status';
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

        const createBadge = (ok, text) => `<div class="badge ${ok ? '' : 'error'}">${text}</div>`;

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
                    <p class="empty">No fue posible obtener datos del permiso de circulación.</p>
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
                    { label: 'Año pago', value: permiso.aaaa_pago },
                    { label: 'Fecha pago', value: permiso.fecha_pago },
                    { label: 'Forma de pago', value: permiso.formaPago },
                    { label: 'Vencimiento', value: permiso.vencimiento },
                    { label: 'Total permiso', value: formatCurrency(permiso.totalpermiso) },
                    { label: 'Cuota', value: formatCurrency(permiso.cuota) },
                    { label: 'Monto a pagar', value: formatCurrency(permiso.apagar) },
                    { label: 'Estado formulario', value: permiso.estadoForm },
                    { label: 'N° formulario', value: permiso.nroformulario }
                ])}
            `;
            permisoCard.classList.add('show');
        };

        const renderSoap = (payload) => {
            if (payload.estado !== 200 || !Array.isArray(payload.soap) || payload.soap.length === 0) {
                soapBody.innerHTML = `
                    ${createBadge(false, payload.msj_estado || 'Sin información')}
                    <p class="empty">No fue posible obtener datos del SOAP.</p>
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
                    { label: 'Término cobertura', value: soap.FechaTermino },
                    { label: 'Estado', value: soap.Estado },
                    { label: 'Glosa', value: soap.Glosa },
                    { label: 'RUT compañía', value: soap.RutCia },
                    { label: 'Ticket', value: soap.Ticket }
                ])}
            `;
            soapCard.classList.add('show');
        };

        const renderPrt = (payload) => {
            if (payload.estado !== 200 || !payload.PRT) {
                prtBody.innerHTML = `
                    ${createBadge(false, payload.msj_estado || 'Sin información')}
                    <p class="empty">No fue posible obtener datos de la revisión técnica.</p>
                `;
                prtCard.classList.add('show');
                return;
            }

            const prt = payload.PRT;
            prtBody.innerHTML = `
                ${createBadge(true, payload.msj_estado)}
                ${createItems([
                    { label: 'Placa', value: prt.placa },
                    { label: 'Resultado', value: prt.ResultadoPRT },
                    { label: 'Estado PRT', value: prt.EstadoPRT },
                    { label: 'Fecha revisión', value: prt.FechaPRT },
                    { label: 'Vencimiento', value: prt.fechaVencPRT },
                    { label: 'Comuna PRT', value: prt.ComunaPRT },
                    { label: 'Código PRT', value: prt.codigoPRT },
                    { label: 'Certificado', value: prt.certificadoPRT },
                    { label: 'Ticket', value: prt.Ticket },
                    { label: 'Fecha ticket', value: prt.fechaTicket }
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

        const stopCamera = () => {
            if (mediaStream) {
                mediaStream.getTracks().forEach((track) => track.stop());
                mediaStream = null;
            }

            cameraVideo.srcObject = null;
            cameraBox.style.display = 'none';
        };

        const showPreview = (source) => {
            imagePreview.src = source;
            imagePreview.hidden = false;
        };

        openCameraButton.addEventListener('click', async () => {
            if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {
                setStatus('La cámara no está disponible en este navegador. Puedes subir una foto desde el equipo.', 'error');
                return;
            }

            try {
                mediaStream = await navigator.mediaDevices.getUserMedia({
                    video: {
                        facingMode: { ideal: 'environment' }
                    },
                    audio: false
                });

                cameraVideo.srcObject = mediaStream;
                cameraBox.style.display = 'block';
                imagePreview.hidden = true;
                clearStatus();
            } catch (error) {
                setStatus('No se pudo abrir la cámara. Revisa los permisos del navegador.', 'error');
            }
        });

        closeCameraButton.addEventListener('click', () => {
            stopCamera();
        });

        captureButton.addEventListener('click', () => {
            if (!mediaStream) {
                setStatus('Primero abre la cámara para tomar la foto.', 'error');
                return;
            }

            const width = cameraVideo.videoWidth;
            const height = cameraVideo.videoHeight;

            if (!width || !height) {
                setStatus('La cámara aún se está preparando. Intenta nuevamente en un momento.', 'error');
                return;
            }

            snapshotCanvas.width = width;
            snapshotCanvas.height = height;
            snapshotCanvas.getContext('2d').drawImage(cameraVideo, 0, 0, width, height);
            showPreview(snapshotCanvas.toDataURL('image/jpeg', 0.92));
            stopCamera();
        });

        photoInput.addEventListener('change', (event) => {
            const [file] = event.target.files || [];
            if (!file) {
                return;
            }

            const reader = new FileReader();
            reader.onload = () => showPreview(reader.result);
            reader.readAsDataURL(file);
            clearStatus();
        });

        clearPhotoButton.addEventListener('click', () => {
            photoInput.value = '';
            imagePreview.src = '';
            imagePreview.hidden = true;
            stopCamera();
        });

        ppuInput.addEventListener('input', () => {
            ppuInput.value = sanitizePPU(ppuInput.value);
        });

        ppuInput.addEventListener('keydown', (event) => {
            if (event.key === 'Enter') {
                event.preventDefault();
                searchButton.click();
            }
        });

        searchButton.addEventListener('click', async () => {
            const ppu = sanitizePPU(ppuInput.value);
            ppuInput.value = ppu;
            resetResults();

            if (!ppu || ppu.length < 5) {
                setStatus('Ingresa una PPU válida para consultar.', 'error');
                return;
            }

            setStatus('Consultando servicios externos...', 'loading');

            try {
                const response = await fetch('api/consulta.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({ ppu })
                });

                const data = await response.json();

                if (!response.ok) {
                    throw new Error(data.message || 'No fue posible completar la consulta.');
                }

                renderSummary(data.permiso, data.soap, data.prt);
                renderPermiso(data.permiso);
                renderSoap(data.soap);
                renderPrt(data.prt);
                setStatus(`Consulta completada para la PPU ${ppu}.`, 'loading');
            } catch (error) {
                setStatus(error.message || 'Se produjo un error inesperado.', 'error');
            }
        });
    </script>
</body>
</html>
