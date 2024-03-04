FROM php:alpine

RUN apk add --no-cache libzip-dev libpng-dev
RUN docker-php-ext-install gd zip

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /app
EXPOSE 80
