FROM php:8.2-apache

RUN docker-php-ext-install pdo_mysql
RUN a2enmod rewrite

WORKDIR /var/www/html
COPY . .

RUN mkdir -p data assets/uploads \
    && chown -R www-data:www-data data assets/uploads

ENV APP_BASE=""
EXPOSE 80