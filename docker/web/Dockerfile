FROM php:8.1.9-apache

RUN apt-get update -yqq && \
    apt-get install -y apt-utils zip unzip && \
    apt-get install -y nano && \
    apt-get install -y libzip-dev && \
    apt-get install -y libicu-dev && \
    apt-get install -y git

RUN curl -sL https://deb.nodesource.com/setup_16.x | bash - && \
    apt-get install -y nodejs

RUN a2enmod rewrite && \
    docker-php-ext-install mysqli pdo pdo_mysql && \
    docker-php-ext-install zip && \
    docker-php-ext-install intl && \
    rm -rf /var/lib/apt/lists/*

RUN php -r "readfile('http://getcomposer.org/installer');" | php -- --install-dir=/usr/bin/ --filename=composer

RUN set -xe; \
	apt-get update; \
	apt-get -y --no-install-recommends install g++ zlib1g-dev; \
	pecl install grpc; \
	docker-php-ext-enable grpc

RUN apt-get update && \
    apt-get install -y cron && \
    apt-get install -y supervisor

COPY etc/apache2/sites-enabled/000-default.conf /etc/apache2/sites-enabled/000-default.conf
COPY etc/supervisor/conf.d/supervisord.conf /etc/supervisor/conf.d/supervisord.conf
COPY etc/php/conf.d/custom.ini /usr/local/etc/php/conf.d/custom.ini

WORKDIR /var/www/app

CMD ["/usr/bin/supervisord"]
