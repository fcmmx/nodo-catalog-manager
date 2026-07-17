# Credenciales que debe configurar NODO 360

Lista de credenciales y decisiones que el propietario del sistema debe proporcionar o tomar. **Ninguna de estas ha sido inventada, simulada o dejada con valores de prueba en el código.**

## Para poner el sistema en producción (obligatorio)

| # | Dato | Dónde se usa | Dónde configurarlo |
|---|---|---|---|
| 1 | Credenciales de la base de datos MySQL (host, nombre, usuario, contraseña) | Conexión principal del sistema | Instalador web (`/install`) o `.env` |
| 2 | Dominio o subdominio final y certificado SSL | `APP_URL`, enlaces generados en el sistema | hPanel de Hostinger |
| 3 | Proveedor y credenciales SMTP (host, usuario, contraseña, remitente) | Recuperación de contraseña, notificaciones | `.env` (`MAIL_*`) — ver `CONFIGURACION-APIS.md` |
| 4 | Nombre, correo y contraseña del primer superadministrador | Cuenta inicial de acceso | Instalador web, paso "Crea tu cuenta de superadministrador" |
| 5 | Logotipo y favicon oficiales de NODO 360 (archivos de imagen) | Identidad visual del sistema | Configuración → Marca e identidad visual |
| 6 | Datos de contacto reales de la empresa (teléfono, WhatsApp, dirección) | Ficha de configuración de empresa | Configuración → Datos de la empresa |

## Para fases futuras (no requeridas todavía)

| # | Dato | Fase que lo requiere |
|---|---|---|
| 7 | App ID, App Secret y tokens de Meta (Facebook/Instagram/WhatsApp Business) | Fase 4 (redes sociales) y Fase 8 (Meta Commerce) |
| 8 | Credenciales de Google Ads / Google Analytics / Google Tag Manager | Fase 6 (landing pages) |
| 9 | Clave de API de un proveedor de inteligencia artificial (texto y/o imagen) | Fase 2 y Fase 3 |
| 10 | Credenciales de un proveedor de email marketing (Brevo, Mailgun, SES, SendGrid) si se desea reemplazar el SMTP básico por un proveedor especializado con seguimiento de aperturas/clics | Fase 5 |

## Notas de seguridad

- Ninguna de estas credenciales debe pegarse directamente en archivos del repositorio ni compartirse por canales no seguros.
- Todas las claves de API de fases futuras se guardarán cifradas en la base de datos mediante el mecanismo ya implementado (`App\Models\Setting::set($key, $value, encrypted: true)`), nunca en texto plano.
- Cambia la contraseña del superadministrador inicial en cuanto tengas acceso (ver `SEGURIDAD.md`).
