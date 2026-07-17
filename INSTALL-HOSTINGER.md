# Instalación en Hostinger — NODO Catalog Manager

Esta guía cubre la instalación en Hostinger con dos métodos (con SSH y sin SSH), y para tres escenarios de dominio: **dominio principal**, **subdominio** y **subcarpeta de `public_html`**.

> Sustituye `tu_cuenta`, `tudominio.com` y las credenciales de ejemplo por los datos reales de tu hosting. hPanel = panel de control de Hostinger.

---

## 0. Antes de empezar

1. Ten a mano: usuario de hPanel, dominio o subdominio a usar, y acceso SSH si lo vas a usar (Plan Business/Cloud de Hostinger).
2. Requisitos del servidor: **PHP 8.2 o superior**, extensiones `pdo_mysql, mbstring, openssl, gd, zip, fileinfo, tokenizer, ctype, json, curl, bcmath` (todas vienen activas por defecto en Hostinger; si falta alguna, actívala en hPanel → Avanzado → PHP Configuration).
3. El archivo ZIP de producción (`nodo-catalog-manager-production.zip`) **ya incluye la carpeta `vendor/` y el CSS compilado** — no necesitas Composer ni Node.js en el servidor.

---

## MÉTODO A — Hostinger con SSH (recomendado, plan Business o superior)

### A.1 Crear el dominio/subdominio

- **Dominio principal**: ya existe al contratar el hosting, apunta a `public_html/`.
- **Subdominio**: hPanel → Dominios → Subdominios → crear `catalogo.tudominio.com`. Hostinger crea automáticamente `public_html/catalogo.tudominio.com/`.
- **Subcarpeta**: no se crea nada especial; usarás una carpeta dentro de `public_html/`, por ejemplo `public_html/catalogo/`.

### A.2 Crear la base de datos MySQL

hPanel → Bases de datos → Bases de datos MySQL → Crear base de datos. Anota:

```
Host:      localhost   (normalmente; Hostinger a veces usa 127.0.0.1)
Base:      u123456789_nodo
Usuario:   u123456789_nodo
Password:  (la que definas)
```

### A.3 Conectarte por SSH

hPanel → Avanzado → SSH Access → activa el acceso y copia el comando de conexión:

```bash
ssh u123456789@tudominio.com -p 65002
```

### A.4 Subir el proyecto

Opción 1 — subir el ZIP y descomprimirlo por SSH:

```bash
cd ~/public_html            # dominio principal
# o: cd ~/public_html/catalogo.tudominio.com   (subdominio)
# o: cd ~/public_html/catalogo                 (subcarpeta, créala si no existe)

# Sube nodo-catalog-manager-production.zip a esta carpeta (con el Administrador de
# Archivos de hPanel, o con scp/sftp desde tu computadora), luego:
unzip nodo-catalog-manager-production.zip -d nodo-tmp
mv nodo-tmp/* nodo-tmp/.[!.]* . 2>/dev/null
rmdir nodo-tmp
rm nodo-catalog-manager-production.zip
```

Al terminar debes tener en esa carpeta: `app/`, `bootstrap/`, `config/`, `database/`, `public/`, `resources/`, `routes/`, `storage/`, `vendor/`, `artisan`, `composer.json`, `.env.example`, etc.

### A.5 Configurar el document root hacia `public/`

Laravel debe servirse desde la carpeta `public/` del proyecto, **no** desde la raíz.

- **Dominio principal o subdominio**: hPanel → Dominios → (tu dominio/subdominio) → **Gestionar** → cambia el *Document Root* para que apunte a `public_html/public` (o `public_html/catalogo.tudominio.com/public`).
- **Subcarpeta sin poder cambiar el document root** (planes que no lo permiten): salta a la sección **A.5-bis**.

### A.5-bis Instalación en subcarpeta sin poder mover el document root

Si tu plan no te permite apuntar el document root a `public/`, coloca el proyecto **fuera** de `public_html` (por ejemplo en `~/nodo-catalog-manager`) y crea una carpeta `public_html/catalogo/` con una copia ajustada de `public/index.php`:

```bash
mkdir -p ~/public_html/catalogo
cp ~/nodo-catalog-manager/public/index.php ~/public_html/catalogo/index.php
cp -r ~/nodo-catalog-manager/public/build ~/public_html/catalogo/build
cp -r ~/nodo-catalog-manager/public/vendor ~/public_html/catalogo/vendor
cp ~/nodo-catalog-manager/public/favicon.ico ~/public_html/catalogo/ 2>/dev/null
cp ~/nodo-catalog-manager/public/robots.txt ~/public_html/catalogo/ 2>/dev/null
```

Edita `public_html/catalogo/index.php` y ajusta las rutas `require` al inicio del archivo para que apunten a la carpeta real del proyecto, por ejemplo:

```php
require __DIR__.'/../../nodo-catalog-manager/vendor/autoload.php';
$app = require_once __DIR__.'/../../nodo-catalog-manager/bootstrap/app.php';
```

(Ajusta el número de `../` según la profundidad real de tu carpeta.)

### A.6 Crear el archivo `.env`

```bash
cd ~/public_html            # o la carpeta real del proyecto si usaste A.5-bis
cp .env.example .env
nano .env
```

Completa como mínimo:

```
APP_URL=https://tudominio.com          # o https://catalogo.tudominio.com, o https://tudominio.com/catalogo
DB_DATABASE=u123456789_nodo
DB_USERNAME=u123456789_nodo
DB_PASSWORD=tu_password_de_bd
MAIL_MAILER=smtp
MAIL_HOST=smtp.tuproveedor.com
MAIL_USERNAME=...
MAIL_PASSWORD=...
```

### A.7 Generar la clave de la aplicación

```bash
php artisan key:generate --force
```

### A.8 Ejecutar migraciones y seeders

```bash
php artisan migrate --force
php artisan db:seed --force
```

Esto crea las tablas, los 9 roles, los permisos, la configuración inicial, las 6 colecciones y los 39 productos de NODO 360, y **un usuario administrador** usando `NODO_ADMIN_EMAIL` / `NODO_ADMIN_PASSWORD` de tu `.env` (o los valores por defecto documentados en `MANUAL-USUARIO.md` si no los definiste — **cámbialos de inmediato**).

> Alternativa: en vez de este paso, puedes simplemente visitar `https://tudominio.com/install` en el navegador y completar el asistente web, que hace lo mismo con una interfaz guiada y te permite definir tu propio usuario y contraseña. Si usas el asistente web, omite este paso A.8.

### A.9 Enlace de almacenamiento (para imágenes subidas)

```bash
php artisan storage:link
```

### A.10 Permisos de carpetas

```bash
chmod -R 755 storage bootstrap/cache
```

### A.11 Bloquear el instalador

Si usaste el paso A.8 (consola), bloquea el instalador manualmente:

```bash
touch storage/app/installed.lock
```

(Si usaste el asistente web `/install`, esto ya ocurre automáticamente al finalizar.)

### A.12 Configurar el cron (scheduler y colas)

hPanel → Avanzado → Cron Jobs → agrega **dos** tareas:

```cron
* * * * * php /home/u123456789/public_html/artisan schedule:run >> /dev/null 2>&1
* * * * * php /home/u123456789/public_html/artisan queue:work --stop-when-empty --max-time=50 >> /dev/null 2>&1
```

Ajusta la ruta `/home/u123456789/public_html/artisan` a la ruta real de tu proyecto (usa `pwd` dentro de la carpeta del proyecto por SSH para confirmarla).

- La primera línea ejecuta el planificador de Laravel cada minuto, que a su vez dispara `social:publish-due` (publicaciones de redes sociales vencidas) y `email:send-due-campaigns` (arma y encola las campañas de email programadas cuya fecha ya llegó).
- La segunda **procesa la cola de trabajos en segundo plano** (importaciones masivas y envío de correos de campañas) cada minuto durante hasta 50 segundos y luego se detiene (`--stop-when-empty`), evitando dejar un proceso permanente — Hostinger no permite workers de cola persistentes en hosting compartido, así que este patrón de cron + `--stop-when-empty` es la alternativa recomendada.

### A.13 Activar SSL

hPanel → Seguridad → SSL → activa el certificado gratuito (Let's Encrypt) para tu dominio/subdominio. Una vez activo, confirma que `APP_URL` en `.env` use `https://`.

### A.14 Verificar

Visita tu URL. Si no corriste el instalador web, deberías ver la pantalla de login directamente. Si algo falla, revisa `storage/logs/laravel.log` y consulta `TROUBLESHOOTING.md`.

### A.15 Confirmar que la depuración está desactivada

```bash
grep APP_DEBUG .env    # debe mostrar APP_DEBUG=false
grep APP_ENV .env       # debe mostrar APP_ENV=production
```

---

## MÉTODO B — Hostinger sin SSH (planes compartidos básicos)

### B.1 Crear la base de datos

Igual que el paso A.2, desde hPanel → Bases de datos MySQL.

### B.2 Descargar el ZIP y subirlo

1. Descarga `nodo-catalog-manager-production.zip` a tu computadora.
2. hPanel → Archivos → Administrador de archivos.
3. Entra a `public_html` (o a la subcarpeta/subdominio que hayas creado, ver A.1).
4. Sube el ZIP con el botón **Subir**.
5. Selecciona el ZIP → botón **Extraer**.
6. Verifica que los archivos (`app/`, `public/`, `vendor/`, etc.) queden directamente dentro de la carpeta de destino, no dentro de una subcarpeta extra generada por el ZIP — si es así, muévelos un nivel arriba con el propio Administrador de Archivos.

### B.3 Ajustar el `index.php` cuando no puedas cambiar el document root

Si tu plan no permite mover el document root a `public/` (ver A.5), mueve el **contenido** de la carpeta `public/` a la raíz de `public_html` (o de tu subcarpeta) y edita `index.php`:

1. Copia todo el contenido de `public/` a la raíz de destino.
2. Copia las carpetas restantes (`app`, `bootstrap`, `config`, `database`, `resources`, `routes`, `storage`, `vendor`, `artisan`, `composer.json`) a una carpeta **fuera** de `public_html`, por ejemplo `~/nodo-app` (usa el Administrador de Archivos para crearla un nivel arriba de `public_html` si tu panel lo permite; si no, créala dentro de `public_html` con un nombre no adivinable y protégela, ver `SEGURIDAD.md`).
3. Edita el `index.php` que quedó en la raíz pública y cambia las dos líneas `require`:

```php
require __DIR__.'/../nodo-app/vendor/autoload.php';
$app = require_once __DIR__.'/../nodo-app/bootstrap/app.php';
```

### B.4 Crear el archivo `.env`

Con el Administrador de Archivos: duplica `.env.example`, renómbralo a `.env`, ábrelo con el editor de texto integrado y completa `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD`, `APP_URL` y los datos de correo, igual que en A.6.

### B.5 Ejecutar el instalador web

Como no tienes SSH para correr `artisan`, usa el instalador web:

1. Visita `https://tudominio.com/install` (o la URL de tu subdominio/subcarpeta).
2. Sigue el asistente: verificación de requisitos → conexión a base de datos → datos de la empresa → usuario administrador → instalación automática (migra y siembra la base de datos, genera la clave de la aplicación, crea el enlace de almacenamiento y bloquea el propio instalador al finalizar).

> El instalador web hace exactamente lo mismo que los pasos A.7–A.11 y A.13, pero con una interfaz guiada — es la vía recomendada cuando no tienes acceso SSH.

### B.6 Configurar el cron desde hPanel

hPanel → Avanzado → Cron Jobs → agrega las mismas dos líneas del paso A.12, ajustando la ruta real de tu proyecto (Hostinger la muestra en el propio formulario de creación de cron, o revisa la ruta completa en el Administrador de Archivos, botón "Propiedades" de la carpeta).

### B.7 Activar SSL

Igual que A.13, desde hPanel → Seguridad → SSL.

### B.8 Probar el sistema

Visita tu URL, inicia sesión con el usuario administrador que creaste en el asistente, y confirma que el dashboard cargue con las 6 colecciones y 39 productos iniciales.

---

## Resumen de rutas de cron

```cron
* * * * * php /ruta/a/tu/proyecto/artisan schedule:run >> /dev/null 2>&1
* * * * * php /ruta/a/tu/proyecto/artisan queue:work --stop-when-empty --max-time=50 >> /dev/null 2>&1
```

## Respaldo

Ver `BACKUP-RESTORE.md` para el procedimiento de respaldo de base de datos y archivos subidos (`storage/app/public`).

## Problemas comunes

Ver `TROUBLESHOOTING.md`.
