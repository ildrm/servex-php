FROM php:8.1-fpm

RUN pecl install swoole && docker-php-ext-enable swoole
RUN apt-get update && apt-get install -y libzip-dev
RUN docker-php-ext-install zip

COPY . /var/www/servex
WORKDIR /var/www/servex
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
RUN composer install

CMD ["php-fpm"]