FROM alpine:3.15
LABEL authors="Paulo Pastoriza <paulo.pastoriza@dbseller.com.br>"

RUN apk add --update --no-cache bash \
        curl \
        git \
        sshpass \
        openssh-client \
	vim \
	jq \
	sqlite \
        php7 \
        php7-bcmath \
        php7-calendar \
        php7-cli \
        php7-ctype \
        php7-curl \
        php7-dom \
        php7-fileinfo \
        php7-gd \
        php7-iconv \
        php7-json \
        php7-mbstring \
        php7-openssl \
        php7-pdo \
        php7-pdo_pgsql \
	php7-pdo_sqlite \
        php7-pear \
        php7-pgsql \
        php7-phar \
        php7-simplexml \
        php7-soap \
        php7-tokenizer \
        php7-xml \
        php7-xmlreader \
        php7-xmlwriter \
        php7-zip

RUN curl --silent --show-error --location https://getcomposer.org/installer | php7 -- --install-dir=/usr/bin --filename=composer && \
	chmod +x /usr/bin/composer

RUN curl --silent --show-error --location https://squizlabs.github.io/PHP_CodeSniffer/phpcs.phar -o /usr/bin/phpcs && \
    chmod +x /usr/bin/phpcs

RUN ln -s /usr/bin/php7 /usr/bin/php7.4

RUN rm -rf /var/cache/apk/*

ENV GIT_SSL_NO_VERIFY=1

CMD ["/bin/sh"]
