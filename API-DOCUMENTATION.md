# Documentación de Rutas — NODO Catalog Manager (Fase 1)

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
