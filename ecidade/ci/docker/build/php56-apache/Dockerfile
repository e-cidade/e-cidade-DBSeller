FROM php:5.6-apache

LABEL maintainer="equipe.infraestrutura@dbseller.com.br"

ENV DEBIAN_FRONTEND noninteractive
ENV GIT_SSH_COMMAND "ssh -o UserKnownHostsFile=/dev/null -o StrictHostKeyChecking=no"
ENV TZ America/Sao_Paulo

RUN ln -snf /usr/share/zoneinfo/$TZ /etc/localtime && echo $TZ > /etc/timezone

# Common
RUN apt-get update \
&& apt-get install -y \
   vim \
unzip \
curl \
git \
libbz2-dev \
libicu-dev \
libmcrypt-dev \
libpq-dev \
libxml2-dev \
zlib1g-dev \
firebird-dev \
libedit-dev \
libxslt1-dev \
libjpeg-dev \
libpng-dev \
libfreetype6-dev \
libreoffice-writer \
tzdata

RUN dpkg-reconfigure tzdata

# Instala as extensões PHP e módulos PECL.
RUN docker-php-ext-configure gd --with-freetype-dir=/usr/include/ --with-jpeg-dir=/usr/include/ \
    && docker-php-ext-install bcmath bz2 calendar iconv intl mbstring mcrypt opcache pdo_pgsql pgsql soap zip gettext pdo_firebird interbase readline exif sysvmsg sockets shmop xmlrpc xsl wddx gd \
    && echo 'extension=readline.so' > /usr/local/etc/php/conf.d/docker-php-ext-readline.ini

COPY docker/build/php56-apache/apache.conf /etc/apache2/sites-available/000-default.conf
COPY docker/build/php56-apache/databases.php /var/www/
COPY scripts/build /var/www/

# Habilita o módulo rewrite.
RUN a2enmod rewrite

# Configura o PHP.INI com os parâmetros necessários para o desenvolvimento.
RUN ln -s /dev/stderr /var/log/php_errors.log \
    && chown www-data. /var/log/php_errors.log \
    && chmod 777 /var/log/php_errors.log \
    && cp /usr/local/etc/php/php.ini-development /usr/local/etc/php/php.ini \
    && sed -i "s/^short_open_tag.*/short_open_tag = On/g" /usr/local/etc/php/php.ini \
    && sed -i "s/^;date.timezone.*/date.timezone = \"America\/Sao_Paulo\"/g" /usr/local/etc/php/php.ini \
    && sed -i "s/^session.gc_maxlifetime.*/session.gc_maxlifetime = 7200/g" /usr/local/etc/php/php.ini \
    && sed -i "s/^;error_log.*/error_log = \/var\/log\/php_errors.log/" /usr/local/etc/php/php.ini \
    && sed -i "s/^;session.save_path.*/session.save_path = \/var\/lib\/php\/sessions/" /usr/local/etc/php/php.ini \
    && mkdir -p /var/lib/php/sessions \
    && chmod 777 /var/lib/php/sessions

# Instala o composer
RUN php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');" \
    && php -r "if (hash_file('sha384', 'composer-setup.php') === '48e3236262b34d30969dca3c37281b3b4bbe3221bda826ac6a9a62d6444cdb0dcd0615698a5cbe587c3f0fe57a54d8f5') { echo 'Installer verified'; } else { echo 'Installer corrupt'; unlink('composer-setup.php'); } echo PHP_EOL;" \
    && php composer-setup.php \
    && php -r "unlink('composer-setup.php');" \
    && mv composer.phar /usr/local/bin/composer

# Aceita os termos EULA para o pacote MS fonts
RUN echo ttf-mscorefonts-installer msttcorefonts/accepted-mscorefonts-eula select true | debconf-set-selections

RUN useradd -d /home/dbseller -g www-data -G sudo,adm,cdrom,dip,plugdev -k /etc/skel -m -s /bin/bash dbseller

# Clean
RUN apt-get clean
RUN rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/* /var/cache/*
