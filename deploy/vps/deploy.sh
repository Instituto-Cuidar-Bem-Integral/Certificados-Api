#!/usr/bin/env bash
set -euo pipefail

APP_DIR="${1:-/var/www/Cuidado-Integral-Api}"

if ! command -v composer >/dev/null 2>&1; then
    echo "Composer nao encontrado no servidor." >&2
    exit 1
fi

if [ ! -d "$APP_DIR" ]; then
    echo "Diretorio da aplicacao nao encontrado: $APP_DIR" >&2
    exit 1
fi

cd "$APP_DIR"

if [ ! -f ".env" ]; then
    echo "Arquivo .env nao encontrado em $APP_DIR" >&2
    exit 1
fi

umask 002

git fetch origin
git reset --hard origin/main

sudo rm -rf vendor

mkdir -p public/qrcodes tmp/mpdf

composer install --no-dev --optimize-autoloader --no-interaction

chown -R www-data:www-data tmp
chmod -R 775 tmp

find public/qrcodes -type d -exec chmod 775 {} \;
find public/qrcodes -type f -exec chmod 664 {} \;

echo "Deploy finalizado em $(date '+%Y-%m-%d %H:%M:%S')"
