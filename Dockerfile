FROM php:8.2-apache
RUN docker-php-ext-install pdo pdo_mysql

# Extension MongoDB pour les statistiques
RUN apt-get update \
    && apt-get install -y libssl-dev pkg-config \
    && pecl install mongodb \
    && docker-php-ext-enable mongodb