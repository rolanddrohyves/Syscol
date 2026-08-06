FROM php:8.4-fpm

# Installer les dépendances système
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    nginx \
    && docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd

# Installer Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Copier les fichiers du projet
COPY . /var/www/html

# Définir le répertoire de travail
WORKDIR /var/www/html

# Variables d'environnement
ENV APP_ENV production
ENV APP_DEBUG false
ENV LOG_CHANNEL stderr

# ✅ CRÉER LE DOSSIER bootstrap/cache AVANT COMPOSER
RUN mkdir -p /var/www/html/bootstrap/cache

# ✅ DONNER LES PERMISSIONS AVANT COMPOSER
RUN chown -R www-data:www-data /var/www/html/bootstrap/cache
RUN chmod -R 775 /var/www/html/bootstrap/cache

# Installer les dépendances Composer
RUN composer install --no-interaction --no-dev --prefer-dist

# Configurer les permissions
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache
RUN chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# Optimisations Laravel
RUN php artisan config:cache || true
RUN php artisan route:cache || true
RUN php artisan view:cache || true

# Exécuter les migrations
RUN php artisan migrate --force || true

# Configurer Nginx
RUN echo "server { \
    listen 8080; \
    root /var/www/html/public; \
    index index.php; \
    location / { \
    try_files \$uri \$uri/ /index.php?\$query_string; \
    } \
    location ~ \.php$ { \
    fastcgi_pass 127.0.0.1:9000; \
    fastcgi_index index.php; \
    fastcgi_param SCRIPT_FILENAME \$realpath_root\$fastcgi_script_name; \
    include fastcgi_params; \
    } \
    }" > /etc/nginx/sites-enabled/default

# Exposer le port
EXPOSE 8080

# Démarrer PHP-FPM et Nginx
CMD php-fpm -D && nginx -g 'daemon off;'