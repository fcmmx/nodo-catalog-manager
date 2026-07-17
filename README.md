# NODO Catalog Manager

**El centro inteligente de contenidos, catálogos y automatización de NODO 360.**

Sistema web para administrar el catálogo de productos y servicios de **NODO 360 MARKETING TECHNOLOGY**, desarrollado en Laravel 12 / PHP 8.2+ / MySQL, pensado para desplegarse en hosting compartido (Hostinger) sin depender de un servidor Node.js en producción.

## Estado del proyecto: Fase 1 (Núcleo)

Este repositorio contiene la **Fase 1** de un desarrollo por fases acordado con el propietario del sistema. Todo lo incluido aquí está **completo y funcional, sin pantallas simuladas ni botones decorativos**:

- ✅ Instalador web guiado (`/install`)
- ✅ Autenticación, roles y permisos (9 roles, permisos granulares)
- ✅ Catálogo de productos y servicios (CRUD completo, campos del brief, edición y acciones masivas, papelera/restauración, duplicar, vista previa)
- ✅ Colecciones y categorías
- ✅ Importación masiva (CSV/XLSX/XLS/JSON) con mapeo de columnas, detección de duplicados, procesamiento por lotes en cola y reporte de errores
- ✅ Exportación (CSV/JSON, filtrable)
- ✅ Dashboard ejecutivo con métricas reales
- ✅ Configuración de empresa, marca, moneda, zona horaria, seguridad de login
- ✅ Registro de actividad (auditoría) de todo el sistema
- ✅ Datos iniciales de NODO 360: 6 colecciones y 39 productos/servicios en borrador, listos para editar
- ✅ 42 pruebas automatizadas (PHPUnit)

**No incluido en esta fase** (módulos completos, pendientes de fases siguientes, ver `CHANGELOG.md`): generador de imágenes con IA, generación de texto con IA, redes sociales, email marketing, landing pages, CRM, feed de Meta Commerce, auditor IA-Ready. Estos módulos requieren credenciales de APIs externas que son propiedad de NODO 360 y no se inventan ni simulan en este entregable.

## Tecnología

| Capa | Tecnología |
|---|---|
| Backend | PHP 8.2+, Laravel 12 |
| Base de datos | MySQL 5.7+/MariaDB 10.3+ |
| Roles y permisos | spatie/laravel-permission |
| Auditoría | spatie/laravel-activitylog |
| Importación/Exportación | maatwebsite/excel, phpoffice/phpspreadsheet |
| Imágenes | intervention/image |
| Frontend | Blade + Tailwind CSS 4 (compilado con el binario standalone, **sin Node.js en producción**) + Alpine.js (servido localmente, sin CDN) |
| Colas | Laravel Queues (driver `database`, procesadas por cron — ver `INSTALL-HOSTINGER.md`) |

## Estructura relevante

```
app/Http/Controllers/       Controladores (Auth, Catalog, Admin, Install)
app/Models/                 Product, Collection, Category, Setting, User, ImportBatch...
app/Services/               EnvEditor, RequirementsChecker, SpreadsheetReader, ProductImportProcessor
app/Jobs/                   ProcessProductImportJob
database/migrations/        Esquema completo de la Fase 1
database/seeders/           Roles, permisos, configuración, colecciones y productos NODO 360
resources/views/            Vistas Blade (layouts, catálogo, admin, instalador)
resources/css/app.css       Fuente de Tailwind (compilar con bin/tailwindcss.exe)
public/build/app.css        CSS ya compilado y listo para producción
public/vendor/alpine/       Alpine.js servido localmente
tests/                      42 pruebas automatizadas
```

## Instalación rápida (desarrollo local)

Requisitos: PHP 8.2+, Composer, MySQL/MariaDB.

```bash
composer install
cp .env.example .env
# Edita .env con tus datos de base de datos
php artisan key:generate
php artisan migrate --seed
php artisan storage:link
php artisan serve
```

Visita `http://localhost:8000` — si no existe `storage/app/installed.lock`, el sistema te llevará automáticamente al instalador web en `/install`. Para omitir el instalador en desarrollo, crea manualmente ese archivo vacío después de migrar y sembrar.

Para producción en Hostinger, sigue la guía completa en **[INSTALL-HOSTINGER.md](INSTALL-HOSTINGER.md)**.

### Recompilar el CSS (solo si modificas clases de Tailwind)

`public/build/app.css` ya viene compilado y listo para usarse — no necesitas hacer nada para desplegar. Si en el futuro cambias `resources/css/app.css` o agregas clases nuevas en las vistas, recompílalo con el CLI standalone de Tailwind (no requiere Node.js):

```bash
# Descarga una sola vez el binario correspondiente a tu sistema operativo desde
# https://github.com/tailwindlabs/tailwindcss/releases (por ejemplo tailwindcss-windows-x64.exe)
# y guárdalo en bin/tailwindcss.exe (Windows) o bin/tailwindcss (Linux/Mac).

./bin/tailwindcss.exe -i resources/css/app.css -o public/build/app.css --minify
```

## Documentación

- [INSTALL-HOSTINGER.md](INSTALL-HOSTINGER.md) — Instalación paso a paso en Hostinger (con y sin SSH)
- [MANUAL-USUARIO.md](MANUAL-USUARIO.md) — Manual de uso del sistema
- [CONFIGURACION-APIS.md](CONFIGURACION-APIS.md) — Integraciones externas: qué está listo y qué falta por conectar
- [SEGURIDAD.md](SEGURIDAD.md) — Medidas de seguridad implementadas y recomendaciones
- [BACKUP-RESTORE.md](BACKUP-RESTORE.md) — Respaldo y restauración
- [DATABASE-DIAGRAM.md](DATABASE-DIAGRAM.md) — Diagrama y descripción de la base de datos
- [API-DOCUMENTATION.md](API-DOCUMENTATION.md) — Rutas web internas de la Fase 1
- [TROUBLESHOOTING.md](TROUBLESHOOTING.md) — Solución de problemas comunes
- [CHANGELOG.md](CHANGELOG.md) — Historial de versiones y hoja de ruta de fases

## Licencia

Software propietario desarrollado para NODO 360 MARKETING TECHNOLOGY. Todos los derechos reservados.
