FROM php:7-alpine

RUN apk add --no-cache linux-headers
RUN docker-php-ext-install sockets

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /app
EXPOSE 80
