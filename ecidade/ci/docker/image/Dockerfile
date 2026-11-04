FROM php:5-stretch

ENV DEBIAN_FRONTEND noninteractive
ENV COMPOSER_ALLOW_SUPERUSER 1
ENV COMPOSER_HOME /tmp

# Common
RUN apt-get update \
        && apt-get install -y \
                rsync \
                git \
                cvs \
                sshpass \
                figlet \
                libxml2-dev \
                libpq-dev \
                zip \
                unzip \
                libjpeg-dev \
                libpng-dev \
                libfreetype6-dev \
                zlib1g-dev


# Composer
RUN curl -sS https://getcomposer.org/installer -o composer-setup.php \
    && php composer-setup.php --install-dir=/usr/local/bin --filename=composer \
    && php /usr/local/bin/composer self-update --1 \
    && rm composer-setup.php

# PHP Code Sniffer
RUN curl -OL https://squizlabs.github.io/PHP_CodeSniffer/phpcs.phar \
        && chmod 755 phpcs.phar \
        && mv phpcs.phar /usr/local/bin/ \
        && ln -s /usr/local/bin/phpcs.phar /usr/local/bin/phpcs \
        && curl -OL https://squizlabs.github.io/PHP_CodeSniffer/phpcbf.phar \
        && chmod 755 phpcbf.phar \
        && mv phpcbf.phar /usr/local/bin/ \
        && ln -s /usr/local/bin/phpcbf.phar /usr/local/bin/phpcbf

# PHP Extensions
RUN docker-php-ext-configure gd --with-freetype-dir=/usr/include/ --with-jpeg-dir=/usr/include/ \
    && docker-php-ext-install bcmath calendar dom pgsql simplexml gd zip soap

# Permissions
RUN chmod -R 700 /usr/local/bin/

RUN useradd -d /home/dbseller -g www-data -G sudo,adm,cdrom,dip,plugdev -k /etc/skel -m -s /bin/bash dbseller

# Clean
RUN apt-get clean
RUN rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/* /var/cache/*
