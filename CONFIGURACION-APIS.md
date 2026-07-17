# Configuración de APIs e Integraciones

Este documento explica, con honestidad, el estado de cada integración externa en la **Fase 1** de NODO Catalog Manager. Ninguna credencial ni token ha sido inventado: donde una integración depende de una API externa que aún no se ha construido en esta fase, se indica explícitamente.

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

## Meta (Facebook/Instagram/WhatsApp) — ⏳ Pendiente (fase futura)

**No implementado en esta fase.** El módulo de feed de Meta Commerce, publicación en redes sociales y el Agente IA para WhatsApp mediante la API oficial de Meta se construirán en una fase posterior, e incluirán:

- Formulario de configuración de credenciales (App ID, App Secret, tokens de página, número de WhatsApp Business) almacenadas cifradas en la tabla `settings` (mecanismo ya implementado y reutilizable: ver `App\Models\Setting::set()` con el parámetro `encrypted: true`).
- Servicio adaptador desacoplado para las llamadas a la Graph API.
- Manejo de errores explícito para: credenciales ausentes, cuenta no autorizada, token expirado, permisos insuficientes.
- Documentación de cada permiso de Meta requerido.

**No se ha usado ningún token de prueba ni credencial simulada.**

## Google (Ads, Analytics, Tag Manager) — ⏳ Pendiente (fase futura)

No implementado en esta fase. Se integrará como parte del módulo de Growth Marketing y del generador de landing pages en una fase posterior.

## Inteligencia artificial (generación de texto e imágenes) — ⏳ Pendiente (fase futura)

No implementado en esta fase. El mecanismo de almacenamiento cifrado de claves de API (`Setting::set(..., encrypted: true)`) ya existe y será reutilizado cuando se construya este módulo, junto con el registro de uso (usuario, fecha, producto, modelo, solicitud, respuesta, consumo) descrito en el proyecto original.

## Proveedores de email marketing (Brevo, Mailgun, SendGrid, Amazon SES) — ⏳ Pendiente (fase futura)

El envío transaccional básico (recuperación de contraseña) ya funciona vía SMTP estándar (ver arriba). El módulo completo de campañas masivas, plantillas visuales, listas y segmentos se construirá en una fase posterior, con cola de envío, control de límites y reintentos para evitar el uso directo de `mail()` de PHP.

## Resumen

| Integración | Estado | Requiere credenciales de NODO 360 |
|---|---|---|
| Base de datos MySQL | ✅ Listo | Sí (ya solicitadas en el instalador) |
| SMTP / correo | ✅ Listo para configurar | Sí (proveedor a elegir) |
| Almacenamiento local | ✅ Listo | No |
| Meta (Facebook/Instagram/WhatsApp) | ⏳ Fase futura | Sí, cuando se construya |
| Google (Ads/Analytics/GTM) | ⏳ Fase futura | Sí, cuando se construya |
| IA generativa (texto/imagen) | ⏳ Fase futura | Sí, cuando se construya |
| Email marketing (Brevo/Mailgun/SES/SendGrid) | ⏳ Fase futura | Sí, cuando se construya |
