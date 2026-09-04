#!/bin/sh
set -e

UPLOADS=/var/www/html/public/uploads

mkdir -p "$UPLOADS"
chown -R www-data:www-data "$UPLOADS" 2>/dev/null || true

if ! su -s /bin/sh www-data -c "touch $UPLOADS/.write-probe" 2>/dev/null; then
    chmod -R 0777 "$UPLOADS" 2>/dev/null || true
fi

rm -f "$UPLOADS/.write-probe"

exec docker-php-entrypoint "$@"
