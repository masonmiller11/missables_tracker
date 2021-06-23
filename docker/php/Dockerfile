FROM php:8.0-fpm

COPY ./aliases.sh /etc/profile.d/aliases.sh

RUN apt-get -y update && \
    apt-get -y install curl git wget unzip mariadb-client

RUN docker-php-ext-install pdo pdo_mysql && \
    docker-php-ext-enable opcache

RUN wget https://composer.github.io/installer.sig -qO composer.sig \
	&& echo "  composer-setup.php" >> composer.sig \
	&& wget https://getcomposer.org/installer -qO composer-setup.php \
	&& sha384sum -c composer.sig \
	&& php composer-setup.php --quiet --install-dir /usr/local/bin --filename composer \
	&& rm -rf composer.sig composer-setup.php

RUN useradd php-data -d /home/php-data -m
USER php-data

COPY ./.my.cnf /home/php-data/.my.cnf

WORKDIR /var/www/html
RUN mkdir /var/www/html/var && chown -R php-data: /var/www/html/var