# Changelog — NODO Catalog Manager

## [2.0.0-fase9] — 2026-07-17

Novena y última entrega funcional del roadmap original: auditor SEO/AEO/GEO — "IA Ready Website" (Fase 9). Con esta fase se completan las 9 fases planeadas para NODO Catalog Manager.

### Agregado

- Auditor de sitios web (`Auditoría → Auditor SEO/AEO/GEO`): analiza cualquier URL externa en tiempo real (descarga su HTML público, `robots.txt`, `sitemap.xml` y `llms.txt`) y genera una calificación de **0 a 100** con desglose en tres categorías:
  - **SEO tradicional** (40 pts): HTTPS, título, meta descripción, un solo H1, enlace canónico, viewport móvil, texto alternativo en imágenes, no bloqueada por `noindex`, sitemap accesible.
  - **AEO — optimización para motores de respuesta por IA** (30 pts): datos estructurados JSON-LD, esquema FAQPage/QAPage, jerarquía de encabezados lógica, encabezados formulados como pregunta, etiquetas Open Graph completas.
  - **GEO — optimización para motores generativos** (30 pts): `robots.txt` no bloquea rastreadores de IA (GPTBot, ClaudeBot, Google-Extended, PerplexityBot), archivo `llms.txt`, estructura HTML5 semántica, datos estructurados de identidad (Organization/WebSite/Person), contenido textual suficiente.
- Cada señal se calcula sobre el contenido real descargado — nada se simula ni se supone; si la URL no responde, la auditoría queda marcada como error con el motivo exacto.
- Reporte de resultados con calificación general, desglose por categoría y el detalle (aprobado/no aprobado, puntos, explicación) de cada una de las 19 señales evaluadas.
- **Reporte descargable en PDF**, generado con `dompdf/dompdf` (sin dependencias externas ni binarios), listo para compartir con un cliente.
- Historial de auditorías anteriores, con calificación y fecha de cada una.
- Permisos granulares nuevos: `ver auditoria`, `crear auditoria`.
- 9 pruebas automatizadas adicionales (139 en total), incluyendo la comparación de un sitio bien optimizado contra uno deficiente, el manejo de URLs inalcanzables, el bloqueo de rastreadores de IA en `robots.txt` y la generación real del PDF.

### Nota importante

El auditor no requiere ninguna credencial de terceros — analiza directamente el HTML y los archivos públicos de la URL que se le indique, igual que lo haría un navegador o un rastreador. No inventa calificaciones: cada punto corresponde a una señal verificada realmente en el sitio analizado.

## [1.7.0-fase8] — 2026-07-17

Octava entrega funcional: Meta Commerce y feeds de catálogo (Fase 8 de la hoja de ruta).

### Agregado

- Feed de catálogo público en formato **CSV** y **XML** (RSS 2.0 / Google Shopping, compatible con Meta Commerce Catalog), protegido por un token propio de alta entropía en la URL — listo para registrarse como "fuente de datos programada" en Meta Commerce Manager.
- El feed incluye únicamente productos **publicados** (estado activo) con precio y enlace público configurados — los campos obligatorios de cualquier catálogo real; un producto sin ellos se omite en vez de inventar un valor.
- Mapeo de disponibilidad del catálogo interno al vocabulario estándar de feeds (`in stock`, `out of stock`, `available for order`, `preorder`).
- Configuración de la conexión con Meta Commerce Manager (`Meta Commerce → Feed de catálogo`): ID de catálogo, ID de cuenta de negocio (opcional) y token de acceso cifrado, con botón **Probar conexión** que hace una llamada real de solo lectura a la Graph API de Meta para confirmar las credenciales — sin publicar ni modificar nada.
- **Regenerar enlace del feed**: invalida el token anterior y genera uno nuevo, para revocar el acceso si la URL se filtró.
- Historial de sincronización: cada solicitud del feed (por Meta o cualquier otra plataforma) y cada prueba de conexión quedan registradas con fecha, estado, cantidad de productos e IP de origen.
- Permisos granulares nuevos: `ver comercio`, `configurar comercio`.
- 12 pruebas automatizadas adicionales (130 en total), incluyendo la generación del feed, el filtrado de productos elegibles, la protección por token, la prueba de conexión con HTTP simulado (éxito y token inválido) y la regeneración del enlace.

### Nota importante

El feed público es la forma estándar en que Meta Commerce Manager sincroniza catálogos (se programa una vez en Meta para que lea la URL periódicamente) — funciona sin ninguna credencial de Meta. Conectar el catálogo (ID + token) solo habilita la prueba de conexión real contra la Graph API; sin esas credenciales, el sistema lo indica con claridad en vez de simular una conexión exitosa.

## [1.6.0-fase7] — 2026-07-17

Séptima entrega funcional: CRM (Fase 7 de la hoja de ruta).

### Agregado

- Pipeline de ventas en tablero Kanban (`/crm`), con columnas por etapa configurable y tarjetas de prospecto que se arrastran entre columnas (drag & drop nativo) — el cambio de etapa se guarda de inmediato vía una llamada AJAX, sin recargar la página.
- Etapas del pipeline totalmente configurables (`/crm/etapas`): nombre, color, orden, y marcado de "ganada" o "perdida" — 7 etapas iniciales incluidas (Nuevo, Contactado, Calificado, Propuesta enviada, Negociación, Ganado, Perdido). Una etapa con prospectos asignados no se puede eliminar, para no perder información.
- Cada prospecto (oportunidad) se liga a un contacto de email marketing (Fase 5) y, opcionalmente, a un producto del catálogo y a un responsable asignado.
- Marcar una oportunidad como **ganada** o **perdida** (con motivo de pérdida opcional) la mueve automáticamente a la etapa correspondiente y actualiza su estado.
- Actividades por prospecto: notas, llamadas, reuniones, tareas/recordatorios (con fecha límite) y registro de contacto por WhatsApp — con indicador visual de recordatorios pendientes y vencidos, y marcar como completado.
- Enlace directo de **WhatsApp** (`wa.me`) en la ficha de cada prospecto, generado a partir del teléfono o WhatsApp del contacto — sin necesidad de la API oficial de WhatsApp Business.
- Conversión con un clic de un prospecto capturado en una landing page (Fase 6) a una oportunidad del CRM: crea (o reutiliza) el contacto correspondiente y enlaza el origen, evitando duplicados si el mismo prospecto se convierte más de una vez.
- Permisos granulares nuevos: `ver crm`, `crear crm`, `editar crm`, `eliminar crm`, `asignar crm`.
- 15 pruebas automatizadas adicionales (118 en total), incluyendo el cambio de etapa, marcar ganado/perdido, actividades y recordatorios, conversión de prospectos de landing y el enlace de WhatsApp.

## [1.5.0-fase6] — 2026-07-17

Sexta entrega funcional: generador de landing pages (Fase 6 de la hoja de ruta).

### Agregado

- Constructor de landing pages por secciones: problema, solución, beneficios, características, testimonios, preguntas frecuentes, producto destacado, texto libre, imagen, video y llamada a la acción — con reordenar/agregar/quitar secciones y persistencia como JSON.
- Cada landing page puede vincularse opcionalmente a un producto del catálogo (autocompleta su información en la sección "Producto destacado").
- Publicación con slug propio y URL pública dedicada (`/lp/{slug}`), independiente del panel administrativo — sin sesión ni permisos.
- Validación antes de publicar: exige titular y al menos una sección de contenido, para evitar landing pages vacías en producción.
- SEO completo por landing: título y descripción meta, imagen para Open Graph/redes sociales, URL canónica, y datos estructurados JSON-LD generados automáticamente (Organization, WebPage, Product cuando hay un producto vinculado, y FAQPage cuando hay una sección de preguntas frecuentes) — pensado para posicionamiento en buscadores tradicionales y motores de respuesta por IA (AEO/GEO).
- Analítica opcional por landing: Google Analytics 4, Meta Pixel y Google Tag Manager, cada uno inyectado en la página pública únicamente si se configura su ID — sin scripts de seguimiento simulados ni cargados por defecto.
- Formulario de captura de prospectos configurable (se puede desactivar por landing), con teléfono y mensaje opcionales, y seguimiento de UTM (source/medium/campaign) para atribución de campañas.
- Integración con email marketing (Fase 5): si la landing tiene una lista de contactos asignada, cada prospecto se crea también como contacto con consentimiento registrado y queda agregado a esa lista; si no, el prospecto se guarda igual pero sin crear un contacto.
- Reporte de prospectos por landing page con contador de vistas, prospectos y tasa de conversión.
- Código QR descargable de cada landing page, generado con el mismo motor de la Fase 3 (endroid/qr-code).
- Duplicar una landing page con un clic para reutilizarla en otra campaña o producto.
- Permisos granulares nuevos: `ver landing`, `crear landing`, `editar landing`, `eliminar landing`, `publicar landing`.
- 14 pruebas automatizadas adicionales (103 en total), incluyendo el flujo completo de publicación, renderizado público, captura de prospectos con y sin lista, y descarga del código QR.

### Nota importante

Google Analytics 4, Meta Pixel y Google Tag Manager son gratuitos y no requieren credenciales de NODO 360 más allá de crear las cuentas correspondientes (Google/Meta) y pegar el ID de medición en cada landing page — el sistema no simula ningún dato de analítica, solo carga el script oficial del proveedor cuando se configura.

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
