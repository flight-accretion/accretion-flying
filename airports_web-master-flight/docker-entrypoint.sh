#!/bin/sh
set -e

mkdir -p /var/www/html/public/uploads /var/www/html/public/uploads/thumbnail /var/www/html/storage /var/www/html/bootstrap/cache

chown -R www-data:www-data /var/www/html/public/uploads /var/www/html/storage /var/www/html/bootstrap/cache 2>/dev/null || true
chmod -R 775 /var/www/html/public/uploads /var/www/html/storage /var/www/html/bootstrap/cache 2>/dev/null || true

exec "$@"