FROM amazonlinux:2

RUN yum -y install https://dl.fedoraproject.org/pub/epel/epel-release-latest-7.noarch.rpm
RUN yum -y install https://rpms.remirepo.net/enterprise/remi-release-7.rpm
RUN yum -y install yum-utils

RUN yum-config-manager --enable remi-php${PHP_VERSION:-80}

# Install composer
COPY --from=composer:latest /usr/bin/composer /usr/local/bin/composer

RUN yum -y update \
  # Other dependencies.
  && yum install -y which man sudo tar wget hostname patch gzip zip unzip procps git --disableplugin=priorities \
  # Install php modules.
  && yum install -y \
  php-{cli,opcache,mbstring,xml,dom,fileinfo,mysqlnd,redis,pecl-memcached,pecl-pcov} \
  --disableplugin=priorities \
  # Clean up
  && yum clean all

# Create User
RUN useradd -ms /bin/bash php
USER php

COPY ["env/bashrc", "/home/php/.bashrc"]

# PHP config
COPY ["env/php/config.ini", "/etc/php.d/zzzzz_docker.ini"]

WORKDIR /data

CMD ["php", "-a"]
