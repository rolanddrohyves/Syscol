FROM richarvey/nginx-php-fpm:latest-php84

# Copier tous les fichiers
COPY . .

# Variables d'environnement
ENV SKIP_COMPOSER 0
ENV WEBROOT /var/www/html/public
ENV PHP_ERRORS_STDERR 1
ENV RUN_SCRIPTS 0
ENV REAL_IP_HEADER 1
ENV APP_ENV production
ENV APP_DEBUG false
ENV LOG_CHANNEL stderr
ENV COMPOSER_ALLOW_SUPERUSER 1

# INSTALLER COMPOSER (pendant la construction)
RUN composer install --no-interaction --no-dev --prefer-dist

# OPTIMISATIONS LARAVEL
RUN php artisan config:cache || true
RUN php artisan route:cache || true
RUN php artisan view:cache || true

# PERMISSIONS
RUN chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# MIGRATIONS (si échec, continue)
RUN php artisan migrate --force || true

# Démarrer le serveur
CMD ["/start.sh"]