#!/bin/sh
# Render boot: start Apache first so port scan passes, then wait for Neon, migrate.
set -eu
cd /var/www/html

if [ ! -f artisan ]; then
  echo "[render] no artisan file found" >&2
  exit 1
fi

PORT="${PORT:-8000}"
export PORT
echo "Listen ${PORT}" > /etc/apache2/ports.conf
sed -i "s/\${PORT:-8000}/${PORT}/g; s/\${PORT}/${PORT}/g" /etc/apache2/sites-available/000-default.conf

mkdir -p storage/framework/cache/data storage/framework/sessions storage/framework/views bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache || true

# Start Apache in background so Render detects open port immediately.
apache2-foreground &
APACHE_PID=$!
await_db() {
  php -r '
    $h = getenv("DB_HOST"); $p = getenv("DB_PORT") ?: "5432";
    $db = getenv("DB_DATABASE") ?: "neondb";
    $u = getenv("DB_USERNAME"); $w = getenv("DB_PASSWORD");
    // Neon cold start can take a while; retry ~5 min. PDO pgsql only, no channel_binding.
    for ($i = 0; $i < 60; $i++) {
      try {
        new PDO("pgsql:host=$h;port=$p;dbname=$db;sslmode=require", $u, $w, [PDO::ATTR_TIMEOUT => 5]);
        echo "db up\n"; exit(0);
      } catch (Throwable $e) {}
      echo "db wait " . ($i + 1) . "/60\n";
      sleep(5);
    }
    fwrite(STDERR, "db not reachable\n"); exit(1);
  '
}
if await_db; then
  echo "[render] running migrations"
  php artisan migrate --force || echo "[render] migrate failed, continuing"
  echo "[render] caching config"
  php artisan config:clear
  php artisan config:cache || true
  php artisan route:cache || true
  php artisan view:cache || true
else
  echo "[render] db never came up, serving without migrate"
fi

wait "$APACHE_PID"
