# Pull existing php image
FROM php:8.2.16

# Set the working dir
WORKDIR /var/www

RUN apt-get update && apt-get install -y apt-transport-https

# Install PCOV using Pecl
RUN pecl install pcov
RUN docker-php-ext-enable pcov

# Install the actual php extensions
RUN docker-php-ext-install pdo
RUN docker-php-ext-enable pdo

RUN docker-php-ext-install pdo_mysql
RUN docker-php-ext-enable pdo_mysql

RUN apt-get install -y libicu-dev
RUN docker-php-ext-configure intl
RUN docker-php-ext-install intl
RUN docker-php-ext-enable intl

RUN docker-php-ext-install opcache
RUN docker-php-ext-enable opcache

RUN apt-get install -y libzip-dev zip 
RUN docker-php-ext-install zip
RUN docker-php-ext-enable zip

# Retrieve the lastest version of composer
COPY --from=composer:latest /usr/bin/composer /usr/local/bin/composer
