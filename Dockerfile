# Pull existing php image
FROM php:8.2.16-fpm

# Set the working dir
WORKDIR /var/www/html

# Install php extension installer
ADD https://github.com/mlocati/docker-php-extension-installer/releases/latest/download/install-php-extensions /usr/local/bin/
RUN chmod +x /usr/local/bin/install-php-extensions

# Install the actual php extensions
RUN install-php-extensions pcov
RUN install-php-extensions pdo_mysql
RUN install-php-extensions intl
RUN install-php-extensions opcache
RUN install-php-extensions zip

# Install Taskfile
RUN sh -c "$(curl --location https://taskfile.dev/install.sh)" -- -d -b  /usr/local/bin

# Retrieve the lastest version of composer
COPY --from=composer:latest /usr/bin/composer /usr/local/bin/composer
