FROM php:8.1-apache

RUN curl -sSLf -o /usr/local/bin/install-php-extensions \
    https://github.com/mlocati/docker-php-extension-installer/releases/latest/download/install-php-extensions \
    && chmod +x /usr/local/bin/install-php-extensions

RUN install-php-extensions gd pdo_mysql zip intl soap \
    && a2enmod rewrite

RUN echo "memory_limit = 256M" >> /usr/local/etc/php/conf.d/qloapps.ini \
    && echo "upload_max_filesize = 32M" >> /usr/local/etc/php/conf.d/qloapps.ini \
    && echo "post_max_size = 32M" >> /usr/local/etc/php/conf.d/qloapps.ini \
    && echo "max_execution_time = 300" >> /usr/local/etc/php/conf.d/qloapps.ini \
    && echo "allow_url_fopen = On" >> /usr/local/etc/php/conf.d/qloapps.ini

WORKDIR /var/www/html

EXPOSE 80
