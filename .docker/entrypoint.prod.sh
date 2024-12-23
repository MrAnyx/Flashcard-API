#!/bin/bash
set -e

# Run database migrations
echo "Running database migrations..."
php bin/console doctrine:migrations:migrate --no-interaction --allow-no-migration

# Start Apache server
echo "Starting Apache..."
exec apache2-foreground

exec docker-php-entrypoint "$@"