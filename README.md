# NODO Catalog Manager

**El centro inteligente de contenidos, catálogos y automatización de NODO 360.**

Sistema web para administrar el catálogo de productos y servicios de **NODO 360 MARKETING TECHNOLOGY**, desarrollado en Laravel 12 / PHP 8.2+ / MySQL, pensado para desplegarse en hosting compartido (Hostinger) sin depender de un servidor Node.js en producción.

## Estado del proyecto: Completo — Fase 1 a Fase 9 (roadmap terminado)

Este repositorio se desarrolló por fases, acordado con el propietario del sistema. Las 9 fases planeadas están **completas y funcionales, sin pantallas simuladas ni botones decorativos**:

**Fase 1 — Núcleo**
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

**Fase 2 — Inteligencia artificial (texto)**
- ✅ Conexión configurable a OpenAI (o compatible) y Google Gemini, con clave de API cifrada
- ✅ 19 tareas de generación de texto (descripciones, beneficios, SEO, WhatsApp, prompts de imagen, traducción, etc.)
- ✅ Botones "Generar con IA" en el formulario de producto con flujo de revisión antes de guardar
- ✅ Generador de contenido general e historial de uso con tokens y costo aproximado
- ⚠️ Requiere que NODO 360 proporcione una clave de API real — mientras tanto, el botón permanece deshabilitado (no se simula ninguna respuesta)

**Fase 3 — Generador de imágenes**
- ✅ Motor de composición propio (GD/Intervention Image): degradados de marca, logotipo, título, subtítulo, precio, CTA, código QR y pie de marca, con tipografía real
- ✅ 5 formatos del brief (cuadrado, vertical, historia, horizontal, portada) y plantilla maestra NODO 360 preconfigurada
- ✅ Fondo desde degradado, imagen subida, imagen del producto, o generado con IA (requiere proveedor OpenAI configurado)
- ✅ Descarga PNG, uso directo como imagen principal o en la galería del producto, historial de imágenes

**Fase 4 — Redes sociales**
- ✅ Cuentas conectadas por canal (Facebook, Instagram, LinkedIn, TikTok, X, Google Business Profile), token cifrado
- ✅ Calendario editorial mensual y vista de lista, filtrable por canal y estado
- ✅ Duplicar por canal, aprobar, cancelar, reintentar, marcar como publicada manualmente, descargar imagen, exportar CSV
- ✅ Publicación automática real hacia Facebook (Graph API de Meta) + comando programado por cron para publicaciones vencidas
- ⚠️ Instagram/LinkedIn/TikTok/X/Google Business todavía no tienen conector automático — se preparan, programan y descargan para publicación manual

**Fase 5 — Email marketing**
- ✅ Contactos con consentimiento, suscripción, listas, importación masiva (CSV/XLSX/XLS) y exportación CSV
- ✅ Constructor visual de campañas por bloques (encabezado, texto, imagen, botón, productos, separador, redes, pie legal), 10 tipos de campaña del brief
- ✅ Envío de prueba, programación y envío inmediato, en cola respetuosa del proveedor SMTP vía cron (`email:send-due-campaigns`)
- ✅ SMTP de campañas independiente del transaccional (SMTP propio, Brevo, Mailgun, SendGrid, Amazon SES), con contraseña cifrada y prueba de conexión real
- ✅ Seguimiento real de aperturas (píxel) y clics (enlaces rastreados), reporte por campaña, baja con página pública de confirmación
- ⚠️ Requiere que NODO 360 proporcione credenciales SMTP reales — mientras tanto, "Enviar prueba" y el envío programado muestran con claridad que falta configurar el proveedor

**Fase 6 — Landing Pages**
- ✅ Constructor por secciones (problema, solución, beneficios, características, testimonios, FAQ, producto destacado, texto, imagen, video, CTA), con producto del catálogo vinculado opcionalmente
- ✅ Publicación con URL pública propia (`/lp/{slug}`), SEO completo (meta title/description, Open Graph, datos estructurados JSON-LD con Product y FAQPage automáticos)
- ✅ Analítica opcional por landing (Google Analytics 4, Meta Pixel, Google Tag Manager) — solo se carga si se configura
- ✅ Formulario de captura de prospectos con seguimiento UTM, integrado opcionalmente con listas de contactos de email marketing (Fase 5)
- ✅ Reporte de vistas/prospectos/conversión por landing, código QR descargable, duplicar landing page

**Fase 7 — CRM**
- ✅ Pipeline de ventas Kanban con etapas configurables, arrastrar y soltar entre columnas con guardado inmediato
- ✅ Oportunidades ligadas a contactos de email marketing, con producto y responsable asignado opcionales
- ✅ Marcar ganado/perdido (con motivo), actividades (notas, llamadas, reuniones, tareas/recordatorios con fecha límite)
- ✅ Enlace directo de WhatsApp (`wa.me`) por prospecto, sin necesidad de la API oficial
- ✅ Conversión con un clic de un prospecto de landing page (Fase 6) a una oportunidad del CRM

**Fase 8 — Meta Commerce y feeds**
- ✅ Feed de catálogo público en CSV y XML (compatible con Meta Commerce Catalog y Google Shopping), protegido por token propio, listo para registrarse como fuente de datos programada
- ✅ Solo incluye productos publicados con precio y enlace configurados; mapeo de disponibilidad al vocabulario estándar de feeds
- ✅ Conexión con el catálogo de Meta (ID + token cifrado) con prueba de conexión real contra la Graph API
- ✅ Regenerar el enlace del feed para revocar acceso; historial de sincronización con cada solicitud del feed y cada prueba de conexión

**Fase 9 — IA Ready Website (auditor SEO/AEO/GEO)**
- ✅ Auditor de URLs externas: descarga en tiempo real el HTML, `robots.txt`, `sitemap.xml` y `llms.txt` del sitio analizado — sin simular ninguna señal
- ✅ Calificación de 0 a 100 con desglose en SEO tradicional (40 pts), AEO — motores de respuesta por IA (30 pts) y GEO — motores generativos (30 pts), 19 señales verificadas en total
- ✅ Reporte con explicación de cada señal (aprobada/no aprobada, puntos, detalle) y **descarga en PDF** (dompdf, sin dependencias externas)
- ✅ Historial de auditorías anteriores

**139 pruebas automatizadas (PHPUnit)** en total.

Con la Fase 9 se completan las **9 fases del roadmap original**. Todos los módulos que requieren credenciales de terceros (IA, SMTP, Meta, etc.) están completamente construidos y probados — indican con claridad cuándo falta que NODO 360 proporcione la credencial correspondiente, en vez de simular una respuesta. Ver `CONFIGURACION-APIS.md` para el detalle de cada integración.

## Tecnología

| Capa | Tecnología |
|---|---|
| Backend | PHP 8.2+, Laravel 12 |
| Base de datos | MySQL 5.7+/MariaDB 10.3+ |
| Roles y permisos | spatie/laravel-permission |
| Auditoría | spatie/laravel-activitylog |
| Importación/Exportación | maatwebsite/excel, phpoffice/phpspreadsheet |
| Imágenes | intervention/image |
| Códigos QR | endroid/qr-code |
| Reportes PDF | dompdf/dompdf |
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
tests/                      139 pruebas automatizadas
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
- [API-DOCUMENTATION.md](API-DOCUMENTATION.md) — Rutas web internas de todo el sistema
- [TROUBLESHOOTING.md](TROUBLESHOOTING.md) — Solución de problemas comunes
- [CHANGELOG.md](CHANGELOG.md) — Historial de versiones y hoja de ruta de fases

## Licencia

Software propietario desarrollado para NODO 360 MARKETING TECHNOLOGY. Todos los derechos reservados.
