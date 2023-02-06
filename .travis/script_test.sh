#!/usr/bin/env sh
set -ev

rm -Rf var/cache/test.sqlite
phpunit -c phpunit.xml.dist --coverage-clover build/logs/clover.xml
