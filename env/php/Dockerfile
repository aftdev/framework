FROM php:${PHP_VERSION:-8.1}-cli

# Install composer
COPY --from=composer:latest /usr/bin/composer /usr/local/bin/composer

# Other dependencies
RUN apt-get update && \
    apt-get install -y man sudo tar wget hostname patch gzip zip unzip procps && \
    apt-get clean

# Install php extensions
COPY --from=mlocati/php-extension-installer /usr/bin/install-php-extensions /usr/local/bin/

RUN install-php-extensions gd opcache mbstring xml dom fileinfo pdo pdo_mysql redis memcached pcov

# Create User
RUN useradd -ms /bin/bash php
USER php

COPY ["env/bashrc", "/home/php/.bashrc"]

# PHP config
COPY ["env/php/config.ini", "/etc/php.d/zzzzz_docker.ini"]

WORKDIR /data

ENV APP_ENV production

CMD ["php", "-a"]
