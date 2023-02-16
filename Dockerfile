ARG FROM_IMAGE=php:7.4-cli
FROM ${FROM_IMAGE}

RUN apt-get update \
 && apt-get upgrade -y \
 && apt-get install -y libzip-dev libicu-dev git wget \
 && rm -rf /var/lib/apt/lists/*

RUN docker-php-ext-install zip intl

ARG XDEBUG_VERSION=""
RUN pecl install xdebug${XDEBUG_VERSION} \
 && docker-php-ext-enable xdebug \
 && echo "xdebug.mode=coverage" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini