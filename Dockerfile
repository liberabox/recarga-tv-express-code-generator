FROM php:7.3-cli

RUN apt-get update && apt-get install -y --no-install-recommends libc-client-dev libkrb5-dev
RUN docker-php-ext-configure imap --with-kerberos --with-imap-ssl
RUN docker-php-ext-install imap

RUN pecl install xdebug
RUN docker-php-ext-enable xdebug

RUN docker-php-ext-install pdo_mysql

RUN apt-get install -y --no-install-recommends git unzip zip libzip-dev
RUN docker-php-ext-install zip

RUN curl --silent --show-error https://getcomposer.org/installer | php
RUN mv composer.phar /usr/bin/composer
