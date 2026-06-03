#!/bin/sh
set -e

# Wait for MySQL
echo "[tmg_worker] Waiting for database at ${DB_HOST}:${DB_PORT}..."
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
    echo "[tmg_worker] Database not ready - retrying in 3s..."
    sleep 3
done
echo "[tmg_worker] Database ready."

# Start queue worker
echo "[tmg_worker] Starting queue worker..."
exec php artisan queue:work \
    --sleep=3 \
    --tries=3 \
    --timeout=60 \
    --max-time=3600
