#!/bin/bash
set -e

echo "=== Despliegue Laravel iniciado ==="

echo "Instalando dependencias PHP (composer)..."
composer install --no-dev --optimize-autoloader

echo "Instalando dependencias JS (npm)..."
npm install

echo "Compilando assets (npm run build)..."
npm run build

echo "Ejecutando migraciones (php artisan migrate --force)..."
php artisan migrate --force

echo "Limpiando y cacheando configuración..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

if ! grep -q "APP_KEY=" .env; then
    echo "Generando clave de aplicación..."
    php artisan key:generate
fi

echo "Ajustando permisos de storage y cache..."
chmod -R 775 storage bootstrap/cache

echo "=== Despliegue Laravel finalizado ==="