#!/bin/bash
set -e

# Run database migrations
echo "Running database migrations..."
php bin/console doctrine:migrations:migrate --no-interaction --allow-no-migration

# Update and start supervisor service
echo "Starting S6-Overlay..."
exec /init
