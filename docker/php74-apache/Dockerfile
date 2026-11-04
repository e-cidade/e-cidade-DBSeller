FROM ubuntu:22.04

LABEL maintainer="equipe.infraestrutura@dbseller.com.br"

ENV DEBIAN_FRONTEND=noninteractive

RUN echo "Acquire::http {No-Cache=True;};" > /etc/apt/apt.conf.d/no-cache

RUN apt update && \
  apt install -y tzdata locales software-properties-common curl unzip zip pigz vim sqlite3 wget && \
  apt-add-repository -y ppa:ondrej/php

RUN echo 'America/Sao_Paulo' > /etc/timezone && \
  rm /etc/localtime && \
  ln -s /usr/share/zoneinfo/America/Sao_Paulo /etc/localtime && \
  echo "tzdata tzdata/Areas select America" | debconf-set-selections && \
  echo "tzdata tzdata/Zones/America select Sao_Paulo" | debconf-set-selections && \
  dpkg-reconfigure -f noninteractive tzdata

RUN localedef -i pt_BR -c -f ISO-8859-1 -A /usr/share/locale/locale.alias pt_BR && \
  locale-gen pt_BR && \
  dpkg-reconfigure locales

RUN touch /etc/apt/sources.list.d/pgdg.list && \
  curl -fsSL https://www.postgresql.org/media/keys/ACCC4CF8.asc | gpg --dearmor > apt.postgresql.org.gpg && \
  install -o root -g root -m 644 apt.postgresql.org.gpg /usr/share/keyrings/ && \
  echo "deb [arch=amd64 signed-by=/usr/share/keyrings/apt.postgresql.org.gpg] http://apt.postgresql.org/pub/repos/apt $(lsb_release -cs)-pgdg main" > /etc/apt/sources.list.d/pgdg.list && \
  apt update && \
  apt -y install postgresql-client-16

RUN apt update && \
  apt install -y --no-install-recommends \
  apache2 libapache2-mod-php7.4 php7.4 php7.4-bcmath php7.4-bz2 php7.4-cli php7.4-common \
  php7.4-curl php7.4-gd php7.4-json php7.4-mbstring \
  php7.4-pgsql php7.4-soap php7.4-sqlite3 php7.4-xml php7.4-xmlrpc php7.4-zip php7.4-intl

RUN echo '<VirtualHost *:80>\n\
        ServerAdmin equipe.infraestrutura@dbseller.com.br\n\
        DocumentRoot /var/www/html\n\
        LimitRequestLine 16382\n\
        LimitRequestFieldSize 16382\n\
        Timeout 12000\n\
        AddDefaultCharset ISO-8859-1\n\
        SetEnvIf Authorization "(.*)" HTTP_AUTHORIZATION=$1\n\
        <Directory /var/www/html>\n\
            Options +Indexes +FollowSymLinks +MultiViews\n\
            AllowOverride All\n\
            Require all granted\n\
        </Directory>\n\
	ErrorLog ${APACHE_LOG_DIR}/error.log\n\
	CustomLog ${APACHE_LOG_DIR}/access.log combined\n\
        RewriteEngine on\n\
</VirtualHost>\n '\
> /etc/apache2/sites-available/000-default.conf

RUN a2enmod rewrite ssl proxy_fcgi setenvif && \
  a2disconf serve-cgi-bin

RUN touch /var/log/php_errors.log && \
  chown www-data. /var/log/php_errors.log && \
  chmod 777 /var/log/php_errors.log && \
  sed -i "s/^short_open_tag.*/short_open_tag = On/g" /etc/php/7.4/apache2/php.ini && \
  sed -i "s/^;date.timezone.*/date.timezone = \"America\/Sao_Paulo\"/g" /etc/php/7.4/apache2/php.ini && \
  sed -i "s/^session.gc_maxlifetime.*/session.gc_maxlifetime = 7200/g" /etc/php/7.4/apache2/php.ini && \
  sed -i "s/^;error_log.*/error_log = \/var\/log\/php_errors.log/" /etc/php/7.4/apache2/php.ini && \
  sed -i "s/^short_open_tag.*/short_open_tag = On/g" /etc/php/7.4/cli/php.ini && \
  sed -i "s/^;date.timezone.*/date.timezone = \"America\/Sao_Paulo\"/g" /etc/php/7.4/cli/php.ini && \
  sed -i "s/^session.gc_maxlifetime.*/session.gc_maxlifetime = 7200/g" /etc/php/7.4/cli/php.ini && \
  sed -i "s/^;error_log.*/error_log = \/var\/log\/php_errors.log/" /etc/php/7.4/cli/php.ini

RUN curl -sS https://getcomposer.org/installer -o composer-setup.php \
    && php composer-setup.php --install-dir=/usr/local/bin --filename=composer \
    && rm composer-setup.php

ENV PATH=$PATH:/root/composer/vendor/bin COMPOSER_ALLOW_SUPERUSER=1

RUN echo ttf-mscorefonts-installer msttcorefonts/accepted-mscorefonts-eula select true | debconf-set-selections

RUN apt update && apt install -y --no-install-recommends libreoffice-writer

RUN apt-get clean && rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/*

EXPOSE 80 443

WORKDIR /var/www/html

RUN rm /var/www/html/index.html

HEALTHCHECK --interval=5s --timeout=3s --retries=3 CMD curl -f http://localhost || exit 1

CMD ["apachectl", "-D", "FOREGROUND"]