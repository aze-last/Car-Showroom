# Production Deployment Guide

## System Requirements
- PHP >= 8.2 (with SQLite, PDO, BCMath, OpenSSL extensions)
- Composer >= 2.6
- Node.js >= 20.x & NPM
- MySQL / MariaDB (Production) or SQLite (Local/Staging)

## Setup Commands

```powershell
# 1. Install PHP & Node Dependencies
composer install --no-dev --optimize-autoloader
npm ci

# 2. Build Frontend Bundle
npm run build

# 3. Database Migration & Seeding
php artisan migrate --force
php artisan db:seed --force

# 4. Storage Link
php artisan storage:link

# 5. Cache Optimization
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

## Environment Variables (.env)

```env
APP_NAME="The Gallery Automotive Group"
APP_ENV=production
APP_KEY=base64:...
APP_URL=https://showroom.gallery.com

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=car_showroom
DB_USERNAME=showroom_user
DB_PASSWORD=...

FILESYSTEM_DISK=public
```

## Scheduled Cron Tasks
Add to server crontab:
```bash
* * * * * cd /path/to/Car-Showroom && php artisan schedule:run >> /dev/null 2>&1
```
