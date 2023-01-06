#!/usr/bin/env sh
set -ev

mkdir --parents "${HOME}/bin"

# PHPUnit install
wget "https://phar.phpunit.de/phpunit-9.5.27.phar" --output-document="${HOME}/bin/phpunit"
chmod u+x "${HOME}/bin/phpunit"

# Coveralls client install
wget https://github.com/satooshi/php-coveralls/releases/download/v1.0.1/coveralls.phar --output-document="${HOME}/bin/coveralls"
chmod u+x "${HOME}/bin/coveralls"

if [ "$SYMFONY" != "" ]; then composer require "symfony/symfony:$SYMFONY" --no-update; fi;
COMPOSER_MEMORY_LIMIT=-1 composer install --prefer-dist --no-interaction
