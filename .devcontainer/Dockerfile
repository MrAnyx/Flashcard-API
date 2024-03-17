# Dockerfile arguments
ARG VARIANT=8.2.16-apache

# Pull existing php image
FROM php:${VARIANT}

# Set the working dir
WORKDIR /var/www

# Install php extension installer
ADD https://github.com/mlocati/docker-php-extension-installer/releases/latest/download/install-php-extensions /usr/local/bin/
RUN chmod +x /usr/local/bin/install-php-extensions

# Install PCOV using Pecl
RUN pecl install pcov
RUN docker-php-ext-enable pcov

# Install the actual php extensions
RUN install-php-extensions pdo_mysql
RUN install-php-extensions intl
RUN install-php-extensions opcache
RUN install-php-extensions zip
# RUN install-php-extensions xdebug

# Retrieve the lastest version of composer
COPY --from=composer:latest /usr/bin/composer /usr/local/bin/composer

# Copy configuration files
COPY apache/vhosts.conf /etc/apache2/sites-available/000-default.conf
# COPY xdebug/xdebug.ini /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini
# COPY xdebug/error_reporting.ini /usr/local/etc/php/conf.d/error_reporting.ini

# Start apache
CMD apachectl -D FOREGROUND
