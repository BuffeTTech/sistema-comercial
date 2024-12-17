FROM php:8.2-fpm

ARG APP_ENV="$(app-env)"
ARG NODE_VERSION=20

RUN apt update && \
    apt install -y gpg-agent curl git nginx libzip-dev libpng-dev libxml2-dev \
    libcurl4-openssl-dev pkg-config libssl-dev $PHPIZE_DEPS && \
    mkdir -p /etc/apt/keyrings

# Instala Node.js e npm
RUN curl -fsSL https://deb.nodesource.com/setup_${NODE_VERSION}.x | bash - && \
    apt install -y nodejs && \
    npm install -g npm@latest

RUN docker-php-ext-install opcache pcntl pdo_mysql zip gd sockets bcmath
RUN pecl update-channels && \
    pecl install ds igbinary xdebug && \
    docker-php-ext-enable ds igbinary sockets bcmath
RUN pecl install -D 'enable-redis-igbinary="yes"' redis && \
    docker-php-ext-enable redis

COPY .docker/nginx/default.conf /etc/nginx/sites-available/default
COPY .docker/nginx/nginx.conf /etc/nginx/nginx.conf
COPY .docker/php/php.ini /usr/local/etc/php/conf.d/php.ini
COPY .docker/php/opcache.ini /usr/local/etc/php/conf.d/opcache.ini

COPY --from=composer /usr/bin/composer /usr/bin/composer

COPY --chown=www-data:www-data . /app

WORKDIR /app

COPY .docker/entrypoint.sh /entrypoint.sh
RUN chmod +x /entrypoint.sh
ENTRYPOINT sh /entrypoint.sh

EXPOSE 80
