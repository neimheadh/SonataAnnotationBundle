ARG FROM_IMAGE=php:7.4
FROM ${FROM_IMAGE}

RUN curl "https://phar.phpunit.de/phpunit-9.5.27.phar" --output "/bin/phpunit" \
 && chmod u+x "/bin/phpunit" \
 && phpunit --version

RUN curl "https://getcomposer.org/installer" --output "composer-setup.php" \
 && php composer-setup.php \
 && chmod +x composer.phar \
 && rm composer-setup.php \
 && mv composer.phar /bin/composer \
 && composer --version

RUN apt-get update \
 && apt-get upgrade -y \
 && apt-get install -y libzip-dev libicu-dev git wget \
 && rm -rf /var/lib/apt/lists/*

RUN docker-php-ext-install zip intl