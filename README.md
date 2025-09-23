# Revi3

# FoodScan – QRCode Restaurant Menu Maker & Contactless Ordering

Este proyecto es un sistema de menús QR con Laravel, Vue 3 y Laravel Mix.
A continuación se detallan los pasos para configurar el entorno local de desarrollo.

---

## 🚀 Requerimientos de entorno

Asegúrate de tener instalado:

- **PHP 8.0.2 o superior** con las extensiones:

  - `exif`
  - `json`
  - `pdo`
    Además, necesitas **GD** o **Imagick** para procesar imágenes.

- **Composer 2**
  Para la instalación de dependencias de PHP.

- **Node.js 16** y **npm**
  Para compilar los assets front-end con Laravel Mix y Vue 3.

- **MySQL**
  Debes restaurar la copia de base de datos proporcionada en tu entorno local.

- **Permisos de escritura** en:

  - `storage/`
  - `bootstrap/cache/`

---

## ⚙️ Configuración del proyecto

1. **Clona el proyecto** en tu máquina y entra a la carpeta.

2. **Instala las dependencias PHP y front-end**:

   ```bash
   composer install
   npm install
   ```

3. **Archivo `.env`**
   Ya tienes un `.env` preconfigurado que incluye la base de datos, APP_KEY y demás variables.
   Copia el archivo proporcionado directamente en la raíz del proyecto (no necesitas generarlo ni configurarlo).

4. **Base de datos local**
   Importa la copia de base de datos que te fue entregada en tu servidor MySQL local:

   ```bash
   mysql -u root -p -h localhost -P 3306 foodscan_dev1 < backup_foodscan.sql
   ```

   ⚠️ El nombre de la base de datos debe coincidir exactamente con el definido en el `.env`:

   ```env
   DB_DATABASE=foodscan_dev1
   DB_USERNAME=root
   DB_PASSWORD=
   ```

   _(Si no existe `foodscan_dev1`, créala primero con `CREATE DATABASE foodscan_dev1;`)_

5. **Enlace de almacenamiento**
   Genera el symlink para los archivos públicos:

   ```bash
   php artisan storage:link
   ```

---

## 📜 Scripts disponibles (`package.json`)

Se añadieron comandos para facilitar el desarrollo:

```json
"scripts": {
  "dev": "npm run development",
  "development": "mix",
  "watch": "mix watch",
  "watch-poll": "mix watch -- --watch-options-poll=1000",
  "hot": "mix watch --hot",
  "prod": "npm run production",
  "production": "mix --production",
  "serve": "php artisan serve --host=127.0.0.1 --port=8000",
  "clean": "php artisan config:clear && php artisan cache:clear && php artisan route:clear && php artisan view:clear",
  "dev:all": "concurrently -k -n \"MIX,PHP\" -c \"cyan,green\" \"npm:watch\" \"npm:serve\"",
  "start": "npm run clean && npm run dev:all"
}
```

---

## 💻 Puesta en marcha en desarrollo

Una vez que tengas la base de datos importada y el `.env` en su lugar, inicia el proyecto con:

```bash
npm run start
```

Este comando se encarga de:

- Limpiar la caché y configuraciones de Laravel
- Levantar el servidor de Laravel (`php artisan serve`)
- Iniciar la compilación y el watcher de los assets front-end

---

## ✅ Notas finales

- No necesitas ejecutar `php artisan migrate --seed`.
- Solo asegúrate de **restaurar la base de datos** y que el nombre coincida con el del `.env`.
- Ajusta en el `.env` los servicios externos que necesites (Firebase, pasarelas de pago, etc.).

---
