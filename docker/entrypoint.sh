#!/bin/sh
set -e

# Wait for MySQL
echo "[tmg_app] Waiting for database at ${DB_HOST}:${DB_PORT}..."
until php -r "
    try {
        new PDO(
            'mysql:host=' . getenv('DB_HOST') . ';port=' . getenv('DB_PORT') . ';dbname=' . getenv('DB_DATABASE'),
            getenv('DB_USERNAME'),
            getenv('DB_PASSWORD'),
            [PDO::ATTR_TIMEOUT => 3]
        );
        exit(0);
    } catch (Exception \$e) {
        exit(1);
    }
" 2>/dev/null; do
    echo "[tmg_app] Database not ready - retrying in 3s..."
    sleep 3
done
echo "[tmg_app] Database ready."

# Run migrations
echo "[tmg_app] Running migrations..."
php artisan migrate --force

# Laravel optimisation
echo "[tmg_app] Optimising..."
php artisan optimize
php artisan storage:link --force 2>/dev/null || true

# Start Nginx + PHP-FPM via Supervisor
echo "[tmg_app] Starting server..."
exec /usr/bin/supervisord -n -c /etc/supervisor/conf.d/supervisord.conf
