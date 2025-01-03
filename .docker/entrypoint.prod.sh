#!/bin/bash
set -e

# Source environment variables
echo "Sourcing environment variables..."
. /etc/environment

# Run database migrations
echo "Running database migrations..."
php bin/console doctrine:migrations:migrate --no-interaction --allow-no-migration

# Update and start supervisor service
echo "Starting supervisor..."
service supervisor start
supervisorctl reread
supervisorctl update
supervisorctl restart all

exec docker-php-entrypoint "$@"
