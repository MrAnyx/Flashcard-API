#!/bin/bash
set -e

# Start Apache server
echo "Starting Apache..."
exec apache2-foreground

exec docker-php-entrypoint "$@"