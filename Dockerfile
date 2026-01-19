FROM php:8.2-fpm

# Dependencias del sistema + extensiones PHP necesarias
RUN apt-get update && apt-get install -y \
    git unzip libzip-dev libpng-dev libonig-dev libxml2-dev \
    && docker-php-ext-install pdo_mysql bcmath zip \
    && rm -rf /var/lib/apt/lists/*

# Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

# Copiamos el código
COPY . .

# Instalamos dependencias PHP
RUN composer install --no-interaction --prefer-dist --optimize-autoloader

# Build frontend (Vite)
RUN curl -fsSL https://deb.nodesource.com/setup_20.x | bash - \
    && apt-get update && apt-get install -y nodejs \
    && npm ci \
    && npm run build

# Permisos típicos de Laravel
RUN chown -R www-data:www-data storage bootstrap/cache

EXPOSE 8080

# Arranque simple (para entrega)
CMD php artisan serve --host 0.0.0.0 --port 8080
