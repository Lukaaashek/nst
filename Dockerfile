FROM php:7.1-fpm
RUN docker-php-ext-install pdo pdo_mysql iconv