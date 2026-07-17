# Respaldo y Restauración — NODO Catalog Manager

## Qué respaldar

1. **Base de datos** MySQL (toda la información del catálogo, usuarios, configuración, actividad).
2. **Archivos subidos**: `storage/app/public` (imágenes de productos, logotipo, favicon).
3. **Archivo `.env`**: contiene la configuración de conexión y claves — guárdalo en un lugar seguro y separado (no lo subas a un repositorio público).

## Respaldo en Hostinger (con SSH)

### Base de datos

```bash
mysqldump -u u123456789_nodo -p u123456789_nodo > respaldo-$(date +%Y%m%d-%H%M).sql
gzip respaldo-*.sql
```

Descarga el archivo `.sql.gz` a tu computadora (SFTP o Administrador de Archivos) y guárdalo fuera del servidor.

### Archivos

```bash
tar -czf respaldo-storage-$(date +%Y%m%d).tar.gz storage/app/public
```

## Respaldo en Hostinger (sin SSH)

- **Base de datos**: hPanel → Bases de datos → phpMyAdmin → selecciona la base de datos → pestaña **Exportar** → formato SQL → Exportar. Guarda el archivo `.sql` descargado.
- **Archivos**: Administrador de Archivos → entra a `storage/app/public` → selecciona todo → **Comprimir** → descarga el `.zip` resultante.

## Respaldo automático (recomendado)

Hostinger ofrece respaldos automáticos diarios/semanales según el plan contratado (hPanel → Archivos → Backups). Actívalos como capa adicional, sin sustituir tus propios respaldos manuales periódicos de la base de datos.

## Restauración

### Base de datos

Por SSH:

```bash
gunzip respaldo-20260101-1200.sql.gz
mysql -u u123456789_nodo -p u123456789_nodo < respaldo-20260101-1200.sql
```

Por phpMyAdmin: selecciona la base de datos vacía (créala si es necesario) → pestaña **Importar** → selecciona el archivo `.sql` → Continuar.

### Archivos

Sube y descomprime `respaldo-storage-*.tar.gz` (o el `.zip` equivalente) dentro de `storage/app/public`, respetando la estructura de carpetas original (`products/`, `products/gallery/`, `branding/`).

### Después de restaurar

```bash
php artisan config:clear
php artisan cache:clear
php artisan storage:link
```

Verifica que el enlace `public/storage` siga apuntando correctamente a `storage/app/public` (el comando `storage:link` es seguro de repetir).

## Recomendación de frecuencia

- Base de datos: diario (contiene todo el catálogo y la actividad, cambia constantemente).
- Archivos (`storage/app/public`): semanal, o inmediatamente después de subir imágenes importantes.
- Conserva al menos las últimas 4 semanas de respaldos en una ubicación distinta al servidor de producción.
