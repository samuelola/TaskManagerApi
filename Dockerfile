FROM php:8.3-fpm

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git curl zip unzip libpng-dev libonig-dev libxml2-dev libzip-dev zlib1g-dev g++ \
    && rm -rf /var/lib/apt/lists/*

# Install PHP extensions
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd zip

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www

# Copy the whole application first (artisan must exist)
COPY . .

# Install PHP dependencies (production)
RUN composer install --no-dev --optimize-autoloader --no-interaction --prefer-dist

# Laravel Optimization
RUN php artisan config:cache \
    && php artisan route:cache \
    && php artisan view:cache

# Set permissions for storage and cache
RUN mkdir -p storage bootstrap/cache \
    && chown -R www-data:www-data storage bootstrap/cache \
    && chmod -R 775 storage bootstrap/cache

CMD ["php-fpm"]
