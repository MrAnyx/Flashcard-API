# Pull existing php image
FROM dunglas/frankenphp:php8.3.10

RUN cp $PHP_INI_DIR/php.ini-development $PHP_INI_DIR/php.ini

# Set the working dir
WORKDIR /app

# Install the actual php extensions
RUN install-php-extensions pcov
RUN install-php-extensions pdo_pgsql
RUN install-php-extensions intl
RUN install-php-extensions opcache
RUN install-php-extensions zip
RUN install-php-extensions @composer

# Install Taskfile
RUN sh -c "$(curl --location https://taskfile.dev/install.sh)" -- -d -b  /usr/local/bin
