# Build image to install dependencies
FROM composer as vendor

COPY composer.json /app/
COPY composer.lock /app/

RUN set -xe \
 && composer install --no-dev --no-scripts --no-interaction --prefer-dist --optimize-autoloader

COPY . /app

RUN composer dump-autoload --no-dev --optimize --classmap-authoritative

# Development image
FROM php:8-apache as app

RUN rm -rf /var/www/html \
	&& ln -s /app/public /var/www/html \
	&& a2enmod rewrite

# TODO Copy config

COPY . /app

COPY --from=vendor /app/vendor /app/vendor

RUN chown -R www-data /app/var 

# Configure production settings for safety and speed
FROM app as app-production

RUN docker-php-ext-install opcache \
	&& ln -s /usr/local/etc/php/php.ini-production /usr/local/etc/php/php.ini

