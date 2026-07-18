# Configuración de APIs e Integraciones

Este documento explica, con honestidad, el estado de cada integración externa en NODO Catalog Manager (Fase 1 a Fase 6). Ninguna credencial ni token ha sido inventado: donde una integración depende de una API externa que aún no se ha construido, se indica explícitamente.

## Correo (SMTP) — ✅ Listo para configurar

El sistema usa el sistema de correo nativo de Laravel, configurado por variables de entorno en `.env`. Requerido para recuperación de contraseña.

```
MAIL_MAILER=smtp
MAIL_HOST=smtp.tu-proveedor.com
MAIL_PORT=587
MAIL_USERNAME=tu_usuario
MAIL_PASSWORD=tu_password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS="info@nodo360mkt.site"
MAIL_FROM_NAME="NODO Catalog Manager"
```

Compatible con cualquier proveedor SMTP estándar (Hostinger Email, Brevo, Mailgun, SendGrid, Amazon SES vía SMTP, Gmail con contraseña de aplicación, etc.). **Pendiente de que NODO 360 proporcione las credenciales reales del proveedor que decida usar.**

## Base de datos (MySQL) — ✅ Listo

Configurado por el instalador web o manualmente en `.env` (`DB_HOST`, `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD`). Sin dependencias externas.

## Almacenamiento de archivos — ✅ Listo (local)

Las imágenes de productos y de marca se guardan en `storage/app/public` y se sirven mediante el enlace simbólico `public/storage` (creado por `php artisan storage:link` o automáticamente por el instalador web). No requiere credenciales de terceros.

Si en el futuro se requiere almacenamiento en la nube (Amazon S3, etc.), Laravel lo soporta de forma nativa cambiando `FILESYSTEM_DISK` — **no incluido por defecto en esta fase** porque no se solicitaron credenciales de un proveedor de almacenamiento externo.

## Inteligencia artificial — generación de texto — ✅ Listo para configurar (Fase 2)

El módulo de generación de contenido con IA (Configuración → IA, y Generador de contenido en el menú lateral) ya está construido y probado con HTTP simulado — solo falta que NODO 360 proporcione una clave de API real de un proveedor.

Proveedores soportados:

- **OpenAI, o cualquier proveedor "compatible con OpenAI"** (Azure OpenAI, Groq, Together AI, servidores de modelos locales, etc.) — solo cambia la URL base en Configuración → IA.
- **Google (Gemini)** — adaptador independiente para la API de `generateContent`.

```
# Configúralo desde el panel: Configuración → IA. No se hace por .env porque
# la clave se guarda cifrada en la base de datos (tabla settings, columna
# is_encrypted), nunca en texto plano ni en archivos versionados.
```

Qué SÍ está implementado y probado:

- Configuración de proveedor, modelo y URL base desde el panel.
- Clave de API cifrada, mostrada solo parcialmente (últimos 4 caracteres).
- Botón "Probar conexión" que hace una llamada real de verificación.
- 19 tareas de generación de texto (nombre comercial, descripciones, beneficios, características, FAQs, palabras clave, metadatos SEO, datos estructurados, publicaciones para redes, asuntos de email, contenido de landing page, mensajes de WhatsApp, prompts de imagen, mejorar texto, cambiar tono, crear variantes, resumir, traducir).
- Botones "Generar con IA" contextuales en el formulario de producto, y un generador general (`/ia/generador`) para contenido no ligado a un producto.
- Flujo de revisión: el contenido generado se muestra en un cuadro editable con opciones de usar, regenerar, aprobar o rechazar — **nunca se guarda automáticamente**.
- Registro de cada solicitud (`/ia/historial`): usuario, fecha, producto, modelo, tokens de entrada/salida y costo aproximado.
- Manejo explícito de errores: sin credenciales, token inválido/revocado, cuota excedida, límite de solicitudes, error de red — cada uno con un mensaje claro, sin inventar una respuesta.

Qué falta (depende de NODO 360): la clave de API real. Mientras no se configure, el botón "Generar con IA" permanece deshabilitado en toda la interfaz — no se simula ninguna respuesta.

## Meta — Facebook (redes sociales) — ✅ Listo para conectar (Fase 4)

La publicación automática hacia el **feed de una página de Facebook** ya está implementada usando la Graph API real de Meta (`POST /{page-id}/feed`), en `Redes Sociales → Cuentas conectadas`.

Para activarla necesitas, de tu cuenta de Meta for Developers:

1. Una app de Meta con el producto "Facebook Login for Business" o acceso a la Graph API.
2. Un **token de acceso de página** (Page Access Token) con el permiso `pages_manage_posts` — de preferencia de larga duración.
3. El **ID de la página** de Facebook que quieres administrar.

Con esos tres datos, agrega la cuenta en el sistema (el token se guarda cifrado). El sistema distingue con claridad: sin credenciales, token expirado/revocado (error 401/190 de la Graph API), y errores devueltos por la plataforma — nunca se muestra "publicado" si la API no lo confirmó.

**No se ha usado ningún token de prueba ni credencial simulada** — las pruebas automatizadas usan HTTP simulado (`Http::fake()`), no una cuenta real.

## Meta — Instagram, WhatsApp Business, Meta Commerce — ⏳ Pendiente (fases futuras)

**No implementado todavía.** Instagram (requiere creación de contenedor de medios antes de publicar, distinto al feed de Facebook), el Agente IA para WhatsApp vía API oficial, y el feed de Meta Commerce se construirán en fases posteriores. Mientras tanto, las publicaciones para Instagram se pueden preparar, programar y descargar desde Redes Sociales, marcándolas como "publicada manualmente" tras publicarlas tú mismo.

## Google Analytics 4, Meta Pixel y Google Tag Manager — ✅ Listo (Fase 6)

Cada landing page permite pegar su propio ID de **Google Analytics 4** (medición), **Meta Pixel** y **Google Tag Manager** (`Landing Pages → editar → Analítica`). Son servicios gratuitos: no requieren clave de API ni credenciales de NODO 360 más allá de crear la cuenta correspondiente en Google/Meta y copiar el ID. El sistema inyecta el script oficial de cada proveedor en la página pública **solo si el campo tiene un valor** — sin IDs configurados, la landing no carga ningún script de seguimiento de terceros.

## Google Ads — ⏳ Pendiente (fase futura)

No implementado. La medición de conversiones de Google Ads requiere una cuenta publicitaria activa y un ID de conversión específico; se integrará cuando NODO 360 defina qué campañas de Ads va a correr.

## Generador de imágenes — ✅ Listo (Fase 3)

El generador de imágenes (`Imágenes → Generador de imágenes`) compone imágenes comerciales completas (fondo, logotipo, título, subtítulo, precio, CTA, QR, pie de marca) sin depender de ninguna API externa — el motor de composición usa GD (Intervention Image) que ya viene con PHP en Hostinger.

La **única parte opcional** que requiere una API externa es generar el **fondo** de la imagen con inteligencia artificial (en vez de subir una imagen o usar un degradado). Esa opción:

- Reutiliza la misma clave de API configurada en Configuración → IA.
- Solo funciona con el proveedor **OpenAI** (endpoint de generación de imágenes DALL·E). Si el proveedor configurado es Google, la interfaz lo indica claramente y sugiere subir una imagen manualmente en su lugar — no se simula ninguna imagen.
- Si no hay ninguna clave configurada, la opción "Generar con IA" muestra una advertencia explicando cómo activarla, sin bloquear las demás opciones de fondo (degradado, subir imagen, imagen del producto), que funcionan siempre.

## Proveedores de email marketing (Brevo, Mailgun, SendGrid, Amazon SES) — ✅ Listo para configurar (Fase 5)

El módulo completo de campañas masivas (`Email Marketing → Configuración de email`) ya está construido y probado: contactos con consentimiento, listas, constructor visual de bloques, envío en cola con control de límites por lote, y seguimiento de aperturas/clics/bajas. Es **independiente** del SMTP transaccional de `.env` (recuperación de contraseña) — usa un mailer dedicado (`campaign_smtp`) configurado en tiempo de ejecución desde la base de datos, para no interferir con el correo del sistema.

Proveedores soportados (todos vía su interfaz SMTP estándar, sin dependencias extra por proveedor):

- **SMTP propio** de NODO 360 o del hosting.
- **Brevo (Sendinblue)**, **Mailgun**, **SendGrid**, **Amazon SES** — cualquiera que exponga host/puerto/usuario/contraseña SMTP.

```
# Configúralo desde el panel: Email Marketing → Configuración de email. No se
# hace por .env porque la contraseña se guarda cifrada en la base de datos
# (tabla settings, columna is_encrypted), nunca en texto plano.
```

Qué SÍ está implementado y probado:

- Configuración de proveedor, host, puerto, usuario, cifrado (TLS/SSL) y remitente desde el panel.
- Contraseña/clave de API cifrada, nunca mostrada completa.
- Botón "Enviar prueba" que envía un correo real de verificación a una dirección que tú indiques.
- Envío de campañas en lotes controlados (`batch_limit`, configurable por campaña) vía el comando `email:send-due-campaigns`, programado cada minuto por el cron del servidor — nunca se satura al proveedor SMTP con miles de envíos simultáneos.
- Envío únicamente a contactos con **consentimiento vigente** y **estado suscrito** — se excluyen automáticamente los dados de baja.
- Seguimiento real de aperturas (píxel) y clics (enlaces rastreados), con reporte por campaña.
- Enlace de baja obligatorio en cada correo, con página pública de confirmación.

Qué falta (depende de NODO 360): las credenciales SMTP reales del proveedor elegido. Mientras no se configuren, "Enviar prueba" y el envío programado muestran con claridad el motivo — no se simula ningún envío.

## Resumen

| Integración | Estado | Requiere credenciales de NODO 360 |
|---|---|---|
| Base de datos MySQL | ✅ Listo | Sí (ya solicitadas en el instalador) |
| SMTP / correo | ✅ Listo para configurar | Sí (proveedor a elegir) |
| Almacenamiento local | ✅ Listo | No |
| IA generativa — texto | ✅ Listo para configurar (Fase 2) | Sí, clave de API de OpenAI o Google |
| Meta — Facebook (publicación automática) | ✅ Listo para conectar (Fase 4) | Sí, token de página de Meta |
| Meta — Instagram/WhatsApp/Commerce | ⏳ Fase futura | Sí, cuando se construya |
| LinkedIn / TikTok / X / Google Business (publicación automática) | ⏳ Fase futura | Sí, cuando se construya |
| Google Ads | ⏳ Fase futura | Sí, cuando se construya |
| Generador de imágenes (composición) | ✅ Listo (Fase 3) | No |
| IA generativa — fondo de imagen (opcional) | ✅ Listo para configurar (Fase 3) | Sí, misma clave de OpenAI |
| Email marketing (SMTP/Brevo/Mailgun/SES/SendGrid) | ✅ Listo para configurar (Fase 5) | Sí, credenciales SMTP del proveedor elegido |
| Landing pages (constructor y publicación) | ✅ Listo (Fase 6) | No |
| Google Analytics 4 / Meta Pixel / Google Tag Manager (por landing) | ✅ Listo para configurar (Fase 6) | Sí, IDs gratuitos de tus cuentas de Google/Meta |
