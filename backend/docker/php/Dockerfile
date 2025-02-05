FROM php:8.1-apache

RUN apt-get update && apt-get install -y \
    nano \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    && docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd

RUN a2enmod rewrite && a2enmod headers

COPY aiven-ca.pem /etc/ssl/certs/aiven-ca.pem
COPY ./docker/apache/000-default.conf /etc/apache2/sites-available/000-default.conf

WORKDIR /var/www/html

RUN mkdir -p /var/www/html/logs \
    && touch /var/www/html/logs/app.log \
    && chown -R www-data:www-data /var/www/html \
    && chmod -R 775 /var/www/html/logs

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer
COPY . .

RUN composer install --no-dev --optimize-autoloader

EXPOSE 80
CMD ["apache2-foreground"]