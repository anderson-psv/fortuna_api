FROM php:7.4-apache

RUN apt-get update && apt-get install -y \
    zlib1g-dev \
    libzip-dev \
    unzip

RUN curl -sS https://getcomposer.org/installer | \
    php -- --install-dir=/usr/local/bin --filename=composer


RUN docker-php-ext-install \
    mysqli \
    pdo \
    pdo_mysql \
    zip \
    && mkdir -p /var/www/fortuna \
    && chmod -R 775 /var/www/fortuna

#Realiza a copia do conf que define a pasta root do apache como /var/www/fortuna
COPY ./conf/ /etc/apache2/sites-available

#Realiza a copia do composer, e conforme configurado no container do mesmo, executa no repositorio
#COPY --from=composer /usr/bin/composer /usr/bin/composer

RUN a2enmod rewrite

WORKDIR /var/www/fortuna

EXPOSE 80