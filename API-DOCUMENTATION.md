# Documentación de Rutas — NODO Catalog Manager (Fase 1 a Fase 9 — completo)

## Alcance

La Fase 1 expone las funcionalidades del sistema como **rutas web autenticadas por sesión** (no como una API REST pública con tokens). Esto es intencional: todas las pantallas del panel consumen estas mismas rutas. Una API REST pública con autenticación por token (Laravel Sanctum) para integraciones externas queda planificada para una fase posterior, cuando exista un consumidor real que la necesite (por ejemplo, la app móvil o integraciones de terceros), evitando construir una superficie de API sin uso concreto todavía.

Todas las rutas listadas abajo:

- Requieren sesión autenticada (`auth` middleware), excepto `/login` y `/install/*`.
- Están protegidas con permisos granulares (`permission:` middleware o `$this->authorize()` dentro del controlador) — ver `MANUAL-USUARIO.md` sección 7 para el detalle de qué permiso tiene cada rol.
- Incluyen protección CSRF automática en formularios.

## Autenticación

| Método | Ruta | Descripción |
|---|---|---|
| GET | `/login` | Formulario de inicio de sesión |
| POST | `/login` | Autenticar (rate limit + bloqueo por intentos fallidos) |
| POST | `/logout` | Cerrar sesión |
| GET/POST | `/olvide-password` | Solicitar enlace de recuperación |
| GET/POST | `/restablecer-password` | Restablecer contraseña con token |

## Catálogo — Productos

| Método | Ruta | Permiso |
|---|---|---|
| GET | `/catalogo/productos` | `ver productos` |
| GET | `/catalogo/productos/create` | `crear productos` |
| POST | `/catalogo/productos` | `crear productos` |
| GET | `/catalogo/productos/{id}/edit` | `ver productos` |
| PUT | `/catalogo/productos/{id}` | `editar productos` |
| DELETE | `/catalogo/productos/{id}` | `eliminar productos` (eliminación lógica) |
| POST | `/catalogo/productos/{id}/restaurar` | `editar productos` |
| POST | `/catalogo/productos/{id}/duplicar` | `crear productos` |
| POST | `/catalogo/productos/{id}/archivar` | `editar productos` |
| GET | `/catalogo/productos/{id}/vista-previa` | `ver productos` |
| POST | `/catalogo/productos/masivo` | `editar productos` (edición masiva) |
| GET | `/catalogo/productos/exportar?format=csv\|json` | `exportar productos` |

## Catálogo — Colecciones y categorías

| Método | Ruta | Permiso |
|---|---|---|
| GET/POST | `/catalogo/colecciones` | `ver colecciones` / `crear colecciones` |
| GET/PUT/DELETE | `/catalogo/colecciones/{id}` | `editar colecciones` / `eliminar colecciones` |
| GET/POST | `/catalogo/categorias` | `ver categorias` / `crear categorias` |
| GET/PUT/DELETE | `/catalogo/categorias/{id}` | `editar categorias` / `eliminar categorias` |

## Importación / Exportación

| Método | Ruta | Permiso |
|---|---|---|
| GET | `/catalogo/importar-exportar` | `importar productos` |
| GET | `/catalogo/importar-exportar/plantilla` | `importar productos` |
| POST | `/catalogo/importar-exportar/subir` | `importar productos` |
| POST | `/catalogo/importar-exportar/{batch}/mapear` | `importar productos` |
| GET | `/catalogo/importar-exportar/{batch}` | `importar productos` |
| GET | `/catalogo/importar-exportar/{batch}/errores` | `importar productos` |

## Administración

| Método | Ruta | Permiso |
|---|---|---|
| GET/POST | `/admin/usuarios` | `ver usuarios` / `administrar usuarios` |
| GET/PUT/DELETE | `/admin/usuarios/{id}` | `administrar usuarios` |
| GET | `/admin/actividad` | `ver actividad` |
| GET | `/admin/configuracion` | `ver configuracion` |
| PUT | `/admin/configuracion` | `administrar configuracion` |

## Inteligencia artificial (Fase 2)

| Método | Ruta | Permiso |
|---|---|---|
| GET | `/admin/ia/configuracion` | `configurar ia` |
| PUT | `/admin/ia/configuracion` | `configurar ia` |
| POST | `/admin/ia/configuracion/probar` | `configurar ia` (prueba de conexión real con el proveedor configurado) |
| GET | `/ia/generador` | `usar ia` |
| POST | `/ia/generar` | `usar ia` (JSON: `task`, más `tema`/`texto`/`tono`/`idioma`/`canal`/`product_id` según la tarea) |
| POST | `/ia/generaciones/{id}/aprobar` | `usar ia` |
| POST | `/ia/generaciones/{id}/rechazar` | `usar ia` |
| GET | `/ia/historial` | `ver historial ia` |

## Generador de imágenes (Fase 3)

| Método | Ruta | Permiso |
|---|---|---|
| GET/POST | `/imagenes/plantillas` | `ver imagenes` / `crear imagenes` |
| GET/PUT/DELETE | `/imagenes/plantillas/{id}` | `editar imagenes` / `eliminar imagenes` |
| GET | `/imagenes/generador` | `ver imagenes` |
| POST | `/imagenes/generar` | `crear imagenes` |
| GET | `/imagenes/historial` | `ver imagenes` |
| GET | `/imagenes/generaciones/{id}` | `ver imagenes` |
| DELETE | `/imagenes/generaciones/{id}` | `eliminar imagenes` |
| POST | `/imagenes/generaciones/{id}/usar-principal` | `editar imagenes` |
| POST | `/imagenes/generaciones/{id}/galeria` | `editar imagenes` |

## Redes sociales (Fase 4)

| Método | Ruta | Permiso |
|---|---|---|
| GET/POST | `/redes/cuentas` | `ver redes` / `conectar cuentas redes` |
| GET/PUT/DELETE | `/redes/cuentas/{id}` | `conectar cuentas redes` |
| GET | `/redes/publicaciones` | `ver redes` (calendario editorial) |
| GET/POST | `/redes/publicaciones/create` | `crear redes` |
| GET/PUT/DELETE | `/redes/publicaciones/{id}` | `editar redes` / `eliminar redes` |
| POST | `/redes/publicaciones/{id}/duplicar` | `crear redes` |
| POST | `/redes/publicaciones/{id}/aprobar` | `aprobar redes` |
| POST | `/redes/publicaciones/{id}/cancelar` | `editar redes` |
| POST | `/redes/publicaciones/{id}/publicar` | `publicar redes` (envío real a Facebook) |
| POST | `/redes/publicaciones/{id}/publicar-manual` | `publicar redes` |
| GET | `/redes/publicaciones/{id}/descargar` | `ver redes` |
| GET | `/redes/calendario/exportar` | `ver redes` (CSV) |

Comando de consola: `php artisan social:publish-due` (programado cada minuto vía el scheduler, ver `INSTALL-HOSTINGER.md`).

## Email marketing (Fase 5)

| Método | Ruta | Permiso |
|---|---|---|
| GET | `/admin/email/configuracion` | `configurar campanas` |
| PUT | `/admin/email/configuracion` | `configurar campanas` |
| POST | `/admin/email/configuracion/probar` | `configurar campanas` (envía un correo de prueba real con la configuración guardada) |
| GET | `/email/contactos` | `ver contactos` |
| GET/POST | `/email/contactos/create` | `crear contactos` |
| GET/PUT/DELETE | `/email/contactos/{id}` | `editar contactos` / `eliminar contactos` |
| GET | `/email/contactos/importar` | `importar contactos` |
| POST | `/email/contactos/importar` | `importar contactos` (CSV/XLSX/XLS) |
| GET | `/email/contactos/exportar` | `exportar contactos` (CSV) |
| GET/POST | `/email/listas` | `ver contactos` / `crear contactos` |
| GET/PUT/DELETE | `/email/listas/{id}` | `editar contactos` / `eliminar contactos` |
| GET | `/email/campanas` | `ver campanas` |
| GET/POST | `/email/campanas/create` | `crear campanas` |
| GET/PUT/DELETE | `/email/campanas/{id}` | `editar campanas` / `eliminar campanas` |
| POST | `/email/campanas/{id}/prueba` | `enviar campanas` (envío de prueba real, requiere `test_email`) |
| POST | `/email/campanas/{id}/programar` | `enviar campanas` (requiere lista de contactos asignada) |
| POST | `/email/campanas/{id}/enviar-ahora` | `enviar campanas` |
| POST | `/email/campanas/{id}/pausar` | `enviar campanas` |
| GET | `/email/campanas/{id}/reporte` | `ver campanas` |

Comando de consola: `php artisan email:send-due-campaigns` (programado cada minuto vía el scheduler, ver `INSTALL-HOSTINGER.md`).

## Landing Pages (Fase 6)

| Método | Ruta | Permiso |
|---|---|---|
| GET | `/landing` | `ver landing` |
| GET/POST | `/landing/create` | `crear landing` |
| GET/PUT/DELETE | `/landing/{id}` | `editar landing` / `eliminar landing` |
| POST | `/landing/{id}/duplicar` | `crear landing` |
| POST | `/landing/{id}/publicar` | `publicar landing` (exige titular y al menos una sección) |
| POST | `/landing/{id}/despublicar` | `publicar landing` |
| GET | `/landing/{id}/prospectos` | `ver landing` |
| GET | `/landing/{id}/qr` | `ver landing` (descarga PNG del código QR) |

### Enlaces públicos de landing pages (sin autenticación)

| Método | Ruta | Descripción |
|---|---|---|
| GET | `/lp/{slug}` | Renderiza la landing page publicada (404 si está en borrador o no existe) |
| POST | `/lp/{slug}/prospecto` | Captura un prospecto desde el formulario público |

### Enlaces públicos de email marketing (sin autenticación)

Incluidos en el HTML de cada correo enviado; usan un token propio de alta entropía (48 caracteres) por envío, no la sesión del usuario.

| Método | Ruta | Descripción |
|---|---|---|
| GET | `/email/abrir/{token}` | Píxel de seguimiento de apertura (1×1, transparente) |
| GET | `/email/clic/{token}?url=...` | Redirección rastreada de clic (URL de destino codificada en base64) |
| GET | `/email/baja/{token}` | Página pública de confirmación de baja |
| POST | `/email/baja/{token}` | Confirma la baja: marca al contacto como no suscrito |

## CRM (Fase 7)

| Método | Ruta | Permiso |
|---|---|---|
| GET | `/crm` | `ver crm` (tablero Kanban) |
| GET/POST | `/crm/create` | `crear crm` |
| PUT/DELETE | `/crm/{id}` | `editar crm` / `eliminar crm` |
| GET | `/crm/{id}/edit` | `ver crm` |
| POST | `/crm/{id}/mover` | `editar crm` (JSON: `stage_id`; usado por el arrastrar-y-soltar del tablero) |
| POST | `/crm/{id}/ganado` | `editar crm` |
| POST | `/crm/{id}/perdido` | `editar crm` (`lost_reason` opcional) |
| POST | `/crm/{id}/asignar` | `asignar crm` |
| POST | `/crm/convertir/{lead}` | `crear crm` (convierte un prospecto de landing page en una oportunidad) |
| POST | `/crm/{id}/actividades` | `editar crm` |
| POST | `/crm/actividades/{id}/completar` | `editar crm` |
| DELETE | `/crm/actividades/{id}` | `editar crm` |
| GET/POST | `/crm/etapas` | `ver crm` / `crear crm` |
| GET/PUT/DELETE | `/crm/etapas/{id}` | `editar crm` / `eliminar crm` |

## Meta Commerce y feeds (Fase 8)

| Método | Ruta | Permiso |
|---|---|---|
| GET | `/admin/comercio/configuracion` | `ver comercio` |
| PUT | `/admin/comercio/configuracion` | `configurar comercio` |
| POST | `/admin/comercio/configuracion/probar` | `configurar comercio` (prueba real de solo lectura contra la Graph API de Meta) |
| POST | `/admin/comercio/configuracion/regenerar-token` | `configurar comercio` (invalida la URL del feed anterior) |
| GET | `/admin/comercio/historial` | `ver comercio` |

### Feed público de catálogo (sin autenticación)

Protegido por un token propio de alta entropía en la URL (no por sesión), pensado para que Meta Commerce Manager u otra plataforma lo lea de forma periódica.

| Método | Ruta | Descripción |
|---|---|---|
| GET | `/feed/{token}/catalogo.csv` | Feed CSV de productos publicados con precio y enlace |
| GET | `/feed/{token}/catalogo.xml` | Mismo feed en XML (RSS 2.0 / espacio de nombres de Google Shopping) |

## Auditor SEO/AEO/GEO — IA Ready Website (Fase 9)

| Método | Ruta | Permiso |
|---|---|---|
| GET | `/auditoria` | `ver auditoria` (historial) |
| POST | `/auditoria` | `crear auditoria` (JSON/form: `url`; ejecuta el análisis en tiempo real) |
| GET | `/auditoria/{id}` | `ver auditoria` (desglose de resultados) |
| GET | `/auditoria/{id}/pdf` | `ver auditoria` (descarga el reporte en PDF) |
| DELETE | `/auditoria/{id}` | `crear auditoria` |

## Instalador

| Método | Ruta | Notas |
|---|---|---|
| GET | `/install` | Bienvenida y verificación de requisitos |
| GET/POST | `/install/base-datos` | Conexión a base de datos |
| GET/POST | `/install/empresa` | Datos de la empresa |
| GET/POST | `/install/administrador` | Creación del superadministrador |
| GET | `/install/instalar` | Ejecuta migraciones, seeders y bloquea el instalador |
| GET | `/install/finalizado` | Confirmación |

Todas las rutas `/install/*` responden **404** automáticamente una vez que el sistema ya fue instalado (existe `storage/app/installed.lock`).

## Colección de pruebas

Se incluye `nodo-catalog-manager.postman_collection.json` en la raíz del proyecto con solicitudes de ejemplo para las rutas principales, listas para importar en Postman o Insomnia (usa autenticación por cookie de sesión: inicia sesión primero desde el navegador o con la solicitud de login incluida, que Postman conservará en su cookie jar).
