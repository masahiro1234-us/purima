FROM php:8.1-fpm

COPY php.ini /usr/local/etc/php/

RUN apt update \
    && apt install -y default-mysql-client zlib1g-dev libzip-dev unzip \
    && docker-php-ext-install pdo_mysql zip \
    # 追記
    && rm -rf /var/lib/apt/lists/*
    # ------------------------------

RUN curl -sS https://getcomposer.org/installer | php \
    && mv composer.phar /usr/local/bin/composer

ENV TZ=Asia/Tokyo
WORKDIR /var/www