FROM php:8.2-fpm

# 必要なパッケージのインストール
RUN apt-get update && apt-get install -y \
    git zip unzip curl libzip-dev gnupg2 ca-certificates \
    && docker-php-ext-install zip pdo pdo_mysql sockets

# Node.js（18.x LTS）をインストール
RUN curl -fsSL https://deb.nodesource.com/setup_18.x | bash - \
    && apt-get install -y nodejs

# Composerを multi-stage build でコピー（これは RUN に含めない！）
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# 作業ディレクトリとコード配置
WORKDIR /var/www/html
COPY ./src/ .

# Laravel 初期処理
RUN composer install \
    && php artisan config:clear
