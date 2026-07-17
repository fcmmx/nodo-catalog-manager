# Configuración de APIs e Integraciones

Este documento explica, con honestidad, el estado de cada integración externa en NODO Catalog Manager (Fase 1 + Fase 2). Ninguna credencial ni token ha sido inventado: donde una integración depende de una API externa que aún no se ha construido, se indica explícitamente.

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

## Meta (Facebook/Instagram/WhatsApp) — ⏳ Pendiente (fase futura)

**No implementado en esta fase.** El módulo de feed de Meta Commerce, publicación en redes sociales y el Agente IA para WhatsApp mediante la API oficial de Meta se construirán en una fase posterior, e incluirán:

- Formulario de configuración de credenciales (App ID, App Secret, tokens de página, número de WhatsApp Business) almacenadas cifradas en la tabla `settings` (mecanismo ya implementado y reutilizable: ver `App\Models\Setting::set()` con el parámetro `encrypted: true`).
- Servicio adaptador desacoplado para las llamadas a la Graph API.
- Manejo de errores explícito para: credenciales ausentes, cuenta no autorizada, token expirado, permisos insuficientes.
- Documentación de cada permiso de Meta requerido.

**No se ha usado ningún token de prueba ni credencial simulada.**

## Google (Ads, Analytics, Tag Manager) — ⏳ Pendiente (fase futura)

No implementado en esta fase. Se integrará como parte del módulo de Growth Marketing y del generador de landing pages en una fase posterior.

## Generador de imágenes — ✅ Listo (Fase 3)

El generador de imágenes (`Imágenes → Generador de imágenes`) compone imágenes comerciales completas (fondo, logotipo, título, subtítulo, precio, CTA, QR, pie de marca) sin depender de ninguna API externa — el motor de composición usa GD (Intervention Image) que ya viene con PHP en Hostinger.

La **única parte opcional** que requiere una API externa es generar el **fondo** de la imagen con inteligencia artificial (en vez de subir una imagen o usar un degradado). Esa opción:

- Reutiliza la misma clave de API configurada en Configuración → IA.
- Solo funciona con el proveedor **OpenAI** (endpoint de generación de imágenes DALL·E). Si el proveedor configurado es Google, la interfaz lo indica claramente y sugiere subir una imagen manualmente en su lugar — no se simula ninguna imagen.
- Si no hay ninguna clave configurada, la opción "Generar con IA" muestra una advertencia explicando cómo activarla, sin bloquear las demás opciones de fondo (degradado, subir imagen, imagen del producto), que funcionan siempre.

## Proveedores de email marketing (Brevo, Mailgun, SendGrid, Amazon SES) — ⏳ Pendiente (fase futura)

El envío transaccional básico (recuperación de contraseña) ya funciona vía SMTP estándar (ver arriba). El módulo completo de campañas masivas, plantillas visuales, listas y segmentos se construirá en una fase posterior, con cola de envío, control de límites y reintentos para evitar el uso directo de `mail()` de PHP.

## Resumen

| Integración | Estado | Requiere credenciales de NODO 360 |
|---|---|---|
| Base de datos MySQL | ✅ Listo | Sí (ya solicitadas en el instalador) |
| SMTP / correo | ✅ Listo para configurar | Sí (proveedor a elegir) |
| Almacenamiento local | ✅ Listo | No |
| IA generativa — texto | ✅ Listo para configurar (Fase 2) | Sí, clave de API de OpenAI o Google |
| Meta (Facebook/Instagram/WhatsApp) | ⏳ Fase futura | Sí, cuando se construya |
| Google (Ads/Analytics/GTM) | ⏳ Fase futura | Sí, cuando se construya |
| Generador de imágenes (composición) | ✅ Listo (Fase 3) | No |
| IA generativa — fondo de imagen (opcional) | ✅ Listo para configurar (Fase 3) | Sí, misma clave de OpenAI |
| Email marketing (Brevo/Mailgun/SES/SendGrid) | ⏳ Fase futura | Sí, cuando se construya |
