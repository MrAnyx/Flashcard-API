#!/bin/bash
set -e

# Run database migrations
echo "Running database migrations..."
php bin/console doctrine:migrations:migrate --no-interaction --allow-no-migration

# Update and start supervisor service
echo "Starting supervisor..."
service supervisor start
supervisorctl reread
supervisorctl update
supervisorctl restart all

# Start Apache server
echo "Starting Apache..."
exec apache2-foreground

exec docker-php-entrypoint "$@"
