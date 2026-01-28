FROM php:8.2-fpm

# Download the installer and run it
ADD --chmod=0755 https://github.com/mlocati/docker-php-extension-installer/releases/latest/download/install-php-extensions /usr/local/bin/

RUN install-php-extensions amqp

RUN apt-get update && apt-get install -y \
    unzip \
    zip \
    libfreetype6-dev \
    && docker-php-ext-install pdo pdo_mysql \
    && docker-php-ext-install -j$(nproc) gd

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

WORKDIR /var/www

COPY . /var/www

RUN composer install --optimize-autoloader

RUN chown -R www-data:www-data /var/www