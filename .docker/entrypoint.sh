#!/bin/bash
set -e

# Run database migrations
php bin/console doctrine:migrations:migrate --no-interaction --allow-no-migration
