FROM php:8.2-apache
RUN docker-php-ext-install pdo pdo_mysql

# Extension MongoDB pour les statistiques
RUN apt-get update \
    && apt-get install -y libssl-dev pkg-config \
    && pecl install mongodb \
    && docker-php-ext-enable mongodb
    
# Programme d'envoi de mails : PHP transmet ses mails à Mailpit (environnement local)
RUN apt-get update \
    && apt-get install -y msmtp \
    && rm -rf /var/lib/apt/lists/*
RUN echo 'sendmail_path = "/usr/bin/msmtp -t --host=mailpit --port=1025 --auth=off --tls=off --from=contact@viteetgourmand.fr"' > /usr/local/etc/php/conf.d/mail.ini