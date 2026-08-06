#!/bin/bash

# Démarrer le service
echo "=== Starting Laravel deployment ==="

# Installer les dépendances si le dossier vendor n'existe pas
if [ ! -d "/var/www/html/vendor" ]; then
    echo "=== Installing Composer dependencies ==="
    composer install --no-interaction --no-dev --prefer-dist
fi

# Optimisations Laravel
echo "=== Running Laravel optimizations ==="
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Exécuter les migrations
echo "=== Running migrations ==="
php artisan migrate --force

# Démarrer le serveur
echo "=== Starting server ==="
exec /start.sh