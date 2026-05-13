FROM php:8.2-apache

RUN apt-get update && apt-get install -y \
    libzip-dev \
    zip \
    unzip \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libcurl4-openssl-dev \
    libxml2-dev \
    libonig-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install pdo_mysql zip gd \
    && a2enmod rewrite

COPY --chown=www-data:www-data upload /var/www/html

COPY cleanup.php /var/www/html/cleanup.php

RUN cp /var/www/html/.htaccess.txt /var/www/html/.htaccess && \
    sed -i 's|AllowOverride None|AllowOverride All|g' /etc/apache2/apache2.conf

USER www-data
RUN cp /var/www/html/config-dist.php /var/www/html/config.php && \
    cp /var/www/html/admin/config-dist.php /var/www/html/admin/config.php

USER root
EXPOSE 80