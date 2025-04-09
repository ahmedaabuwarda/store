FROM php:7.4-fpm as php

RUN apt-get update -y
RUN apt-get install -y unzip libgd-dev libpq-dev libcurl4-gnutls-dev git
RUN docker-php-ext-install pdo pdo_mysql bcmath gd zip

WORKDIR /var/www
COPY . .

COPY --from=composer:2.8.7 /usr/bin/composer /usr/bin/composer

ENV PORT=8000
ENTRYPOINT [ "Docker/entrypoint.sh" ]
