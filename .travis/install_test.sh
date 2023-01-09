#!/usr/bin/env sh
set -ev

mkdir --parents "${HOME}/bin"

# PHPUnit install
wget "https://phar.phpunit.de/phpunit-9.5.27.phar" --output-document="${HOME}/bin/phpunit"
chmod u+x "${HOME}/bin/phpunit"

#  @todo Allow symfony version specification.
# if [ "$SYMFONY" != "" ]; then
#   composer require "symfony/symfony:$SYMFONY" --no-update
#   composer require "symfony/config:$SYMFONY" --no-update
#   composer require "symfony/dependency-injection:$SYMFONY" --no-update
#   composer require "symfony/http-kernel:$SYMFONY" --no-update
#   composer require "symfony/phpunit-bridge:$SYMFONY" --no-update --dev
#   composer require "symfony/translation:$SYMFONY" --no-update --dev
#   composer require "symfony/yaml:$SYMFONY" --no-update --dev
# fi
COMPOSER_MEMORY_LIMIT=-1 composer install --prefer-dist --no-interaction
