# Seguridad — NODO Catalog Manager

## Medidas implementadas en la Fase 1

- **Contraseñas**: hash con Bcrypt (`Hash::make`), nunca almacenadas en texto plano.
- **CSRF**: protección automática de Laravel en todos los formularios (token `_token`).
- **XSS**: Blade escapa por defecto todo el contenido dinámico (`{{ }}`); no se usa `{!! !!}` con datos de usuario sin control.
- **Inyección SQL**: todo acceso a datos pasa por Eloquent/Query Builder con parámetros enlazados; no hay consultas SQL concatenadas manualmente.
- **Rate limiting**: el login usa `RateLimiter` de Laravel además de un bloqueo configurable por intentos fallidos (por defecto 5 intentos → 15 minutos de bloqueo, editable en Configuración → Seguridad).
- **Bloqueo de cuentas inactivas**: un usuario marcado como inactivo no puede iniciar sesión aunque la contraseña sea correcta.
- **Sesiones**: cookies de sesión firmadas y, por defecto, con el payload cifrado en disco (`SESSION_ENCRYPT=true`).
- **Roles y permisos**: sistema granular (spatie/laravel-permission) — cada acción sensible del backend valida el permiso correspondiente, no solo se oculta el botón en la interfaz.
- **Registro de actividad**: toda acción relevante (login, logout, creación, edición, eliminación, importación, exportación, cambios de configuración) queda auditada con usuario, fecha y descripción.
- **Eliminación lógica**: productos y usuarios se eliminan con `soft delete`, permitiendo restauración y evitando pérdida accidental de información.
- **Validación de archivos subidos**: tipo MIME, extensión y tamaño máximo validados en servidor (no solo en el navegador) para imágenes de productos, logotipo/favicon y archivos de importación.
- **Nombres de archivo aleatorios**: Laravel genera nombres únicos al guardar archivos subidos, evitando colisiones y adivinación de rutas.
- **Instalador autoblocante**: `/install` deja de estar accesible en cuanto termina la instalación (archivo `storage/app/installed.lock`) y responde 404 a cualquier intento posterior.
- **Claves de API cifradas**: el modelo `Setting` soporta guardar valores cifrados (`Setting::set($key, $value, encrypted: true)`) y nunca los expone completos en pantalla. Ya en uso real por el módulo de IA (Fase 2): la clave del proveedor de IA se guarda cifrada y solo se muestran sus últimos 4 caracteres.
- **Sin claves reales en el código**: este repositorio no contiene tokens, contraseñas ni claves de API reales; todo se configura por variables de entorno fuera del control de versiones (`.env` está en `.gitignore`).

## Recomendaciones después de instalar

1. **Cambia la contraseña del administrador inicial** inmediatamente después de tu primer login.
2. Verifica que `.env` tenga `APP_DEBUG=false` y `APP_ENV=production` en el servidor real (el instalador web ya lo configura así automáticamente).
3. Configura HTTPS/SSL en tu dominio (ver `INSTALL-HOSTINGER.md`) y confirma que `APP_URL` use `https://`.
4. Restringe el acceso SSH/FTP de tu hosting con contraseñas fuertes y, si Hostinger lo ofrece, autenticación de dos factores en el propio hPanel.
5. Configura respaldos periódicos (ver `BACKUP-RESTORE.md`).
6. Revisa periódicamente **Actividad del sistema** para detectar accesos o cambios inusuales.
7. Ajusta los intentos de login permitidos según tu tolerancia al riesgo (Configuración → Seguridad).
8. No compartas la cuenta de Superadministrador entre varias personas; crea una cuenta por persona con el rol mínimo necesario.
9. Cuando conectes integraciones externas en fases futuras (Meta, IA, email marketing), usa siempre el mecanismo de configuración cifrada del sistema — nunca pegues claves directamente en el código o en archivos de configuración versionados.
10. Mantén PHP y las dependencias de Composer actualizadas (`composer update` en un entorno de pruebas antes de aplicar en producción).
