echo "Start building ..."
rm -rf vendor
rm -f composer.lock composer.phar
set -xe
# Install composer with dev dependencies so we can run tests.
composer install --dev
