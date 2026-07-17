# Manual de Usuario — NODO Catalog Manager

## 1. Acceso al sistema

Entra a la URL de tu instalación (por ejemplo `https://catalogo.nodo360mkt.site`) e inicia sesión con tu correo y contraseña.

**Usuario administrador inicial** (creado durante la instalación):

- Si instalaste con el **asistente web** (`/install`): el correo y contraseña que capturaste tú mismo en el paso "Crea tu cuenta de superadministrador".
- Si instalaste **por consola** con `php artisan db:seed`: el correo/contraseña definidos en `NODO_ADMIN_EMAIL` / `NODO_ADMIN_PASSWORD` de tu `.env`, o por defecto `admin@nodo360mkt.site` / `Nodo360#Admin2026` si no los configuraste — **cámbiala de inmediato** desde "Mi perfil".

Si olvidaste tu contraseña, usa el enlace **¿Olvidaste tu contraseña?** en la pantalla de login (requiere que el correo SMTP esté configurado, ver `CONFIGURACION-APIS.md`).

## 2. Panel principal (Dashboard)

Al iniciar sesión verás:

- **Tarjetas de métricas**: productos totales, activos, borradores, sin imagen, colecciones, destacados. Cada tarjeta es un enlace directo al listado filtrado correspondiente.
- **Gráfica de crecimiento del catálogo**: productos creados por mes, últimos 6 meses.
- **Productos por colección**: distribución del catálogo.
- **Actividad reciente**: últimas acciones del sistema.
- **Últimos productos**: accesos rápidos a los productos más recientes.

## 3. Catálogo de productos y servicios

Menú lateral → **Productos y servicios**.

### 3.1 Crear un producto

Botón **+ Nuevo producto**. Completa las secciones:

- **Información general**: SKU (único), nombre, tipo (producto/servicio), colección, categoría.
- **Contenido**: descripción corta, descripción completa, beneficios y características (una idea por línea).
- **Precio**: precio, precio anterior, moneda, modalidad de cobro, texto "Desde", si el precio incluye IVA.
- **Enlaces**: URL, demo, video, WhatsApp (con mensaje predeterminado).
- **SEO**: meta título, meta descripción, palabras clave, texto SEO adicional.
- **Publicación**: estado (borrador/activo/inactivo/archivado), disponibilidad, fecha de publicación, orden, destacado, etiquetas.
- **Imágenes**: imagen principal y galería.

Guarda con el botón **Guardar**. El producto queda disponible de inmediato para edición; para que sea visible como "activo" cambia su **Estado**.

### 3.2 Acciones sobre un producto

Desde el listado, cada fila tiene:

- **Vista previa**: abre una página de previsualización (no indexable, marcada como "no publicado") con el diseño final del producto.
- **Editar**.
- **Duplicar**: crea una copia en borrador con el sufijo "(copia)".
- **Eliminar**: envía el producto a la papelera (eliminación lógica, no se pierde la información).
- **Restaurar**: recupera un producto de la papelera (activa el filtro "Papelera" para verlos).

### 3.3 Edición masiva

Selecciona varios productos con las casillas de la izquierda. Aparecerá una barra con las acciones disponibles:

- Cambiar estado, precio, categoría, colección, disponibilidad, marcar como destacado, reemplazar etiquetas, o eliminar en lote.

### 3.4 Filtros y búsqueda

Usa el buscador (por nombre o SKU) y los filtros de estado, colección y categoría. Activa la casilla **Papelera** para ver productos eliminados.

## 4. Colecciones y categorías

Menú lateral → **Colecciones** / **Categorías**. Ambas permiten crear, editar, activar/desactivar y eliminar (solo si no tienen productos asociados). Las categorías pueden asociarse opcionalmente a una colección.

## 5. Importación masiva

Menú lateral → **Importar / Exportar**.

1. Descarga la **plantilla CSV** para conocer las columnas esperadas.
2. Sube tu archivo (CSV, XLSX, XLS o JSON) y elige qué hacer si un SKU ya existe: **Omitir** o **Actualizar**.
3. En la pantalla de mapeo, relaciona cada columna de tu archivo con el campo correspondiente del sistema (el sistema intenta adivinar la relación automáticamente si los nombres coinciden).
4. Al iniciar la importación, el sistema procesa los registros **en lotes**, en segundo plano. La pantalla de detalle se actualiza automáticamente cada pocos segundos mostrando el progreso.
5. Al finalizar verás cuántos registros se importaron correctamente y cuántos tuvieron error, con un botón para **descargar el reporte de errores** (fila y motivo).

Las categorías y colecciones mencionadas en el archivo que no existan se crean automáticamente.

## 6. Exportación

Desde el listado de productos, botones **Exportar CSV** / **Exportar JSON** — respetan los filtros de búsqueda activos en ese momento.

## 7. Usuarios y roles

Menú lateral → **Usuarios y roles** (requiere permiso de administración de usuarios).

- **Roles disponibles**: Superadministrador, Administrador, Marketing, Diseñador, Ventas, Editor, Analista, Cliente, Solo lectura — cada uno con permisos distintos sobre ver/crear/editar/eliminar/publicar/exportar/importar.
- Crea un usuario, asígnale uno o varios roles, y actívalo o desactívalo según necesites.
- Un usuario no puede eliminar su propia cuenta.

## 8. Actividad del sistema

Menú lateral → **Actividad del sistema**. Registro de inicios de sesión, creaciones, ediciones, eliminaciones, importaciones, exportaciones y cambios de configuración, con usuario, fecha y descripción. Filtrable por categoría.

## 9. Configuración

Menú lateral → **Configuración** (requiere permiso de administración).

- **Datos de la empresa**: nombre, nombre visible del sistema, correo, sitio web, teléfono, WhatsApp, dirección.
- **Regional y moneda**: moneda (MXN/USD), zona horaria, porcentaje de IVA. El sistema muestra los precios en formato `$12,345.67 MXN`.
- **Marca**: logotipo, favicon, colores primario y de acento, texto principal y llamada a la acción.
- **Seguridad de inicio de sesión**: número de intentos fallidos antes de bloquear una cuenta y minutos de bloqueo.

## 10. Mi perfil

Menú del avatar (esquina superior derecha) → **Mi perfil**: actualiza tu nombre, correo, teléfono y contraseña.

## 11. Modo claro/oscuro

Icono de sol/luna en la barra superior — tu preferencia se recuerda en el navegador.

## 12. Módulos en fases siguientes

Los siguientes módulos descritos en el proyecto original **no están incluidos en esta fase** y se entregarán en fases posteriores, cada uno funcional de extremo a extremo: generación de imágenes con IA, generación de texto con IA, redes sociales, email marketing, landing pages, CRM, feed de Meta Commerce, auditor IA-Ready. Ver `CHANGELOG.md` para la hoja de ruta.
