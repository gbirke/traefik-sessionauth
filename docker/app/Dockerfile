FROM composer as vendor

COPY composer.json /app/
COPY composer.lock /app/

RUN set -xe \
 && composer install --no-dev --no-scripts --no-interaction --prefer-dist --optimize-autoloader

COPY . /app

RUN composer dump-autoload --no-dev --optimize --classmap-authoritative


FROM php:8-fpm-alpine as app

WORKDIR /app

COPY . /app

COPY --from=vendor /app/vendor /app/vendor

RUN chown -R www-data /app/var
