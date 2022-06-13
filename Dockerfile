FROM php:7.4-apache

WORKDIR /var/www/fortuna

COPY --from=composer /usr/bin/composer /usr/bin/composer

RUN apt-get update && apt-get install -y \
    zlib1g-dev \
    libzip-dev \
    unzip

RUN docker-php-ext-install \
    mysqli \
    pdo \
    pdo_mysql \
    zip \
    && mkdir -p /var/www/fortuna \
    && chmod -R 775 /var/www/fortuna



#Realiza a copia do conf que define a pasta root do apache como /var/www/fortuna
COPY ./conf/sites-available /etc/apache2/sites-available

#Realiza a copia do composer, e conforme configurado no container do mesmo, executa no repositorio
#COPY --from=composer /usr/bin/composer /usr/bin/composer

RUN a2enmod rewrite

RUN pecl install xdebug

COPY /conf/xdebug.ini $PHP_INI_DIR/conf.d/docker-php-ext-xdebug.ini

RUN docker-php-ext-enable xdebug

EXPOSE 80 9003