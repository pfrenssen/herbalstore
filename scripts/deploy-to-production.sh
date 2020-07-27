#!/bin/bash

set -e
set -x

# Place the site in maintenance mode.
./vendor/bin/drush @production state:set system.maintenance_mode 1 --input-format=integer
./vendor/bin/drush @production cr

# Backup database.
./vendor/bin/drush @production sql-dump --result-file=auto

# Backup files.
SUFFIX=$(date +"%Y%m%d%H%M%S")
mkdir -p backups/deploy-$SUFFIX
./vendor/bin/drush rsync @production:%files ./backups/deploy-$SUFFIX --yes

# Pull latest changes.
ssh pocomas@srv1.pocomas.be 'cd /home/pocomas/domains/zonnedauw.pocomas.be/herbalstore && git pull'

# Update dependencies.
ssh pocomas@srv1.pocomas.be 'cd /home/pocomas/domains/zonnedauw.pocomas.be/herbalstore && ./vendor/bin/composer install'

# Perform updates.
./vendor/bin/drush @production updatedb --yes
./vendor/bin/drush @production cr
./vendor/bin/drush @production config:import --yes

# Disable maintenance mode.
./vendor/bin/drush @production state:set system.maintenance_mode 0 --input-format=integer
./vendor/bin/drush @production cr
