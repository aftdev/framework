FROM amazonlinux:2

RUN yum -y install https://dl.fedoraproject.org/pub/epel/epel-release-latest-7.noarch.rpm
RUN yum -y install https://rpms.remirepo.net/enterprise/remi-release-7.rpm
RUN yum -y install yum-utils

RUN yum-config-manager --enable remi-php73

RUN yum -y update

# Install php modules.
RUN yum install -y php-cli php-opcache php-xdebug php-mysqlnd php-pecl-memcached php-redis --disableplugin=priorities

# Other dependencies.
RUN yum install -y composer which man sudo tar wget hostname patch gzip zip unzip procps --disableplugin=priorities

# PHP config
# CLI
COPY ["env/php/config.ini", "/etc/php.d/zzzzz_docker.ini"]

WORKDIR /data

CMD ["php", "-a"]
