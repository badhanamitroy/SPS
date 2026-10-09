# SPS Platform - Production Deployment Guide

## 1. Web Server Root Configuration
- The web server DocumentRoot **MUST** be set strictly to the `/public` directory:
  - **Nginx**: `root /path/to/SPS/public;`
  - **Apache**: `DocumentRoot "/path/to/SPS/public"`
- The application root (`/app`, `/storage`, `/Media`, `/tests`, `/vendor`, `.env`) must reside outside of the web-accessible directory.
- Direct web access to `storage/` and `Media/` from the browser must return `403 Forbidden` or `404 Not Found`.

### Nginx Example Configuration
```nginx
server {
    listen 443 ssl http2;
    server_name sps.org.bd;
    root /var/www/sps/public;
    index index.php;

    # Protect parent and internal directories
    location ~ ^/(app|storage|Media|tests|\.env|\.git) {
        deny all;
        return 404;
    }

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        include fastcgi_params;
        fastcgi_pass unix:/var/run/php/php8.0-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
    }
}
```

### Apache (.htaccess in /public)
Ensure `AllowOverride All` is enabled on `/public`. Requests are routed through `/public/index.php`. Internal files outside `/public` are inaccessible by virtue of DocumentRoot isolation.

---

## 2. Production Environment Settings (.env)
In production, verify the following critical environment variables in `.env`:

```ini
APP_ENV=production
APP_DEBUG=false
SESSION_SECURE_COOKIE=true
SESSION_LIFETIME=1800
```

- **`APP_DEBUG=false`**: Disables verbose error displays, debug diagnostics, role-switching simulators, and stack trace leaks.
- **`SESSION_SECURE_COOKIE=true`**: Enforces HTTPS-only transmission of session cookies with `Secure`, `HttpOnly`, and `SameSite=Lax`.

---

## 3. Storage & Media Backup Procedures

The SPS platform uses a file-based storage architecture in `storage/data/` alongside uploaded assets in `Media/`.

### Directory Overview
- **`storage/data/`**: Core JSON databases (membership, blogs, activities, notices, audit logs, rate limits).
- **`Media/`**: User-uploaded profile avatars, payment transaction screenshots, and library PDFs.

### Recommended Backup Script (Daily Cron)
Run a daily automated backup of the critical data directories using tar/gzip:

```bash
#!/bin/bash
BACKUP_DATE=$(date +"%Y%m%d_%H%M%S")
BACKUP_DIR="/var/backups/sps"
APP_DIR="/var/www/sps"

mkdir -p "$BACKUP_DIR"

# 1. Archive storage data (JSON records)
tar -czf "$BACKUP_DIR/sps_data_${BACKUP_DATE}.tar.gz" -C "$APP_DIR" storage/data

# 2. Archive uploaded media and documents
tar -czf "$BACKUP_DIR/sps_media_${BACKUP_DATE}.tar.gz" -C "$APP_DIR" Media

# 3. Secure archive permissions
chmod 600 "$BACKUP_DIR"/sps_*_${BACKUP_DATE}.tar.gz

# 4. Retain past 30 days of backups
find "$BACKUP_DIR" -type f -name "sps_*.tar.gz" -mtime +30 -delete

echo "SPS Backup completed: ${BACKUP_DATE}"
```

### Restoration Instructions
To restore from backup:
```bash
# 1. Stop web server temporarily to avoid lock contention
sudo systemctl stop nginx php8.0-fpm

# 2. Extract archived data to application root
tar -xzf /var/backups/sps/sps_data_YYYYMMDD_HHMMSS.tar.gz -C /var/www/sps
tar -xzf /var/backups/sps/sps_media_YYYYMMDD_HHMMSS.tar.gz -C /var/www/sps

# 3. Ensure proper file permissions
chown -R www-data:www-data /var/www/sps/storage /var/www/sps/Media
chmod -R 775 /var/www/sps/storage /var/www/sps/Media

# 4. Restart services
sudo systemctl start php8.0-fpm nginx
```
