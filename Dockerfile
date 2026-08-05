FROM richarvey/nginx-php-fpm:latest

COPY . .

# Image config
ENV SKIP_COMPOSER 0
ENV WEBROOT /var/www/html/public
ENV PHP_ERRORS_STDERR 1
ENV RUN_SCRIPTS 1
ENV REAL_IP_HEADER 1

# Laravel config
ENV APP_ENV production
ENV APP_DEBUG false
ENV LOG_CHANNEL stderr

# Allow composer to run as root
ENV COMPOSER_ALLOW_SUPERUSER 1

# Installer les dépendances Composer
RUN composer install --no-interaction --no-dev --prefer-dist

# Créer le lien storage et définir les permissions
RUN php artisan storage:link || true
RUN chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# Optimisations Laravel (cache)
RUN php artisan config:cache || true
RUN php artisan route:cache || true
RUN php artisan view:cache || true

# Exécuter les migrations (si échec, continue)
RUN php artisan migrate --force || true

CMD ["/start.sh"]