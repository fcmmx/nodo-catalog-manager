# Changelog — NODO Catalog Manager

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
- **Fase 3 — Generador de imágenes**: plantillas gráficas, conexión a proveedores de generación de imágenes, composición con elementos propios cuando no haya API configurada.
- **Fase 4 — Redes sociales**: calendario editorial, preparación y programación de publicaciones, estados de publicación, integración con Meta cuando existan credenciales.
- **Fase 5 — Email marketing**: contactos, listas, segmentos, constructor visual de correos, campañas, automatizaciones, integración SMTP/Brevo/Mailgun/SES/SendGrid.
- **Fase 6 — Landing pages**: generador de páginas por producto, SEO, Open Graph, analítica, captura de prospectos.
- **Fase 7 — CRM**: pipeline Kanban, prospectos, actividades, conversión desde formularios y landing pages.
- **Fase 8 — Meta Commerce y feeds**: generación de feed de catálogo, integración con la API de Meta.
- **Fase 9 — IA Ready Website**: auditor de SEO/AEO/GEO para sitios web, calificación y reporte descargable.

## Limitaciones conocidas de esta entrega

- No incluye integraciones con APIs externas de terceros (Meta, Google, proveedores de IA, proveedores de email marketing) — ver `CONFIGURACION-APIS.md` para el detalle honesto de qué está listo y qué no.
- El instalador web ejecuta migraciones y siembra de datos de forma síncrona en una sola petición HTTP; en hosting muy limitado esto puede tardar algunos segundos, es normal.
- No se incluye una API REST pública con tokens (Sanctum) en esta fase; las rutas son web autenticadas por sesión (ver `API-DOCUMENTATION.md`).
