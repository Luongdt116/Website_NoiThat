# Dockerfile - PHP 8.3 + Composer (Laravel)
FROM php:8.3-cli
RUN apt-get update && apt-get install -y \
    git unzip libzip-dev zip && \
    docker-php-ext-install pdo_mysql zip
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer
WORKDIR /var/www
EXPOSE 8000
