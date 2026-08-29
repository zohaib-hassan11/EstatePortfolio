#!/usr/bin/env bash
#
# Pull and release, for Hostinger shared hosting.
#
#   ssh -p 65002 uXXXXXX@your-server
#   cd ~/domains/yourdomain.com/app && ./deploy.sh
#
# Assets are NOT built here - shared hosting has no Node. Run `npm run build`
# on your own machine and commit public/build before deploying.

set -euo pipefail

echo "==> Pulling latest"
git pull origin main

echo "==> Installing PHP dependencies (production)"
composer install --no-dev --optimize-autoloader --no-interaction

echo "==> Migrating"
# `migrate --force` adds new tables without touching existing data.
# Never `migrate:fresh` here - it drops every table.
php artisan migrate --force

echo "==> Linking storage"
php artisan storage:link || echo "    (already linked)"

echo "==> Caching config, routes and views"
php artisan optimize:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo "==> Fixing permissions"
chmod -R 775 storage bootstrap/cache

echo ""
echo "Done. If you changed anything under resources/, make sure you ran"
echo "'npm run build' locally and committed public/build before this deploy."
