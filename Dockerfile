FROM php:7.1-cli

RUN apt-get update && apt-get -y install bash wget vim git zip unzip
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

COPY . /app
WORKDIR /app

RUN composer install