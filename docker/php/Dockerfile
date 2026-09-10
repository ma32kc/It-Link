FROM php:8.3-fpm

# Системные зависимости и расширения PHP для работы с PostgreSQL
RUN apt-get update \
    && apt-get install -y --no-install-recommends \
        git \
        unzip \
        libpq-dev \
    && docker-php-ext-install pdo pdo_pgsql \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# Composer из официального образа
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html
