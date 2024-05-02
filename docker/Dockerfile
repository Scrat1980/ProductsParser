FROM php:8.2-fpm

RUN docker-php-ext-install mysqli pdo pdo_mysql

RUN pecl install xdebug && docker-php-ext-enable xdebug

RUN pecl install mailparse && docker-php-ext-enable mailparse