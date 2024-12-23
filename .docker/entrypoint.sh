#!/bin/bash
set -e

php bin/console doctrine:migrations:migrate --no-interaction --allow-no-migration
exec apache2-foreground
