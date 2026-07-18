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

## 10. Generador de contenido con IA

Menú lateral → **Inteligencia Artificial → Generador de contenido** (requiere permiso "usar ia").

1. Un administrador debe configurar primero un proveedor en **Inteligencia Artificial → Configuración de IA**: elige OpenAI (o un proveedor compatible) o Google, indica el modelo, la URL base y pega la clave de API. Usa el botón **Probar conexión** para confirmar que funciona. Mientras no haya una clave configurada, el botón "Generar con IA" permanece deshabilitado en todo el sistema — no se simula ningún resultado.
2. Con la IA configurada, verás enlaces **Generar con IA** junto a los campos de descripción, beneficios, características, palabras clave y mensaje de WhatsApp dentro del formulario de producto. Al generar, el texto aparece en un cuadro editable con las opciones **Usar este texto**, **Regenerar** o **Cerrar** — nunca se inserta automáticamente sin tu confirmación.
3. Para contenido que no está ligado a un producto (asuntos de email, publicaciones para redes, contenido de landing page, traducciones, cambios de tono, resúmenes), usa la página general del **Generador de contenido**, elige el tipo de contenido y completa los campos que te pida.
4. Cada generación queda registrada en **Inteligencia Artificial → Historial de uso** (requiere permiso "ver historial ia"): usuario, fecha, producto, modelo, tokens consumidos y costo aproximado.

## 11. Generador de imágenes

Menú lateral → **Imágenes → Generador de imágenes** (requiere permiso "ver/crear imagenes").

1. Elige una **plantilla** (la "Plantilla maestra NODO 360" mantiene la identidad de marca) y, opcionalmente, un **producto relacionado** — esto autocompleta el título y subtítulo.
2. Completa título, subtítulo, texto de precio, llamada a la acción y, si quieres, una URL para el código QR.
3. Elige el **origen del fondo**: degradado de marca, imagen que subas, imagen principal del producto, o generado con inteligencia artificial (esta última opción requiere que un administrador haya configurado y habilitado el proveedor OpenAI en Configuración de IA; mientras tanto se explica claramente en pantalla).
4. Al generar, verás la imagen final con botones para **descargarla en PNG**, **usarla como imagen principal del producto** o **agregarla a su galería**.
5. **Imágenes → Plantillas** te permite crear tus propias plantillas (colores, formato, posición del título, si muestra precio/QR) además de las 6 incluidas. La plantilla maestra no se puede eliminar.
6. **Imágenes → Historial de imágenes** muestra todas las imágenes generadas, filtrables por producto.

## 12. Redes sociales

Menú lateral → **Redes Sociales → Calendario editorial** (requiere permiso "ver redes").

1. En **Redes Sociales → Cuentas conectadas** puedes conectar tus cuentas (Facebook, Instagram, LinkedIn, TikTok, X, Google Business Profile). Guarda el nombre de la cuenta, el ID de página/cuenta y el token de acceso — se guarda cifrado. Sin una cuenta conectada y autorizada, igual puedes preparar y programar contenido; solo no se podrá enviar automáticamente.
2. **+ Nueva publicación**: elige canal, cuenta (opcional), producto relacionado (opcional), contenido, hashtags, enlace, imagen y, si quieres programarla, fecha y hora. Si no defines fecha, queda como borrador.
3. El **calendario editorial** agrupa las publicaciones por día dentro del mes, con filtros por canal y estado, y navegación entre meses. También puedes ver todas en una lista y exportar el calendario a CSV.
4. Acciones disponibles según tus permisos: **duplicar** para otro canal (ajusta el texto después), **aprobar**, **cancelar**, **publicar ahora** (Facebook) o **reintentar** si falló, **marcar como publicada manualmente** (para las redes sin conector automático todavía), y **descargar la imagen**.
5. Las publicaciones programadas para **Facebook** con una cuenta autorizada se envían automáticamente cuando llega su fecha (vía el cron del servidor, cada minuto). Para las demás redes, descarga el contenido y publícalo tú mismo, luego márcalo como "publicada manualmente" para llevar el registro.

## 13. Email marketing

Menú lateral → **Email Marketing → Campañas** (requiere permiso "ver campanas").

1. Antes de enviar, un administrador debe configurar un proveedor SMTP en **Email Marketing → Configuración de email** (requiere permiso "configurar campanas"): SMTP propio o de un proveedor como Brevo, Mailgun, SendGrid o Amazon SES — host, puerto, usuario, contraseña (se guarda cifrada) y remitente. Usa **Enviar prueba** para confirmar que funciona. Sin esta configuración, "Enviar prueba" y el envío programado muestran con claridad el motivo — no se simula ningún envío.
2. En **Email Marketing → Contactos**, agrega contactos manualmente o impórtalos desde CSV/XLSX/XLS (columna `email` obligatoria; `name`/`nombre` y `phone`/`telefono` opcionales). Cada contacto tiene consentimiento y estado de suscripción — solo se les envía si ambos están activos. También puedes exportarlos a CSV.
3. En **Email Marketing → Listas**, agrupa contactos para segmentar tus envíos.
4. **+ Nueva campaña**: elige tipo (10 disponibles: newsletter, lanzamiento, promoción, seguimiento, bienvenida, recuperación, reactivación, recordatorio, cotización, confirmación), asunto, remitente y lista de contactos. Arma el contenido con el **constructor de bloques**: encabezado, texto, imagen, botón, productos del catálogo, separador, redes sociales y pie legal — agrega, reordena y quita bloques libremente.
5. Guarda la campaña y usa **Enviar prueba** (a tu propio correo) antes de programarla. Luego **Programar envío** (fecha y hora) o **Enviar ahora** — el envío real ocurre en lotes vía el cron del servidor (`email:send-due-campaigns`, cada minuto), respetando el tamaño de lote configurado para no saturar al proveedor SMTP. Puedes **pausar** una campaña en curso.
6. El **reporte de campaña** muestra enviados, aperturas, clics, rebotes y bajas, con el detalle de cada envío. Cada correo incluye un enlace de baja obligatorio: al hacer clic, el destinatario ve una página de confirmación pública (sin iniciar sesión) y, al confirmar, queda excluido de futuros envíos.

## 14. Landing Pages

Menú lateral → **Landing Pages** (requiere permiso "ver landing").

1. **+ Nueva landing page**: dale un nombre interno, vincula opcionalmente un producto del catálogo (autocompleta la sección "Producto destacado"), y define el titular y subtitular que aparecerán en el hero. Puedes subir una imagen principal y, opcionalmente, una imagen distinta para compartir en redes (Open Graph).
2. Configura la **llamada a la acción**: texto del botón, y un enlace externo y/o número de WhatsApp con mensaje predefinido — si defines WhatsApp, el botón abre un chat directo.
3. Arma el **contenido** con el constructor de secciones: problema, solución, beneficios, características, testimonios, preguntas frecuentes, producto destacado, texto libre, imagen, video (YouTube/Vimeo) y llamada a la acción — agrega, reordena y quita secciones libremente. Las secciones de tipo lista (problema, solución, beneficios, características, testimonios, FAQ) permiten agregar tantos elementos como necesites.
4. En **SEO y datos estructurados**, define el título y descripción que aparecerán en buscadores y al compartir en redes. El sistema genera automáticamente los datos estructurados (JSON-LD) de tipo Organización, Página, Producto (si hay uno vinculado) y Preguntas Frecuentes (si agregaste esa sección) — pensados tanto para buscadores tradicionales como para motores de respuesta por IA.
5. En **Analítica**, agrega los IDs de Google Analytics 4, Meta Pixel y/o Google Tag Manager si quieres medir el tráfico y las conversiones — son gratuitos y los obtienes directamente de tu cuenta de Google/Meta. Si los dejas vacíos, no se carga ningún script de seguimiento.
6. En **Captura de prospectos**, decide si la landing muestra un formulario de contacto y, si quieres, elige una lista de contactos de email marketing (Fase 5): cada prospecto que complete el formulario se agregará también como contacto con consentimiento registrado.
7. Al guardar, la landing queda en **Borrador**. Usa **Publicar** para ponerla en línea con su propia URL (`tudominio.com/lp/tu-slug`) — el sistema exige que tenga al menos un titular y una sección de contenido antes de permitir publicarla. Puedes **despublicar** en cualquier momento para quitarla de circulación sin perder su contenido.
8. **Ver prospectos** muestra el reporte de esa landing: vistas, prospectos capturados, tasa de conversión y el detalle de cada prospecto (incluyendo el origen UTM si llegó desde una campaña). **Descargar QR** genera un código QR listo para imprimir que apunta a la URL pública. **Duplicar** crea una copia en borrador para reutilizarla en otra campaña o producto.

## 15. Mi perfil

Menú del avatar (esquina superior derecha) → **Mi perfil**: actualiza tu nombre, correo, teléfono y contraseña.

## 16. Modo claro/oscuro

Icono de sol/luna en la barra superior — tu preferencia se recuerda en el navegador.

## 17. Módulos en fases siguientes

Los siguientes módulos descritos en el proyecto original **no están incluidos todavía** y se entregarán en fases posteriores, cada uno funcional de extremo a extremo: CRM, feed de Meta Commerce, auditor IA-Ready. La generación de **texto** con IA (sección 10), el **generador de imágenes** (sección 11), **redes sociales** (sección 12), **email marketing** (sección 13) y **landing pages** (sección 14) ya están disponibles. Ver `CHANGELOG.md` para la hoja de ruta.
