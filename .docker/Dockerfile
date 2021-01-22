FROM php:7.3-apache

COPY ./000-default.conf /etc/apache2/sites-enabled/000-default.conf

RUN apt-get update && apt-get install -y --no-install-recommends \
    software-properties-common \
    zlib1g-dev \
    libzip-dev \
    unzip \
    git \
    wget \
    vim

RUN docker-php-ext-configure zip --with-libzip

RUN docker-php-ext-install pdo_mysql zip

RUN pecl install xdebug && docker-php-ext-enable xdebug

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

RUN apt-get clean; rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/* /usr/share/doc/*

WORKDIR /var/www/html

RUN a2enmod rewrite

EXPOSE 80
