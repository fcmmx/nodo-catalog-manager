# Solución de Problemas — NODO Catalog Manager

## Pantalla en blanco o error 500

1. Revisa `storage/logs/laravel.log` (las últimas líneas muestran el error real).
2. Confirma permisos de escritura: `chmod -R 755 storage bootstrap/cache`.
3. Confirma que `.env` existe y tiene `APP_KEY` generada (`php artisan key:generate`).
4. Si el mensaje menciona "could not find driver" para MySQL: activa la extensión `pdo_mysql` en hPanel → PHP Configuration.

## "500 | Server Error" justo después de instalar

- Verifica que el *document root* apunte a la carpeta `public/` (ver `INSTALL-HOSTINGER.md`, sección A.5). Si `index.php` no encuentra `vendor/autoload.php`, revisa las rutas `require` si usaste el método de subcarpeta sin document root propio.

## El instalador (`/install`) no aparece, va directo a otra pantalla

- El sistema ya está instalado: existe `storage/app/installed.lock`. Si necesitas reinstalar desde cero (por ejemplo en un entorno de pruebas), elimina ese archivo y vacía las tablas de la base de datos — **esto borra todos los datos**, hazlo solo si estás seguro.

## No puedo conectar a la base de datos desde el instalador

- Verifica host, usuario, contraseña y nombre exacto de la base de datos en hPanel → Bases de datos. En Hostinger el host suele ser `localhost`, pero algunos planes requieren `127.0.0.1`.
- Confirma que el usuario de base de datos tenga privilegios sobre esa base específica (hPanel los asigna automáticamente al crearla desde el panel).

## Las importaciones se quedan en "processing" o "mapeo_pendiente" para siempre

La importación se procesa mediante una **cola en segundo plano** (`QUEUE_CONNECTION=database`). Si no configuraste el cron de la cola (ver `INSTALL-HOSTINGER.md`, paso "Configurar el cron"), los trabajos nunca se ejecutan. Verifica:

```bash
php artisan queue:work --stop-when-empty --max-time=50
```

Si al ejecutarlo manualmente el lote avanza, el problema es que el cron no está configurado o la ruta del `artisan` en el cron es incorrecta.

## Las imágenes subidas no se muestran (404)

Falta el enlace simbólico de almacenamiento:

```bash
php artisan storage:link
```

En hosting sin SSH, el instalador web lo crea automáticamente; si lo hiciste por consola sin este paso, créalo manualmente.

## No llegan los correos de recuperación de contraseña

- Revisa que `MAIL_MAILER`, `MAIL_HOST`, `MAIL_USERNAME`, `MAIL_PASSWORD` estén configurados con un proveedor SMTP real (no `log`, que solo escribe el correo en los logs en vez de enviarlo). Ver `CONFIGURACION-APIS.md`.
- Revisa la carpeta de spam del destinatario.

## "Page Expired" (419) al enviar un formulario

- La sesión expiró o las cookies están bloqueadas. Verifica que `APP_URL` en `.env` coincida exactamente con el dominio real (incluyendo `https://`), y que el navegador acepte cookies del sitio.

## Los precios no se ven en formato mexicano

- El formato `$12,345.67 MXN` se calcula automáticamente desde el campo `price` y la moneda del producto — no requiere configuración adicional. Si ves un formato distinto, confirma que no estés viendo un valor exportado directamente de una hoja de cálculo con configuración regional distinta.

## Error de permisos ("This action is unauthorized")

- El usuario no tiene el permiso necesario para esa acción. Revisa su rol en **Usuarios y roles** y compara con la tabla de permisos de `MANUAL-USUARIO.md` sección 7, o con `API-DOCUMENTATION.md` para el permiso exacto que exige cada ruta.

## El cron no ejecuta nada

- Confirma la ruta absoluta al binario de PHP y al archivo `artisan` (usa `which php` y `pwd` por SSH, o revisa las propiedades de la carpeta en el Administrador de Archivos).
- En hPanel, algunos planes requieren la ruta completa a PHP, por ejemplo `/usr/bin/php8.2` en vez de solo `php`.

## Extensiones PHP faltantes al correr Composer/artisan localmente

Si desarrollas localmente con XAMPP/WAMP/Laragon y ves errores sobre `ext-gd` o `ext-zip`, edita tu `php.ini` y descomenta (quita el `;`) las líneas `extension=gd` y `extension=zip`, luego reinicia el servidor web.

## ¿Dónde reporto un error que no está en esta lista?

Revisa primero `storage/logs/laravel.log` con el error completo y la traza (stack trace) — esa información es la que necesitarás para diagnosticar o solicitar ayuda.
