# PHP image (mod_php or PHP-FPM) with the extensions the application needs.
ARG BASE=php:8.3-apache
FROM ${BASE}
RUN docker-php-ext-install pdo_mysql
