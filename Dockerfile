FROM php:7.4-fpm as php

RUN apt-get update -y \
    && apt-get install -y --no-install-recommends unzip libgd-dev libpq-dev libcurl4-gnutls-dev libzip-dev git \
    && rm -rf /var/lib/apt/lists/*
RUN docker-php-ext-install pdo pdo_pgsql pdo_mysql bcmath gd zip

WORKDIR /var/www
COPY . .

COPY --from=composer:2.8.7 /usr/bin/composer /usr/bin/composer

ENV PORT=8000
ENTRYPOINT [ "Docker/entrypoint.sh" ]
