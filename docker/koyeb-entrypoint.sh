#!/bin/sh
# Koyeb boot: wait for Neon, migrate, cache config, serve via Apache.
set -eu
cd /var/www/html

if [ ! -f artisan ]; then
  echo "[koyeb] no artisan file found" >&2
  exit 1
fi

await_db() {
  DB_HOST="${DB_HOST:?set DB_HOST}" DB_PORT="${DB_PORT:?set DB_PORT}" \
  DB_USERNAME="${DB_USERNAME:-}" DB_PASSWORD="${DB_PASSWORD:-}" \
  php -r '
    $h = getenv("DB_HOST"); $p = (int) getenv("DB_PORT");
    for ($i = 0; $i < 60; $i++) {
      $fp = @fsockopen($h, $p, $errno, $errstr, 2);
      if ($fp) { fclose($fp); echo "db up\n"; exit(0); }
      sleep(1);
    }
    fwrite(STDERR, "db not reachable\n"); exit(1);
  '
}

PORT="${PORT:-8000}"
export PORT
echo "Listen ${PORT}" > /etc/apache2/ports.conf
sed -i "s/\${PORT:-8000}/${PORT}/g; s/\${PORT}/${PORT}/g" /etc/apache2/sites-available/000-default.conf

mkdir -p storage/framework/cache/data storage/framework/sessions storage/framework/views bootstrap/cache

echo "[koyeb] waiting for database"
await_db

echo "[koyeb] running migrations"
php artisan migrate --force

echo "[koyeb] caching config"
php artisan config:cache
php artisan route:cache
php artisan view:cache

chown -R www-data:www-data storage bootstrap/cache || true
exec apache2-foreground
