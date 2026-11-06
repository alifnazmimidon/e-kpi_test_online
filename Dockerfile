FROM php:8.4-cli

# Install keperluan sistem & extension PHP yang lengkap
RUN apt-get update && apt-get install -y \
    git curl libpng-dev libonig-dev libxml2-dev libzip-dev zip unzip \
    && docker-php-ext-install pdo_mysql mbstring gd zip

WORKDIR /app
COPY . /app

# Install Composer & benarkan akses superuser
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer
ENV COMPOSER_ALLOW_SUPERUSER=1

# Install pakej composer tanpa melepasi had versi platform
RUN composer install --no-dev --optimize-autoloader --ignore-platform-reqs

EXPOSE 10000
CMD php artisan serve --host=0.0.0.0 --port=${PORT:-10000}