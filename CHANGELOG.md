# Changelog — NODO Catalog Manager

## [1.4.0-fase5] — 2026-07-17

Quinta entrega funcional: email marketing (Fase 5 de la hoja de ruta).

### Agregado

- Gestión de contactos: nombre, empresa, teléfono, WhatsApp, correo (único), origen, notas, consentimiento (con fecha) y estado de suscripción — con papelera (borrado suave).
- Listas de contactos para segmentar campañas, con conteo de contactos y filtro directo desde el listado de contactos.
- Importación masiva de contactos desde CSV/XLSX/XLS (reutiliza el lector de hojas de cálculo de la Fase 1), detectando columnas `email`/`name`/`nombre`/`phone`/`telefono` por encabezado, omitiendo filas sin correo válido y registrando el consentimiento de la importación.
- Exportación de contactos a CSV.
- 10 tipos de campaña del brief: newsletter, lanzamiento, promoción, seguimiento, bienvenida, recuperación de prospectos, reactivación, recordatorio, cotización y confirmación.
- Constructor visual de campañas por bloques (encabezado, texto, imagen, botón, productos del catálogo, separador, redes sociales, pie legal), con reordenar/agregar/quitar bloques y persistencia como JSON.
- Envío de prueba a una dirección propia antes de programar el envío masivo.
- Envío en cola respetuoso del proveedor SMTP: comando `email:send-due-campaigns` programado cada minuto vía el scheduler de Laravel, que arma la cola de envíos (solo contactos suscritos y con consentimiento vigente) y despacha lotes configurables (`batch_limit`, 50 por defecto) a través de `SendCampaignEmailJob`.
- SMTP de campañas independiente del correo transaccional del sistema (Configuración → Email Marketing), compatible con SMTP propio, Brevo, Mailgun, SendGrid o Amazon SES, con contraseña cifrada y prueba de conexión real.
- Seguimiento real de aperturas (píxel de 1×1) y clics (enlaces reescritos con redirección), con reporte por campaña: enviados, aperturas, clics, rebotes y bajas.
- Enlace de baja obligatorio en cada correo, con página pública de confirmación (sin necesidad de iniciar sesión) que marca al contacto como no suscrito y actualiza el contador de bajas de la campaña.
- Permisos granulares nuevos: `ver contactos`, `crear contactos`, `editar contactos`, `eliminar contactos`, `importar contactos`, `exportar contactos`, `ver campanas`, `crear campanas`, `editar campanas`, `eliminar campanas`, `enviar campanas`, `configurar campanas`.
- 18 pruebas automatizadas adicionales (89 en total), incluyendo el flujo completo de baja, el píxel de apertura, el clic rastreado, el filtrado por consentimiento/suscripción del comando de envío y el fallo explícito al enviar sin proveedor configurado.

### Corregido

- `CampaignController::sendTest()` y `EmailSettingsController::test()` pasaban un objeto `Stringable` (de `$request->string(...)`) como dirección de correo, lo que rompía la construcción del mensaje en el motor de correo de Laravel; ahora usan `$request->input(...)`.
- El comando `email:send-due-campaigns` solo marcaba una campaña como "enviada" cuando el lote recién despachado ya venía vacío, retrasando innecesariamente la detección de finalización; ahora revisa directamente si quedan envíos pendientes tras despachar el lote.

### Nota importante

El envío de campañas requiere credenciales SMTP reales (propias o de un proveedor como Brevo, Mailgun, SendGrid o Amazon SES) configuradas en Configuración → Email Marketing. Sin ellas, "Enviar prueba" y el envío programado muestran con claridad que falta configurar el proveedor, en vez de simular un envío que no ocurrió.

## [1.3.0-fase4] — 2026-07-17

Cuarta entrega funcional: redes sociales (Fase 4 de la hoja de ruta).

### Agregado

- Gestión de cuentas conectadas por canal (Facebook, Instagram, LinkedIn, TikTok, X, Google Business Profile), con token de acceso cifrado.
- Publicaciones con producto relacionado, contenido, hashtags, enlace, imagen, fecha/hora y zona horaria de programación.
- Calendario editorial mensual (con navegación entre meses) y vista de lista completa, filtrable por canal y por estado.
- Estados claramente diferenciados: borrador, programada, enviando, enviada, pendiente de autorización, con error, publicada manualmente, cancelada — nunca se muestra "enviada" si no se confirmó con la plataforma.
- Duplicar una publicación para otro canal con un clic, para adaptar el texto por red.
- Aprobar, cancelar, reintentar, publicar ahora, marcar como publicada manualmente, descargar la imagen y exportar el calendario editorial en CSV.
- **Publicación automática real** hacia Facebook mediante la Graph API de Meta (`/{page-id}/feed`), incluyendo manejo explícito de token inválido/expirado y errores de la plataforma.
- Comando `social:publish-due`, programado cada minuto vía el scheduler de Laravel, que envía automáticamente las publicaciones programadas cuya fecha ya llegó (requiere el cron de `schedule:run`, ver `INSTALL-HOSTINGER.md`).
- Permisos granulares nuevos: `ver redes`, `crear redes`, `editar redes`, `eliminar redes`, `aprobar redes`, `publicar redes`, `conectar cuentas redes`.
- 11 pruebas automatizadas adicionales (71 en total) con HTTP simulado para el envío real a Facebook y para cada estado posible.

### Nota importante

Instagram, LinkedIn, TikTok, X y Google Business Profile no tienen conector de publicación automática todavía — el sistema lo indica con claridad y permite programar el contenido, descargar la imagen y marcarlo como "publicado manualmente" en vez de simular un envío que no ocurrió.

## [1.2.0-fase3] — 2026-07-17

Tercera entrega funcional: generador de imágenes comerciales (Fase 3 de la hoja de ruta).

### Agregado

- Motor de composición de imágenes propio (GD vía Intervention Image): fondo (degradado de marca, imagen subida, imagen del producto o generado con IA), superposición oscura para legibilidad, logotipo de la empresa, título, subtítulo, insignia de precio, botón de llamada a la acción, código QR y pie de marca — todo renderizado con tipografía real (Inter, licencia OFL, incluida en el proyecto).
- 5 tamaños del brief: cuadrado (1080×1080), vertical (1080×1350), historia (1080×1920), horizontal (1200×628) y portada de colección (1920×1080).
- Plantilla maestra NODO 360 preconfigurada con la identidad visual de marca, más 5 plantillas adicionales listas para usar.
- CRUD de plantillas propias (nombre, formato, colores, posición del título, mostrar precio/QR, pie de marca).
- Generador (`/imagenes/generador`): elige plantilla, producto relacionado (opcional, autocompleta título/subtítulo), textos, y origen del fondo (degradado, imagen subida, imagen del producto, o generado con IA vía DALL·E cuando el proveedor OpenAI está configurado).
- Resultado descargable en PNG, con acciones para usarla como imagen principal del producto o agregarla a su galería.
- Historial de imágenes generadas, filtrable por producto.
- Enlace directo "Generar imagen comercial" desde el formulario de producto.
- Permisos granulares nuevos: `ver imagenes`, `crear imagenes`, `editar imagenes`, `eliminar imagenes`.
- 6 pruebas automatizadas adicionales (60 en total).

### Nota importante

La composición manual (degradado, imagen subida, imagen del producto) funciona sin ninguna credencial externa. Solo la opción "Generar con IA" para el fondo requiere la misma clave de OpenAI configurada en el módulo de IA de la Fase 2 — si no está configurada, esa opción muestra una advertencia clara en vez de simular un resultado.

## [1.1.0-fase2] — 2026-07-17

Segunda entrega funcional: módulo de inteligencia artificial para generación de texto (Fase 2 de la hoja de ruta).

### Agregado

- Configuración de proveedor de IA (Configuración → IA): OpenAI o cualquier proveedor compatible, y Google Gemini, con modelo y URL base configurables.
- Clave de API cifrada en base de datos, nunca mostrada completa, con botón de prueba de conexión real.
- 19 tareas de generación de texto: nombre comercial, descripción corta, descripción completa, beneficios, características, preguntas frecuentes, palabras clave, metadatos SEO, datos estructurados (JSON-LD), publicaciones para redes, asuntos de email, contenido para landing page, mensajes de WhatsApp, prompts de imagen, mejorar texto, cambiar tono, crear variantes, resumir y traducir.
- Botones "Generar con IA" contextuales en el formulario de producto (descripciones, beneficios, características, palabras clave, mensaje de WhatsApp, prompt de imagen) con flujo de revisión: usar, regenerar o cerrar — nunca se guarda sin confirmación del usuario.
- Generador de contenido general (`/ia/generador`) para contenido no ligado a un producto.
- Historial de uso de IA (`/ia/historial`): usuario, fecha, producto, modelo, tokens de entrada/salida y costo aproximado por solicitud.
- Manejo explícito de errores sin inventar respuestas: sin credenciales configuradas, token inválido, cuota excedida, límite de solicitudes, error de red.
- Permisos granulares nuevos: `usar ia`, `ver historial ia`, `configurar ia`, asignados a los roles correspondientes.
- 12 pruebas automatizadas adicionales (54 en total) con HTTP simulado para cada escenario de proveedor.

### Nota importante

Este módulo requiere que NODO 360 proporcione una clave de API real de OpenAI o Google. Hasta entonces, el botón "Generar con IA" permanece deshabilitado en toda la interfaz — no hay ninguna clave de prueba ni respuesta simulada en el código.

## [1.0.0-fase1] — 2026-07-17

Primera entrega funcional del sistema (Fase 1 — Núcleo), acordada con el propietario del proyecto para desarrollar el sistema completo por fases en lugar de intentar entregar los 27 módulos del brief original en una sola pasada.

### Agregado

- Instalador web guiado en 5 pasos, autoblocante tras completarse.
- Autenticación con rate limiting y bloqueo configurable por intentos fallidos.
- Recuperación de contraseña por correo.
- 9 roles y permisos granulares (spatie/laravel-permission).
- Módulo de catálogo: productos y servicios con todos los campos del brief, CRUD completo, duplicar, archivar, papelera/restaurar, vista previa, edición masiva.
- Colecciones y categorías.
- Importación masiva (CSV/XLSX/XLS/JSON) con plantilla descargable, mapeo de columnas, detección de duplicados, procesamiento por lotes en cola y reporte de errores descargable.
- Exportación CSV/JSON filtrable.
- Dashboard ejecutivo con métricas reales, gráficas de crecimiento y actividad reciente.
- Configuración de empresa, marca, moneda, zona horaria, IVA y seguridad de login.
- Registro de actividad (auditoría) en todo el sistema.
- Datos iniciales: 6 colecciones y 39 productos/servicios de NODO 360 en estado borrador.
- Interfaz premium con modo claro/oscuro, sidebar colapsable, estados vacíos diseñados, confirmaciones antes de eliminar.
- 42 pruebas automatizadas (PHPUnit).
- Documentación completa (instalación en Hostinger con y sin SSH, manual de usuario, seguridad, respaldo, base de datos, rutas, solución de problemas).

### Decisiones técnicas relevantes

- Tailwind CSS 4 compilado con el binario standalone (sin Node.js en producción).
- Alpine.js servido localmente (sin CDN externo).
- Sesiones y caché en archivos (`file`), sin depender de tablas de base de datos para arrancar — necesario para que el propio instalador web funcione antes de que existan las tablas.
- Cola de trabajos con driver `database`, procesada por cron con `--stop-when-empty`, compatible con hosting compartido sin workers permanentes.

## Hoja de ruta — fases siguientes (no incluidas todavía)

Cada fase se entregará completa y funcional de extremo a extremo, sin simulaciones:

- ~~**Fase 2 — Inteligencia artificial (texto)**~~ ✅ Entregada — ver arriba.
- ~~**Fase 3 — Generador de imágenes**~~ ✅ Entregada — ver arriba.
- ~~**Fase 4 — Redes sociales**~~ ✅ Entregada — ver arriba.
- **Fase 5 — Email marketing**: contactos, listas, segmentos, constructor visual de correos, campañas, automatizaciones, integración SMTP/Brevo/Mailgun/SES/SendGrid.
- **Fase 6 — Landing pages**: generador de páginas por producto, SEO, Open Graph, analítica, captura de prospectos.
- **Fase 7 — CRM**: pipeline Kanban, prospectos, actividades, conversión desde formularios y landing pages.
- **Fase 8 — Meta Commerce y feeds**: generación de feed de catálogo, integración con la API de Meta.
- **Fase 9 — IA Ready Website**: auditor de SEO/AEO/GEO para sitios web, calificación y reporte descargable.

## Limitaciones conocidas de esta entrega

- No incluye integraciones con APIs externas de terceros (Meta, Google, proveedores de IA, proveedores de email marketing) — ver `CONFIGURACION-APIS.md` para el detalle honesto de qué está listo y qué no.
- El instalador web ejecuta migraciones y siembra de datos de forma síncrona en una sola petición HTTP; en hosting muy limitado esto puede tardar algunos segundos, es normal.
- No se incluye una API REST pública con tokens (Sanctum) en esta fase; las rutas son web autenticadas por sesión (ver `API-DOCUMENTATION.md`).
