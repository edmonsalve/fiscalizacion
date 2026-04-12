# App movil nativa

Este proyecto PHP puede convertirse en app nativa Android/iOS usando un contenedor
Capacitor. La app movil no ejecuta PHP dentro del telefono: carga por HTTPS la
aplicacion desplegada en el servidor y reutiliza el backend actual.

## Lo que ya queda preparado en este repo

- `package.json` con dependencias y comandos de Capacitor.
- `capacitor.config.json` generado automaticamente con `appId`, `appName` y `server.url`.
- `native-shell/index.html` como shell minima para el proyecto nativo.
- `.gitignore` para excluir artefactos nativos y `node_modules`.

## Antes de generar Android/iOS

1. Publica esta aplicacion en una URL HTTPS estable.
2. Configura las URLs por ambiente con variables de entorno:

```bash
export FISCALIZACION_PUBLIC_BASE_URL="https://fiscalizacion.example.cl"
export FISCALIZACION_PPU_API_BASE_URL="https://api.example.cl/api/ppu"
```

3. Si necesitas que el contenedor nativo use una URL distinta a la web publica,
   define opcionalmente:

```bash
export FISCALIZACION_CAP_SERVER_URL="https://m.fiscalizacion.example.cl"
```

4. Instala Node.js 20 o superior.
5. Instala Android Studio para Android.
6. Usa macOS + Xcode para iOS.

## Comandos base

```bash
npm install
npm run cap:add:android
npm run cap:add:ios
npm run cap:sync
```

## Abrir proyectos nativos

```bash
npm run cap:open:android
npm run cap:open:ios
```

## Flujo de trabajo recomendado

1. Desplegar cambios del frontend/PHP al servidor del ambiente correspondiente.
2. Exportar `FISCALIZACION_PUBLIC_BASE_URL` y `FISCALIZACION_PPU_API_BASE_URL`.
3. Ejecutar `npm run cap:sync`.
4. Abrir Android Studio o Xcode.
5. Compilar, firmar y publicar.

## Variables de entorno relevantes

- `FISCALIZACION_PUBLIC_BASE_URL`: URL publica donde vive esta app web.
- `FISCALIZACION_PPU_API_BASE_URL`: URL base del servicio externo de consulta PPU/SOAP/PRT.
- `FISCALIZACION_CAP_SERVER_URL`: override opcional para `server.url` de Capacitor.

Los comandos `cap:*` ejecutan primero `php scripts/build_capacitor_config.php`, por lo que
`capacitor.config.json` se regenera automaticamente antes de sincronizar o abrir el proyecto nativo.

## Almacenamiento del enrolamiento

Hoy el token del equipo se guarda en el cliente web. Para una version nativa mas
robusta se recomienda el siguiente paso:

1. Crear un puente nativo o plugin para guardar el token en Keychain/Keystore.
2. Mantener el backend actual validando `X-Device-Token`.
3. Renovar token o re-enrolar solo cuando el backend responda
   `device_not_authorized`.

## Recomendaciones de publicacion

- Android: configurar `applicationId`, iconos adaptativos, splash y firma.
- iOS: configurar `Bundle Identifier`, permisos y firma en Xcode.
- Seguridad: usar solo HTTPS y certificados validos.
- Operacion: mantener la API y la app bajo el mismo dominio o subdominios
  controlados.

## Limitaciones actuales

- En este servidor no hay `npm`, por lo que `android/` e `ios/` no se generaron aqui.
- La generacion de iOS solo puede completarse en macOS.
- Si cambias cualquier URL de ambiente, debes volver a ejecutar `npm run cap:sync`.
