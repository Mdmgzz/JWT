FROM php:8.4-fpm-alpine
RUN apk add --no-cache libpng-dev libzip-dev zip unzip git curl
RUN docker-php-ext-install pdo_mysql gd zip
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer
WORKDIR /var/www
COPY . .
RUN composer install --no-dev --optimize-autoloader --no-interaction
RUN chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache
EXPOSE 9000
CMD ["php-fpm"]