FROM alpine:3.8
LABEL authors="Paulo Pastoriza <paulo.pastoriza@dbseller.com.br>"

RUN apk add --update --no-cache bash \
	curl \
	git \
	sshpass \
	openssh-client \
	vim \
	jq \
	sqlite \
	php5 \
	php5-bcmath \
	php5-calendar \
	php5-cli \
	php5-ctype \
	php5-curl \
	php5-dom \
	php5-gd \
	php5-iconv \
	php5-json \
	php5-openssl \
	php5-pdo \
	php5-pdo_pgsql \
	php5-pdo_sqlite \
	php5-pear \
	php5-pgsql \
	php5-phar \
	php5-soap \
	php5-xml \
	php5-xmlreader \
	php5-zip

RUN curl --silent --show-error --location https://getcomposer.org/installer | php5 -- --install-dir=/usr/bin --filename=composer && \
	chmod +x /usr/bin/composer

RUN curl --silent --show-error https://getcomposer.org/download/1.10.26/composer.phar -o /usr/bin/composer_v1 && \
	chmod +x /usr/bin/composer_v1

RUN curl --silent --show-error --location https://squizlabs.github.io/PHP_CodeSniffer/phpcs.phar -o /usr/bin/phpcs && \
    chmod +x /usr/bin/phpcs

RUN ln -s /usr/bin/php5 /usr/bin/php5.6 && \
	ln -s /usr/bin/php5 /usr/bin/php

RUN rm -rf /var/cache/apk/*

ENV GIT_SSL_NO_VERIFY=1

CMD ["/bin/sh"]
